<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        $this->roleAccess();
        $data['cs'] = DB::table("users")->where("role_id","537469")->orderBy("fullname","asc")->get();
        return view('pages.custlist',$data);
    }

    public function table(string $custTypeId, Request $request)
    {

        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $custModel = new Customer();
        $lists = $custModel->getDT($request, $search, $custTypeId);

        if ($custTypeId == "IND") {

            foreach ($lists as $list) {
                $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
                $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
                $label = $list->cust_type_id == "IND" ? "Customer" : "Client";
                $editBtn = Auth::user()->custlist_edit?"<a class='dropdown-item' id='editBtn' data-reference='".$list->reference."' data-id='" . $list->id . "' data-firstName='" . $list->first_name . "' data-middleName='" . $list->middle_name . "' data-lastName='" . $list->last_name . "' data-phone='" . $list->phone . "' data-email='" . $list->email . "' data-address='" . $list->address . "' data-subDistrict='" . $list->sub_district . "' data-district='" . $list->district . "' data-city='" . $list->city . "' data-prov='" . $list->prov . "' data-postalCode='" . $list->postal_code . "' data-custTypeId='" . $list->cust_type_id . "' href='#'>Edit Data</a>":"";
                $hapusBtn = Auth::user()->custlist_hapus?"<a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->first_name . "','" . $label . "','" . url('/custlist/hapus/') . "','custlist')\" href='#'><div style='color:red'>Hapus Data</div></a>":"";
                $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$editBtn.$hapusBtn."</div></div>";

                $no++;
                $row = [];
                $row[] = $no;
                $row[] = "<div style='font-weight:700'>" . $list->updated_by . "</div>".$jabatan."<div>Edited At : </div><div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
                $row[] = "<div style='font-weight:700'>" . $list->first_name . " " . $list->middle_name . " " . $list->last_name . "</div><div>ID" . strval($list->id) . "</div><div>Created At : </div><div>".$this->dateFormatIndo($list->created_at,0)."</div>";
                $row[] = "<div>" . $list->phone . "</div><div>" . $list->email . "</div><div>" . $list->address . ", " . $list->sub_district . ", " . $list->district . ", " . $list->city . ", " . $list->prov . ", " . $list->postal_code . "</div>";
                $row[] = "<div>".$this->hitungInvoiceCust($list->id)."</div>";
                $row[] = "<div>".round($this->hitungBeratCust($list->id,0),2)." Kg</div><div>".$this->hitungItemCust($list->id,0)." Item</div><div>".round($this->hitungCbmCust($list->id,0),2)." CBM</div>";
                
                if(Auth::user()->custlist_nom){
                    $row[] = "<div style='font-weight:700'>".$this->rupiah($this->hitungLabaCust($list->id,0))."</div>";
                }

                if(Auth::user()->role_id=="537469"){
                    $row[] = $list->reference!=""?strtoupper(DB::table("users")->where("id",$list->reference)->value("fullname")):"<button type='button' data-id=".$list->id." data-name=".$list->first_name." ".$list->middle_name." ".$list->last_name." class='btn bg-gradient-success applyBtn'><i class='fas fa-user-plus'></i> Apply</button>";
                }else{
                    $row[] = $list->reference!=""?strtoupper(DB::table("users")->where("id",$list->reference)->value("fullname")):"-";
                }

                if(Auth::user()->custlist_edit||Auth::user()->custlist_hapus){
                    $row[] = $wholeBtn;
                }

                $data[] = $row;
            }
        } else {

            foreach ($lists as $list) {
                $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
                $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
                $label = $list->cust_type_id == "IND" ? "Customer" : "Client";
                $editBtn = Auth::user()->custlist_edit?"<a class='dropdown-item' id='editBtn' data-reference='".$list->reference."' data-id='" . $list->id . "' data-firstName='" . $list->first_name . "' data-middleName='" . $list->middle_name . "' data-lastName='" . $list->last_name . "' data-phone='" . $list->phone . "' data-email='" . $list->email . "' data-address='" . $list->address . "' data-subDistrict='" . $list->sub_district . "' data-district='" . $list->district . "' data-city='" . $list->city . "' data-prov='" . $list->prov . "' data-postalCode='" . $list->postal_code . "' data-custTypeId='" . $list->cust_type_id . "' href='#'>Edit Data</a>":"";
                $hapusBtn = Auth::user()->custlist_hapus?"<a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->first_name . "','" . $label . "','" . url('/custlist/hapus/') . "','custlist')\" href='#'><div style='color:red'>Hapus Data</div></a>":"";
                $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$editBtn.$hapusBtn."</div></div>";
                
                $no++;
                $row = [];
                $row[] = $no;
                $row[] = "<div style='font-weight:700'>" . $list->updated_by . "</div>".$jabatan."<div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
                $row[] = "<div style='font-weight:700'>" . $list->first_name . " " . $list->middle_name . " " . $list->last_name . "</div><div>CP" . strval($list->id) . "</div>";
                $row[] = "<div>" . $list->phone . "</div><div>" . $list->email . "</div><div>" . $list->address . ", " . $list->sub_district . ", " . $list->district . ", " . $list->city . ", " . $list->prov . ", " . $list->postal_code . "</div>";
                $row[] = "<div>".$this->hitungInvoiceCust($list->id)."</div>";
                $row[] = "<div>".$this->hitungResiCust($list->id)."</div>";
                $row[] = "<div>".number_format((float)$this->hitungBeratCust($list->id,0), 2, '.', '')." Kg</div><div>".$this->hitungItemCust($list->id,0)." Item</div><div>".round($this->hitungCbmCust($list->id,0),2)." CBM</div>";
                
                if(Auth::user()->custlist_nom){
                    $row[] = "<div style='font-weight:700'>".$this->rupiah($this->hitungLabaCust($list->id,0))."</div>";
                }
                
                if(Auth::user()->role_id=="537469"){
                    $row[] = $list->reference!=""?strtoupper(DB::table("users")->where("id",$list->reference)->value("fullname")):"<button type='button' data-id=".$list->id." data-name=".$list->first_name." ".$list->middle_name." ".$list->last_name." class='btn bg-gradient-success applyBtn'><i class='fas fa-user-plus'></i> Apply</button>";
                }else{
                    $row[] = $list->reference!=""?strtoupper(DB::table("users")->where("id",$list->reference)->value("fullname")):"-";
                }

                if(Auth::user()->custlist_edit||Auth::user()->custlist_hapus){
                    $row[] = $wholeBtn;
                }

                $data[] = $row;
            }
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $custModel->countAll(),
            'recordsFiltered' => $custModel->countFiltered($request, $search, $custTypeId),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function tambah(Request $request)
    {
        $choosenCustID = $request->input('choosenCustID');
        $label = $choosenCustID == "IND" ? "Customer" : "Client";
        $username = Auth::user()->username;

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
        $custModel->reference = $request->input('reference') ?? "";
        $custModel->cust_type_id = $choosenCustID;
        $custModel->created_by = $username;
        $custModel->updated_by = $username;

        $insert = $custModel->save();

        if(!$insert){
            $encode = array("status" => "Gagal", "text" => "Gagal Tambah " . $label);
            return json_encode($encode);
        }

        $dataHistory=[
            "codename" => "BC",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Buat Customer/Client</b> dengan detail,<br><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id", $choosenCustID)->value("name")."</b><br>
            Reference : <b>".strtoupper(DB::table("users")->where("id",$request->input('reference'))->value("fullname"))." - ".strtoupper(DB::table("users")->where("id",$request->input('reference'))->value("username"))."</b><br>
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

        $encode = array("status" => "Berhasil", "text" => "Berhasil Tambah " . $label);
        return json_encode($encode);
    }

    public function hapus(Request $request)
    {
        $id = $request->input('id');
        $subject = $request->input('subject');
        $username = Auth::user()->username;

        $custModel = new Customer;
        $lawas = $custModel->where("id",$id)->first();

        $dataHistory=[
            "codename" => "HC",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Hapus Customer/Client</b> dengan detail,<br><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id", $lawas->cust_type_id)->value("name")."</b><br>
            Reference : <b>".strtoupper(DB::table("users")->where("id",$lawas->reference)->value("fullname"))." - ".strtoupper(DB::table("users")->where("id",$lawas->reference)->value("username"))."</b><br>
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

        $delete = $custModel::where('id', '=', $id)->delete();

        if (!$delete) {
            $encode = array("status" => "Gagal", "text" => "Gagal Hapus " . $subject);
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Hapus " . $subject);
        return json_encode($encode);
    }

    public function edit(Request $request)
    {
        $custId = $request->input('custId');
        $username = Auth::user()->username;

        $custModel = new Customer;
        $lawas = $custModel->where("id",$custId)->first();

        $fullAddressNew = $request->input('address').", ".$request->input('subDistrict').", ".$request->input('district').", ".$request->input('city').", ".$request->input('prov').", ".$request->input('postalCode');
        $fullAddressLawas = $lawas->address.", ".$lawas->sub_district.", ".$lawas->district.", ".$lawas->city.", ".$lawas->prov.", ".$lawas->postal_code;

        $lawasReference = DB::table("users")->where("id",$lawas->reference)->value("fullname");
        $newReference = DB::table("users")->where("id",$request->input('reference'))->value("fullname");
        
        $dataHistory=[
            "codename" => "EC",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Edit Customer/Client</b> dengan detail,<br><br>
            ID : <b>".$lawas->id."</b><br>
            Tipe : <b>".DB::table("cust_type_list")->where("id", $lawas->cust_type_id)->value("name")."</b><br>
            Reference : ".($lawas->reference==$request->input('reference')?"<b>".$newReference."</b><br>":"<b style='color:red'>".$lawasReference."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$newReference."</b><br>")."
            First Name : ".($lawas->first_name==$request->input('firstName')?"<b>".$request->input('firstName')."</b><br>":"<b style='color:red'>".$lawas->first_name."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$request->input('firstName')."</b><br>")."
            Middle Name : ".($lawas->middle_name==$request->input('middleName')?"<b>".$request->input('middleName')."</b><br>":"<b style='color:red'>".$lawas->middle_name."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$request->input('middleName')."</b><br>")."
            Last Name : ".($lawas->last_name==$request->input('lastName')?"<b>".$request->input('lastName')."</b><br>":"<b style='color:red'>".$lawas->last_name."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$request->input('lastName')."</b><br>")."
            Telpon : ".($lawas->phone==$request->input('phone')?"<b>".$request->input('phone')."</b><br>":"<b style='color:red'>".$lawas->phone."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$request->input('phone')."</b><br>")."
            Email : ".($lawas->email==$request->input('email')?"<b>".$request->input('email')."</b><br>":"<b style='color:red'>".$lawas->email."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$request->input('email')."</b><br>")."
            Alamat : ".($fullAddressLawas==$fullAddressNew?"<b>".$fullAddressNew."</b><br>":"<b style='color:red'>".$fullAddressLawas."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$fullAddressNew."</b>"),
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $data = [
            'updated_by' => Auth::user()->username,
            'id' => $request->input('custId'),
            'first_name' => $request->input('firstName'),
            'middle_name' => $request->input('middleName') ?? "",
            'last_name' => $request->input('lastName') ?? "",
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'sub_district' => $request->input('subDistrict') ?? "",
            'district' => $request->input('district'),
            'city' => $request->input('city'),
            'prov' => $request->input('prov'),
            'postal_code' => $request->input('postalCode'),
            'reference' => $request->input('reference'),
        ];

        $edit = $custModel::where('id', '=', $custId)->update($data);

        if(!$edit){
            $encode = array("status" => "Gagal", "text" => "Gagal Edit Customer");
            return json_encode($encode);
        }

        $custTypeId = DB::table("cust_list")->where("id",$custId)->value("cust_type_id");


        $dataDL = [
            'first_name' => $request->input('firstName'),
            'middle_name' => $request->input('middleName') ?? "",
            'last_name' => $request->input('lastName') ?? "",
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address'),
            'sub_district' => $request->input('subDistrict') ?? "",
            'district' => $request->input('district'),
            'city' => $request->input('city'),
            'prov' => $request->input('prov'),
            'postal_code' => $request->input('postalCode'),
        ];
        DB::table('order_list')->whereRaw("cust_id='$custId' AND invoice_id=''")->update($dataDL);
        $getDataList = DB::table('data_list')->select("mismass_order_id")->whereRaw("cust_id='$custId' AND shipping_number=''")->get();
        foreach($getDataList as $gd){
            DB::table('order_list')->where("id",$gd->mismass_order_id)->update($dataDL);
        }

        $dataCOR = [
            'sender_first_name' => $request->input('firstName'),
            'sender_middle_name' => $request->input('middleName') ?? "",
            'sender_last_name' => $request->input('lastName') ?? "",
            'sender_email' => $request->input('email'),
            'sender_phone' => $request->input('phone'),
            'sender_address' => $request->input('address'),
            'sender_sub_district' => $request->input('subDistrict') ?? "",
            'sender_district' => $request->input('district'),
            'sender_city' => $request->input('city'),
            'sender_prov' => $request->input('prov'),
            'sender_postal_code' => $request->input('postalCode'),
        ];

        $dataIND = [
            'cons_first_name' => $request->input('firstName'),
            'cons_middle_name' => $request->input('middleName') ?? "",
            'cons_last_name' => $request->input('lastName') ?? "",
            'cons_email' => $request->input('email'),
            'cons_phone' => $request->input('phone'),
            'cons_address' => $request->input('address'),
            'cons_sub_district' => $request->input('subDistrict') ?? "",
            'cons_district' => $request->input('district'),
            'cons_city' => $request->input('city'),
            'cons_prov' => $request->input('prov'),
            'cons_postal_code' => $request->input('postalCode'),
        ];

        $data=[];
        $data = $custTypeId=="IND" ? $dataIND : $dataCOR;
        DB::table("data_list")->whereRaw("cust_id='$custId' AND shipping_number=''")->update($data);

        $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Customer");
        return json_encode($encode);
    }

    public function submitref(Request $request){
        $custId = $request->input('id');

        $getCustData = DB::table("cust_list")->where("id",$custId)->first();
        $getUserData = DB::table("users")->where("id",Auth::user()->id)->first();

        $custModel = new Customer;
        $data = [
            'updated_by' => Auth::user()->username,
            'reference' => Auth::user()->id,
        ];

        $submit = $custModel::where('id', $custId)->update($data);

        if(!$submit){
            $encode = array("status" => "Gagal", "text" => "Gagal Submit Reference");
            return json_encode($encode);
        }

        $dataHistory=[
            "codename" => "SR",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => Auth::user()->username,
            "description" => "<b>Submit Reference</b> dengan detail,<br><br>
            Nama Customer : <b>".$getCustData->first_name." ".$getCustData->middle_name." ".$getCustData->last_name."</b><br>
            Nama Marketing : <b>".$getUserData->fullname."</b>",
        ];
        $insertHistory = DB::table('history_list')->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Submit Reference");
        return json_encode($encode);
    }

    public function export(string $custType)
    {
        $this->roleAccess();
        $data['title'] = $custType == "individual" ? "INDIVIDUAL CUSTOMER LIST" : "CORPORATE CLIENT LIST";
        $data['custTypeId'] = $custType == "individual" ? "IND" : "COR";
        $data['customer'] = DB::table('cust_list')
        ->selectRaw(
            "cust_list.*"
            )
        ->where('cust_list.cust_type_id',$data['custTypeId'])
        ->orderBy('cust_list.updated_at','desc')
        ->get();
        $data['tanggal'] = $this->dateFormatIndo(date("Y-m-d"),1);
        return view('export.customer', $data);
    }
}
