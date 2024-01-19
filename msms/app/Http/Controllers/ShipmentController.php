<?php

namespace App\Http\Controllers;

use illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Tracking;
use App\Models\Warehouse;
use App\Models\Service;
use App\Models\Customer;

class ShipmentController extends Controller
{
    public function index()
    {
        $this->roleAccess();
        $wareModel = new Warehouse;
        $data['warehouse'] = $wareModel::all();
        return view('pages.newship', $data);
    }
    
    public function import(Request $request){
        $array = Excel::toArray(0, $request->file('excel'));
        return json_encode($array);
    }

    public function lastShippingId()
    {
        $check = DB::table("pool_shipping_id")->count();
        if ($check < 1) {
            $encode = array("shippingIdAv" => "TR/ALY/".date("y")."0001");
            return json_encode($encode);
        }
        $check2 = DB::table("pool_shipping_id")->orderBy("id", "desc")->first();
        $lastId = $check2->id + 1;

        $rolling = true;
        while($rolling==true){
            $check3 = DB::table("data_list")->where("shipping_number","TR/ALY/".date("y")."000" . $lastId)->count();
            $rolling = false;
            if($check3 > 0){
                $rolling = true;
                $lastId ++;            
            }
        }

        $encode = array("shippingIdAv" => "TR/ALY/".date("y")."000" . $lastId);
        return json_encode($encode);
    }

    public function foreignRate(){
        $foreignCurrency = "SGD";
        $table = DB::table("foreign_rate");
        $get = $table->where("id",$foreignCurrency)->first();
        $finalRate = $get->value;
        $dateDBOld = strtotime(date("Y-m-d",strtotime($get->updated_at)));
        $dateNow = strtotime(date("Y-m-d"));
        if($dateNow-$dateDBOld>0||$finalRate==0){
            $finalRate = $this->getForeignRate($foreignCurrency);
            $updateData = [
                "updated_at" => date("Y-m-d H:i:s"),
                "value" => $finalRate
            ];
            $table->where("id",$foreignCurrency)->update($updateData);
        }
        return json_encode($finalRate);
    }

    public function buatOrder(Request $request)
    {
        $username = Auth::user()->username;
        $fullAddress = $request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode');
        $custId = $request->input("regCust");
        $custTypeId = $request->input('custTypeId');
        $textFail = "Gagal Buat Order";
        $textSuccess = "Shipment Order Berhasil Dibuat. Silahkan Cek Pada Halaman Shipment List.";
        
        $firstName = $request->input("firstName");
        $middleName = $request->input('middleName');
        $lastName = $request->input("lastName");
        $fullName = $middleName=="" ? ($lastName==""?$firstName:$firstName." ".$lastName) : ($lastName==""?$firstName." ".$middleName:$firstName." ".$middleName." ".$lastName);

        if ($request->input('statusCust') != "REG") {

            $custModel = new Customer;
            $custModel->first_name = $request->input('firstName');
            $custModel->middle_name = $request->input('middleName') ?? "";
            $custModel->last_name = $request->input('lastName') ?? "";
            $custModel->phone = $request->input('phone');
            $custModel->email = $request->input('email');
            $custModel->address = $request->input('address');
            $custModel->sub_district = $request->input('subDistrict') ?? "";
            $custModel->district = $request->input('district');
            $custModel->city = $request->input('city');
            $custModel->prov = $request->input('prov');
            $custModel->postal_code = $request->input('postalCode');
            $custModel->cust_type_id = $custTypeId;
            $custModel->created_by = $username;
            $custModel->updated_by = $username;

            $insert = $custModel->save();

            $custId = DB::table("cust_list")->where("phone", "=", $request->input("phone"))->value("id");

            $textFail = "Gagal Tambah Customer Dan Buat Order";
            $textSuccess = "Customer Dan Shipment Order Berhasil Dibuat. Silahkan Cek Pada Halaman Shipment List.";

            $dataHistory=[
                "codename" => "BC",
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => $username,
                "description" => "<b>Buat Customer/Client</b> dengan detail,<br><br>
                Tipe : <b>".DB::table("cust_type_list")->where("id", $custTypeId)->value("name")."</b><br>
                First Name : <b>".$request->input('firstName')."</b><br>
                Middle Name : <b>".$request->input('middleName')."</b><br>
                Last Name : <b>".$request->input('lastName')."</b><br>
                Telpon : <b>".$request->input('phone')."</b><br>
                Email : <b>".$request->input('email')."</b><br>
                Alamat : <b>".$request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode')."</b>",
            ];
    
            $insertHistory = DB::table('history_list')->insert($dataHistory);
            
            if(!$insertHistory){
                $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
                return json_encode($encode);
            }
        }

        $orderModel = new Order;
        $orderModel->cust_id = $custId;
        $orderModel->cust_type_id = $custTypeId;
        $orderModel->order_status_id = "READY";
        $orderModel->invoice_id = "";
        $orderModel->created_by = Auth::user()->username;
        $orderModel->updated_by = Auth::user()->username;
        $orderModel->first_name = $request->input('firstName');
        $orderModel->middle_name = $request->input('middleName') ?? "";
        $orderModel->last_name = $request->input('lastName') ?? "";
        $orderModel->phone = $request->input('phone');
        $orderModel->email = $request->input('email');
        $orderModel->address = $request->input('address');
        $orderModel->sub_district = $request->input('subDistrict') ?? "";
        $orderModel->district = $request->input('district');
        $orderModel->city = $request->input('city');
        $orderModel->prov = $request->input('prov');
        $orderModel->postal_code = $request->input('postalCode');
        $orderModel->second_name = $custTypeId=="IND" ? $fullName : '';
        $orderModel->second_phone = $custTypeId=="IND" ? $request->input('phone') : '';

        $insert = $orderModel->save();

        if(!$insert){
            $encode = array("status" => "Gagal", "text" => $textFail);
            return json_encode($encode);
        }

        $dataHistory=[
            "codename" => "BO",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Buat Order</b> dengan detail,<br><br>
            ID Sistem : <b>".DB::table("order_list")->where("phone",$request->input('phone'))->value("id")."</b><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id", $custTypeId)->value("name")."</b><br>
            First Name : <b>".$request->input('firstName')."</b><br>
            Middle Name : <b>".$request->input('middleName')."</b><br>
            Last Name : <b>".$request->input('lastName')."</b><br>
            Telpon : <b>".$request->input('phone')."</b><br>
            Email : <b>".$request->input('email')."</b><br>
            Alamat : <b>".$request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode')."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }
        
        $data = [
            "phone" => $request->input('phone'),
            "message" => "Halo *".$fullName."* below is your order details.
    
Full Name : *".$fullName."*
Whatsapp No : *".$request->input('phone')."*
Email : *".$request->input('email')."*
Full Address : *".$fullAddress."*
            
Kindly wait for further confirmations.
Thank You.

Send from website https://www.mismasslogistic.com",
        ];

        $sendWA = $this->sendWAForm($data);

        if(!$sendWA){
            $encode = array("status" => "Gagal", "text" => "Gagal Kirim Notif Watzap");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => $textSuccess);
        return json_encode($encode);
    }

