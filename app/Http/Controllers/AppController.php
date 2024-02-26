<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Service;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InvoiceExport;
use App\Exports\ShippingExport;

class AppController extends Controller
{

    public function index()
    {
        if (Auth::check()) {
            $this->roleAccess();
            $data["username"] = Auth::user()->username;
            return view('app', $data);
        }

        return redirect('/login');
    }

    public function dashboard()
    {
        $wareModel = new Warehouse;
        $data['warehouse'] = $wareModel::all();
        $this->roleAccess();
        return view('pages.dashboard', $data);
    }

    public function loadDashboard(Request $request){

        $warehouse = $request->input("filterWarehouse");
        $bulan = $request->input("filterBulan");
        $tahun = $request->input("filterTahun");

        $filterWarehouse = $warehouse=="" ? "" : " AND data_list.warehouse_id='$warehouse'";
        $fTanggal = $bulan=="" ? $tahun : $tahun."-".$bulan;
        $filterTanggal = "created_at LIKE '$fTanggal%'";

        $where=$filterTanggal.$filterWarehouse;
        $whereCust=$filterTanggal;

        $get = DB::table("data_list")
        ->selectRaw("sum(weight) as totalBerat,
        sum(CASE WHEN data_list.cust_type_id='IND' THEN data_list.weight ELSE 0 END) as totalBeratInd,
        sum(CASE WHEN data_list.cust_type_id='COR' THEN data_list.weight ELSE 0 END) as totalBeratCor,
        sum(cbm) as totalCbm,
        sum(CASE WHEN data_list.cust_type_id='IND' THEN data_list.cbm ELSE 0 END) as totalCbmInd,
        sum(CASE WHEN data_list.cust_type_id='COR' THEN data_list.cbm ELSE 0 END) as totalCbmCor,
        sum(data_list.sub_total) as totalPendapatan,
        sum(data_list.discount) as totalDiskon,
        sum(CASE WHEN data_list.cust_type_id='IND' THEN data_list.sub_total ELSE 0 END) as totalPendapatanInd,
        sum(CASE WHEN data_list.cust_type_id='COR' THEN data_list.sub_total ELSE 0 END) as totalPendapatanCor,
        sum(CASE WHEN data_list.cust_type_id='IND' THEN data_list.discount ELSE 0 END) as totalDiskonInd,
        sum(CASE WHEN data_list.cust_type_id='COR' THEN data_list.discount ELSE 0 END) as totalDiskonCor,
        sum(CASE WHEN data_list.invoice_status='PAID' THEN data_list.discount ELSE 0 END) as totalDiskonPaid,
        sum(CASE WHEN data_list.invoice_status='UNPAID' THEN data_list.discount ELSE 0 END) as totalDiskonUnpaid,
        sum(CASE WHEN data_list.invoice_status='PAID' THEN data_list.sub_total ELSE 0 END) as totalPaid,
        sum(CASE WHEN data_list.invoice_status='UNPAID' THEN data_list.sub_total ELSE 0 END) as totalUnpaid")
                    ->whereRaw($where)
                    ->first();
        
        // $get2 = DB::table("data_list")
        //         ->selectRaw("sum(CASE WHEN invoice_status='UNPAID' THEN sub_total ELSE 0 END) as totalUnpaid")
        //         ->first();
                
        $totalCust = DB::table("cust_list")->selectRaw("COUNT(DISTINCT(id)) as allTotal,
            SUM(CASE WHEN cust_type_id='IND' THEN 1 ELSE 0 END) as ind,
            SUM(CASE WHEN cust_type_id='COR' THEN 1 ELSE 0 END) as cor")
            ->whereRaw($whereCust)
            ->first();

        $yearBerat=[];
        $yearCustomer=[];
        $yearInd=[];
        $yearCor=[];

        for($i=0;$i<=11;$i++){
            
            $fTanggal = date("Y-m",strtotime($tahun."-".$i+1));
            $filterTanggalC = "cust_list.created_at LIKE '$fTanggal%'";
            $filterTanggal = "data_list.created_at LIKE '$fTanggal%'";
            $where=$filterTanggal.$filterWarehouse;

            $getDataYear = DB::table("data_list")
        ->selectRaw("sum(weight) as totalBerat,
        sum(CASE WHEN data_list.cust_type_id='IND' THEN data_list.discount ELSE 0 END) as totalDiskonInd,
        sum(CASE WHEN data_list.cust_type_id='COR' THEN data_list.discount ELSE 0 END) as totalDiskonCor,
        sum(CASE WHEN data_list.cust_type_id='IND' THEN data_list.sub_total ELSE 0 END) as totalPendapatanInd,
        sum(CASE WHEN data_list.cust_type_id='COR' THEN data_list.sub_total ELSE 0 END) as totalPendapatanCor")
                    ->join("cust_list","cust_list.id","=","data_list.cust_id")
                    ->whereRaw($where)
                    ->first();
                    
            $totalC = DB::table("cust_list")->selectRaw("COUNT(DISTINCT(id)) as totalCustomer")
            ->whereRaw($filterTanggalC)
            ->first();
            
            $totalDiskonInd = $getDataYear->totalDiskonInd==null?0:$getDataYear->totalDiskonInd;
            $totalDiskonCor = $getDataYear->totalDiskonCor==null?0:$getDataYear->totalDiskonCor;

            $yearBerat[$i]=$getDataYear->totalBerat==null?0:$getDataYear->totalBerat;
            $yearCustomer[$i]=$totalC->totalCustomer;
            $yearInd[$i]=$getDataYear->totalPendapatanInd==null?0:$getDataYear->totalPendapatanInd-$totalDiskonInd;
            $yearCor[$i]=$getDataYear->totalPendapatanCor==null?0:$getDataYear->totalPendapatanCor-$totalDiskonCor;
        }        

        $encode = array(
            "totalBerat"=>$get->totalBerat,
            "totalBeratInd"=>$get->totalBeratInd,
            "totalBeratCor"=>$get->totalBeratCor,
            "totalCbm"=>$get->totalCbm,
            "totalCbmInd"=>$get->totalCbmInd,
            "totalCbmCor"=>$get->totalCbmCor,
            "totalCustomer"=>$totalCust->allTotal,
            "totalCustomerInd"=>$totalCust->ind,
            "totalCustomerCor"=>$totalCust->cor,
            "totalPendapatan"=>$get->totalPendapatan-$get->totalDiskon,
            "totalDiskon"=>$get->totalDiskon,
            "totalPendapatanInd"=>$get->totalPendapatanInd-$get->totalDiskonInd,
            "totalPendapatanCor"=>$get->totalPendapatanCor-$get->totalDiskonCor,
            "totalDiskonInd"=>$get->totalDiskonInd,
            "totalDiskonCor"=>$get->totalDiskonCor,
            "totalPaid"=>$get->totalPaid-$get->totalDiskonPaid,
            "totalUnpaid"=>$get->totalUnpaid-$get->totalDiskonUnpaid,
            "yearBerat"=>$yearBerat,
            "yearCustomer"=>$yearCustomer,
            "yearInd"=>$yearInd,
            "yearCor"=>$yearCor
        );     

        // dd($encode);

        return json_encode($encode);

    }

    public function checkSes()
    {
        $encode = array("status" => true);
        if (!Auth::check()) {
            $encode = array("status" => false);
        }

        return json_encode($encode);
    }

    public function checkPhoneAndEmail(Request $request)
    {
        $encode = false;
        $email = $request->input("email") ?? "";
        $phone = $request->input("phone") ?? "";
        $statusCust = $request->input("statusCust");

        if ($statusCust == "REG") {
            $encode = true;
            return json_encode($encode);
        }

        if ($statusCust == "EDIT") {
            $emailOld = $request->input("emailOld") ?? "";
            $phoneOld = $request->input("phoneOld") ?? "";

            if ($phone != "") {

                $exist = DB::table("cust_list")->where("phone", $phone)->first();

                if ($exist == null) {
                    $encode = true;
                } else {
                    if ($phone == $phoneOld) {
                        $encode = true;
                    }
                }

                return json_encode($encode);
            }

            if ($email != "") {

                $exist = DB::table("cust_list")->where("email", $email)->first();

                if ($exist == null) {
                    $encode = true;
                } elseif($email=="belum_ada_email@mismass.com"){
                    $encode = true;
                }else {
                    if ($email == $emailOld) {
                        $encode = true;
                    }
                }

                return json_encode($encode);
            }
        }

        $custModel = new Customer;
        if ($phone != "") {
            $exist = $custModel->where("phone", $phone)->first();
        }

        if ($email != "") {
            $exist = $custModel->where("email", $email)->first();
        }

        if ($exist == null) {
            $encode = true;
        }

        return json_encode($encode);
    }

    public function invoiceLinkDoku(Request $request)
    {
        $encode = false;
        $invoice = $request->input("invoiceDoku") ?? "";
        $link = $request->input("linkDoku") ?? "";
        $mismassOrderId = $request->input("mismassOrderId");

        if ($invoice != "") {
            $select2="doku_invoice_id as data";
            $value = $invoice; 
            $whereRaw = "doku_invoice_id LIKE '$invoice%'";
        }

        if ($link != "") {
            $select2="doku_link as data";
            $value = $link; 
            $whereRaw = "doku_link LIKE '$link%'";
        }

        $exist = DB::table("data_list")->whereRaw($whereRaw)->first();

        if ($exist == null) {
            $encode = true;
        }else{
            $exist2 = DB::table("data_list")->select($select2)->where("mismass_order_id", $mismassOrderId)->first();
            $encode = $exist2 ==null ? false : ($exist2->data == $value ? true : false);
        }

        return json_encode($encode);
    }

    public function getCustList(Request $request)
    {
        $id = $request->input("custTypeId");
        $custModel = new Customer;
        $get = $custModel::where("cust_type_id", "=", $id)->where("id", "!=", "000000")->get();
        
        $data = "<option value='' hidden>Pilih Client</option>";
        if($id=="IND"){
            $data = "<option value='' hidden>Pilih Customer</option>";
        }

        foreach ($get as $g) {
            $data .= "<option value='" . $g->id . "'>" . $g->first_name . " " . $g->middle_name . " " . $g->last_name . "</option>";
        }

        $encode = array("data" => $data);

        return json_encode($encode);
    }

    public function getServList(Request $request)
    {
        $data = "<option value=''>ALL SERVICES</option>";
        if($request->input("inv")=="true"){
            $data = "<option value='' hidden>PILIH SERVICES</option>";
        }

        $id = $request->input("warehouseId");
        $servModel = new Service;
        $get = $servModel::where("warehouse_id", "=", $id)->get();

        foreach ($get as $g) {
            $data .= "<option value='" . $g->id . "'>" . $g->name . "</option>";
        }

        $encode = array("data" => $data);

        return json_encode($encode);
    }

    public function getCustData(Request $request)
    {
        $id = $request->input('id');
        $custModel = new Customer;
        $get = $custModel::where("id", "=", $id)->get();

        foreach ($get as $g) {
            $id = $g->id;
            $custTypeId = $g->cust_type_id;
            $firstName = $g->first_name;
            $middleName = $g->middle_name;
            $lastName = $g->last_name;
            $email = $g->email;
            $phone = $g->phone;
            $address = $g->address;
            $subDistrict = $g->sub_district;
            $district = $g->district;
            $city = $g->city;
            $prov = $g->prov;
            $postalCode = $g->postal_code;
        }

        $encode = array(
            "id" => $id,
            "custTypeId" => $custTypeId,
            "firstName" => $firstName,
            "middleName" => $middleName,
            "lastName" => $lastName,
            "email" => $email,
            "phone" => $phone,
            "address" => $address,
            "subDistrict" => $subDistrict,
            "district" => $district,
            "city" => $city,
            "prov" => $prov,
            "postalCode" => $postalCode
        );

        return json_encode($encode);
    }

    public function getServData(Request $request)
    {
        $id = $request->input("serviceId");
        $servModel = new Service;
        $get = $servModel::select(["pricekg", "pricevol", "priceitem", "pricecbm"])
            ->where("id", "=", $id)
            ->get();

        foreach ($get as $g) {
            $priceKg = $g->pricekg;
            $priceVol = $g->pricevol;
            $priceItem = $g->priceitem;
            $priceCbm = $g->pricecbm;
        }

        $encode = array(
            "priceKg" => $priceKg,
            "priceVol" => $priceVol,
            "priceItem" => $priceItem,
            "priceCbm" => $priceCbm
        );

        return json_encode($encode);
    }

    public function getUomList(Request $request)
    {
        $data = "<option value='' hidden>Pilih Satuan Berat</option>";
        $id = $request->input("serviceId");
        $servModel = new Service;
        $getServData = $servModel::where("id", "=", $id)->first();

        $getServData->pricekg > 0 ? $data .= "<option value='KG'>KILOGRAM</option>" : "";
        $getServData->pricevol > 0 ? $data .= "<option value='VOL'>VOLUME</option>" : "";
        $getServData->priceitem > 0 ? $data .= "<option value='ITEM'>ITEM</option>" : "";
        $getServData->pricecbm > 0 ? $data .= "<option value='CBM'>CBM</option>" : "";

        $encode = array("data" => $data);

        return json_encode($encode);
    }
    
    public function encrypt(string $pass, Request $request){
        $stringLength = strlen($pass);
        $requiredLength = 8;

        if($stringLength<$requiredLength){
            return "Jumlah Karakter Yang Anda Input <b style='font-size:25px'>".$stringLength."</b>. <b>Minimal</b> Tambah <b style='font-size:25px'>".$requiredLength-$stringLength."</b> Karakter Lagi";
        }
        return bcrypt($this->saltThis($pass));
    }

    public function getShipNum(Request $request){
        $id = $request->input("id") ?? "";
        $noResi = $request->input("noResi");
        $idDB = DB::table('data_list')->where('shipping_number',$noResi)->value("id");
        $count = DB::table('data_list')->where('shipping_number',$noResi)->count();

        if($count>0){
            if($id!=$idDB){
                $encode=false;
                return json_encode($encode);
            }
        }

        $encode=true;
        return json_encode($encode);
    }

    public function getShipId(Request $request){
        $a = 0;
        while($a==0){
            $id = $this->generateRandomString(8);
            $shipIdExist = DB::table("data_list")->where("shipping_number",$id)->count();
            if($shipIdExist==0||$shipIdExist==null){
                $a++;
            }
        }

        $encode = array(
            "value" => $id,
        );
        return json_encode($encode);
    }

    public function getShipNumFromInvoice(Request $request){
        $missmassInvoiceId = $request->input("id");

        $get = DB::table("data_list")->select("shipping_number")->where("mismass_invoice_id",$missmassInvoiceId)->get();

        $encode = array(
            "data" => $get
        );

        return json_encode($encode);
    }

    public function importExampleInvoice(){
        return Excel::download(new InvoiceExport, 'MISMASS IMPORT INVOICE CORPORATE.xlsx');
    }

    public function importExampleShipping(string $id){
        return Excel::download(new ShippingExport($id), 'MISMASS IMPORT RESI CORPORATE - NO INVOICE INV-AJV-'.$id.'.xlsx');
    }
 
}
