<?php

namespace App\Http\Controllers\Addon;

use App\Http\Controllers\Controller;
use App\Models\Addon\SmsSetting;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Classes;
use Craftsys\Msg91\Facade\Msg91;




class SmsController extends Controller
{
    function __construct() {
        $this->middleware(function ($request, $next) {
            $this->user = Auth()->user();
            $this->check_sms_setting(Auth()->user()->school_id);
            return $next($request);
        });


    }

    function check_sms_setting($school_id = ""){
        $sms_settings = SmsSetting::where('school_id', $school_id)->get()->count();

        if($sms_settings == 0) {
            $data = new SmsSetting;
            $data['twilio_sid'] = "Test_sid_xxxxxxxxx";
            $data['twilio_token']= "Test_token_xxxxxxxx";
            $data['twilio_from'] = "Test_number_xxxxxxxxx";
            $data['msg91_authentication_key'] = "Test_auth_xxxxxxxxx";
            $data['msg91_sender_id'] = "Test_sender_id_xxxxxxxxxxxx";
            $data['msg91_route'] = "Test_route_xxxxxxxxxxx";
            $data['msg91_country_code'] = "Test_country_code_xxxx";
            $data['active_sms'] = "none";
            $data['school_id'] = auth()->user()->school_id;
            $data->save();

        }
    }

   function settingsIndex(){
        $sms_settings = SmsSetting::where('school_id', auth()->user()->school_id)->get();
        return view('admin/sms_center/sms_settings', compact('sms_settings'));
   }

   public function settingsInserted(){
        $data = new SmsSetting;
        $data['twilio_sid'] = "Test_sid_xxxxxxxxx";
        $data['twilio_token']= "Test_token_xxxxxxxx";
        $data['twilio_from'] = "Test_number_xxxxxxxxx";
        $data['msg91_authentication_key'] = "Test_auth_xxxxxxxxx";
        $data['msg91_sender_id'] = "Test_sender_id_xxxxxxxxxxxx";
        $data['msg91_route'] = "Test_route_xxxxxxxxxxx";
        $data['msg91_country_code'] = "Test_country_code_xxxx";
        $data['school_id'] = auth()->user()->school_id;
        $data->save();

   }

   public function settingsUpdate( Request  $request)
   {
           // save data hare

        if($request->twilio_sid && $request->twilio_token && $request->twilio_from || $request->activet_sms){
            $data['twilio_sid'] = $request->twilio_sid;
            $data['twilio_token'] = $request->twilio_token;
            $data['twilio_from'] = $request->twilio_from;

        }
        elseif($request->msg91_authentication_key && $request->msg91_sender_id && $request->msg91_route && $request->msg91_country_code ){
            $data['msg91_authentication_key'] = $request->msg91_authentication_key;
            $data['msg91_sender_id'] = $request->msg91_sender_id;
            $data['msg91_route'] = $request->msg91_route;
            $data['msg91_country_code'] = $request->msg91_country_code;

        }
        elseif($request->active_sms){
            $data['active_sms'] = $request->active_sms;
        }

           SmsSetting::where('school_id', auth()->user()->school_id)->update($data);


       return redirect()->back()->with('message', 'Updated successfully.');

   }


public function tuiliomsg($message, $phone)
{
    $tuilo = SmsSetting::where('school_id', auth()->user()->school_id)->first();
    $twilio_sid = $tuilo->twilio_sid;
    $twilio_token = $tuilo->twilio_token;
    $twilio_from = $tuilo->twilio_from;
    
    try {
        $account_sid = "$twilio_sid";
        $auth_token = $twilio_token;
        $twilio_number = $twilio_from;

        
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($phone,[
            
            'from' => $twilio_number,   
            'body' => $message
        ]);
         // Message sent successfully
            return response()->json([
                'message' => 'SMS sent successfully',
                'message_sid' => $message->sid,
            ]);
        } catch (\Exception $e) {
            // Failed to send SMS
            return response()->json([
                'message' => 'Failed to send SMS',
                'error' => $e->getMessage(),
            ], 500);
        }
}

   public function msg91($message , $phones) {

    $msg91 = SmsSetting::where('school_id', auth()->user()->school_id)->first();

    $authKey = $msg91->msg91_authentication_key;
    $senderId = $msg91->msg91_sender_id;
    $country_code = $msg91->msg91_country_code;
    $route         = $msg91->msg91_route;

    //Multiple mobiles numbers separated by comma

        $mobileNumber = $phones;


    //Your message to send, Add URL encoding here.
    $message = urlencode($message);

    //Prepare you post parameters
    $postData = array(
        'authkey' => $authKey,
        'mobiles' => $mobileNumber,
        'message' => $message,
        'sender' => $senderId,
        'route' => $route
    );
    

    //API URL
    $url="http://api.msg91.com/api/sendhttp.php";

    // init the resource
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData
        //,CURLOPT_FOLLOWLOCATION => true
    ));


    //Ignore SSL certificate verification
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


    //get response
    $output = curl_exec($ch);

    //Print error if any
    print_r($output);
    if(curl_errno($ch))
    {
        echo 'error:' . curl_error($ch);
       
    }
    curl_close($ch);
}


   function smsSenderIndex(){
        $sms_settings = SmsSetting::where('school_id', auth()->user()->school_id)->get();
        $classes = Classes::where('school_id', auth()->user()->school_id)->get();

        return view('admin.sms_center.sms_sender', ['classes' => $classes], compact('sms_settings'));
   }


   function receivers(Request $request){
        $data = $request->all();

        $page_data['class_id'] = $data['class_id'];
        $page_data['section_id'] = $data['section_id'];
        $page_data['receiver'] = $data['receiver'];
        return view('admin.sms_center.receivers', ['page_data' => $page_data]);
   }



   function smsSending(Request $request){

    $sms_settings = SmsSetting::where('school_id', auth()->user()->school_id)->get();
    $message = "";
    $phones = $request->input('phones');
    $messages = $request->input('messages');

    if (!is_null($phones) > 0 && !is_null($messages) > 0) {
        foreach ($phones as $key => $phone) {

            if ($messages[$key] != "") {
                    $message = $messages[$key];
            }
            if ($sms_settings[0]->active_sms == 'none'){

                return redirect()->back()->with('error', 'activate a sms gateway');
            }
            elseif($sms_settings[0]->active_sms == 'msg91'){
                $this->msg91($message, $phone);

            }elseif($sms_settings[0]->active_sms == 'twilio'){
                $this->tuiliomsg($message, $phone);
            }
            
        }

        return redirect()->back()->with('message', 'Sms Sent');

    }else{
        return redirect()->back()->with('error', 'pick at least one number and message can not be empty');
    }

   }

}
