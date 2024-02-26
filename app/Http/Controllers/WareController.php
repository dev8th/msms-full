<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class WareController extends Controller
{
    public function index()
    {
        $this->roleAccess();
        return view('pages.warehouse');
    }

    public function inputChecking(Request $request)
    {
        $id = $request->input('warehouseID');
        $idOld = $request->input('warehouseIDOld');
        $check = Warehouse::where('id', $id)->first();
        $status = $check == null ? true : ($id == $idOld ? true : false);

        return json_encode($status);
    }

    public function table(Request $request)
    {
        $this->roleAccess();
        $data = [];
        $no = $request->input('start');
        $search = $request->input('search')['value'];
        $wareModel = new Warehouse();
        $lists = $wareModel->getDT($request, $search);

        foreach ($lists as $list) {
            $getRank = DB::table('users')->where("username",$list->updated_by)->value("rank");
            $jabatan = $getRank != null ? "<div class='bg-mismass' style='padding:1px 5px'>".$getRank."</div>" : "";
            $editBtn = Auth::user()->warelist_edit?"<a class='dropdown-item' id='editBtn' data-id='" . $list->id . "' data-name='" . $list->name . "' data-location='$list->location' href='#'>Edit Data</a>":"";
            $hapusBtn = Auth::user()->warelist_hapus?"<a class='dropdown-item' id='hapusBtn' onclick=\"konfirm_hapus('" . $list->id . "','" . $list->name . "','Warehouse','" . url('/warehouse/hapus/') . "','warehouse')\" href='#'><div style='color:red'>Hapus Data</div></a>":"";
            $wholeBtn = "<div class='btn-group dropleft'><button type='button' class='btn btn-secondary nobtn' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fas fa-ellipsis-v'></i></button><div class='dropdown-menu' x-placement='right-start' style='position: absolute; transform: translate3d(111px, 0px, 0px); top: 0px; left: 0px; will-change: transform;'>".$editBtn.$hapusBtn."</div></div>";

            $no++;
            $row = [];
            $row[] = $no;
            $row[] = "<div style='font-weight:700'>" . $list->updated_by . "</div>".$jabatan."<div>Edited At : </div><div>" . $this->dateFormatIndo($list->updated_at,2) . "</div>";
            $row[] = "<div style='font-weight:700'>" . $list->id . "</div><div>" . $list->name . "</div><div>Created At : </div><div>".$this->dateFormatIndo($list->created_at,0)."</div>";
            $row[] = $list->location;
            $row[] = "<div>".$this->hitungCustomerWare($list->id)."</div>";
            $row[] = "<div>".$this->hitungServiceWare($list->id)."</div>";
            $row[] = "<div>".$this->hitungInvoiceWare($list->id)."</div>";
            $row[] = "<div>".round($this->hitungBeratWare($list->id,0),2)." Kg</div><div>".$this->hitungItemWare($list->id,0)." Item</div><div>".round($this->hitungCbmWare($list->id,0),2)." CBM</div>";
            if(Auth::user()->warelist_nom){
                $row[] = "<div style='font-weight:700'>".$this->rupiah($this->hitungLabaWare($list->id,0))."</div>";
            }
            if(Auth::user()->warelist_edit||Auth::user()->warelist_hapus){
                $row[] = $wholeBtn;
            }

            $data[] = $row;
        }

        $output = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $wareModel->countAll(),
            'recordsFiltered' => $wareModel->countFiltered($request, $search),
            'data' => $data
        ];

        return json_encode($output);
    }

    public function tambah(Request $request)
    {
        $username = Auth::user()->username;
        $id = $request->input('warehouseID');
        $name = $request->input('warehouseName') ?? "";
        $location = $request->input('warehouseLoc');

        $wareModel = new Warehouse();
        $wareModel->id = $id;
        $wareModel->status_id = 1;
        $wareModel->name = $name;
        $wareModel->location = $location;
        $wareModel->description = "";
        $wareModel->created_by = $username;
        $wareModel->updated_by = $username;

        $insert = $wareModel->save();

        if (!$insert) { 
            $encode = array("status" => "Gagal", "text" => "Gagal Buat Warehouse");
            return json_encode($encode);
        }

        $dataHistory = [
            "codename" => "BW",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Buat Warehouse</b> dengan detail,<br><br>
            ID : <b>".$id."</b><br>
            Nama : <b>".$name."</b><br>
            Lokasi : <b>".$location."</b>",
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Buat History");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Buat Warehouse");
        return json_encode($encode);
        
    }

    public function hapus(Request $request)
    {
        $id = $request->input('id');
        $subject = $request->input('subject');
        $username = Auth::user()->username;

        $encode = array("status" => "Gagal", "text" => "Anda Tidak Bisa Menghapus Data Warehouse.");
        return json_encode($encode);

        // $wareModel = new Warehouse;
        // $lawas = $wareModel::where('id',$id)->first();
        // $dataHistory = [
        //     "codename" => "HW",
        //     "created_at" => date("Y-m-d H:i:s"),
        //     "created_by" => $username,
        //     "description" => "<b>Hapus Warehouse</b> dengan detail,<br><br>
        //     ID : <b>".$lawas->id."</b><br>
        //     Nama : <b>".$lawas->name."</b><br>
        //     Lokasi : <b>".$lawas->location."</b>",
        // ];
        // $insertHistory = DB::table("history_list")->insert($dataHistory);
        // if(!$insertHistory){
        //     $encode = array("status" => "Gagal", "text" => "Gagal Tambah History");
        //     return json_encode($encode);
        // }

        // $delete = $wareModel::where('id', $id)->delete();

        // if (!$delete) {
        //     $encode = array("status" => "Gagal", "text" => "Gagal Hapus " . $subject);
        //     return json_encode($encode);
        // }
        
        // $encode = array("status" => "Berhasil", "text" => "Berhasil Hapus " . $subject);
        // return json_encode($encode);
        

        
    }

    public function edit(Request $request)
    {
        $username = Auth::user()->username;
        $id = $request->input('warehouseID');
        $name = $request->input('warehouseName') ?? "";
        $location = $request->input('warehouseLoc');

        $wareModel = new Warehouse();
        $lawas = $wareModel::where("id",$id)->first();

        $dataHistory = [
            "codename" => "EW",
            "created_at" => date("Y-m-d H:i:s"),
            "created_by" => $username,
            "description" => "<b>Edit Warehouse</b> dengan detail,<br><br>
            ID : <b>".$id."</b><br>
            Nama : ".($lawas->name==$name?"<b>".$name."</b><br>":"<b style='color:red'>".$lawas->name."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$name."</b><br>")."
            Lokasi : ".($lawas->location==$location?"<b>".$location."</b>":"<b style='color:red'>".$lawas->location."</b> <i class='fas fa-arrow-right'></i> <b style='color:red'>".$location."</b>"),
        ];
        $insertHistory = DB::table("history_list")->insert($dataHistory);
        if(!$insertHistory){
            $encode = array("status" => "Gagal", "text" => "Gagal Tambah History");
            return json_encode($encode);
        }

        $data = [
            'updated_by' => $username,
            'id' => $id,
            'name' => $name,
            'location' => $location
        ];

        $wareModel = new Warehouse;
        $edit = $wareModel::where("id", $id)->update($data);

        if (!$edit) {
            $encode = array("status" => "Gagal", "text" => "Gagal Edit Warehouse");
            return json_encode($encode);
        }

        $encode = array("status" => "Berhasil", "text" => "Berhasil Edit Warehouse");
        return json_encode($encode);
    }

    public function export()
    {
        $this->roleAccess();
        $wareModel = new Warehouse;
        $data['warehouse'] = $wareModel::selectRaw("warehouse_list.*")
                            ->whereRaw("warehouse_list.id!=''")
                            ->orderBy("warehouse_list.updated_at","desc")
                            ->get();            
        $data['tanggal'] = $this->dateFormatIndo(date("Y-m-d"),1);
        return view('export.warehouse', $data);
    }
}
