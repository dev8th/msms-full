<?php

namespace App\Http\Controllers;

use illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Tracking;
use App\Models\Warehouse;
use App\Models\Service;
use App\Models\Customer;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;

class WebFormController extends Controller
{
    public function index()
    {
        return view("webform.index");
    }

    public function input(Request $request)
    {

        $firstName = $request->input('firstName');
        $middleName = $request->input('middleName') ?? "";
        $lastName = $request->input('lastName') ?? "";
        $phone = $request->input('phone');
        $email = $request->input('email');
        $address = $request->input('address');
        $subDistrict = $request->input('subDistrict') ?? "";
        $district = $request->input('district');
        $city = $request->input('city');
        $prov = $request->input('prov');
        $postalCode = $request->input('postalCode');
        $secondName = $request->input('secondName');
        $secondPhone = $request->input('secondPhone');
        
        $firstN = $firstName;
        $middleN = " ".$middleName ?? "";
        $lastN = " ".$lastName ?? "";
        $fullName = $firstN.$middleN.$lastN;
        
        $fullAddress = $address.", ".$subDistrict.", ".$district.", ".$city.", ".$prov.", ".$postalCode;
        $registerErrText = "Your data has been registered. Please Kindly message our admin for the further detail."; 

        $cek_phone = DB::table('cust_list')->where("phone",$phone)->first();

        if($cek_phone!=null){
            $encode = array("status" => "Something Wrong", "text" => $registerErrText, "noAdmin" => env('APP_ADMIN_NUMBER_1'));
            return json_encode($encode);
        }

        $cek_email = DB::table('cust_list')->where("email",$email)->first();

        if($cek_email!=null){
            $encode = array("status" => "Something Wrong", "text" => $registerErrText, "noAdmin" => env('APP_ADMIN_NUMBER_1'));
            return json_encode($encode);
        }

        $custModel = new Customer;
        $custModel->first_name = $firstName;
        $custModel->middle_name = $middleName;
        $custModel->last_name = $lastName;
        $custModel->phone = $phone;
        $custModel->email = $email;
        $custModel->address = $address;
        $custModel->sub_district = $subDistrict;
        $custModel->district = $district;
        $custModel->city = $city;
        $custModel->prov = $prov;
        $custModel->postal_code = $postalCode;
        $custModel->cust_type_id = "IND";
        $custModel->created_by = "WEBFORM";
        $custModel->updated_by = "WEBFORM";

        $insert = $custModel->save();

        $custId = DB::table("cust_list")->where("phone", "=", $request->input("phone"))->value("id");

        if(!$insert){
            $encode = array("status" => "Something Wrong", "text" => "Fail to input Customer");
            return json_encode($encode);
        }

        $orderModel = new Order;
        $orderModel->cust_id = $custId;
        $orderModel->cust_type_id = "IND";
        $orderModel->order_status_id = "READY";
        $orderModel->invoice_id = "";
        $orderModel->created_by = "WEBFORM";
        $orderModel->updated_by = "WEBFORM";
        $orderModel->first_name = $firstName;
        $orderModel->middle_name = $middleName;
        $orderModel->last_name = $lastName;
        $orderModel->phone = $phone;
        $orderModel->email = $email;
        $orderModel->address = $address;
        $orderModel->sub_district = $subDistrict;
        $orderModel->district = $district;
        $orderModel->city = $city;
        $orderModel->prov = $prov;
        $orderModel->postal_code = $postalCode;
        $orderModel->second_name = $secondName;
        $orderModel->second_phone = $secondPhone;

        $insert2 = $orderModel->save();

        if(!$insert2){
            $encode = array("status" => "Something Wrong", "text" => "Fail to Input Order");
            return json_encode($encode);
        }

        $ccEmail = explode(",",env('MAIL_CC'));
        $bccEmail = env('MAIL_BCC');

        $emailData = [
            "subject" => "New Form Order | ".$fullName." ".$phone,
            "name" => $fullName,
            "address" => $fullAddress,
            "email" => $email,
            "phone" => $phone,
        ];

        $data = [
            "phone" => $phone,
            "message" => "Halo *".$fullName."* We have received your registration with below details : 

Full Name : *".$fullName."*
Whatsapp No : *".$phone."*
Email : *".$email."*
Full Address : *".$fullAddress."*
            
Kindly wait for further confirmations. Thank You.

Send from website https://www.mismasslogistic.com",
        ];

        $sendingMail = Mail::to($email)
                        ->cc($ccEmail)
                        ->bcc($bccEmail)
                        ->send(new SendMail($emailData));

        // if(!$sendingMail){
        //     $encode = array("status" => "Something Wrong", "text" => "Fail to send email");
        //     return json_encode($encode);
        // }

        $sendWA = $this->sendWAForm($data);

        // if(!$sendWA){
        //     $encode = array("status" => "Something Wrong", "text" => "Fail to send wa");
        //     return json_encode($encode);
        // }

        $encode = array("status" => "Success", "text" => "Kindly wait for further confirmations. Thank You.");
        return json_encode($encode);

    }
}
