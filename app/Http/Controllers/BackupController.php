<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Warehouse;

class BackupController extends Controller
{
    public function index()
    {
        $this->roleAccess();
        $wareModel = new Warehouse;
        $data['warehouse'] = $wareModel::all();
        $data['marketing'] = DB::table("users")->where("role_id","537469")->get(); 
        return view("pages.backup", $data);
    }

    public function export(Request $request)
    {
        $tipeCustomer = $request->input("tipecustomer");
        $jenisExport = $request->input("jenisexport");
        $idWarehouse = $request->input("idwarehouse") ?? "";
        $idCustomer = $request->input("idcustomer") ?? "";
        $filterCustomer = $request->input("filtercustomer") ?? "";
        $reference = $request->input("reference") ?? "";
        $paymentStatus = $request->input("paymentStatus") ?? "";
        $tanggalAwal = $request->input("tanggalawal");
        $tanggalAkhir = $request->input("tanggalakhir");
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($tanggalAkhir));
        $tanggalTitle = $tanggalAwal==$tanggalAkhir?$this->dateFormatIndo($tanggalAwal,1):$this->dateFormatIndo($tanggalAwal,1)." - ".$this->dateFormatIndo($tanggalAkhir,1);

        if($tipeCustomer=="IND"){

            if($jenisExport=="BW"){
                
                $viewExport = "export.backupindbywh";
                $whereRaw = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $orderBy = ["data_list.updated_at","desc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getWh = DB::table("warehouse_list")
                        ->where("id","=",$idWarehouse)
                        ->first();
                $data['title'] = "INDIVIDUAL SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location." | ".$tanggalTitle;        
            
            }elseif($jenisExport=="BC"){

                $viewExport = "export.backupindbycust";
                $whereRaw="data_list.cust_id='$idCustomer' AND data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $orderBy = ["data_list.mismass_invoice_id","asc"];
                $groupBy = "data_list.id";
                $getCust = DB::table("cust_list")
                            ->where("id","=","$idCustomer")
                            ->first();
                $data['title'] = "INDIVIDUAL SHIPMENT BY CUSTOMER | ".$getCust->first_name." ".$getCust->middle_name." ".$getCust->last_name." | ".$tanggalTitle;
                $data['reference'] = DB::table('users')->where("id",$getCust->reference)->value("fullname");
            }

            if($jenisExport=="BR"){
                $viewExport = "export.backupindbyref";
                $payStatusWhere = $paymentStatus=="ALL"? "" : "data_list.invoice_status='$paymentStatus' AND";
                $filterCustWhere = $filterCustomer=="ALL"? "" : ($filterCustomer=="ADM" ? "order_list.created_by!='WEBFORM' AND" : "order_list.created_by='WEBFORM' AND");
                $where = "cust_list.reference='$reference' AND $payStatusWhere $filterCustWhere data_list.cust_type_id='IND' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $data['username'] = DB::table("users")->where("id",$reference)->value("username");
                $data['fullname'] = DB::table("users")->where("id",$reference)->value("fullname");
                $data['totalweight'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.weight) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcbm'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.cbm) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcustomer'] = count(DB::table("data_list")
                                        ->selectRaw("data_list.cons_first_name")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.cust_id")
                                        ->get());
                $data['tanggalTitle'] = $tanggalTitle;
                $data['list'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    cust_list.id as custid,
                    cust_list.reference,
                    order_list.created_by as ordercreatedby,
                    order_list.created_at as ordercreatedat,
                    (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim",
                    )
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->groupBy("data_list.id")
                ->get();
            }else{
                $data['list'] = DB::table("data_list")
                ->select(
                    "data_list.*",
                    "warehouse_list.id as wareid",
                    "warehouse_list.name as warename",
                    "warehouse_list.location as wareloc",
                    "cust_list.reference"
                    )
                ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->whereRaw($whereRaw)
                ->orderBy($orderBy[0],$orderBy[1])
                ->groupBy($groupBy)
                ->get();
            }

        }elseif($tipeCustomer=="COR"){
            
            if($jenisExport=="BW"){
            
                $viewExport = "export.backupcorbywh";
                $whereRaw = "data_list.warehouse_id='$idWarehouse' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $orderBy = ["data_list.updated_at","desc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getWh = DB::table("warehouse_list")
                        ->where("id","=",$idWarehouse)
                        ->first();
                $data['title'] = "CORPORATE SHIPMENT BY WAREHOUSE | ".$getWh->id." | ".$getWh->name." | ".$getWh->location." | ".$tanggalTitle;
                $data['list2'] = DB::table("data_list")
                                    ->select(
                                        "mismass_invoice_id",
                                        "shipping_number",
                                        "forwarder_id",
                                        "forwarder_name",
                                        "shipping_created_at",
                                        "weight","item","cbm",
                                        "cons_first_name",
                                        "cons_middle_name",
                                        "cons_last_name",
                                        "cons_phone",
                                        "cons_address",
                                        "cons_sub_district",
                                        "cons_district",
                                        "cons_city",
                                        "cons_prov",
                                        "cons_postal_code"
                                        )
                                    ->whereRaw($whereRaw)
                                    ->orderBy($orderBy[0],$orderBy[1])
                                    ->get();
            
            }elseif($jenisExport=="BC"){
            
                $viewExport = "export.backupcorbycust";
                $whereRaw="data_list.cust_id='$idCustomer' AND data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $orderBy = ["data_list.mismass_invoice_id","asc"];
                $groupBy = "data_list.mismass_invoice_id";
                $getCust = DB::table("cust_list")
                            ->where("id","=","$idCustomer")
                            ->first();
                $data['title'] = "CORPORATE SHIPMENT BY CLIENT | ".$getCust->first_name." ".$getCust->middle_name." ".$getCust->last_name." | ".$tanggalTitle;
                $data['reference'] = DB::table('users')->where("id",$getCust->reference)->value("fullname");
                $data['list2'] = DB::table("data_list")
                                    ->select(
                                        "data_list.*",
                                        "warehouse_list.id as wareid",
                                        "warehouse_list.name as warename",
                                        "warehouse_list.location as wareloc"
                                        )
                                    ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                                    ->whereRaw($whereRaw)
                                    ->orderBy($orderBy[0],$orderBy[1])
                                    ->get();
            }

            if($jenisExport=="BR"){
                $viewExport = "export.backupcorbyref";
                $payStatusWhere = $paymentStatus=="ALL"? "" : "data_list.invoice_status='$paymentStatus' AND";
                $filterCustWhere = $filterCustomer=="ALL"? "" : ($filterCustomer=="ADM" ? "order_list.created_by!='WEBFORM' AND" : "order_list.created_by='WEBFORM' AND");
                $where = "cust_list.reference='$reference' AND $payStatusWhere $filterCustWhere data_list.cust_type_id='COR' AND data_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";
                $data['username'] = DB::table("users")->where("id",$reference)->value("username");
                $data['fullname'] = DB::table("users")->where("id",$reference)->value("fullname");
                $data['totalweight'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.weight) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcbm'] = DB::table("data_list")
                                        ->selectRaw("SUM(data_list.cbm) as value")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->value("value");
                $data['totalcustomer'] = count(DB::table("data_list")
                                        ->selectRaw("data_list.cons_first_name")
                                        ->join("cust_list","cust_list.id","=","data_list.cust_id")
                                        ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                                        ->whereRaw($where)
                                        ->groupBy("data_list.cust_id")
                                        ->get());
                $data['tanggalTitle'] = $tanggalTitle;
                $data['list'] = DB::table("data_list")
                ->selectRaw(
                    "data_list.*,
                    cust_list.id as custid,
                    cust_list.reference,
                    order_list.created_by as ordercreatedby,
                    order_list.created_at as ordercreatedat,
                    (SELECT COUNT(data_list.cust_id) FROM data_list WHERE data_list.cust_id=cust_list.id) as jumlahkirim"
                    )
                ->join("cust_list","cust_list.id","=","data_list.cust_id")
                ->join("order_list","order_list.id","=","data_list.mismass_order_id")
                ->whereRaw($where)
                ->orderBy("data_list.created_at","asc")
                ->groupBy("data_list.id")
                ->get();
            }else{
                $data['list'] = DB::table("data_list")
                            ->select(
                                "data_list.*",
                                "warehouse_list.id as wareid",
                                "warehouse_list.name as warename",
                                "warehouse_list.location as wareloc",
                                "cust_list.reference"
                                )
                            ->join("warehouse_list","warehouse_list.id","=","data_list.warehouse_id")
                            ->join("cust_list","cust_list.id","=","data_list.cust_id")
                            ->whereRaw($whereRaw)
                            ->orderBy($orderBy[0],$orderBy[1])
                            ->groupBy($groupBy)
                            ->get();
            }
        }

        // dd($data['title']);
        return view($viewExport,$data);
    }

