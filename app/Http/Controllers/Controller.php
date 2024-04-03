<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function saltThis($a)
    {
        $b = $a . "!withsaltfrommismass!";
        return $b;
    }

    public function roleAccess(){
        $get = DB::table("role_access_list")->where("role_id",Auth::user()->role_id)->first();

        Auth::user()->dashboard_page = $get->dashboard_page;
        Auth::user()->dashboard_berat_board = $get->dashboard_berat_board;
        Auth::user()->dashboard_cust_board = $get->dashboard_cust_board;
        Auth::user()->dashboard_diskon_board = $get->dashboard_diskon_board;
        Auth::user()->dashboard_cbm_board = $get->dashboard_cbm_board;
        Auth::user()->dashboard_pendapatan_board = $get->dashboard_pendapatan_board;
        Auth::user()->dashboard_paid_board = $get->dashboard_paid_board;
        Auth::user()->dashboard_berat_chart = $get->dashboard_berat_chart;
        Auth::user()->dashboard_pendapatan_chart = $get->dashboard_pendapatan_chart;
        Auth::user()->neworder_page = $get->neworder_page;
        Auth::user()->shiplist_page = $get->shiplist_page;
        Auth::user()->shiplist_tab_order = $get->shiplist_tab_order;
        Auth::user()->shiplist_tab_invoice = $get->shiplist_tab_invoice;
        Auth::user()->shiplist_tab_resi = $get->shiplist_tab_resi;
        Auth::user()->shiplist_ganti_status = $get->shiplist_ganti_status;
        Auth::user()->shiplist_hapus_order = $get->shiplist_hapus_order;
        Auth::user()->shiplist_buat_invoice = $get->shiplist_buat_invoice;
        Auth::user()->shiplist_edit_invoice = $get->shiplist_edit_invoice;
        Auth::user()->shiplist_hapus_invoice = $get->shiplist_hapus_invoice;
        Auth::user()->shiplist_buat_resi = $get->shiplist_buat_resi;
        Auth::user()->shiplist_edit_resi = $get->shiplist_edit_resi;
        Auth::user()->shiplist_printout_invoice = $get->shiplist_printout_invoice;
        Auth::user()->shiplist_printout_resi = $get->shiplist_printout_resi;
        Auth::user()->shiplist_nom = $get->shiplist_nom;
        Auth::user()->custlist_page = $get->custlist_page;
        Auth::user()->custlist_nom = $get->custlist_nom;
        Auth::user()->custlist_export = $get->custlist_export;
        Auth::user()->custlist_buat = $get->custlist_buat;
        Auth::user()->custlist_edit = $get->custlist_edit;
        Auth::user()->custlist_hapus = $get->custlist_hapus;
        Auth::user()->warelist_page = $get->warelist_page;
        Auth::user()->warelist_nom = $get->warelist_nom;
        Auth::user()->warelist_export = $get->warelist_export;
        Auth::user()->warelist_buat = $get->warelist_buat;
        Auth::user()->warelist_edit = $get->warelist_edit;
        Auth::user()->warelist_hapus = $get->warelist_hapus;
        Auth::user()->servlist_page = $get->servlist_page;
        Auth::user()->servlist_nom = $get->servlist_nom;
        Auth::user()->servlist_export = $get->servlist_export;
        Auth::user()->servlist_buat = $get->servlist_buat;
        Auth::user()->servlist_edit = $get->servlist_edit;
        Auth::user()->servlist_hapus = $get->servlist_hapus;
        Auth::user()->export_page = $get->export_page;
        Auth::user()->history_page = $get->history_page;

        return true;
    }

    public static function ifEmpty($a)
    {
        return $a == "" ? $a = "-" : $a;
    }

    public static function dateFormatIndo($time,$format=0)
    {
        //0 = 28/Agu/1994
        //1 = 28 Agustus 1994
        //2 = 28/Agu/1994 10:00:00
        //3 = 28-Agu-1994

        if($time=="0000-00-00 00:00:00"){
            return "-";
        }

        $tanggal = date("Y-m-d", strtotime($time));
        $waktu = date("H:i:s", strtotime($time));
        $array = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
        $explode = explode("-", $tanggal);

        if($format==0){
            return $explode[2]."/".substr($array[$explode[1]-1],0,3)."/".$explode[0];
        }

        if($format==1){
            return $explode[2]." ".$array[$explode[1]-1]." ".$explode[0];
        }

        if($format==2){
            return $explode[2]."/".substr($array[$explode[1]-1],0,3)."/".$explode[0]." ".$waktu;
        }

        if($format==3){
            return $explode[2]."-".substr($array[$explode[1]-1],0,3)."-".$explode[0];
        }

    }


    public static function pembulatan($a){
        if($a<=0.3&&$a>0){
            return 1;
        }
        if(preg_match("/\./", $a)){
            $b = explode(".",$a);
            $c = floatval("0.".$b[1]);
            if($c>0.3){
                return intVal($b[0])+1;
            }else{
                return intVal($b[0]);
            }
        }
        return $a;
    }

    public static function rupiah($angka)
    {
        $hasil_rupiah = "Rp." . number_format((float)$angka, 0, '', '.');
        return $hasil_rupiah;
    }

    public static function noRupiah($angka)
    {
        $hasil_rupiah = number_format((float)$angka, 0, '', '.');
        return $hasil_rupiah;
    }

    public function noDot($angka)
    {
        return (int)str_replace(".", "", (string)$angka);
    }

    public function noPlus($a, $b)
    {
        $c = explode("+", $a);
        return $c[$b];
    }

    public function invOnlyId($z)
    {
        return str_replace("INV/AJV/", "", $z);
    }

    public function resiOnlyId($z, $a)
    {
        $b = $a == "MISMASS" ? str_replace("TR/ALY/", "", $z) : $z;
        return $b;
    }
    
    public function resiNoGaring($v){
        return str_replace("/","$",$v);
    }

    function generateRandomString($length = 20)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }
    
    function normalizeInput($a){
        $b = str_replace(".", "", (string)$a);
        $c = str_replace(",",".", (string)$b);
        return $c;
    }

    public static function getReferenceFullName($id){
        return DB::table('users')->where("id",$id)->value('fullname');
    }
    
    public static function hitungTotalBeratByInvoice($id){
        $b = DB::table('data_list')
            ->selectRaw('SUM(weight) as total')
            ->where('mismass_invoice_id',$id)
            ->groupBy('mismass_invoice_id')
            ->first();
            
        return $b->total;
        
    }
    
    public static function hitungTotalItemByInvoice($id){
        $b = DB::table('data_list')
            ->selectRaw('SUM(item) as total')
            ->where('mismass_invoice_id',$id)
            ->groupBy('mismass_invoice_id')
            ->first();
            
        return $b->total;
        
    }

    public static function hitungTotalCbmByInvoice($id){
        $b = DB::table('data_list')
            ->selectRaw('SUM(cbm) as total')
            ->where('mismass_invoice_id',$id)
            ->groupBy('mismass_invoice_id')
            ->first();
            
        return $b->total;
        
    }

    public static function hitungTotalDiskonByInvoice($id){
        $b = DB::table('data_list')
            ->selectRaw('SUM(discount) as total')
            ->where('mismass_invoice_id',$id)
            ->groupBy('mismass_invoice_id')
            ->first();
            
        return $b->total;
        
    }
    
    public static function hitungTotalSubTotalByInvoice($id){
        $b = DB::table('data_list')
            ->selectRaw('SUM(sub_total) as total')
            ->where('mismass_invoice_id',$id)
            ->groupBy('mismass_invoice_id')
            ->first();
            
        return $b->total;
        
    }

    public static function hitungServiceWare($wh){
        $b = DB::table('service_list')
            ->where('warehouse_id',$wh)
            ->get();
        
        return count($b);
    }

    public static function hitungInvoiceWare($a){

        $oldInv="";
        $num=0;

        $b = DB::table('data_list')
            ->select("mismass_invoice_id")
            ->where("warehouse_id","=",$a)
            ->where("invoice_status","=","PAID")
            ->orderBy("mismass_invoice_id")
            ->get();
        
        foreach($b as $g){
            if($oldInv!=$g->mismass_invoice_id){
                $num++;
                $oldInv=$g->mismass_invoice_id;
            }
        }

        return $num;

    }

    public static function hitungCustomerWare($a){

        $c = [];
        $z = 1;
        $oldCust="";
        $num=0;

        $b = DB::table('data_list')
            ->select("data_list.cust_id")
            ->where("data_list.warehouse_id","=",$a)
            ->orderBy("data_list.mismass_order_id")
            ->get();
        
        foreach($b as $g){
            if(array_search($g->cust_id,$c)==""){
                $c[$z]=$g->cust_id;
                $num++;
                $z++;
            }
        }

        return $num;

    }

    public static function hitungCbmWare($wh,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN warehouse_id='$wh' $where2 THEN cbm ELSE 0 END) AS totalCbm"
            )
            ->whereRaw($where)
            ->first();

        return round($a->totalCbm, 2);
    }

    public static function hitungBeratWare($wh,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN warehouse_id='$wh' $where2 THEN weight ELSE 0 END) AS totalWeight"
            )
            ->whereRaw($where)
            ->first();

        return round($a->totalWeight, 2);
    }

    public static function hitungItemWare($wh,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN warehouse_id='$wh' $where2 THEN item ELSE 0 END) AS totalItem"
            )
            ->whereRaw($where)
            ->first();

        return $a->totalItem;
    }

    public static function hitungDiskonWare($wh,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN warehouse_id='$wh' $where2 THEN discount ELSE 0 END) AS totalDisc"
            )
            ->whereRaw($where)
            ->first();

        return $a->totalDisc;
    }

    public static function hitungLabaWare($wh,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN warehouse_id='$wh' $where2 THEN sub_total ELSE 0 END) AS totalPendapatan"
            )
            ->whereRaw($where)
            ->first();
        
        return $a->totalPendapatan;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////

    public static function hitungInvoiceCust($custId){
        $a = DB::table('data_list')
            ->select("mismass_invoice_id")
            ->where('cust_id',$custId)
            ->orderBy('mismass_invoice_id','asc')
            ->get();
        $oldInv = "";
        $total = 0;

        foreach($a as $b){
            if($b->mismass_invoice_id!=$oldInv){
                $total++;
                $oldInv=$b->mismass_invoice_id;
            }
        }

        return $total;
    }

    public static function hitungResiCust($custId){
        $a = DB::table('data_list')
            ->select("shipping_number")
            ->where('cust_id',$custId)
            ->orderBy('shipping_number','asc')
            ->get();
        $old = "";
        $total = 0;

        foreach($a as $b){
            if($b->shipping_number!=$old){
                $total++;
                $oldInv=$b->shipping_number;
            }
        }

        return $total;
    }

    public static function hitungBeratCust($custId,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN cust_id='$custId' $where2 THEN weight ELSE 0 END) AS totalWeight"
            )
            // ->where($where)
            ->first();
            
        return round($a->totalWeight, 2);
    }

    public static function hitungCbmCust($custId,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN cust_id='$custId' $where2 THEN cbm ELSE 0 END) AS totalCbm"
            )
            // ->where($where)
            ->first();
            
        return round($a->totalCbm, 2);
    }

    public static function hitungItemCust($custId,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN cust_id='$custId' $where2 THEN item ELSE 0 END) AS totalItem"
            )
            // ->where($where)
            ->first();

        return $a->totalItem;
    }

    public static function hitungDiskonCust($custId,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN cust_id='$custId' $where2 THEN discount ELSE 0 END) AS totalDisc"
            )
            // ->where($where)
            ->first();

        return $a->totalDisc;
    }

    public static function hitungLabaCust($custId,$type,$mismass_invoice_id=""){
        //0 ALL
        //1 IND
        //2 COR
        $where = $type == 0 ? "cust_type_id!=''" : ($type == 1 ? "cust_type_id='IND'" : ($type == 2 ? "cust_type_id='COR'" : ""));
        $where2 = $mismass_invoice_id == "" ? "AND mismass_invoice_id!=''" : "AND mismass_invoice_id='$mismass_invoice_id'"; 

        $a = DB::table('data_list')
            ->selectRaw(
            "SUM(CASE WHEN cust_id='$custId' $where2 THEN sub_total ELSE 0 END) AS totalPendapatan"
            )
            // ->where($where)
            ->first();

        return $a->totalPendapatan;
    }

    public function createDescForInvoice($mismassInvoiceId,$mode){
        $order=1;
        $total=0;
        $label = $mode=="BI" ? "Buat Invoice" : "Hapus Invoice";
        $getDataFirst = DB::table("data_list")->where("mismass_invoice_id",$mismassInvoiceId)->first();
        $getDataList = DB::table("data_list")->where("mismass_invoice_id",$mismassInvoiceId)->get();
        $getOrderList = DB::table("order_list")->where("invoice_id",$mismassInvoiceId)->first();

        $desc = "<b>".$label."</b> dengan detail,<br><br>
        Tanggal order : <b>".$this->dateFormatIndo($getOrderList->created_at,2)."</b><br>
        ID Sistem : <b>".$getOrderList->id."</b><br>
        No. invoice Mismass: <b>".$mismassInvoiceId."</b><br>
        Tanggal Invoice : <b>".$this->dateFormatIndo($getDataFirst->mismass_invoice_date,1)."</b><br>
        Tipe Order : <b>".DB::table("cust_type_list")->where("id",$getDataFirst->cust_type_id)->value("name")."</b><br>
        Konversi Mata Uang : <b>".($getDataFirst->fc_symbol!=""?$getDataFirst->fc_symbol." - ".$this->rupiah($getDataFirst->fc_value):"-")."</b><br> 
        Format Alamat Invoice : <b>".DB::table("template_list")->where("id",$getDataFirst->template_id)->value("name")."</b><br> 
        <br>
        <b>Pengirim</b><br>
        Nama : <b>".$getDataFirst->sender_first_name." ".$getDataFirst->sender_middle_name." ".$getDataFirst->sender_last_name."</b><br>
        Telpon : <b>".$getDataFirst->sender_phone."</b><br>
        Alamat : <b>".($getDataFirst->sender_address!=""?$getDataFirst->sender_address.", ".$getDataFirst->sender_sub_district.", ".$getDataFirst->sender_district.", ".$getDataFirst->sender_city.", ".$getDataFirst->sender_prov.", ".$getDataFirst->sender_postal_code:"-")."</b><br>
        <br>
        ".($getDataFirst->cust_type_id=="IND"?
        "<b>Penerima</b><br>
        Nama : <b>".$getDataFirst->cons_first_name." ".$getDataFirst->cons_middle_name." ".$getDataFirst->cons_last_name."</b><br>
        Telpon : <b>".$getDataFirst->cons_phone."</b><br>
        Alamat : <b>".$getDataFirst->cons_address.", ".$getDataFirst->cons_sub_district.", ".$getDataFirst->cons_district.", ".$getDataFirst->cons_city.", ".$getDataFirst->cons_prov.", ".$getDataFirst->cons_postal_code."</b><br><br>":"")."
        Pembayaran : <b>".($getDataFirst->bank_name!=""?"BANK":"DOKU")."</b><br>
        ".($getDataFirst->bank_name!=""?
        "Nama Bank : <b>".$getDataFirst->bank_name."</b><br>
        Nama Pemilik Rekening : <b>".$getDataFirst->bank_account_name."</b><br>
        No. Rekening / Virtual Account : <b>".$getDataFirst->bank_account_id."</b><br>":
        "Order Number Doku : <b>".$getDataFirst->doku_invoice_id."</b><br>
        Link Pembayaran Doku : <b>".$getDataFirst->doku_link."</b><br>")."
        <br>";

        foreach($getDataList as $g){
            $warehouse = DB::table("warehouse_list")->where("id",$g->warehouse_id)->first();
            $desc.="<div style='border:1px solid black;padding:10px;border-radius:10px'>
                    <b>Customer #".$order."</b><br><br>
                    ".($getDataFirst->cust_type_id=="COR"?
                    "<b>Penerima</b><br>
                    Nama : <b>".$g->cons_first_name." ".$g->cons_middle_name." ".$g->cons_last_name."</b><br>
                    Telpon : <b>".$g->cons_phone."</b><br>
                    Alamat : <b>".$g->cons_address.", ".$g->cons_sub_district.", ".$g->cons_district.", ".$g->cons_city.", ".$g->cons_prov.", ".$g->cons_postal_code."</b><br><br>":
                    "")."
                    Warehouse : <b>".$warehouse->id." - ".$warehouse->name." - ".$warehouse->location."</b><br>
                    Service : <b>".$g->service_name."</b><br>
                    Berat : <b>".($g->length>0?$g->weight." Kg (".$g->length."x".$g->width."x".$g->height.")":$g->weight." Kg")."</b><br>
                    ".(
                        $g->length>0?
                        "Berat Aktual : <b>".$g->actual_weight." Kg</b><br>":
                        ""
                    )."
                    Item : <b>".$g->item."</b><br>
                    CBM : <b>".$g->cbm." CBM</b><br>
                    Harga/satuan : <b>".$this->rupiah($g->service_price_per)."</b><br>
                    <br>
                    ".($g->discount>0?
                    "<b>Diskon</b><br>
                    Nominal : <b>".$this->rupiah($g->discount)."</b><br>
                    <br>":
                    "").
                    ($g->pickup_weight>0?
                    "<b>Pickup</b><br>
                    Berat : <b>".$g->pickup_weight." Kg</b><br>
                    Total Charge : <b>".$this->rupiah($g->pickup_charge)."</b><br>
                    <br>":
                    "").
                    ($g->document>0?
                    "<b>Dokumen</b><br>
                    Item : <b>".$g->document."</b><br>
                    Harga/item : <b>".$this->rupiah($g->document_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->document_total)."</b><br>
                    <br>":
                    "").
                    ($g->packing>0?
                    "<b>Packing</b><br>
                    Item : <b>".$g->packing."</b><br>
                    Harga/item : <b>".$this->rupiah($g->packing_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->packing_total)."</b><br>
                    <br>":
                    "")
                    .($g->import_permit>0?
                    "<b>Import Permit</b><br>
                    Item : <b>".$g->import_permit."</b><br>
                    Harga/item : <b>".$this->rupiah($g->import_permit_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->import_permit_total)."</b><br>
                    <br>":
                    "").
                    ($g->dr_medicine>0?
                    "<b>Dr Medicine</b><br>
                    Item : <b>".$g->dr_medicine."</b><br>
                    Harga/item : <b>".$this->rupiah($g->dr_medicine_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->dr_medicine_total)."</b><br>
                    <br>":
                    "").
                    ($g->insurance_item_price>0?
                    "<b>Asuransi</b><br>
                    Harga Barang : <b>".$this->rupiah($g->insurance_item_price)."</b><br>
                    Persentase : <b>".$g->insurance_percent." %</b><br>
                    Total Charge : <b>".$this->rupiah($g->insurance_total)."</b><br>
                    <br>":
                    "").
                    ($g->fee_item_price>0?
                    "<b>Fee</b><br>
                    Harga Barang : <b>".$this->rupiah($g->fee_item_price)."</b><br>
                    Persentase : <b>".$g->fee_percent." %</b><br>
                    Total Charge : <b>".$this->rupiah($g->fee_total)."</b><br>
                    <br>":
                    "").
                    ($g->tax_item_price>0?
                    "<b>Tax</b><br>
                    Harga Barang : <b>".$this->rupiah($g->tax_item_price)."</b><br>
                    Persentase : <b>".$g->tax_percent." %</b><br>
                    Total Charge : <b>".$this->rupiah($g->tax_total)."</b><br>
                    <br>":
                    "").
                    ($g->extra_cost_price>0?
                    "<b>Extra Ongkir</b><br>
                    Nominal : <b>".$this->rupiah($g->extra_cost_price)."</b><br>
                    Tujuan : <b>".$g->extra_cost_dest."</b><br>
                    Vendor : <b>".$g->extra_cost_vendor_name."</b><br>
                    <br>":
                    "").
                    ($g->additional_nom>0?
                    "<b>Additional</b><br>
                    Deskripsi : <b>".$g->additional_desc."</b><br>
                    Nominal : <b>".$this->rupiah($g->additional_nom)."</b><br>
                    <br>":
                    "")."
                    Sub Total : <b>".$this->rupiah($g->sub_total)."</b><br>
                    </div>
                    <br>";

                    $total+=$g->sub_total;
                    $order++;
        }
        
        $desc.="Total : <b style='font-size:30px'>".$this->rupiah($total)."</b>";
        return $desc;
    }

    public function createDescForEditInvoice($newData){
        $order=1;
        $totalLama=0;
        $totalBaru=0;
        $dataLama="";
        $dataBaru="";
        $getDataFirst = DB::table("data_list")->where("mismass_invoice_id",$newData->input('mismassInvoiceId'))->first();
        $getDataList = DB::table("data_list")->where("mismass_invoice_id",$newData->input('mismassInvoiceId'))->get();
        $getOrderList = DB::table("order_list")->where("invoice_id",$newData->input('mismassInvoiceId'))->first();

        foreach($getDataList as $g){
            $warehouse = DB::table("warehouse_list")->where("id",$g->warehouse_id)->first();
            $dataLama.="<div style='border:1px solid black;padding:10px;border-radius:10px'>
                    <b>Service #".$order."</b><br><br>
                    ".($getDataFirst->cust_type_id=="COR"?
                    "<b>Penerima</b><br>
                    Nama : <b>".$g->cons_first_name." ".$g->cons_middle_name." ".$g->cons_last_name."</b><br>
                    Telpon : <b>".$g->cons_phone."</b><br>
                    Alamat : <b>".$g->cons_address.", ".$g->cons_sub_district.", ".$g->cons_district.", ".$g->cons_city.", ".$g->cons_prov.", ".$g->cons_postal_code."</b><br><br>":
                    "")."
                    Warehouse : <b>".$warehouse->id." - ".$warehouse->name." - ".$warehouse->location."</b><br>
                    Service : <b>".$g->service_name."</b><br>
                    Berat : <b>".($g->length>0?$g->weight." Kg (".$g->length."x".$g->width."x".$g->height.")":$g->weight." Kg")."</b><br>
                    ".(
                        $g->length>0?
                        "Berat Aktual : <b>".$g->actual_weight." Kg</b><br>":
                        ""
                    )."
                    Item : <b>".$g->item."</b><br>
                    CBM : <b>".$g->cbm." CBM</b><br>
                    Harga/satuan : <b>".$this->rupiah($g->service_price_per)."</b><br>
                    <br>
                    ".($g->discount>0?
                    "<b>Diskon</b><br>
                    Nominal : <b>".$this->rupiah($g->discount)."</b><br>
                    <br>":
                    "").
                    ($g->pickup_weight>0?
                    "<b>Pickup</b><br>
                    Berat : <b>".$g->pickup_weight." Kg</b><br>
                    Total Charge : <b>".$this->rupiah($g->pickup_charge)."</b><br>
                    <br>":
                    "").
                    ($g->document>0?
                    "<b>Dokumen</b><br>
                    Item : <b>".$g->document."</b><br>
                    Harga/item : <b>".$this->rupiah($g->document_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->document_total)."</b><br>
                    <br>":
                    "").
                    ($g->packing>0?
                    "<b>Packing</b><br>
                    Item : <b>".$g->packing."</b><br>
                    Harga/item : <b>".$this->rupiah($g->packing_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->packing_total)."</b><br>
                    <br>":
                    "")
                    .($g->import_permit>0?
                    "<b>Import Permit</b><br>
                    Item : <b>".$g->import_permit."</b><br>
                    Harga/item : <b>".$this->rupiah($g->import_permit_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->import_permit_total)."</b><br>
                    <br>":
                    "").
                    ($g->dr_medicine>0?
                    "<b>Dr Medicine</b><br>
                    Item : <b>".$g->dr_medicine."</b><br>
                    Harga/item : <b>".$this->rupiah($g->dr_medicine_per)."</b><br>
                    Total Charge : <b>".$this->rupiah($g->dr_medicine_total)."</b><br>
                    <br>":
                    "").
                    ($g->insurance_item_price>0?
                    "<b>Asuransi</b><br>
                    Harga Barang : <b>".$this->rupiah($g->insurance_item_price)."</b><br>
                    Persentase : <b>".$g->insurance_percent." %</b><br>
                    Total Charge : <b>".$this->rupiah($g->insurance_total)."</b><br>
                    <br>":
                    "").
                    ($g->fee_item_price>0?
                    "<b>Fee</b><br>
                    Harga Barang : <b>".$this->rupiah($g->fee_item_price)."</b><br>
                    Persentase : <b>".$g->fee_percent." %</b><br>
                    Total Charge : <b>".$this->rupiah($g->fee_total)."</b><br>
                    <br>":
                    "").
                    ($g->tax_item_price>0?
                    "<b>Tax</b><br>
                    Harga Barang : <b>".$this->rupiah($g->tax_item_price)."</b><br>
                    Persentase : <b>".$g->tax_percent." %</b><br>
                    Total Charge : <b>".$this->rupiah($g->tax_total)."</b><br>
                    <br>":
                    "").
                    ($g->extra_cost_price>0?
                    "<b>Extra Ongkir</b><br>
                    Nominal : <b>".$this->rupiah($g->extra_cost_price)."</b><br>
                    Tujuan : <b>".$g->extra_cost_dest."</b><br>
                    Vendor : <b>".$g->extra_cost_vendor_name."</b><br>
                    <br>":
                    "").
                    ($g->additional_nom>0?
                    "<b>Additional</b><br>
                    Deskripsi : <b>".$g->additional_desc."</b><br>
                    Nominal : <b>".$this->rupiah($g->additional_nom)."</b><br>
                    <br>":
                    "")."
                    Sub Total : <b>".$this->rupiah($g->sub_total)."</b><br>
                    </div>
                    <br>";

                    $totalLama+=$g->sub_total;
                    $order++;
        }

        $order=1;

        for($i = 0; $i <= count($newData->input("warehouse")) - 1; $i++){
            $warehouse = DB::table("warehouse_list")->where("id",$newData->input("warehouse")[$i])->first();
            $service = DB::table("service_list")->where("id",$newData->input("service")[$i])->first();
            $panjang = isset($newData->input("panjang")[$i]) ? $this->normalizeInput($newData->input("panjang")[$i]) : 0;
            $lebar = isset($newData->input("lebar")[$i]) ? $this->normalizeInput($newData->input("lebar")[$i]) : 0;
            $tinggi = isset($newData->input("tinggi")[$i]) ? $this->normalizeInput($newData->input("tinggi")[$i]) : 0;
            $cbm = isset($newData->input("cbm")[$i]) ? $this->normalizeInput($newData->input("cbm")[$i]) : 0;
            $berat = isset($newData->input("kg")[$i]) ? $this->normalizeInput($newData->input("kg")[$i]) : 0;
            $beratAktual = isset($newData->input("actualKg")[$i]) ? $this->normalizeInput($newData->input("actualKg")[$i]) : 0;
            $item = isset($newData->input("item")[$i]) ? $this->normalizeInput($newData->input("item")[$i]) : 0;
            $pricePer = isset($newData->input("pricePer")[$i]) ? $this->normalizeInput($newData->input("pricePer")[$i]) : 0;
            $pickupWeight = $this->normalizeInput($newData->input("pickUpWeight" . $i));
            $pickupCharge = $this->normalizeInput($newData->input("pickUpCharge" . $i));
            $document = $this->normalizeInput($newData->input("document" . $i));
            $documentPer = $this->normalizeInput($newData->input("documentPer" . $i));
            $documentTotal = $this->normalizeInput($newData->input("documentTotal" . $i));
            $import = $this->normalizeInput($newData->input("import" . $i));
            $importPer = $this->normalizeInput($newData->input("importPer" . $i));
            $importTotal = $this->normalizeInput($newData->input("importTotal" . $i));
            $medicine = $this->normalizeInput($newData->input("medicine" . $i));
            $medicinePer = $this->normalizeInput($newData->input("medicinePer" . $i));
            $medicineTotal = $this->normalizeInput($newData->input("medicineTotal" . $i));
            $discount = $this->normalizeInput($newData->input("discount" . $i));
            $packing = $this->normalizeInput($newData->input("packing" . $i));
            $packingPer = $this->normalizeInput($newData->input("packingPer" . $i));
            $packingTotal = $this->normalizeInput($newData->input("packingTotal" . $i));
            $insuranceItemPrice = $this->normalizeInput($newData->input("insurancePriceItem" . $i));
            $insurancePercent = $this->normalizeInput($newData->input("insurancePercent" . $i));
            $insuranceTotal= $this->normalizeInput($newData->input("insuranceTotal" . $i));
            $taxItemPrice = $this->normalizeInput($newData->input("taxPriceItem" . $i));
            $taxPercent = $this->normalizeInput($newData->input("taxPercent" . $i));
            $taxTotal= $this->normalizeInput($newData->input("taxTotal" . $i));
            $feeItemPrice = $this->normalizeInput($newData->input("feePriceItem" . $i));
            $feePercent = $this->normalizeInput($newData->input("feePercent" . $i));
            $feeTotal= $this->normalizeInput($newData->input("feeTotal" . $i));
            $extraCostPrice = $this->normalizeInput($newData->input("extraCostPrice" . $i));
            $extraCostDest = $newData->input("extraCostDest" . $i) ?? "";
            $extraCostVendorName = $newData->input("extraCostVendorName" . $i) ?? "";
            $additionalDesc = $newData->input("additionalDesc" . $i) ?? "";
            $additionalNom = $this->normalizeInput($newData->input("additionalNominal" . $i));
            $subTotal = isset($newData->input("subTotal")[$i]) ? $this->normalizeInput($newData->input("subTotal")[$i]) : 0;

            $dataBaru.="<div style='border:1px solid black;padding:10px;border-radius:10px'>
                    <b>Service #".$order."</b><br><br>
                    ".($getDataFirst->cust_type_id=="COR"?
                    "<b>Penerima</b><br>
                    Nama : <b>".$newData->input("consFirstName")[$i]??""." ".$newData->input("consMiddleName")[$i]??""." ".$newData->input("consLastName")[$i]??""."</b><br>
                    Telpon : <b>".$newData->input("consPhone")[$i]??""."</b><br>
                    Alamat : <b>".$newData->input("consAddress")[$i]??"".", ".$newData->input("consSubDistrict")[$i]??"".", ".$newData->input("consDistrict")[$i]??"".", ".$newData->input("consCity")[$i]??"".", ".$newData->input("consProv")[$i]??"".", ".$newData->input("consPostalCode")[$i]??""."</b><br><br>":
                    "")."
                    Warehouse : <b>".$warehouse->id." - ".$warehouse->name." - ".$warehouse->location."</b><br>
                    Service : <b>".$service->name."</b><br>
                    Berat : <b>".(
                        $panjang>0?
                        $berat." Kg (".$panjang."x".$lebar."x".$tinggi.")":
                        $berat." Kg")."</b><br>
                    ".(
                        $panjang>0?
                        "Berat Aktual : <b>".$beratAktual." Kg</b><br>":
                        ""
                    )."
                    Item : <b>".$item."</b><br>
                    CBM : <b>".$cbm." CBM</b><br>
                    Harga/satuan : <b>".$this->rupiah($pricePer)."</b><br>
                    <br>
                    ".($discount>0?
                    "<b>Diskon</b><br>
                    Nominal : <b>".$this->rupiah($discount)."</b><br>
                    <br>":
                    "").
                    ($pickupWeight>0?
                    "<b>Pickup</b><br>
                    Berat : <b>".$pickupWeight." Kg</b><br>
                    Total Charge : <b>".$this->rupiah($pickupCharge)."</b><br>
                    <br>":
                    "").
                    ($document>0?
                    "<b>Dokumen</b><br>
                    Item : <b>".$document."</b><br>
                    Harga/item : <b>".$this->rupiah($documentPer)."</b><br>
                    Total Charge : <b>".$this->rupiah($documentTotal)."</b><br>
                    <br>":
                    "").
                    ($packing>0?
                    "<b>Packing</b><br>
                    Item : <b>".$packing."</b><br>
                    Harga/item : <b>".$this->rupiah($packingPer)."</b><br>
                    Total Charge : <b>".$this->rupiah($packingTotal)."</b><br>
                    <br>":
                    "")
                    .($import>0?
                    "<b>Import Permit</b><br>
                    Item : <b>".$import."</b><br>
                    Harga/item : <b>".$this->rupiah($importPer)."</b><br>
                    Total Charge : <b>".$this->rupiah($importTotal)."</b><br>
                    <br>":
                    "").
                    ($medicine>0?
                    "<b>Dr Medicine</b><br>
                    Item : <b>".$medicine."</b><br>
                    Harga/item : <b>".$this->rupiah($medicinePer)."</b><br>
                    Total Charge : <b>".$this->rupiah($medicineTotal)."</b><br>
                    <br>":
                    "").
                    ($insuranceItemPrice>0?
                    "<b>Asuransi</b><br>
                    Harga Barang : <b>".$this->rupiah($insuranceItemPrice)."</b><br>
                    Persentase : <b>".$insurancePercent." %</b><br>
                    Total Charge : <b>".$this->rupiah($insuranceTotal)."</b><br>
                    <br>":
                    "").
                    ($feeItemPrice>0?
                    "<b>Fee</b><br>
                    Harga Barang : <b>".$this->rupiah($feeItemPrice)."</b><br>
                    Persentase : <b>".$feePercent." %</b><br>
                    Total Charge : <b>".$this->rupiah($feeTotal)."</b><br>
                    <br>":
                    "").
                    ($taxItemPrice>0?
                    "<b>Tax</b><br>
                    Harga Barang : <b>".$this->rupiah($taxItemPrice)."</b><br>
                    Persentase : <b>".$taxPercent." %</b><br>
                    Total Charge : <b>".$this->rupiah($taxTotal)."</b><br>
                    <br>":
                    "").
                    ($extraCostPrice>0?
                    "<b>Extra Ongkir</b><br>
                    Nominal : <b>".$this->rupiah($extraCostPrice)."</b><br>
                    Tujuan : <b>".$extraCostDest."</b><br>
                    Vendor : <b>".$extraCostVendorName."</b><br>
                    <br>":
                    "").
                    ($additionalNom>0?
                    "<b>Additional</b><br>
                    Deskripsi : <b>".$additionalDesc."</b><br>
                    Nominal : <b>".$this->rupiah($additionalNom)."</b><br>
                    <br>":
                    "")."
                    Sub Total : <b>".$this->rupiah($subTotal)."</b><br>
                    </div>
                    <br>";

                    $totalBaru+=$subTotal;
                    $order++;
        }

        $desc = "<b>Edit Invoice</b> dengan detail,<br><br>
        <div style='display:flex'>
        <div style='width:50%;padding-right:10px;'>
        <b style='font-size:20px'>DATA LAMA</b><br>
        Tanggal order : <b>".$this->dateFormatIndo($getOrderList->created_at,2)."</b><br>
        ID Sistem : <b>".$getOrderList->id."</b><br>
        No. invoice Mismass : <b>".$newData->input('mismassInvoiceId')."</b><br>
        Tanggal Invoice : <b>".$this->dateFormatIndo($getDataFirst->mismass_invoice_date,1)."</b><br>
        Tipe Order : <b>".DB::table("cust_type_list")->where("id",$getDataFirst->cust_type_id)->value("name")."</b><br>
        Konversi Mata Uang : <b>".($getDataFirst->fc_symbol!=""?$getDataFirst->fc_symbol." - ".$this->rupiah($getDataFirst->fc_value):"-")."</b><br> 
        Format Alamat Invoice : <b>".DB::table("template_list")->where("id",$getDataFirst->template_id)->value("name")."</b><br>
        <br>
        <b>Pengirim</b><br>
        Nama : <b>".$getDataFirst->sender_first_name." ".$getDataFirst->sender_middle_name." ".$getDataFirst->sender_last_name."</b><br>
        Telpon : <b>".$getDataFirst->sender_phone."</b><br>
        Alamat : <b>".($getDataFirst->sender_address!=""?$getDataFirst->sender_address.", ".$getDataFirst->sender_sub_district.", ".$getDataFirst->sender_district.", ".$getDataFirst->sender_city.", ".$getDataFirst->sender_prov.", ".$getDataFirst->sender_postal_code:"-")."</b><br>
        <br>
        ".($getDataFirst->cust_type_id=="IND"?
        "<b>Penerima</b><br>
        Nama : <b>".$getDataFirst->cons_first_name." ".$getDataFirst->cons_middle_name." ".$getDataFirst->cons_last_name."</b><br>
        Telpon : <b>".$getDataFirst->cons_phone."</b><br>
        Alamat : <b>".$getDataFirst->cons_address.", ".$getDataFirst->cons_sub_district.", ".$getDataFirst->cons_district.", ".$getDataFirst->cons_city.", ".$getDataFirst->cons_prov.", ".$getDataFirst->cons_postal_code."</b><br><br>":"")."
        Pembayaran : <b>".($getDataFirst->bank_name!=""?"BANK":"DOKU")."</b><br>
        ".($getDataFirst->bank_name!=""?
        "Nama Bank : <b>".$getDataFirst->bank_name."</b><br>
        Nama Pemilik Rekening : <b>".$getDataFirst->bank_account_name."</b><br>
        No. Rekening / Virtual Account : <b>".$getDataFirst->bank_account_id."</b><br>":
        "Order Number Doku : <b>".$getDataFirst->doku_invoice_id."</b><br>
        Link Pembayaran Doku : <b>".$getDataFirst->doku_link."</b><br>")."
        <br>
        ".$dataLama."
        <br>
        Total : <b style='font-size:30px'>".$this->rupiah($totalLama)."</b>
        </div>
        <div style='width:50%;padding-right:10px;'>
        <b style='font-size:20px'>DATA BARU</b><br>
        Tanggal order : <b>".$this->dateFormatIndo($getOrderList->created_at,2)."</b><br>
        ID Sistem : <b>".$getOrderList->id."</b><br>
        No. invoice Mismass : <b>".$newData->input('mismassInvoiceId')."</b><br>
        Tanggal Invoice : <b>".$this->dateFormatIndo($newData->input("tanggalInvoice"),1)."</b><br>
        Tipe Order : <b>".DB::table("cust_type_list")->where("id",$getDataFirst->cust_type_id)->value("name")."</b><br>
        Konversi Mata Uang : <b>".($newData->input('foreignSymbol')!=""?$newData->input('foreignSymbol')." - ".$this->rupiah($this->normalizeInput($newData->input("foreignRateValue"))):"-")."</b><br> 
        Format Alamat Invoice : <b>".DB::table("template_list")->where("id",$newData->input('templateId'))->value("name")."</b><br>
        <br>
        <b>Pengirim</b><br>
        Nama : <b>".$newData->input('senderFirstName')[0]." ".$newData->input('senderMiddleName')[0]." ".$newData->input('senderLastName')[0]."</b><br>
        Telpon : <b>".$newData->input('senderPhone')[0]."</b><br>
        Alamat : <b>".($newData->input('senderAddress')[0]!=""?$newData->input('senderAddress')[0].", ".$newData->input('senderSubDistrict')[0].", ".$newData->input('senderDistrict')[0].", ".$newData->input('senderCity')[0].", ".$newData->input('senderProv')[0].", ".$newData->input('senderPostalCode')[0]:"-")."</b><br>
        <br>
        ".($getDataFirst->cust_type_id=="IND"?
        "<b>Penerima</b><br>
        Nama : <b>".$newData->input('consFirstName')[0]." ".$newData->input('consMiddleName')[0]." ".$newData->input('consLastName')[0]."</b><br>
        Telpon : <b>".$newData->input('consPhone')[0]."</b><br>
        Alamat : <b>".($newData->input('consAddress')[0]!=""?$newData->input('consAddress')[0].", ".$newData->input('consSubDistrict')[0].", ".$newData->input('consDistrict')[0].", ".$newData->input('consCity')[0].", ".$newData->input('consProv')[0].", ".$newData->input('consPostalCode')[0]:"-")."</b><br><br>":"")."
        Pembayaran : <b>".($newData->input('namaBank')!=""?"BANK":"DOKU")."</b><br>
        ".($newData->input('namaBank')!=""?
        "Nama Bank : <b>".$newData->input('namaBank')."</b><br>
        Nama Pemilik Rekening : <b>".$newData->input('namaRekening')."</b><br>
        No. Rekening / Virtual Account : <b>".$newData->input('noRekening')."</b><br>":
        "Order Number Doku : <b>".$newData->input('invoiceDoku')."</b><br>
        Link Pembayaran Doku : <b>".$newData->input('linkDoku')."</b><br>")."
        <br>
        ".$dataBaru."
        <br>
        Total : <b style='font-size:30px'>".$this->rupiah($totalBaru)."</b>
        </div>
        </div>";

        return $desc;
    }

    public function createDescForResi($mismassInvoiceId){
        $order=1;
        $total=0;
        $getDataFirst = DB::table("data_list")->where("mismass_invoice_id",$mismassInvoiceId)->first();
        $getDataList = DB::table("data_list")->where("mismass_invoice_id",$mismassInvoiceId)->get();
        $getOrderList = DB::table("order_list")->where("invoice_id",$mismassInvoiceId)->first();
        $getTotalData = DB::table("data_list")->selectRaw("SUM(weight) AS totalBerat,
                                                           SUM(item) AS totalItem,
                                                           SUM(sub_total) AS totalBayar")
                                              ->where("mismass_invoice_id",$mismassInvoiceId)
                                              ->first();

        $desc = "<b>Buat Resi</b> dengan detail,<br><br>
        Tanggal order : <b>".$this->dateFormatIndo($getOrderList->created_at,2)."</b><br>
        ID Sistem : <b>".$getOrderList->id."</b><br>
        No. invoice Mismass: <b>".$mismassInvoiceId."</b><br>
        Tanggal Invoice : <b>".$this->dateFormatIndo($getDataFirst->mismass_invoice_date,1)."</b><br>
        Tipe Order : <b>".DB::table("cust_type_list")->where("id",$getDataFirst->cust_type_id)->value("name")."</b><br>
        <br>
        <b>Pengirim</b><br>
        Nama : <b>".$getDataFirst->sender_first_name." ".$getDataFirst->sender_middle_name." ".$getDataFirst->sender_last_name."</b><br>
        Telpon : <b>".$getDataFirst->sender_phone."</b><br>
        Alamat : <b>".($getDataFirst->sender_address!=""?$getDataFirst->sender_address.", ".$getDataFirst->sender_sub_district.", ".$getDataFirst->sender_district.", ".$getDataFirst->sender_city.", ".$getDataFirst->sender_prov.", ".$getDataFirst->sender_postal_code:"-")."</b><br>
        <br>
        ".($getDataFirst->cust_type_id=="IND"?
        "<b>Penerima</b><br>
        Nama : <b>".$getDataFirst->cons_first_name." ".$getDataFirst->cons_middle_name." ".$getDataFirst->cons_last_name."</b><br>
        Telpon : <b>".$getDataFirst->cons_phone."</b><br>
        Alamat : <b>".$getDataFirst->cons_address.", ".$getDataFirst->cons_sub_district.", ".$getDataFirst->cons_district.", ".$getDataFirst->cons_city.", ".$getDataFirst->cons_prov.", ".$getDataFirst->cons_postal_code."</b><br><br>":"")."
        Pembayaran : <b>".($getDataFirst->bank_name!=""?"BANK":"DOKU")."</b><br>
        ".($getDataFirst->bank_name!=""?
        "Nama Bank : <b>".$getDataFirst->bank_name."</b><br>
        Nama Pemilik Rekening : <b>".$getDataFirst->bank_account_name."</b><br>
        No. Rekening / Virtual Account : <b>".$getDataFirst->bank_account_id."</b><br>":
        "Order Number Doku : <b>".$getDataFirst->doku_invoice_id."</b><br>
        Link Pembayaran Doku : <b>".$getDataFirst->doku_link."</b><br>")."<br>
        Total Berat : <b>".round($getTotalData->totalBerat, 2)." Kg</b><br>
        Total Item : <b>".$getTotalData->totalItem."</b><br>
        Total Pembayaran : <b>".$this->rupiah($getTotalData->totalBayar)."</b><br><br>";

        foreach($getDataList as $g){
            $desc.="<div style='border:1px solid black;padding:10px;border-radius:10px'>
            <b>Resi #".$order."</b><br><br>
            ".($getDataFirst->cust_type_id=="COR"?
            "<b>Penerima</b><br>
            Nama : <b>".$g->cons_first_name." ".$g->cons_middle_name." ".$g->cons_last_name."</b><br>
            Telpon : <b>".$g->cons_phone."</b><br>
            Alamat : <b>".$g->cons_address.", ".$g->cons_sub_district.", ".$g->cons_district.", ".$g->cons_city.", ".$g->cons_prov.", ".$g->cons_postal_code."</b><br><br>":
            "")."
            Ekspedisi : <b>".($g->forwarder_id=="MISMASS"?$g->forwarder_id:$g->forwarder_name)."</b><br>
            Nama Kurir : <b>".($g->forwarder_id=="MISMASS"?$g->forwarder_name:"-")."</b><br>
            Status No. Resi : <b>".($g->shipping_number_stats==1?"BELUM ADA":"ADA")."</b><br>
            No. Resi/ID : <b>".$g->shipping_number."</b><br>
            </div>
            <br>";

            $total+=$g->sub_total;
            $order++;

            if($getDataFirst->cust_type_id=="IND"){
                break;
            }

        }

        return $desc;
    }

    public function createDescForEditResi($newData){
        $order=1;
        $total=0;
        $getDataFirst = DB::table("data_list")->where("mismass_invoice_id",$newData->input('mismassInvoiceId'))->first();
        $getDataList = DB::table("data_list")->where("mismass_invoice_id",$newData->input('mismassInvoiceId'))->get();
        $getOrderList = DB::table("order_list")->where("invoice_id",$newData->input('mismassInvoiceId'))->first();
        $getTotalData = DB::table("data_list")->selectRaw("SUM(weight) AS totalBerat,
                                                           SUM(item) AS totalItem,
                                                           SUM(sub_total) AS totalBayar")
                                              ->where("mismass_invoice_id",$newData->input('mismassInvoiceId'))
                                              ->first();

        $dataLama = "";
        $dataBaru = "";
        $desc="";

        foreach($getDataList as $g){
            $dataLama.="<div style='border:1px solid black;padding:10px;border-radius:10px'>
            <b>Resi #".$order."</b><br><br>
            ".($getDataFirst->cust_type_id=="COR"?
            "<b>Penerima</b><br>
            Nama : <b>".$g->cons_first_name." ".$g->cons_middle_name." ".$g->cons_last_name."</b><br>
            Telpon : <b>".$g->cons_phone."</b><br>
            Alamat : <b>".$g->cons_address.", ".$g->cons_sub_district.", ".$g->cons_district.", ".$g->cons_city.", ".$g->cons_prov.", ".$g->cons_postal_code."</b><br><br>":
            "")."
            Ekspedisi : <b>".($g->forwarder_id=="MISMASS"?$g->forwarder_id:$g->forwarder_name)."</b><br>
            Nama Kurir : <b>".($g->forwarder_id=="MISMASS"?$g->forwarder_name:"-")."</b><br>
            Status No. Resi : <b>".(($g->shipping_number_stats ?? "") == 1 ? "BELUM ADA" : "ADA")."</b><br>
            No. Resi/ID : <b>".$g->shipping_number."</b><br>
            </div>
            <br>";

            $total+=$g->sub_total;
            $order++;

            if($getDataFirst->cust_type_id=="IND"){
                break;
            }

        }

        $order=1;
        for($i = 0; $i <= count($newData->input("id")) - 1; $i++){
            $dataBaru.="<div style='border:1px solid black;padding:10px;border-radius:10px'>
            <b>Resi #".$order."</b><br><br>
            ".($getDataFirst->cust_type_id=="COR"?
            "<b>Penerima</b><br>
            Nama : <b>".$newData->input('consFirstName')[$i]." ".$newData->input('consMiddleName')[$i]." ".$newData->input('consLastName')[$i]."</b><br>
            Telpon : <b>".$newData->input('consPhone')[$i]."</b><br>
            Alamat : <b>".$newData->input('consAddress')[$i].", ".$newData->input('consSubDistrict')[$i].", ".$newData->input('consDistrict')[$i].", ".$newData->input('consCity')[$i].", ".$newData->input('consProv')[$i].", ".$newData->input('consPostalCode')[$i]."</b><br><br>":
            "")."
            Ekspedisi : <b>".($newData->input('tipeForwarder')[$i]=="MISMASS" || $newData->input('tipeForwarder')[$i]=="PICK-UP" ? $newData->input('tipeForwarder')[$i] : $newData->input('namaForwarder')[$i] ?? "")."</b><br>
            Nama Kurir : <b>".($newData->input('tipeForwarder')[$i]=="MISMASS" ? $newData->input('namaForwarder')[$i] ?? "" : "-")."</b><br>
            Status No. Resi : <b>".(($newData->input('shippingNumberStats')[$i] ?? "") == "on" ? "BELUM ADA" : "ADA")."</b><br>
            No. Resi/ID : <b>".$newData->input('noResi')[$i]."</b><br>
            </div>
            <br>";

            $total+=$g->sub_total;
            $order++;

            if($getDataFirst->cust_type_id=="IND"){
                break;
            }
        }

        $desc.="<b>Edit Resi</b> dengan detail,<br><br>
        <div style='display:flex'>
        <div style='width:50%;padding-right:10px;'>
        <b style='font-size:20px'>DATA LAMA</b><br>
        Tanggal order : <b>".$this->dateFormatIndo($getOrderList->created_at,2)."</b><br>
        ID Sistem : <b>".$getOrderList->id."</b><br>
        No. invoice Mismass: <b>".$newData->input('mismassInvoiceId')."</b><br>
        Tanggal Invoice : <b>".$this->dateFormatIndo($getDataFirst->mismass_invoice_date,1)."</b><br>
        Tipe Order : <b>".DB::table("cust_type_list")->where("id",$getDataFirst->cust_type_id)->value("name")."</b><br>
        <br>
        <b>Pengirim</b><br>
        Nama : <b>".$getDataFirst->sender_first_name." ".$getDataFirst->sender_middle_name." ".$getDataFirst->sender_last_name."</b><br>
        Telpon : <b>".$getDataFirst->sender_phone."</b><br>
        Alamat : <b>".($getDataFirst->sender_address!=""?$getDataFirst->sender_address.", ".$getDataFirst->sender_sub_district.", ".$getDataFirst->sender_district.", ".$getDataFirst->sender_city.", ".$getDataFirst->sender_prov.", ".$getDataFirst->sender_postal_code:"-")."</b><br>
        <br>
        ".($getDataFirst->cust_type_id=="IND"?
        "<b>Penerima</b><br>
        Nama : <b>".$getDataFirst->cons_first_name." ".$getDataFirst->cons_middle_name." ".$getDataFirst->cons_last_name."</b><br>
        Telpon : <b>".$getDataFirst->cons_phone."</b><br>
        Alamat : <b>".$getDataFirst->cons_address.", ".$getDataFirst->cons_sub_district.", ".$getDataFirst->cons_district.", ".$getDataFirst->cons_city.", ".$getDataFirst->cons_prov.", ".$getDataFirst->cons_postal_code."</b><br><br>":"")."
        Pembayaran : <b>".($getDataFirst->bank_name!=""?"BANK":"DOKU")."</b><br>
        ".($getDataFirst->bank_name!=""?
        "Nama Bank : <b>".$getDataFirst->bank_name."</b><br>
        Nama Pemilik Rekening : <b>".$getDataFirst->bank_account_name."</b><br>
        No. Rekening / Virtual Account : <b>".$getDataFirst->bank_account_id."</b><br>":
        "Order Number Doku : <b>".$getDataFirst->doku_invoice_id."</b><br>
        Link Pembayaran Doku : <b>".$getDataFirst->doku_link."</b><br>")."<br>
        Total Berat : <b>".round($getTotalData->totalBerat, 2)." Kg</b><br>
        Total Item : <b>".$getTotalData->totalItem."</b><br>
        Total Pembayaran : <b>".$this->rupiah($getTotalData->totalBayar)."</b><br><br>
        ".$dataLama."
        </div>
        <div style='width:50%;padding-right:10px;'>
        <b style='font-size:20px'>DATA BARU</b><br>
        Tanggal order : <b>".$this->dateFormatIndo($getOrderList->created_at,2)."</b><br>
        ID Sistem : <b>".$getOrderList->id."</b><br>
        No. invoice Mismass: <b>".$newData->input('mismassInvoiceId')."</b><br>
        Tanggal Invoice : <b>".$this->dateFormatIndo($getDataFirst->mismass_invoice_date,1)."</b><br>
        Tipe Order : <b>".DB::table("cust_type_list")->where("id",$getDataFirst->cust_type_id)->value("name")."</b><br>
        <br>
        <b>Pengirim</b><br>
        Nama : <b>".$getDataFirst->sender_first_name." ".$getDataFirst->sender_middle_name." ".$getDataFirst->sender_last_name."</b><br>
        Telpon : <b>".$getDataFirst->sender_phone."</b><br>
        Alamat : <b>".($getDataFirst->sender_address!=""?$getDataFirst->sender_address.", ".$getDataFirst->sender_sub_district.", ".$getDataFirst->sender_district.", ".$getDataFirst->sender_city.", ".$getDataFirst->sender_prov.", ".$getDataFirst->sender_postal_code:"-")."</b><br>
        <br>
        ".($getDataFirst->cust_type_id=="IND"?
        "<b>Penerima</b><br>
        Nama : <b>".$getDataFirst->cons_first_name." ".$getDataFirst->cons_middle_name." ".$getDataFirst->cons_last_name."</b><br>
        Telpon : <b>".$getDataFirst->cons_phone."</b><br>
        Alamat : <b>".$getDataFirst->cons_address.", ".$getDataFirst->cons_sub_district.", ".$getDataFirst->cons_district.", ".$getDataFirst->cons_city.", ".$getDataFirst->cons_prov.", ".$getDataFirst->cons_postal_code."</b><br><br>":"")."
        Pembayaran : <b>".($getDataFirst->bank_name!=""?"BANK":"DOKU")."</b><br>
        ".($getDataFirst->bank_name!=""?
        "Nama Bank : <b>".$getDataFirst->bank_name."</b><br>
        Nama Pemilik Rekening : <b>".$getDataFirst->bank_account_name."</b><br>
        No. Rekening / Virtual Account : <b>".$getDataFirst->bank_account_id."</b><br>":
        "Order Number Doku : <b>".$getDataFirst->doku_invoice_id."</b><br>
        Link Pembayaran Doku : <b>".$getDataFirst->doku_link."</b><br>")."<br>
        Total Berat : <b>".round($getTotalData->totalBerat, 2)." Kg</b><br>
        Total Item : <b>".$getTotalData->totalItem."</b><br>
        Total Pembayaran : <b>".$this->rupiah($getTotalData->totalBayar)."</b><br><br>
        ".$dataBaru."
        </div>
        </div>";

        return $desc;
    }

    public function getForeignRate($fc){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.freecurrencyapi.com/v1/latest?apikey='.env("FCA_API_KEY").'&currencies=IDR&base_currency='.$fc,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($response);
        return round($json->data->IDR);
    }

    public function simpleDesc($desc,$id){
        if(strlen($desc)>300){

            // if(preg_match("/>/i", substr($desc,347,7))){
            //     $desc = substr($desc,0,370);
            // }else{
            //     $desc = substr($desc,0,350);
            // }
            $desc = substr($desc,0,300);

            $desc .= " ........<a></a>";
        }
        return $desc;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    public function sendWA($id,$status)
    {

        $get = DB::table("data_list")
            ->join("order_list", "data_list.mismass_invoice_id", "=", "order_list.invoice_id")
            ->join("cust_type_list", "order_list.cust_type_id", "=", "cust_type_list.id")
            ->select(
                "data_list.mismass_order_id",
                "data_list.mismass_invoice_id",
                "data_list.mismass_invoice_date",
                "data_list.mismass_invoice_link",
                "data_list.doku_link",
                "data_list.doku_invoice_id",
                "data_list.bank_name",
                "data_list.bank_account_name",
                "data_list.bank_account_id",
                "data_list.invoice_status",
                "data_list.forwarder_id",
                "data_list.forwarder_name",
                "data_list.shipping_number",
                "data_list.updated_at",
                "data_list.sender_first_name",
                "data_list.sender_middle_name",
                "data_list.sender_last_name",
                "data_list.sender_phone",
                "data_list.cons_first_name",
                "data_list.cons_middle_name",
                "data_list.cons_last_name",
                "data_list.cons_phone",
                "order_list.cust_type_id",
                "cust_type_list.name as custTypeName"
            )
            ->where("mismass_invoice_id", "like", '%' . $id . "%")->first();

//##################################################################################################

        $notif="";
        if($status=="EI"){
            $notif = "

*INFORMATION*
Your invoice has been *UPDATED* as per your request on ".date("d F Y")."
Please kindly check.

======================";   
        }else if($status=="HI"){
            $notif = "
            
*INFORMATION*
Your parcel has been *CANCELLED* as per your request on ".date("d F Y")."
            
=======================";
        }

//##################################################################################################        

        if($get->doku_link!=""){
            $informasi_pembayaran = "Please click below payment link :
" . $get->doku_link;
            $orderNumber = $get->doku_invoice_id;
        }else{
            $informasi_pembayaran = "*Payment info :* 
Bank : *" . $get->bank_name."*
Account Number : *".$get->bank_account_id."*
Account Name : *".$get->bank_account_name."*";
            $orderNumber = "-";
        }   

//##################################################################################################

if($get->cust_type_id=="IND"){
    $firstName = $get->cons_first_name;
    $middleName = " ".$get->cons_middle_name ?? "";
    $lastName = " ".$get->cons_last_name ?? "";
    $phone = $get->cons_phone;
}else if($get->cust_type_id=="COR"){
    $firstName = $get->sender_first_name;
    $middleName = " ".$get->sender_middle_name ?? "";
    $lastName = " ".$get->sender_last_name ?? "";
    $phone = $get->sender_phone;
}

//##################################################################################################

        if ($get->invoice_status == "UNPAID") {
            $header = "

Halo *" . $firstName . $middleName . $lastName . "* Here is your invoice, please make payment at your earliest convenience before we deliver your parcel.

". $informasi_pembayaran . "

======================

*Your Invoice Details :*
Invoice Date : *" . date("d F Y",strtotime($get->mismass_invoice_date)) . "*
Invoice No. : *" . $get->mismass_invoice_id . "*
Order Number : *" . $orderNumber . "*
Payment Status : *" . $get->invoice_status . "*
Customer : *" . $get->custTypeName . "*

Below link is your copy of invoice :
print." .env('APP_URL'). "/p/" . $get->mismass_invoice_link;
        } else {
            $header = "

Halo *" . $firstName . $middleName . $lastName . "* Thank you, we received your payment !

Your parcel is on the way to you, please ensure someone is available to receive it.

*Your Tracking Details :*
Tracking Date : *" . date("d F Y",strtotime($get->updated_at)) . "*
Tracking Number : *" . $get->shipping_number . "*
Courier : *" . ($get->forwarder_id == "MISMASS" ? $get->forwarder_id : $get->forwarder_name) . "*
No. Invoice : *".$get->mismass_invoice_id."*
Payment Status : *".$get->invoice_status."*

To track your parcel, please click below link.
https://www.mismasslogistic.com/tracking

======================

Thanks for being with MISMASS ! Your experience is important to us, kindly share your opinion on how can we serve you better
https://www.mismasslogistic.com/feedbackform";
        }

//##################################################################################################

$message = "*_Auto-Generated Message_*"
. $notif 
. $header . "

Thank You
*MISMASS LOGISTIC*
www.mismasslogistic.com";

//##################################################################################################

        $dataSending = array();
        $dataSending["api_key"] = env('WATZAP_API_KEY');
        $dataSending["number_key"] = env('WATZAP_NUMBER_KEY');
        $dataSending["phone_no"] = $phone;
        $dataSending["message"] = $message;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dataSending),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public function sendWAResiCOR($id,$sendTo,$mode){

        //mode
        //0 = create resi
        //1 = edit resi

        //send to
        //0 => to sender
        //1 => to consignee
        $get = DB::table('data_list')
                ->join('cust_type_list','cust_type_list.id','data_list.cust_type_id')
                ->select(
                    'sender_phone',
                    'sender_first_name',
                    'sender_middle_name',
                    'sender_last_name',
                    'sender_address',
                    'sender_city',
                    'sender_prov',
                    'cons_phone',
                    'cons_first_name',
                    'cons_middle_name',
                    'cons_last_name',
                    'cons_address',
                    'cons_city',
                    'cons_prov',
                    'updated_at',
                    'shipping_number',
                    'forwarder_id',
                    'forwarder_name',
                    'mismass_invoice_date',
                    'mismass_invoice_id',
                    'mismass_invoice_link',
                    'invoice_status',
                    'cust_type_list.name as custTypeName',
                    'doku_link',
                    'doku_invoice_id'
                )
                ->where('data_list.id',$id)
                ->first();

        $headDetail = $mode == 0 ? "Your parcel is on the way to you, please ensure someone is available to receive it." : "Tracking Detail has been *UPDATED* as per your request on ".date("d F Y")." Please kindly check.";
        $orderNumber = $get->doku_link != "" ? $get->doku_invoice_id : "-";
        $phone = $get->sender_phone;
        $detilresi = "";
        $mescor = " Thank you for your payment.";
        $detilinvoice = "
".$headDetail."

*Your Invoice Details :*
Invoice Date : *" . date("d F Y",strtotime($get->mismass_invoice_date)) . "*
Invoice No. : *" . $get->mismass_invoice_id . "*
Order Number : *" . $orderNumber . "*
Payment Status : *" . $get->invoice_status . "*
Customer : *" . $get->custTypeName . "*

Below link is your copy of invoice :
print." .env('APP_URL'). "/p/" . $get->mismass_invoice_link."

======================

Thanks for being with MISMASS ! Your experience is important to us, kindly share your opinion on how can we serve you better
https://www.mismasslogistic.com/feedbackform

";
    $firstName = $get->sender_first_name;
    $middleName = " ".$get->sender_middle_name ?? "";
    $lastName = " ".$get->sender_last_name ?? "";

if($sendTo==1){

    $headDetail = $mode == 0 ? "" : "Tracking Detail has been *UPDATED* as per your request on ".date("d F Y")." Please kindly check.";

    $detilresi="
".$headDetail."
    
*Your Tracking details :*
Tracking Date : *" . date("d F Y",strtotime($get->updated_at)) . "*
Tracking Number : *" . $get->shipping_number . "*
Courier : *" . ($get->forwarder_id == "MISMASS" ? $get->forwarder_id : $get->forwarder_name) . "*

For tracking your parcel, please kindly check the link below.
https://www.mismasslogistic.com/tracking";

    $mescor = "";
    $detilinvoice = "";
    $phone = $get->cons_phone;

    $firstName = $get->cons_first_name;
    $middleName = " ".$get->cons_middle_name ?? "";
    $lastName = " ".$get->cons_last_name ?? "";

}

    $name = $firstName.$middleName.$lastName;

        $header = "

Hallo *" . $name . "*".$mescor." ".$detilresi;

        $message = "*_AUTO-GENERATED MESSAGE_*"

. $header . "
".$detilinvoice."
Thank You
*MISMASS LOGISTIC*
www.mismasslogistic.com";

        // $data['phone'] = $phone;
        // $data['message'] = $message;
        
        // sendWAForm($data);

        $dataSending = array();
        $dataSending["api_key"] = env('WATZAP_API_KEY');
        $dataSending["number_key"] = env('WATZAP_NUMBER_KEY');
        $dataSending["phone_no"] = $phone;
        $dataSending["message"] = $message;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dataSending),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

    public function sendWAForm($data){

        $dataSending = array();
        $dataSending["api_key"] = env('WATZAP_API_KEY');
        $dataSending["number_key"] = env('WATZAP_NUMBER_KEY');
        $dataSending["phone_no"] = $data['phone'];
        $dataSending["message"] = $data['message'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($dataSending),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;

    }

}
