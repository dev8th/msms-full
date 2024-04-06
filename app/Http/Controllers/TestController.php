<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    public function index(){
        // return view("test");
        abort(404);
    }


    public function getToken(){
        // $curl = curl_init();

        // curl_setopt_array($curl, [
        // CURLOPT_URL => "https://service-chat.qontak.com/oauth/token",
        // CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_ENCODING => "",
        // CURLOPT_MAXREDIRS => 10,
        // CURLOPT_TIMEOUT => 30,
        // CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        // CURLOPT_CUSTOMREQUEST => "POST",
        // CURLOPT_POSTFIELDS => json_encode([
        //     'username' => env("QONTAK_USERNAME"),
        //     'password' => env("QONTAK_PASSWORD"),
        //     'grant_type' => 'password',
        //     'client_id' => env("QONTAK_CLIENT_ID"),
        //     'client_secret' => env("QONTAK_CLIENT_SECRET")
        // ]),
        // CURLOPT_HTTPHEADER => [
        //     "Content-Type: application/json"
        // ],
        // ]);

        // $response = curl_exec($curl);
        // $err = curl_error($curl);

        // curl_close($curl);

        // if ($err) {
        // dd("cURL Error #:" . $err);
        // } else {
        // dd($response);
        // }
    }

    public function test(){

        $data[0] = "+62085232350505";
        $data[1] = "JOHN PANTAU";
        $data[2] = env("QONTAK_TEMPLATE_ID_CREATE_RESI");
        $data[3] = ['code' => 'en'];
        $data[4] = [
            [
                'key' => '1',
                'value' => 'full_name',
                'value_text' => 'JOHN PANTAU'
            ],
            [
                'key' => '2',
                'value' => 'ship_created_at',
                'value_text' => '6 April 2024'
            ],
            [
                'key' => '3',
                'value' => 'shipping_number',
                'value_text' => '-'
            ],
            [
                'key' => '4',
                'value' => 'forwarder',
                'value_text' => 'PICKUP'
            ],
            [
                'key' => '5',
                'value' => 'm_invoice_id',
                'value_text' => 'INV/P/123456'
            ],
            [
                'key' => '6',
                'value' => 'invoice_status',
                'value_text' => 'PAID'
            ]
        ];
        $this->sendWA($data);
    }
    
    // public function testing(Request $request)
    // {
        // $email = "halobro777@gmail.com";
        // $ccEmail = explode(",",env("MAIL_CC2"));
        
        // $emailData = [
        //     "subject" => "New Form Order | TESTING",
        //     "name" => "TESTING",
        //     "address" => "TESTING",
        //     "email" => "TESTING",
        //     "phone" => "TESTING",
        // ];
        
        // $sendingMail = Mail::to($email)
        //                 ->cc($ccEmail)
        //                 ->send(new SendMail($emailData));
                        
        // dd($sendingMail);
        
        // Excel::import(new UsersImport,$request->file('file'));
        // dd(Excel::import(new CustImport,request()->file('file')));
               
        // return back();
        
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://carikodepos.com/?s=60119',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
            // CURLOPT_POSTFIELDS => json_encode($dataSending),
            // CURLOPT_HTTPHEADER => array(
            //     'Content-Type: application/json'
            // ),
        // ));
        // $response = curl_exec($curl);
        // curl_close($curl);
        // dd($response);
        
        // $data["phone"] = "085232350505";
        // $data["message"] = "Testing123 Watzap";
        // $a = $this->sendWAForm($data);
        // dd($a);
        
        // if (!Auth::check()) {
        //     dd("tidak ada sesi");
        // } else {
        //     dd("ada sesi");
        // }
        
        // dd(bcrypt(""));
        // dd(fake()->postcode);
        // $test = DB::table("warehouse_list")->where("id","AUID")->first();

        // dd($test->id);

        // dd(env('MAIL_CC'));
        // dd(Auth::user());

        // $abc="";
        // for($i=0;$i<count($array[0]);$i++){
        //     $abc.=$array[0][$i][0];
        // }

        // $array = Excel::toArray(0, $request->file('excel'));
        // return json_encode($array);
        // dd($array);

        // dd($array);

        // abort(404);
    // }

    ///////////////////////////////////////////////////////////////////////////////////////////////
    // public function sendWA($id,$status)
    // {

        // $get = DB::table("data_list")
        //     ->join("order_list", "data_list.mismass_invoice_id", "=", "order_list.invoice_id")
        //     ->join("cust_type_list", "order_list.cust_type_id", "=", "cust_type_list.id")
        //     ->select(
        //         "data_list.mismass_order_id",
        //         "data_list.mismass_invoice_id",
        //         "data_list.mismass_invoice_date",
        //         "data_list.mismass_invoice_link",
        //         "data_list.doku_link",
        //         "data_list.doku_invoice_id",
        //         "data_list.bank_name",
        //         "data_list.bank_account_name",
        //         "data_list.bank_account_id",
        //         "data_list.invoice_status",
        //         "data_list.forwarder_id",
        //         "data_list.forwarder_name",
        //         "data_list.shipping_number",
        //         "data_list.updated_at",
        //         "data_list.sender_first_name",
        //         "data_list.sender_middle_name",
        //         "data_list.sender_last_name",
        //         "data_list.sender_phone",
        //         "data_list.cons_first_name",
        //         "data_list.cons_middle_name",
        //         "data_list.cons_last_name",
        //         "data_list.cons_phone",
        //         "order_list.cust_type_id",
        //         "cust_type_list.name as custTypeName"
        //     )
        //     ->where("mismass_invoice_id", "like", '%' . $id . "%")->first();

