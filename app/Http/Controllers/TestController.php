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
    
    public function testing(Request $request)
    {
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
    }

}
