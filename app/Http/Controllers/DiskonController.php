<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Diskon;

class DiskonController extends Controller
{
    public function index()
    {
        $this->roleAccess();
        return view('pages.diskon');
    }

    public function table(string $custTypeId, Request $request)
    {

        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $diskonModel = new Diskon();
        $lists = $diskonModel->getDT($request, $search, $custTypeId);

        if ($custTypeId == "IND") {

            foreach ($lists as $list) {
                $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'></div></div>";

                $no++;
                $row = [];
                $row[] = $no;
                $row[] = "<div style='font-weight:700'>" . $list->updated_by . "</div><div>Edited At : </div><div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
                $row[] = "<div style='font-weight:700'>" . $list->first_name . " " . $list->middle_name . " " . $list->last_name . "</div><div>ID" . strval($list->id) . "</div><div>Created At : </div><div>".$this->dateFormatIndo($list->created_at,0)."</div>";
                $row[] = "<div>" . $list->phone . "</div><div>" . $list->email . "</div><div>" . $list->address . ", " . $list->sub_district . ", " . $list->district . ", " . $list->city . ", " . $list->prov . ", " . $list->postal_code . "</div>";
                $row[] = "<div>".$this->hitungInvoiceCust($list->id)."</div>";
                $row[] = "<div>".round($this->hitungBeratCust($list->id,0),2)." Kg</div><div>".$this->hitungItemCust($list->id,0)." Item</div><div>".round($this->hitungCbmCust($list->id,0),2)." CBM</div>";

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
            'recordsTotal' => $diskonModel->countAll(),
            'recordsFiltered' => $diskonModel->countFiltered($request, $search, $custTypeId),
            'data' => $data
        ];

        return json_encode($output);
    }

}

?>