//##################################################################################################

//         $notif="";
//         if($status=="EI"){
//             $notif = "

// *INFORMATION*
// Your invoice has been *UPDATED* as per your request on ".date("d F Y")."
// Please kindly check.

// ======================";   
//         }else if($status=="HI"){
//             $notif = "
            
// *INFORMATION*
// Your parcel has been *CANCELLED* as per your request on ".date("d F Y")."
            
// =======================";
//         }

//##################################################################################################        

//         if($get->doku_link!=""){
//             $informasi_pembayaran = "Please click below payment link :
// " . $get->doku_link;
//             $orderNumber = $get->doku_invoice_id;
//         }else{
//             $informasi_pembayaran = "*Payment info :* 
// Bank : *" . $get->bank_name."*
// Account Number : *".$get->bank_account_id."*
// Account Name : *".$get->bank_account_name."*";
//             $orderNumber = "-";
//         }   

//##################################################################################################

// if($get->cust_type_id=="IND"){
//     $firstName = $get->cons_first_name;
//     $middleName = " ".$get->cons_middle_name ?? "";
//     $lastName = " ".$get->cons_last_name ?? "";
//     $phone = $get->cons_phone;
// }else if($get->cust_type_id=="COR"){
//     $firstName = $get->sender_first_name;
//     $middleName = " ".$get->sender_middle_name ?? "";
//     $lastName = " ".$get->sender_last_name ?? "";
//     $phone = $get->sender_phone;
// }

//##################################################################################################

//         if ($get->invoice_status == "UNPAID") {
//             $header = "

// Halo *" . $firstName . $middleName . $lastName . "* Here is your invoice, please make payment at your earliest convenience before we deliver your parcel.

// ". $informasi_pembayaran . "

// ======================

// *Your Invoice Details :*
// Invoice Date : *" . date("d F Y",strtotime($get->mismass_invoice_date)) . "*
// Invoice No. : *" . $get->mismass_invoice_id . "*
// Order Number : *" . $orderNumber . "*
// Payment Status : *" . $get->invoice_status . "*
// Customer : *" . $get->custTypeName . "*

// Below link is your copy of invoice :
// print." .env('APP_URL'). "/p/" . $get->mismass_invoice_link;
//         } else {
//             $header = "

// Halo *" . $firstName . $middleName . $lastName . "* Thank you, we received your payment !

// Your parcel is on the way to you, please ensure someone is available to receive it.

// *Your Tracking Details :*
// Tracking Date : *" . date("d F Y",strtotime($get->updated_at)) . "*
// Tracking Number : *" . $get->shipping_number . "*
// Courier : *" . ($get->forwarder_id == "MISMASS" ? $get->forwarder_id : $get->forwarder_name) . "*
// No. Invoice : *".$get->mismass_invoice_id."*
// Payment Status : *".$get->invoice_status."*

// To track your parcel, please click below link.
// https://www.mismasslogistic.com/tracking

// ======================

// Thanks for being with MISMASS ! Your experience is important to us, kindly share your opinion on how can we serve you better
// https://www.mismasslogistic.com/feedbackform";
//         }

//##################################################################################################

// $message = "*_Auto-Generated Message_*"
// . $notif 
// . $header . "

// Thank You
// *MISMASS LOGISTIC*
// www.mismasslogistic.com";

//##################################################################################################

    //     $dataSending = array();
    //     $dataSending["api_key"] = env('WATZAP_API_KEY');
    //     $dataSending["number_key"] = env('WATZAP_NUMBER_KEY');
    //     $dataSending["phone_no"] = $phone;
    //     $dataSending["message"] = $message;

    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => json_encode($dataSending),
    //         CURLOPT_HTTPHEADER => array(
    //             'Content-Type: application/json'
    //         ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     return $response;
    // }

    // public function sendWAResiCOR($id,$sendTo,$mode){

        //mode
        //0 = create resi
        //1 = edit resi

        //send to
        //0 => to sender
        //1 => to consignee