    public function exportops(Request $request){
        $tipeCustomer = $request->input("tipecustomer");
        $tanggalAwal = $request->input("tanggalawal");
        $tanggalAkhir = $request->input("tanggalakhir");
        $filterTanggalAwal = date("Y-m-d 00:00:00",strtotime($tanggalAwal));
        $filterTanggalAkhir = date("Y-m-d 23:59:59",strtotime($tanggalAkhir));

        $tipeCust = DB::table("cust_type_list")
                    ->where("id",$tipeCustomer)
                    ->value("name");

        if($tipeCustomer=="IND"){
            $viewExport = "export.backupopsind";
        }elseif($tipeCustomer=="COR"){
            $viewExport = "export.backupopscor";
            $data['list2'] = DB::table('data_list')
                            ->orderBy("updated_at","desc")
                            ->get();
        }else{
            return json_encode(false);
        }

        $whereRaw = "order_list.cust_type_id='$tipeCustomer' AND order_list.created_at BETWEEN '$filterTanggalAwal' AND '$filterTanggalAkhir'";

        $data['title'] =  "Export Data Operational | ".$tipeCust." | ".$this->dateFormatIndo($tanggalAwal,1)." - ".$this->dateFormatIndo($tanggalAkhir,1);
        $data['list'] = DB::table("order_list")
                        ->select(
                            "order_list.id as orderid",
                            "order_list.order_status_id as orderstatusid",
                            "order_list.created_at as ordercreatedat",
                            "order_list.invoice_id as orderinvoiceid",
                            "order_list.first_name as orderfirstname",
                            "order_list.middle_name as ordermiddlename",
                            "order_list.last_name as orderlastname",
                            "order_list.phone as orderphone",
                            "order_list.address as orderaddress",
                            "order_list.sub_district as ordersubdistrict",
                            "order_list.district as orderdistrict",
                            "order_list.city as ordercity",
                            "order_list.prov as orderprov",
                            "order_list.postal_code as orderpostalcode",
                            "data_list.*",
                            )
                        ->join("data_list","data_list.mismass_order_id","=","order_list.id")
                        ->whereRaw($whereRaw)
                        ->orderBy("order_list.updated_at","desc")
                        ->groupBy("order_list.id")
                        ->get();

        // dd($data['list']);

        return view($viewExport,$data);
    }
}