    public function tableOrder(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggal = $request->input('filterTanggal');
        $filter = [
            "filterTanggal" => $filterTanggal,
            "custTypeId" => $custTypeId
        ];
        $orderModel = new Order();
        $lists = $orderModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $hapusBtn = Auth::user()->shiplist_hapus_order?"<a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->first_name . "','Order','" . url('/shiplist/hapus/order') . "','shiplist')\" href='#'><div style='color:red'>Hapus Data</div></a>":"";
            $buatBtn = Auth::user()->shiplist_buat_invoice?"<a class='dropdown-item' id='buatBtn' data-custId='".$list->cust_id."' data-custTypeId='".$list->cust_type_id."' data-id='" . $list->id . "' data-firstName='" . $list->first_name . "' data-middleName='" . $list->middle_name . "' data-lastName='" . $list->last_name . "' data-phone='".$list->phone."' data-email='".$list->email."' data-address='".$list->address."' data-subDistrict='".$list->sub_district."' data-district='".$list->district."' data-city='".$list->city."' data-prov='".$list->prov."' data-postalCode='".$list->postal_code."' data-secondName='".$list->second_name."' data-secondPhone='".$list->second_phone."' data-tanggal='" . $this->dateFormatIndo($list->created_at,1) . "' href='#'>Buat Invoice</a>":"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$buatBtn.$hapusBtn."</div></div>";

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = "<div class='font-weight-bold'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = $list->first_name . " " . $list->middle_name . " " . $list->last_name;
            $row[] = "<div>" . $list->phone . "</div><div>" . $list->email . "</div><div>" . $list->address . ", " . $list->sub_district . ", " . $list->district . ", " . $list->city . ", " . $list->prov . ", " . $list->postal_code . "</div>";
            $row[] = $list->order_status_id == "READY" ? "<div id='btnStatusId' data-orderid='" . $list->id . "' class='btnStatus btnStatusReady'>" . $list->order_status_id . "</div>" : "<div id='btnStatusId' data-orderid='" . $list->id . "' class='btnStatus btnStatusHold'>" . $list->order_status_id . "</div>";
            if(Auth::user()->shiplist_buat_invoice||Auth::user()->shiplist_hapus_order){
                $row[] = $wholeBtn;
            }
            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $orderModel->countAll(),
            'recordsFiltered' => $orderModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function hapusOrder(Request $request)
    {
        $id = $request->input('id');
        $username = Auth::user()->username;

        $orderModel = new Order;
        $lawas = $orderModel->where("id",$id)->first();

        $dataHistory=[
            "codename" => "HO",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Hapus Order</b> dengan detail,<br><br>
            ID Sistem : <b>".$lawas->id."</b>
            Tipe : <b>".DB::table("cust_type_list")->where("id",$lawas->cust_type_id)->value("name")."</b><br>
            First Name : <b>".$lawas->first_name."</b><br>
            Middle Name : <b>".$lawas->middle_name."</b><br>
            Last Name : <b>".$lawas->last_name."</b><br>
            Telpon : <b>".$lawas->phone."</b><br>
            Email : <b>".$lawas->email."</b><br>
            Alamat : <b>".$lawas->address.", ".$lawas->sub_district.", ".$lawas->district.", ".$lawas->city.", ".$lawas->prov.", ".$lawas->postal_code."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $delete = $orderModel::where('id', '=', $id)->delete();

        if (!$delete) {
            $encode = array("status" => "Gagal", "text" => "Gagal Hapus Order");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Hapus Order");
        return json_encode($encode);
    }

    public function editOrderStatus(Request $request)
    {
        $this->roleAccess();
        $status = $request->input("status");
        $orderId = $request->input("orderId");
        $statusNow = $status == "READY" ? "HOLD" : "READY";
        $username = Auth::user()->username;

        if(!Auth::user()->shiplist_ganti_status){
            $encode = array("status" => "Gagal","text" => "Anda Tidak Berwenang Mengganti Status Order");
            return json_encode($encode);
        }

        $update = DB::table("order_list")->where("id", "=", $orderId)->update(["order_status_id" => $statusNow]);
        $encode = array("status" => "Gagal");
        if ($update) {
            $encode = array("status" => "Berhasil");
        }

        return json_encode($encode);
    }

    public function shiplist()
    {
        $this->roleAccess();
        $wareModel = new Warehouse;
        $servModel = new Service;
        $data['warehouse'] = $wareModel::all();
        $data['service'] = $servModel::all();
        $data['additional'] = DB::table("additional_list")->orderBy("order_byid", "asc")->get();
        return view('pages.shiplist', $data);
    }

    public function buatInvoice(Request $request)
    {
        $success = 0;
        $randomLink = str::random(20);
        $uniqId = str::random(30);
        DB::table('pool_invoice_id')->insert(
            ['uniq_id' => $uniqId]
        );
        $mismass_invoice_id = DB::table('pool_invoice_id')->where("uniq_id", $uniqId)->value("id");
        $prefix_mismass_invoice_id = "INV/AJV/" . date("y");
        $mismass_invoice = $prefix_mismass_invoice_id . $mismass_invoice_id;

        for ($i = 0; $i <= count($request->input("warehouse")) - 1; $i++) {

            $service_name =  DB::table('service_list')->where("id", $request->input("service")[$i])->value("name");

            $invoiceModel = new Invoice;
            $invoiceModel->created_by = Auth::user()->username;
            $invoiceModel->updated_by = Auth::user()->username;
            $invoiceModel->warehouse_id = $request->input("warehouse")[$i];
            $invoiceModel->cust_id = $request->input("dbCustId");
            $invoiceModel->cust_type_id = $request->input("dbCustTypeId");
            $invoiceModel->service_id = $request->input("service")[$i];
            // $invoiceModel->invoice_updated_at = date("Y-m-d H:i:s");
            // $invoiceModel->invoice_created_by = Auth::user()->username;
            // $invoiceModel->invoice_updated_at = date("Y-m-d H:i:s");
            // $invoiceModel->invoice_updated_by = Auth::user()->username;
            $invoiceModel->sender_first_name = $request->input("senderFirstName")[$i]??"";
            $invoiceModel->sender_middle_name = $request->input("senderMiddleName")[$i] ?? "";
            $invoiceModel->sender_last_name = $request->input("senderLastName")[$i] ?? "";
            $invoiceModel->sender_email = $request->input("senderEmail")[$i] ?? "";
            $invoiceModel->sender_phone = $request->input("senderPhone")[$i] ?? "";
            $invoiceModel->sender_address = $request->input("senderAddress")[$i] ?? "";
            $invoiceModel->sender_sub_district = $request->input("senderSubDistrict")[$i] ?? "";
            $invoiceModel->sender_district = $request->input("senderDistrict")[$i] ?? "";
            $invoiceModel->sender_city = $request->input("senderCity")[$i] ?? "";
            $invoiceModel->sender_prov = $request->input("senderProv")[$i] ?? "";
            $invoiceModel->sender_postal_code = $request->input("senderPostalCode")[$i] ?? "";

            $invoiceModel->cons_first_name = $request->input("consFirstName")[$i] ?? "";
            $invoiceModel->cons_middle_name = $request->input("consMiddleName")[$i] ?? "";
            $invoiceModel->cons_last_name = $request->input("consLastName")[$i] ?? "";
            $invoiceModel->cons_email = $request->input("consEmail")[$i] ?? "";
            $invoiceModel->cons_phone = $request->input("consPhone")[$i] ?? "";
            $invoiceModel->cons_address = $request->input("consAddress")[$i];
            $invoiceModel->cons_sub_district = $request->input("consSubDistrict")[$i] ?? "";
            $invoiceModel->cons_district = $request->input("consDistrict")[$i] ?? "";
            $invoiceModel->cons_city = $request->input("consCity")[$i] ?? "";
            $invoiceModel->cons_prov = $request->input("consProv")[$i] ?? "";
            $invoiceModel->cons_postal_code = $request->input("consPostalCode")[$i] ?? "";

            $invoiceModel->mismass_order_id = $request->input("mismassOrderId");
            $invoiceModel->mismass_invoice_id = $mismass_invoice;
            $invoiceModel->mismass_invoice_date = date("Y-m-d", strtotime($request->input("tanggalInvoice")));
            $invoiceModel->mismass_invoice_link = $randomLink;
            $invoiceModel->invoice_status = "UNPAID";
            $invoiceModel->doku_invoice_id = $request->input("invoiceDoku") ?? "";
            $invoiceModel->doku_link = $request->input("linkDoku") ?? "";
            $invoiceModel->bank_name = $request->input("namaBank") ?? "";
            $invoiceModel->bank_account_name = $request->input("namaRekening") ?? "";
            $invoiceModel->bank_account_id = $request->input("noRekening") ?? "";
            $invoiceModel->forwarder_id = "";
            $invoiceModel->shipping_number = "";
            $invoiceModel->shipping_created_at = "";
            $invoiceModel->shipping_created_by = "";
            $invoiceModel->shipping_updated_at = "";
            $invoiceModel->shipping_updated_by = "";
            $invoiceModel->length = isset($request->input("panjang")[$i]) ? $this->normalizeInput($request->input("panjang")[$i]) : 0;
            $invoiceModel->width = isset($request->input("lebar")[$i]) ? $this->normalizeInput($request->input("lebar")[$i]) : 0;
            $invoiceModel->height = isset($request->input("tinggi")[$i]) ? $this->normalizeInput($request->input("tinggi")[$i]) : 0;
            $invoiceModel->weight = isset($request->input("kg")[$i]) ? $this->normalizeInput($request->input("kg")[$i]) : 0;
            $invoiceModel->actual_weight = isset($request->input("actualKg")[$i]) ? $this->normalizeInput($request->input("actualKg")[$i]) : 0;
            $invoiceModel->item = isset($request->input("item")[$i]) ? $this->normalizeInput($request->input("item")[$i]) : 0;
            $invoiceModel->service_name = $service_name;
            $invoiceModel->service_price_per = isset($request->input("pricePer")[$i]) ? $this->normalizeInput($request->input("pricePer")[$i]) : 0;

            $invoiceModel->discount = $this->normalizeInput($request->input("discount" . $i));
            $invoiceModel->packing = $this->normalizeInput($request->input("packing" . $i));
            $invoiceModel->packing_per = $this->normalizeInput($request->input("packingPer" . $i));
            $invoiceModel->packing_total = $this->normalizeInput($request->input("packingTotal" . $i));
            $invoiceModel->packing_desc = $request->input("packingDesc" . $i) ?? "";
            $invoiceModel->import_permit = $this->normalizeInput($request->input("import" . $i));
            $invoiceModel->import_permit_per = $this->normalizeInput($request->input("importPer" . $i));
            $invoiceModel->import_permit_total = $this->normalizeInput($request->input("importTotal" . $i));
            $invoiceModel->import_permit_desc = $request->input("importDesc" . $i) ?? "";
            $invoiceModel->document = $this->normalizeInput($request->input("document" . $i));
            $invoiceModel->document_per = $this->normalizeInput($request->input("documentPer" . $i));
            $invoiceModel->document_total = $this->normalizeInput($request->input("documentTotal" . $i));
            $invoiceModel->document_desc = $request->input("documentDesc" . $i) ?? "";
            $invoiceModel->dr_medicine = $this->normalizeInput($request->input("medicine" . $i));
            $invoiceModel->dr_medicine_per = $this->normalizeInput($request->input("medicinePer" . $i));
            $invoiceModel->dr_medicine_total = $this->normalizeInput($request->input("medicineTotal" . $i));
            $invoiceModel->dr_medicine_desc = $request->input("medicineDesc" . $i) ?? "";
            $invoiceModel->insurance_item_price = $this->normalizeInput($request->input("insurancePriceItem" . $i));
            $invoiceModel->insurance_percent = $this->normalizeInput($request->input("insurancePercent" . $i));
            $invoiceModel->insurance_total = $this->normalizeInput($request->input("insuranceTotal" . $i));
            $invoiceModel->fee_item_price = $this->normalizeInput($request->input("feePriceItem" . $i));
            $invoiceModel->fee_percent = $this->normalizeInput($request->input("feePercent" . $i));
            $invoiceModel->fee_total = $this->normalizeInput($request->input("feeTotal" . $i));
            $invoiceModel->tax_item_price = $this->normalizeInput($request->input("taxPriceItem" . $i));
            $invoiceModel->tax_percent = $this->normalizeInput($request->input("taxPercent" . $i));
            $invoiceModel->tax_total = $this->normalizeInput($request->input("taxTotal" . $i));
            $invoiceModel->extra_cost_price = $this->normalizeInput($request->input("extraCostPrice" . $i));
            $invoiceModel->extra_cost_dest = $request->input("extraCostDest" . $i) ?? "";
            $invoiceModel->extra_cost_vendor_name = $request->input("extraCostVendorName" . $i) ?? "";
            $invoiceModel->extra_cost_shipping_number = $request->input("extraCostShippingNum" . $i) ?? "";
            $invoiceModel->pickup_weight = $this->normalizeInput($request->input("pickUpWeight" . $i));
            $invoiceModel->pickup_charge = $this->normalizeInput($request->input("pickUpCharge" . $i));
            $invoiceModel->sub_total = isset($request->input("subTotal")[$i]) ? $this->normalizeInput($request->input("subTotal")[$i]) : 0;
            $invoiceModel->fc_symbol = $this->normalizeInput($request->input("foreignRateValue"))>0?$request->input("foreignSymbol"):""; 
            $invoiceModel->fc_value = $this->normalizeInput($request->input("foreignRateValue"))>0?$this->normalizeInput($request->input("foreignRateValue")):0; 

            $insert = $invoiceModel->save();

            if ($insert) {
                $success++;
            }
        }

        $updateInvoice = DB::table("order_list")->where("id", $request->input("mismassOrderId"))->update(["invoice_id" => $mismass_invoice]);

        if ($updateInvoice) {
            $this->sendWA($mismass_invoice_id,"BI");
        }

        $dataHistory = [
            "codename" => "BI",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForInvoice($mismass_invoice,"BI"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => "Gagal", "text" => "Gagal Buat Invoice", "url" => "");
        if ($success == count($request->input("warehouse"))) {
            $encode = array("status" => "Berhasil", "text" => "Data Invoice Berhasil dibuat dan telah dikirim ke Whatsapp Customer. Silahkan Cek Pada Tabel Tracking.", "url" => url('/printout/invoice/' . $this->invOnlyId($mismass_invoice)));
        }

        return json_encode($encode);
    }

    public function tableInvoice(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggalAwal = $request->input('filterTanggalAwal');
        $filterTanggalAkhir = $request->input('filterTanggalAkhir');
        $filterWarehouse = $request->input('filterWarehouse');
        $filterService = $request->input('filterService');
        $filter = [
            $filterTanggalAwal,
            $filterTanggalAkhir,
            $filterWarehouse,
            $filterService,
            $custTypeId
        ];
        $invoiceModel = new Invoice();
        $lists = $invoiceModel->getDT($request, $search, $filter);

        foreach ($lists as $list) {
            $getData = DB::table('data_list')->select('id','sub_total','weight','item','length','height','width','cons_first_name','cons_middle_name','cons_last_name','cons_phone','cons_address','cons_sub_district','cons_district','cons_city','cons_prov','cons_postal_code')->where('mismass_invoice_id',$list->mismass_invoice_id)->get();
            $countRow = count($getData);
            $dokuInvoiceId = $list->doku_invoice_id != "" ? ($list->updated_at!=$list->created_at?"<div style='color:red'>".$list->doku_invoice_id."</div>":$list->doku_invoice_id) : "-";

            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px;'>".$getRank."</div>" : "";

            $buatResiBtn = Auth::user()->shiplist_buat_resi?"<a class='dropdown-item' id='buatResiBtn' data-getData='".$getData."' data-senderAddress='" . $list->sender_address . ", " . $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code . "' data-senderPhone='" . $list->sender_phone . "' data-senderName='" . $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name . "' data-consAddress='" . $list->cons_address . ", " . $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code . "' data-consPhone='" . $list->cons_phone . "' data-consName='" . $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name . "' data-custTypeId='".$list->cust_type_id."' data-countRow='".$countRow."' data-totalPrice='" . $this->rupiah($list->totalPrice) . "' data-totalItem='" . $list->totalItem . "' data-totalWeight='" . $list->totalWeight . "' data-custTypeName='" . $list->custTypeName . "' data-mismassInvoiceDate='" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "' data-createdAt='" . $this->dateFormatIndo($list->created_at,1) . "' data-dokuInvoiceId='" . $list->doku_invoice_id . "' data-mismassInvoiceId='" . $list->mismass_invoice_id . "' href='#'>Buat Resi</a>":"";
            $printoutInvoice = Auth::user()->shiplist_printout_invoice?"<a class='dropdown-item' href='" . url('/printout/invoice/' . $this->invOnlyId($list->mismass_invoice_id)) . "' target='_blank'>Print Invoice</a>":"";
            $editInvoiceBtn = Auth::user()->shiplist_edit_invoice?"<a class='dropdown-item' id='editInvoiceBtn' data-tglInv='".date("d-m-Y",strtotime($list->mismass_invoice_date))."' data-id='" . $list->mismass_order_id . "' href='#'>Edit Data</a>":"";
            $hapusBtn = Auth::user()->shiplist_hapus_invoice?"<a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->mismass_invoice_id . "','" . $list->mismass_invoice_id . "','Invoice','" . url('/shiplist/hapus/invoice/') . "','shiplist')\" href='#'><div style='color:red'>Hapus Data</div></a>":"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$buatResiBtn.$printoutInvoice.$editInvoiceBtn.$hapusBtn."</div></div>";

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = "<div class='font-weight-bold'>" . $list->mismass_invoice_id . "</div><div>" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "</div>";
            $row[] = "<div class='font-weight-bold'>" . $dokuInvoiceId . "</div>";
            $row[] = "<div class='font-weight-bold'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = $custTypeId=="IND" ? $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name : $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name;
            $row[] = $custTypeId=="IND" ? "<div>".$list->cons_phone."</div><div>".$list->cons_address . ", " . $list->cons_sub_district . ", " . $list->cons_district . ", " . $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code."</div>" : "<div>".$list->sender_address . ", " . $list->sender_sub_district . ", " . $list->sender_district . ", " . $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code."</div>";
            $row[] = "<div>" . number_format((float)$list->totalWeight, 2, '.', '') . " KG</div><div>" . $list->totalItem . " Item</div>";
            
            if(Auth::user()->shiplist_nom){
                $detilDisc = "<div>Total : ".$this->rupiah($list->totalDisc+$list->totalPrice)."</div><div>Diskon : ".$this->rupiah($list->totalDisc)."</div>";
                $detilPrice = $list->doku_link!=""?"<div><a href='" . $list->doku_link . "' target='_blank'>" . $list->doku_link . "</a></div>":"<div style='color:red'>".$list->bank_name." - ".$list->bank_account_name."</div><div style='color:blue'>".$list->bank_account_id."</div>";
                $row[] = $detilDisc."<div class='font-weight-bold'>Total Biaya : " . $this->rupiah($list->totalPrice) . "</div>".$detilPrice;
            }

            if(Auth::user()->shiplist_edit_invoice||Auth::user()->shiplist_hapus_invoice||Auth::user()->shiplist_buat_resi||Auth::user()->shiplist_printout_invoice){
                $row[] = $wholeBtn;
            }

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $invoiceModel->countAll(),
            'recordsFiltered' => $invoiceModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function editInvoice(Request $request){
        $n = 0;
        $i = 0;    
        $success = 0; 
        $getOld = DB::table('data_list')
                    ->where('mismass_invoice_id',$request->input('mismassInvoiceId'))
                    ->get();

        if(count($request->input("warehouse"))!=count($getOld)){
            $n++;
        }

        foreach($getOld as $go){

                $doku_invoice_id = $request->input("invoiceDoku") ?? "";
                
                $sender_first_name = $request->input("senderFirstName")[$i] ?? "";
                $sender_middle_name = $request->input("senderMiddleName")[$i] ?? "";
                $sender_last_name = $request->input("senderLastName")[$i] ?? "";
                $sender_email = $request->input("senderEmail")[$i] ?? "";
                $sender_phone = $request->input("senderPhone")[$i] ?? "";
                $sender_address = $request->input("senderAddress")[$i] ?? "";
                $sender_sub_district = $request->input("senderSubDistrict")[$i] ?? "";
                $sender_district = $request->input("senderDistrict")[$i] ?? "";
                $sender_city = $request->input("senderCity")[$i] ?? "";
                $sender_prov = $request->input("senderProv")[$i] ?? "";
                $sender_postal_code = $request->input("senderPostalCode")[$i] ?? "";
                
                $cons_first_name = $request->input("consFirstName")[$i] ?? "";
                $cons_middle_name = $request->input("consMiddleName")[$i] ?? "";
                $cons_last_name = $request->input("consLastName")[$i] ?? "";
                $cons_email = $request->input("consEmail")[$i] ?? "";
                $cons_phone = $request->input("consPhone")[$i] ?? "";
                $cons_address = $request->input("consAddress")[$i] ?? "";
                $cons_sub_district = $request->input("consSubDistrict")[$i] ?? "";
                $cons_district = $request->input("consDistrict")[$i] ?? "";
                $cons_city = $request->input("consCity")[$i] ?? "";
                $cons_prov = $request->input("consProv")[$i] ?? "";
                $cons_postal_code = $request->input("consPostalCode")[$i] ?? "";
                
                $doku_link = $request->input("linkDoku") ?? "";
                $bank_name = $request->input("namaBank") ?? "";
                $bank_account_name = $request->input("namaRekening") ?? "";
                $bank_account_id = $request->input("noRekening") ?? "";
                $length = isset($request->input("panjang")[$i]) ? $this->normalizeInput($request->input("panjang")[$i]) : 0;
                $width = isset($request->input("lebar")[$i]) ? $this->normalizeInput($request->input("lebar")[$i]) : 0;
                $height = isset($request->input("tinggi")[$i]) ? $this->normalizeInput($request->input("tinggi")[$i]) : 0;
                $weight = isset($request->input("kg")[$i]) ? $this->normalizeInput($request->input("kg")[$i]) : 0;
                $actualWeight = isset($request->input("actualKg")[$i]) ? $this->normalizeInput($request->input("actualKg")[$i]) : 0;
                $item = isset($request->input("item")[$i]) ? $this->normalizeInput($request->input("item")[$i]) : 0;
                $discount = $this->normalizeInput($request->input("discount" . $i));
                $packing = $this->normalizeInput($request->input("packing" . $i));
                $packing_per = $this->normalizeInput($request->input("packingPer" . $i));
                $packing_total = $this->normalizeInput($request->input("packingTotal" . $i));
                $packing_desc = $request->input("packingDesc" . $i) ?? "";
                $import_permit = $this->normalizeInput($request->input("import" . $i));
                $import_permit_per = $this->normalizeInput($request->input("importPer" . $i));
                $import_permit_total = $this->normalizeInput($request->input("importTotal" . $i));
                $import_permit_desc = $request->input("importDesc" . $i) ?? "";
                $document = $this->normalizeInput($request->input("document" . $i));
                $document_per = $this->normalizeInput($request->input("documentPer" . $i));
                $document_total = $this->normalizeInput($request->input("documentTotal" . $i));
                $document_desc = $request->input("documentDesc" . $i) ?? "";
                $dr_medicine = $this->normalizeInput($request->input("medicine" . $i));
                $dr_medicine_per = $this->normalizeInput($request->input("medicinePer" . $i));
                $dr_medicine_total = $this->normalizeInput($request->input("medicineTotal" . $i));
                $dr_medicine_desc = $request->input("medicineDesc" . $i) ?? "";
                $insurance_item_price = $this->normalizeInput($request->input("insurancePriceItem" . $i));
                $insurance_percent = $this->normalizeInput($request->input("insurancePercent" . $i));
                $insurance_total = $this->normalizeInput($request->input("insuranceTotal" . $i));
                $fee_item_price = $this->normalizeInput($request->input("feePriceItem" . $i));
                $fee_percent = $this->normalizeInput($request->input("feePercent" . $i));
                $fee_total = $this->normalizeInput($request->input("feeTotal" . $i));
                $tax_item_price = $this->normalizeInput($request->input("taxPriceItem" . $i));
                $tax_percent = $this->normalizeInput($request->input("taxPercent" . $i));
                $tax_total = $this->normalizeInput($request->input("taxTotal" . $i));
                $extra_cost_price = $this->normalizeInput($request->input("extraCostPrice" . $i));
                $extra_cost_dest = $request->input("extraCostDest" . $i) ?? "";
                $extra_cost_vendor_name = $request->input("extraCostVendorName" . $i) ?? "";
                $extra_cost_shipping_number = $request->input("extraCostShippingNum" . $i) ?? "";
                $sub_total = isset($request->input("subTotal")[$i]) ? $this->normalizeInput($request->input("subTotal")[$i]) : 0;
                $fc_symbol = $request->input("foreignSymbol") ?? "";
                $fc_value = $this->normalizeInput($request->input("foreignRateValue")) ?? 0;

                $go->warehouse_id != $request->input("warehouse")[$i] ? $n++ : '';
                $go->service_id != $request->input("service")[$i] ? $n++ : '';
                $go->mismass_order_id != $request->input("mismassOrderId") ? $n++ : '';
                $go->mismass_invoice_date != date("Y-m-d H:i:s", strtotime($request->input("tanggalInvoice"))) ? $n++ : '';
                $go->sender_first_name != $sender_first_name ? $n++ : '';
                $go->sender_middle_name != $sender_middle_name ? $n++ : '';
                $go->sender_last_name != $sender_last_name ? $n++ : '';
                $go->sender_email != $sender_email ? $n++ : '';
                $go->sender_phone != $sender_phone ? $n++ : '';
                $go->sender_address != $sender_address ? $n++ : '';
                $go->sender_sub_district != $sender_sub_district ? $n++ : '';
                $go->sender_district != $sender_district ? $n++ : '';
                $go->sender_city != $sender_city ? $n++ : '';
                $go->sender_prov != $sender_prov ? $n++ : '';
                $go->sender_postal_code != $sender_postal_code ? $n++ : '';
                
                $go->cons_first_name != $cons_first_name ? $n++ : '';
                $go->cons_middle_name != $cons_middle_name ? $n++ : '';
                $go->cons_last_name != $cons_last_name ? $n++ : '';
                $go->cons_email != $cons_email ? $n++ : '';
                $go->cons_phone != $cons_phone ? $n++ : '';
                $go->cons_address != $cons_address ? $n++ : '';
                $go->cons_sub_district != $cons_sub_district ? $n++ : '';
                $go->cons_district != $cons_district ? $n++ : '';
                $go->cons_city != $cons_city ? $n++ : '';
                $go->cons_prov != $cons_prov ? $n++ : '';
                $go->cons_postal_code != $cons_postal_code ? $n++ : '';

                $go->doku_invoice_id != $doku_invoice_id ? $n++ : '';
                $go->doku_link != $doku_link ? $n++ : '';
                $go->bank_name != $bank_name ? $n++ : '';
                $go->bank_account_name != $bank_account_name ? $n++ : '';
                $go->bank_account_id != $bank_account_id ? $n++ : '';
                $go->length != $length ? $n++ : '';
                $go->width != $width ? $n++ : '';
                $go->height != $height ? $n++ : '';
                $go->weight != $weight ? $n++ : '';
                $go->actual_weight != $actualWeight ? $n++ : '';
                $go->item != $item ? $n++ : '';
                $go->discount != $discount ? $n++ : '';
                $go->packing != $packing ? $n++ : '';
                $go->packing_per != $packing_per ? $n++ : '';
                $go->packing_total != $packing_total ? $n++ : '';
                $go->packing_desc != $packing_desc ? $n++ : '';
                $go->import_permit != $import_permit ? $n++ : '';
                $go->import_permit_per != $import_permit_per ? $n++ : '';
                $go->import_permit_total != $import_permit_total ? $n++ : '';
                $go->import_permit_desc != $import_permit_desc ? $n++ : '';
                $go->document != $document ? $n++ : '';
                $go->document_per != $document_per ? $n++ : '';
                $go->document_total != $document_total ? $n++ : '';
                $go->document_desc != $document_desc ? $n++ : '';
                $go->dr_medicine != $dr_medicine ? $n++ : '';
                $go->dr_medicine_per != $dr_medicine_per ? $n++ : '';
                $go->dr_medicine_total != $dr_medicine_total ? $n++ : '';
                $go->dr_medicine_desc != $dr_medicine_desc ? $n++ : '';
                $go->insurance_item_price != $insurance_item_price ? $n++ : '';
                $go->insurance_percent != $insurance_percent ? $n++ : '';
                $go->insurance_total != $insurance_total ? $n++ : '';
                $go->tax_item_price != $tax_item_price ? $n++ : '';
                $go->tax_percent != $tax_percent ? $n++ : '';
                $go->tax_total != $tax_total ? $n++ : '';
                $go->fee_item_price != $fee_item_price ? $n++ : '';
                $go->fee_percent != $fee_percent ? $n++ : '';
                $go->fee_total != $fee_total ? $n++ : '';
                $go->extra_cost_price != $extra_cost_price ? $n++ : '';
                $go->extra_cost_dest != $extra_cost_dest ? $n++ : '';
                $go->extra_cost_vendor_name != $extra_cost_vendor_name ? $n++ : '';
                $go->extra_cost_shipping_number != $extra_cost_shipping_number ? $n++ : '';
                $go->sub_total != $sub_total ? $n++ : '';
                $go->fc_symbol != $fc_symbol ? $n++ : '';
                $go->fc_value != $fc_value ? $n++ : '';

                $i++;
        }

        //jika tidak ada perbedaan maka tidak disimpan
        if($n==0){
            $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "url" => url('/printout/invoice/' . $this->invOnlyId($request->input('mismassInvoiceId'))));
            return json_encode($encode);
        }

        $dataHistory = [
            "codename" => "EI",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForEditInvoice($request),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        //jika ada perbedaan maka yg lama dihapus dan yang baru diinsert
        DB::table('data_list')->where("mismass_invoice_id",$request->input("mismassInvoiceId"))->delete();

        for ($i = 0; $i <= count($request->input("warehouse")) - 1; $i++) {

            $values = array ("created_at" => date("Y-m-d H:i:s",strtotime($request->input('createdAt'))),
                            "created_by" => Auth::user()->username,
                            "updated_at" => date("Y-m-d H:i:s"),
                            "updated_by" => Auth::user()->username,
                            "warehouse_id" => $request->input("warehouse")[$i],
                            "cust_id" => $request->input("custId"),
                            "cust_type_id" => $request->input("custTypeId"),
                            "service_id" => $request->input("service")[$i],
                            "mismass_order_id" => $request->input("mismassOrderId"),
                            "mismass_invoice_id" => $request->input('mismassInvoiceId'),
                            "mismass_invoice_date" => date("Y-m-d", strtotime($request->input("tanggalInvoice"))),
                            "mismass_invoice_link" => $request->input('mismassInvoiceLink'),
                            "invoice_status" => "UNPAID",
                            "sender_first_name" => $request->input("senderFirstName")[$i] ?? "",
                            "sender_middle_name" => $request->input("senderMiddleName")[$i] ?? "",
                            "sender_last_name" => $request->input("senderLastName")[$i] ?? "",
                            "sender_email" => $request->input("senderEmail")[$i] ?? "",
                            "sender_phone" => $request->input("senderPhone")[$i] ?? "",
                            "sender_address" => $request->input("senderAddress")[$i] ?? "",
                            "sender_sub_district" => $request->input("senderSubDistrict")[$i] ?? "",
                            "sender_district" => $request->input("senderDistrict")[$i] ?? "",
                            "sender_city" => $request->input("senderCity")[$i] ?? "",
                            "sender_prov" => $request->input("senderProv")[$i] ?? "",
                            "sender_postal_code" => $request->input("senderPostalCode")[$i] ?? "",
                            "cons_first_name" => $request->input("consFirstName")[$i] ?? "",
                            "cons_middle_name" => $request->input("consMiddleName")[$i] ?? "",
                            "cons_last_name" => $request->input("consLastName")[$i] ?? "",
                            "cons_email" => $request->input("consEmail")[$i] ?? "",
                            "cons_phone" => $request->input("consPhone")[$i] ?? "",
                            "cons_address" => $request->input("consAddress")[$i] ?? "",
                            "cons_sub_district" => $request->input("consSubDistrict")[$i] ?? "",
                            "cons_district" => $request->input("consDistrict")[$i] ?? "",
                            "cons_city" => $request->input("consCity")[$i] ?? "",
                            "cons_prov" => $request->input("consProv")[$i] ?? "",
                            "cons_postal_code" => $request->input("consPostalCode")[$i] ?? "",
                            "doku_invoice_id" => $request->input("invoiceDoku") ?? "",
                            "doku_link" => $request->input("linkDoku") ?? "",
                            "bank_name" => $request->input("namaBank") ?? "",
                            "bank_account_name" => $request->input("namaRekening") ?? "",
                            "bank_account_id" => $request->input("noRekening") ?? "",
                            "forwarder_id" => "",
                            "shipping_number" => "",
                            "shipping_created_at" => "",
                            "shipping_created_by" => "",
                            "shipping_updated_at" => "",
                            "shipping_updated_by" => "",
                            "length" => isset($request->input("panjang")[$i]) ? $this->normalizeInput($request->input("panjang")[$i]) : 0,
                            "width" => isset($request->input("lebar")[$i]) ? $this->normalizeInput($request->input("lebar")[$i]) : 0,
                            "height" => isset($request->input("tinggi")[$i]) ? $this->normalizeInput($request->input("tinggi")[$i]) : 0,
                            "weight" => isset($request->input("kg")[$i]) ? $this->normalizeInput($request->input("kg")[$i]) : 0,
                            "actual_weight" => isset($request->input("actualKg")[$i]) ? $this->normalizeInput($request->input("actualKg")[$i]) : 0,
                            "item" => isset($request->input("item")[$i]) ? $this->normalizeInput($request->input("item")[$i]) : 0,
                            "service_name" => $request->input('serviceName'),
                            "service_price_per" => isset($request->input("pricePer")[$i]) ? $this->normalizeInput($request->input("pricePer")[$i]) : 0,
                            "discount" => $this->normalizeInput($request->input("discount" . $i)),
                            "packing" => $this->normalizeInput($request->input("packing" . $i)),
                            "packing_per" => $this->normalizeInput($request->input("packingPer" . $i)),
                            "packing_total" => $this->normalizeInput($request->input("packingTotal" . $i)),
                            "packing_desc" => $request->input("packingDesc" . $i) ?? "",
                            "import_permit" => $this->normalizeInput($request->input("import" . $i)),
                            "import_permit_per" => $this->normalizeInput($request->input("importPer" . $i)),
                            "import_permit_total" => $this->normalizeInput($request->input("importTotal" . $i)),
                            "import_permit_desc" => $request->input("importDesc" . $i) ?? "",
                            "document" => $this->normalizeInput($request->input("document" . $i)),
                            "document_per" => $this->normalizeInput($request->input("documentPer" . $i)),
                            "document_total" => $this->normalizeInput($request->input("documentTotal" . $i)),
                            "document_desc" => $request->input("documentDesc" . $i) ?? "",
                            "dr_medicine" => $this->normalizeInput($request->input("medicine" . $i)),
                            "dr_medicine_per" => $this->normalizeInput($request->input("medicinePer" . $i)),
                            "dr_medicine_total" => $this->normalizeInput($request->input("medicineTotal" . $i)),
                            "dr_medicine_desc" => $request->input("medicineDesc" . $i) ?? "",
                            "insurance_item_price" => $this->normalizeInput($request->input("insurancePriceItem" . $i)),
                            "insurance_percent" => $this->normalizeInput($request->input("insurancePercent" . $i)),
                            "insurance_total" => $this->normalizeInput($request->input("insuranceTotal" . $i)),
                            "fee_item_price" => $this->normalizeInput($request->input("feePriceItem" . $i)),
                            "fee_percent" => $this->normalizeInput($request->input("feePercent" . $i)),
                            "fee_total" => $this->normalizeInput($request->input("feeTotal" . $i)),
                            "tax_item_price" => $this->normalizeInput($request->input("taxPriceItem" . $i)),
                            "tax_percent" => $this->normalizeInput($request->input("taxPercent" . $i)),
                            "tax_total" => $this->normalizeInput($request->input("taxTotal" . $i)),
                            "extra_cost_price" => $this->normalizeInput($request->input("extraCostPrice" . $i)),
                            "extra_cost_dest" => $request->input("extraCostDest" . $i) ?? "",
                            "extra_cost_vendor_name" => $request->input("extraCostVendorName" . $i) ?? "",
                            "extra_cost_shipping_number" => $request->input("extraCostShippingNum" . $i) ?? "",
                            "sub_total" => isset($request->input("subTotal")[$i]) ? $this->normalizeInput($request->input("subTotal")[$i]) : 0,
                            "fc_symbol" => $request->input("foreignSymbol") ?? "",
                            "fc_value" => $this->normalizeInput($request->input("foreignRateValue")) ?? 0);

            $insert = DB::table('data_list')->insert($values);

            if ($insert) {
                $success++;
            }
        }

        $this->sendWA($request->input('mismassInvoiceId'),"EI");

        $encode = array("status" => "Gagal", "text" => "Gagal Buat Invoice", "url" => "");
        if ($success == count($request->input("warehouse"))) {
            $encode = array("status" => "Berhasil", "text" => "Data Invoice Berhasil diedit dan telah dikirim ke Whatsapp Customer.", "url" => url('/printout/invoice/' . $this->invOnlyId($request->input('mismassInvoiceId'))));
        }

        return json_encode($encode);

    }

    // public function editDetilCons(Request $request){
    //     $n=0;
    //     $orderId = $request->input("orderId");
    //     $firstName = $request->input("firstName");
    //     $middleName = $request->input("middleName");
    //     $lastName = $request->input("lastName");
    //     $phone = $request->input("phone");
    //     $email = $request->input("email");
    //     $address = $request->input("address");
    //     $subDistrict = $request->input("subDistrict");
    //     $district = $request->input("district");
    //     $city = $request->input("city");
    //     $prov = $request->input("prov");
    //     $postalCode = $request->input("postalCode");

    //     $getOrderList = DB::table("order_list")->where("id",$orderId)->get();
        
    //     foreach($getOrderList as $gol){
    //         $oldCreatedAt = $gol->created_at;
    //         $oldInvoiceId = $gol->invoice_id;
    //         $oldFirstName = $gol->first_name;
    //         $oldMiddleName = $gol->middle_name;
    //         $oldLastName = $gol->last_name;
    //         $oldPhone = $gol->phone;
    //         $oldEmail = $gol->email;
    //         $oldAddress = $gol->address;
    //         $oldSubDistrict = $gol->sub_district;
    //         $oldDistrict = $gol->district;
    //         $oldCity = $gol->city;
    //         $oldProv = $gol->prov;
    //         $oldPostalCode = $gol->postal_code;
            
    //         $oldFirstName != $firstName ? $n++ : '';
    //         $oldMiddleName != $middleName ? $n++ : '';
    //         $oldLastName != $lastName ? $n++ : '';
    //         $oldPhone != $phone ? $n++ : '';
    //         $oldEmail != $email ? $n++ : '';
    //         $oldAddress != $address ? $n++ : '';
    //         $oldSubDistrict != $subDistrict ? $n++ : '';
    //         $oldDistrict != $district ? $n++ : '';
    //         $oldCity != $city ? $n++ : '';
    //         $oldProv != $prov ? $n++ : '';
    //         $oldPostalCode != $postalCode ? $n++ : '';
    //     }
        
    //     if($n==0){
    //         $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "data" => "");
    //         return json_encode($encode);
    //     }

    //     $updateDataList = DB::table("data_list")->where("mismass_order_id",$orderId)->update([
    //         "updated_at" => date("Y-m-d H:i:s"),
    //         "updated_by" => Auth::user()->username,
    //         "cons_first_name" => $firstName,
    //         "cons_middle_name" => $middleName,
    //         "cons_last_name" => $lastName,
    //         "cons_phone" => $phone,
    //         "cons_email" => $email,
    //         "cons_address" => $address,
    //         "cons_sub_district" => $subDistrict,
    //         "cons_district" => $district,
    //         "cons_city" => $city,
    //         "cons_prov" => $prov,
    //         "cons_postal_code" => $postalCode,
    //     ]);

    //     if(!$updateDataList){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Input Data List");
    //         return json_encode($encode);
    //     }

    //     $updateOrderList = DB::table("order_list")->where("id",$orderId)->update([
    //         "updated_at" => date("Y-m-d H:i:s"),
    //         "updated_by" => Auth::user()->username,
    //         "first_name" => $firstName,
    //         "middle_name" => $middleName,
    //         "last_name" => $lastName,
    //         "phone" => $phone,
    //         "email" => $email,
    //         "address" => $address,
    //         "sub_district" => $subDistrict,
    //         "district" => $district,
    //         "city" => $city,
    //         "prov" => $prov,
    //         "postal_code" => $postalCode,
    //     ]);

    //     if(!$updateOrderList){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Input Order List");
    //         return json_encode($encode);
    //     }

    //     $description = "<b>Edit Data Penerima</b> dengan detail,<br><br>
    //     <div style='display:flex'>
    //     <div style='width:50%;padding-right:10px;'>
    //     <b style='font-size:20px'>DATA LAMA</b><br>
    //     Tanggal order : <b>".$this->dateFormatIndo($oldCreatedAt,2)."</b><br>
    //     ID Sistem : <b>".$orderId."</b><br>
    //     No. invoice Mismass: <b>".$oldInvoiceId."</b><br>
    //     <br>
    //     Nama Depan : <b>".$oldFirstName."</b><br>
    //     Nama Tengah : <b>".$oldMiddleName."</b><br>
    //     Nama Terakhir : <b>".$oldLastName."</b><br>
    //     Email : <b>".$oldEmail."</b><br>
    //     Telpon : <b>".$oldPhone."</b><br>
    //     Alamat : <b>".$oldAddress."</b><br>
    //     Kelurahan : <b>".$oldSubDistrict."</b><br>
    //     Kecamatan : <b>".$oldDistrict."</b><br>
    //     Kab/Kota : <b>".$oldCity."</b><br>
    //     Provinsi : <b>".$oldProv."</b><br>
    //     Kode Pos : <b>".$oldPostalCode."</b><br>
    //     <br>        
    //     </div>
    //     <div style='width:50%;padding-right:10px;'>
    //     <b style='font-size:20px'>DATA BARU</b><br>
    //     Tanggal order : <b>".$this->dateFormatIndo($oldCreatedAt,2)."</b><br>
    //     ID Sistem : <b>".$orderId."</b><br>
    //     No. invoice Mismass: <b>".$oldInvoiceId."</b><br>
    //     <br>
    //     Nama Depan : <b>".$firstName."</b><br>
    //     Nama Tengah : <b>".$middleName."</b><br>
    //     Nama Terakhir : <b>".$lastName."</b><br>
    //     Email : <b>".$email."</b><br>
    //     Telpon : <b>".$phone."</b><br>
    //     Alamat : <b>".$address."</b><br>
    //     Kelurahan : <b>".$subDistrict."</b><br>
    //     Kecamatan : <b>".$district."</b><br>
    //     Kab/Kota : <b>".$city."</b><br>
    //     Provinsi : <b>".$prov."</b><br>
    //     Kode Pos : <b>".$postalCode."</b><br>
    //     <br>
    //     </div>
    //     </div>";

    //     $dataHistory = [
    //         "codename" => "EDP",
    //         "created_at" => date("Y-m-d H:i:s"),
    //         "created_by" => Auth::user()->username,
    //         "description" => $description,
    //     ];
    //     $insertHistory = DB::table("history_list")->insert($dataHistory);
    //     if(!$insertHistory){
    //         $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
    //         return json_encode($encode);
    //     }

    //     $data["firstName"] = $firstName;
    //     $data["middleName"] = $middleName;
    //     $data["lastName"] = $lastName;
    //     $data["phone"] = $phone;
    //     $data["email"] = $email;
    //     $data["address"] = $address;
    //     $data["subDistrict"] = $subDistrict;
    //     $data["district"] = $district;
    //     $data["city"] = $city;
    //     $data["prov"] = $prov;
    //     $data["postalCode"] = $postalCode;

    //     $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Data Penerima", "data"=>$data);
    //     return json_encode($encode);
    // }

    public function editInvoiceGetData(Request $request){
        $mismassOrderId = $request->input("mismassOrderId");

        $data = DB::table("data_list")
                ->select("data_list.*","order_list.created_at as orderCreatedAt")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->where("data_list.mismass_order_id","=",$mismassOrderId)
                ->get();

        $output = [
            'data' => $data
        ];

        return json_encode($output);
    }

    public function hapusInvoice(Request $request){
        $mismassInvoiceId = $request->input("id");

        $this->sendWa($mismassInvoiceId,"HI");

        $dataHistory = [
            "codename" => "BI",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForInvoice($mismassInvoiceId,"HI"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $delete = DB::table("data_list")->where("mismass_invoice_id",$mismassInvoiceId)->delete();
        if(!$delete){
            $encode = array("status" => "Gagal", "text" => "Gagal Hapus Invoice 001");
            return json_encode($encode);
        }

        // $orderModel = new Order;
        // $update = $orderModel->where("id",$get->mismass_order_id)->update(["invoice_id" => ""]);

        // if(!$update){
        //     $encode = array("status" => "Gagal", "text" => "Gagal Hapus Invoice 002");
        //     return json_encode($encode);
        // }

        $lawas = DB::table("order_list")->where("invoice_id",$mismassInvoiceId)->first();
        $dataHistory=[
            "codename" => "HO",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => "<b>Hapus Order</b> dengan detail,<br><br>
            ID Sistem : <b>".$lawas->id."</b><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id", $lawas->cust_type_id)->value("name")."</b><br>
            First Name : <b>".$lawas->first_name."</b><br>
            Middle Name : <b>".$lawas->middle_name."</b><br>
            Last Name : <b>".$lawas->last_name."</b><br>
            Telpon : <b>".$lawas->phone."</b><br>
            Email : <b>".$lawas->email."</b><br>
            Alamat : <b>".$lawas->address.", ".$lawas->sub_district.", ".$lawas->district.", ".$lawas->city.", ".$lawas->prov.", ".$lawas->postal_code."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $deleteOrder = DB::table("order_list")->where("invoice_id",$mismassInvoiceId)->delete();
        if(!$deleteOrder){
            $encode = array("status" => "Gagal", "text" => "Gagal Hapus Invoice 002");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Hapus Invoice");
        return json_encode($encode);
    }

    public function buatResi(Request $request)
    {
        $cust_type_id = $request->input("custTypeId");

        if($cust_type_id=="IND"){

            $shipping_number = $request->input("noResi")[0];
            if ($request->input("tipeForwarder")[0]  != "VENDOR") {
                $uniqId = str::random(30);
                DB::table('pool_shipping_id')->insert(
                    ['uniq_id' => $uniqId]
                );
                $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
                $prefix_shipping_id = "TR/ALY/" . date("y");
                $shipping_number = $prefix_shipping_id . $shipping_id;
            }

            $update = DB::table("data_list")->where("mismass_invoice_id", "=", $request->input("mismassInvoiceId"))
            ->update([
                "forwarder_id" => $request->input("tipeForwarder")[0],
                "forwarder_name" => $request->input("namaForwarder")[0] ?? "",
                "shipping_number" => $shipping_number,
                "shipping_created_at" => date("Y-m-d H:i:s"),
                "shipping_created_by" => Auth::user()->username,
                "shipping_updated_at" => date("Y-m-d H:i:s"),
                "shipping_updated_by" => Auth::user()->username,
                "invoice_status" => "PAID"
            ]);

            $encode = array("status" => "Gagal", "text" => "Gagal Buat Resi");
            if ($update) {
                $this->sendWA($request->input("mismassInvoiceId"),"BR");
                $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Resi Dan Telah Dikirim Ke Whatsapp Customer. Silahkan Cek Pada Tabel Status.", "url" => url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($shipping_number, $request->input("tipeForwarder")[0]).'=')));
            }

        }else{

            $saklar = true;
            $urlResi = "/printout/resi/";
            for($i=0;$i<=count($request->input('id'))-1;$i++){

                $shipping_number = $request->input("noResi")[$i];
                if ($request->input("tipeForwarder")[$i]  != "VENDOR") {
                    $uniqId = str::random(30);
                    DB::table('pool_shipping_id')->insert(
                        ['uniq_id' => $uniqId]
                    );
                    $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
                    $prefix_shipping_id = "TR/ALY/" . date("y");
                    $shipping_number = $prefix_shipping_id . $shipping_id;
                }
                
                $urlResi .= $this->resiNoGaring($this->resiOnlyId($shipping_number, $request->input("tipeForwarder")[$i]))."=";

                $update = DB::table("data_list")->where("id", "=", $request->input("id")[$i])
                        ->update([
                            "forwarder_id" => $request->input("tipeForwarder")[$i],
                            "forwarder_name" => $request->input("namaForwarder")[$i] ?? "",
                            "shipping_number" => $shipping_number,
                            "shipping_created_at" => date("Y-m-d H:i:s"),
                            "shipping_created_by" => Auth::user()->username,
                            "shipping_updated_at" => date("Y-m-d H:i:s"),
                            "shipping_updated_by" => Auth::user()->username,
                            "invoice_status" => "PAID"
                        ]);
                if($saklar){
                    $this->sendWAResiCOR($request->input('id')[$i],0,0);
                }
                $this->sendWAResiCOR($request->input('id')[$i],1,0);
                $saklar = false;
            }
            $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Resi Dan Telah Dikirim Ke Whatsapp Customer. Silahkan Cek Pada Tabel Status.", "url" => url($urlResi));
        }

        $dataHistory = [
            "codename" => "BR",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => $this->createDescForResi($request->input("mismassInvoiceId")),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        return json_encode($encode);
    }

    public function editResi(Request $request)
    {
            $saklar = true;
            $urlResi = "/printout/resi/";
            $noSame=0;

            for($i=0;$i<=count($request->input('id'))-1;$i++){
                $oldData = DB::table("data_list")->where("id",$request->input("id")[$i])->first();

                $request->input("consFirstName")[$i]!=$oldData->cons_first_name?$noSame++:'';
                $request->input("consMiddleName")[$i]!=$oldData->cons_middle_name?$noSame++:'';
                $request->input("consLastName")[$i]!=$oldData->cons_last_name?$noSame++:'';
                $request->input("consPhone")[$i]!=$oldData->cons_phone?$noSame++:'';
                $request->input("consAddress")[$i]!=$oldData->cons_address?$noSame++:'';
                $request->input("consSubDistrict")[$i]!=$oldData->cons_sub_district?$noSame++:'';
                $request->input("consDistrict")[$i]!=$oldData->cons_district?$noSame++:'';
                $request->input("consCity")[$i]!=$oldData->cons_city?$noSame++:'';
                $request->input("consProv")[$i]!=$oldData->cons_prov?$noSame++:'';
                $request->input("consPostalCode")[$i]!=$oldData->cons_postal_code?$noSame++:'';
                $request->input("tipeForwarder")[$i]!=$oldData->forwarder_id?$noSame++:'';
                $request->input("namaForwarder")[$i]??""!=$oldData->forwarder_name?$noSame++:'';
                $request->input("noResi")[$i]!=$oldData->shipping_number?$noSame++:'';
            }

            if($noSame==0){
                $encode = array("status" => "Berhasil", "text" => "Tidak Ada Perubahan Data", "url" => "");
                return json_encode($encode);
            }

            $dataHistory = [
                "codename" => "ER",
                "created_at" => date("Y-m-d H:i:s"),
                "created_by" => Auth::user()->username,
                "description" => $this->createDescForEditResi($request),
            ];
            $insertHistory = DB::table("history_list")->insert($dataHistory);
            if(!$insertHistory){
                $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
                return json_encode($encode);
            }

            for($i=0;$i<=count($request->input('id'))-1;$i++){

                    $shipping_number = $request->input("noResi")[$i];
                    if ($request->input("tipeForwarder")[$i] != "VENDOR") {
                        if($request->input("forwarderIdOld")[$i] == "VENDOR"){
                            $uniqId = str::random(30);
                            DB::table('pool_shipping_id')->insert(
                                ['uniq_id' => $uniqId]
                            );
                            $shipping_id = DB::table('pool_shipping_id')->where("uniq_id", $uniqId)->value("id");
                            $prefix_shipping_id = "TR/ALY/" . date("y");
                            $shipping_number = $prefix_shipping_id . $shipping_id;
                        }
                    }
                    
                    $urlResi .= $this->resiNoGaring($this->resiOnlyId($shipping_number, $request->input("tipeForwarder")[$i]))."=";

                    DB::table("data_list")->where("id", "=", $request->input("id")[$i])
                            ->update([
                                "cons_first_name" => $request->input("consFirstName")[$i],
                                "cons_middle_name" => $request->input("consMiddleName")[$i] ?? "",
                                "cons_last_name" => $request->input("consLastName")[$i] ?? "",
                                "cons_phone" => $request->input("consPhone")[$i],
                                "cons_address" => $request->input("consAddress")[$i],
                                "cons_sub_district" => $request->input("consSubDistrict")[$i] ?? "",
                                "cons_district" => $request->input("consDistrict")[$i],
                                "cons_city" => $request->input("consCity")[$i],
                                "cons_prov" => $request->input("consProv")[$i],
                                "cons_postal_code" => $request->input("consPostalCode")[$i],
                                "forwarder_id" => $request->input("tipeForwarder")[$i],
                                "forwarder_name" => $request->input("namaForwarder")[$i] ?? "",
                                "shipping_number" => $shipping_number,
                                "shipping_updated_at" => date("Y-m-d H:i:s"),
                                "shipping_updated_by" => Auth::user()->username,
                            ]);

                    if($saklar){
                        $this->sendWAResiCOR($request->input('id')[$i],0,1);
                    }

                    $this->sendWAResiCOR($request->input('id')[$i],1,1);
                    
                    $saklar = false;

            }

            $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Resi. Notif Perubahan Telah Dikirim Ke Customer", "url" => url($urlResi));

        return json_encode($encode);
    }

    public function tableTracking(string $custTypeId, Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $filterTanggalAwal = $request->input('filterTanggalAwal');
        $filterTanggalAkhir = $request->input('filterTanggalAkhir');
        $filterWarehouse = $request->input('filterWarehouse');
        $filterService = $request->input('filterService');
        $filterOrderId = $request->input('mismassOrderId');
        $filter = [
            $filterTanggalAwal,
            $filterTanggalAkhir,
            $filterWarehouse,
            $filterService,
            $custTypeId,
            $filterOrderId
        ];
        $trackingModel = new Tracking();
        $lists = $trackingModel->getDT($request, $search, $filter);
        
        if($filterOrderId==""){

                foreach ($lists as $list) {
                    $getData = DB::table('data_list')->select('id','sub_total','weight','item','length','height','width','cons_first_name','cons_middle_name','cons_last_name','cons_phone','cons_address','cons_sub_district','cons_district','cons_city','cons_prov','cons_postal_code','forwarder_id','forwarder_name','shipping_number')->where('mismass_invoice_id',$list->mismass_invoice_id)->get();
                    $countRow = count($getData);
                    $detailControl = $custTypeId=="COR" ? "<div class='detail-control hidden-child' id='" . $list->mismass_order_id . "' data-createdAt='".$this->dateFormatIndo($list->shipping_created_at,1)."'></div>":"<input type='checkbox' style='margin-left:4px' name='checkResi' data-resi='".$this->resiOnlyId($list->shipping_number, $list->forwarder_id)."'>";
                    $dokuInvoiceId = $list->doku_invoice_id!="" ? $list->doku_invoice_id : "-";
                    $forwarder = $list->forwarder_id == "MISMASS" ? $list->forwarder_id." - ".$list->forwarder_name : ($list->forwarder_id=="PICK-UP" ? "<div style='color:red'>PICK-UP SENDIRI</div>" : $list->forwarder_name);
                    $fullname = $custTypeId=="COR" ? $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name : $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name;
                    $phone = $custTypeId=="COR" ? $list->sender_phone : $list->cons_phone;
                    $address = $custTypeId=="COR" ? $list->sender_address . ", " . $list->sender_sub_district . ", " . $list->sender_district . ", " . $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code : $list->cons_address . ", " . $list->cons_sub_district . ", " . $list->cons_district . ", " . $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code;
                    $detilPrice = $list->doku_link!=""?"<div><a href='" . $list->doku_link . "' target='_blank'>" . $list->doku_link . "</a></div>":"<div style='color:red'>".$list->bank_name." - ".$list->bank_account_name."</div><div style='color:blue'>".$list->bank_account_id."</div>";
                    $getRank = DB::table('users')->where("username",$list->shipping_updated_by)->value("rank");
                    $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";

                    $resiBtn = Auth::user()->shiplist_printout_resi?($custTypeId=="IND" ? "<a class='dropdown-item' href='" . url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($list->shipping_number, $list->forwarder_id)).'=') . "' target='_blank'>Print Resi</a>" : ""):"";
                    $resiBtnAll = Auth::user()->shiplist_printout_resi?"<a class='dropdown-item printResiAll' href='#' data-mismassInvoiceId='".$list->mismass_invoice_id."'>Print Resi All</a>":"";
                    $invoiceBtn = Auth::user()->shiplist_printout_invoice?"<a class='dropdown-item' href='" . url('/printout/invoice/' . $this->invOnlyId($list->mismass_invoice_id)) . "' target='_blank'>Print Invoice</a>":"";
                    $resiEditBtn = Auth::user()->shiplist_edit_resi?($custTypeId=="COR"?"<a class='dropdown-item' id='editResiBtn' data-getData='".$getData."' data-senderAddress='" . $list->sender_address . ", " . $list->sender_city . ", " . $list->sender_prov . ", " . $list->sender_postal_code . "' data-senderPhone='" . $list->sender_phone . "' data-senderName='" . $list->sender_first_name . " " . $list->sender_middle_name . " " . $list->sender_last_name . "' data-consAddress='" . $list->cons_address . ", " . $list->cons_city . ", " . $list->cons_prov . ", " . $list->cons_postal_code . "' data-consPhone='" . $list->cons_phone . "' data-consName='" . $list->cons_first_name . " " . $list->cons_middle_name . " " . $list->cons_last_name . "' data-custTypeId='".$list->cust_type_id."' data-countRow='".$countRow."' data-totalPrice='" . $this->rupiah($list->totalPrice) . "' data-totalItem='" . $list->totalItem . "' data-totalWeight='" . $list->totalWeight . "' data-custTypeName='" . $list->custTypeName . "' data-mismassInvoiceDate='" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "' data-createdAt='" . $this->dateFormatIndo($list->created_at,1) . "' data-dokuInvoiceId='" . $list->doku_invoice_id . "' data-mismassInvoiceId='" . $list->mismass_invoice_id . "' href='#'>Edit Resi</a>":""):"";
                    $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$resiBtn.$resiBtnAll.$invoiceBtn.$resiEditBtn."</div></div>";
    
                    $no++;
                    $row = [];
                    $row[] = "<div class='orderNum'>".$no."</div>".$detailControl;
                    $row[] = "<div class='font-weight-bold'>" . $list->mismass_invoice_id . "</div><div>" . $this->dateFormatIndo($list->mismass_invoice_date,1) . "</div>";
                    $row[] = "<div class='font-weight-bold'>" . $dokuInvoiceId . "</div>";
                    $row[] = "<div class='font-weight-bold'>" . $forwarder . "</div><div class='font-weight-bold'>" . $list->shipping_number . "</div><div>" . $this->dateFormatIndo($list->shipping_updated_at,2) . "</div>";
                    $row[] = "<div class='font-weight-bold'>" . $list->shipping_updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->shipping_updated_at,2) . "</div>";
                    $row[] = $fullname;
                    $row[] = "<div>" . $phone . "</div><div>" . $address . "</div>";
                    $row[] = "<div>" . number_format((float)$list->totalWeight, 2, '.', '') . " KG</div><div>" . $list->totalItem . " Item</div>";

                    if(Auth::user()->shiplist_nom){
                        $detilDisc = "<div>Total : ".$this->rupiah($list->totalDisc+$list->totalPrice)."</div><div>Diskon : ".$this->rupiah($list->totalDisc)."</div>";
                        $row[] = $detilDisc."<div class='font-weight-bold'>Total Biaya : " . $this->rupiah($list->totalPrice) . "</div>".$detilPrice;
                    }
                    
                    if(Auth::user()->shiplist_printout_invoice||Auth::user()->shiplist_printout_resi||Auth::user()->shiplist_edit_resi){
                        $row[] = $wholeBtn;
                    }
    
                    // <a class='dropdown-item' id='editResiBtn' data-shippingnumber='" . $list->shipping_number . "' href='#'>Edit Data</a>
                    // <a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->shipping_number . "','" . $list->shipping_number . "','Resi','" . url('/shiplist/hapus/resi/') . "','" . url('/shiplist') . "','" . csrf_token() . "')\" href='#'><div style='color:red'>Hapus Data</div></a>
    
                    $data[] = $row;
                }

        }else{

            foreach ($lists as $list) {
                $forwarder = $list->forwarder_id == "MISMASS" ? $list->forwarder_id." - ".$list->forwarder_name : ($list->forwarder_id=="PICK-UP" ? "<div style='color:red'>PICK-UP SENDIRI</div>" : $list->forwarder_name);

                $no++;
                $row = [];
                $row[] = $no;
                $row[] = "<div class='font-weight-bold'>" . $forwarder . "</div><div class='font-weight-bold'>" . $list->shipping_number . "</div>";
                $row[] = $list->cons_first_name." ".$list->cons_middle_name." ".$list->cons_last_name;
                $row[] = "<div>".$list->cons_phone."</div><div>".$list->cons_address.", ".$list->cons_sub_district.", ".$list->cons_district.", ".$list->cons_city.", ".$list->cons_prov.", ".$list->cons_postal_code."</div>";
                $row[] = "<div>" . number_format((float)$list->totalWeight, 2, '.', '') . " KG</div><div>" . $list->totalItem . " Item</div>";
                
                if(Auth::user()->shiplist_printout_resi){
                    $row[] = "<a class='btn btn-success' href='" . url('/printout/resi/' . $this->resiNoGaring($this->resiOnlyId($list->shipping_number, $list->forwarder_id))) . "=' target='_blank'><i class='fas fa-print'></i></a>";
                }

                $data[] = $row;
            }

        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $trackingModel->countAll($request, $search, $filter),
            'recordsFiltered' => $trackingModel->countFiltered($request, $search, $filter),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function printOutInvoice(string $id)
    {
        $this->roleAccess();
        abort_if(!Auth::user()->shiplist_printout_invoice, 403);

        $invoiceModel = new Invoice;
        $data["full"] = $invoiceModel::select(
            "data_list.*",
            "cust_type_list.name as custTypeName",
            "warehouse_list.name as wareName",
            "warehouse_list.location as wareLoc",

        )
            ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->get();
            
        if(count($data['full'])<1){
            abort(404);
        }

        $data["sum"] = $invoiceModel::selectRaw("
        SUM(weight) as totalWeight,
        SUM(item) as totalItem,
        SUM(sub_total) as totalPrice")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->get();

        $data["subTotalWeight"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalW")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("weight", ">", 0)
            ->get();

        $data["subTotalItem"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalI")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("item", ">", 0)
            ->get();

        return view('printout.invoice', $data);
    }
    
    public function printOutInvoiceEditable(string $id)
    {
        $this->roleAccess();
        abort_if(!Auth::user()->shiplist_printout_invoice, 403);
        $invoiceModel = new Invoice;
        $data["full"] = $invoiceModel::select(
            "data_list.*",
            "cust_type_list.name as custTypeName",
            "warehouse_list.name as wareName",
            "warehouse_list.location as wareLoc",

        )
            ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->get();
            
        if(count($data['full'])<1){
            abort(404);
        }

        $data["sum"] = $invoiceModel::selectRaw("
        SUM(weight) as totalWeight,
        SUM(item) as totalItem,
        SUM(sub_total) as totalPrice")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->get();

        $data["subTotalWeight"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalW")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("weight", ">", 0)
            ->get();

        $data["subTotalItem"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalI")
            ->where("mismass_invoice_id", "like", "%" . $id)
            ->where("item", ">", 0)
            ->get();

        return view('printout.invoice-editable', $data);
    }

    public function printOutResi(string $id)
    {   
        $this->roleAccess();
        abort_if(!Auth::user()->shiplist_printout_resi, 403);
        $array = explode("=",$id);

        $trackingModel = new Tracking;
        for($i=0;$i<=count($array)-2;$i++){
            $array[$i] = str_replace("$","/",$array[$i]);
            $data["get"][$i] = $trackingModel::select(
                "data_list.*",
                "cust_type_list.name as custTypeName",
            )
                ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
                ->where("shipping_number", "like", "%" . $array[$i])
                ->get();
        }
            
        if(count($data['get'][0])<1){
            abort(404);
        }

        return view('printout.resi', $data);
    }

    public function printOutInvoiceForCustomer(string $id)
    {
        $invoiceModel = new Invoice;
        $data["full"] = $invoiceModel::select(
            "data_list.*",
            "cust_type_list.name as custTypeName",
            "warehouse_list.name as wareName",
            "warehouse_list.location as wareLoc",

        )
            ->join("warehouse_list", "warehouse_list.id", "=", "data_list.warehouse_id")
            ->join("cust_type_list", "cust_type_list.id", "=", "data_list.cust_type_id")
            ->where("mismass_invoice_link", "=", $id)
            ->get();
            
        if(count($data['full'])<1){
            abort(404);
        }

        $data["sum"] = $invoiceModel::selectRaw("
        SUM(weight) as totalWeight,
        SUM(item) as totalItem,
        SUM(sub_total) as totalPrice")
            ->where("mismass_invoice_link", "=", $id)
            ->get();

        $data["subTotalWeight"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalW")
            ->where("mismass_invoice_link", "=", $id)
            ->where("weight", ">", 0)
            ->get();

        $data["subTotalItem"] = $invoiceModel::selectRaw("SUM(sub_total) as subTotalI")
            ->where("mismass_invoice_link", "=", $id)
            ->where("item", ">", 0)
            ->get();

        return view('printout.invoice', $data);
    }
}