//         $get = DB::table('data_list')
//                 ->join('cust_type_list','cust_type_list.id','data_list.cust_type_id')
//                 ->select(
//                     'sender_phone',
//                     'sender_first_name',
//                     'sender_middle_name',
//                     'sender_last_name',
//                     'sender_address',
//                     'sender_city',
//                     'sender_prov',
//                     'cons_phone',
//                     'cons_first_name',
//                     'cons_middle_name',
//                     'cons_last_name',
//                     'cons_address',
//                     'cons_city',
//                     'cons_prov',
//                     'updated_at',
//                     'shipping_number',
//                     'forwarder_id',
//                     'forwarder_name',
//                     'mismass_invoice_date',
//                     'mismass_invoice_id',
//                     'mismass_invoice_link',
//                     'invoice_status',
//                     'cust_type_list.name as custTypeName',
//                     'doku_link',
//                     'doku_invoice_id'
//                 )
//                 ->where('data_list.id',$id)
//                 ->first();

//         $headDetail = $mode == 0 ? "Your parcel is on the way to you, please ensure someone is available to receive it." : "Tracking Detail has been *UPDATED* as per your request on ".date("d F Y")." Please kindly check.";
//         $orderNumber = $get->doku_link != "" ? $get->doku_invoice_id : "-";
//         $phone = $get->sender_phone;
//         $detilresi = "";
//         $mescor = " Thank you for your payment.";
//         $detilinvoice = "
// ".$headDetail."

// *Your Invoice Details :*
// Invoice Date : *" . date("d F Y",strtotime($get->mismass_invoice_date)) . "*
// Invoice No. : *" . $get->mismass_invoice_id . "*
// Order Number : *" . $orderNumber . "*
// Payment Status : *" . $get->invoice_status . "*
// Customer : *" . $get->custTypeName . "*

// Below link is your copy of invoice :
// print." .env('APP_URL'). "/p/" . $get->mismass_invoice_link."

// ======================

// Thanks for being with MISMASS ! Your experience is important to us, kindly share your opinion on how can we serve you better
// https://www.mismasslogistic.com/feedbackform

// ";
//     $firstName = $get->sender_first_name;
//     $middleName = " ".$get->sender_middle_name ?? "";
//     $lastName = " ".$get->sender_last_name ?? "";

// if($sendTo==1){

//     $headDetail = $mode == 0 ? "" : "Tracking Detail has been *UPDATED* as per your request on ".date("d F Y")." Please kindly check.";

//     $detilresi="
// ".$headDetail."
    
// *Your Tracking details :*
// Tracking Date : *" . date("d F Y",strtotime($get->updated_at)) . "*
// Tracking Number : *" . $get->shipping_number . "*
// Courier : *" . ($get->forwarder_id == "MISMASS" ? $get->forwarder_id : $get->forwarder_name) . "*

// For tracking your parcel, please kindly check the link below.
// https://www.mismasslogistic.com/tracking";

//     $mescor = "";
//     $detilinvoice = "";
//     $phone = $get->cons_phone;

//     $firstName = $get->cons_first_name;
//     $middleName = " ".$get->cons_middle_name ?? "";
//     $lastName = " ".$get->cons_last_name ?? "";

// }

//     $name = $firstName.$middleName.$lastName;

//         $header = "

// Hallo *" . $name . "*".$mescor." ".$detilresi;

//         $message = "*_AUTO-GENERATED MESSAGE_*"

// . $header . "
// ".$detilinvoice."
// Thank You
// *MISMASS LOGISTIC*
// www.mismasslogistic.com";

        // $data['phone'] = $phone;
        // $data['message'] = $message;
        
        // sendWAForm($data);

    //     $dataSending = array();
    //     $dataSending["api_key"] = env('WATZAP_API_KEY');
    //     $dataSending["number_key"] = env('WATZAP_NUMBER_KEY');
    //     $dataSending["phone_no"] = $phone;
    //     $dataSending["message"] = $message;

    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => json_encode($dataSending),
    //         CURLOPT_HTTPHEADER => array(
    //             'Content-Type: application/json'
    //         ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     return $response;

    // }

    // public function sendWAForm($data){

    //     $dataSending = array();
    //     $dataSending["api_key"] = env('WATZAP_API_KEY');
    //     $dataSending["number_key"] = env('WATZAP_NUMBER_KEY');
    //     $dataSending["phone_no"] = $data['phone'];
    //     $dataSending["message"] = $data['message'];

    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => 'https://api.watzap.id/v1/send_message',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => '',
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 0,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => 'POST',
    //         CURLOPT_POSTFIELDS => json_encode($dataSending),
    //         CURLOPT_HTTPHEADER => array(
    //             'Content-Type: application/json'
    //         ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     return $response;

    // }

}
