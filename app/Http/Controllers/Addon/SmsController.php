<?php

namespace App\Http\Controllers\Addon;

use App\Http\Controllers\Controller;
use App\Models\Addon\SmsSetting;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Models\Classes;
use Craftsys\Msg91\Facade\Msg91;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\DailyAttendances;
use Illuminate\Support\Facades\Log;




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


/*
public function sendDirectSMS($phone, $message, $school_id = null)
{
    // ✅ Use passed school_id if provided, fallback to logged-in user
    if (!$school_id) {
        $school_id = auth()->user()->school_id ?? null;
    }

    if (!$school_id) {
        \Log::warning("❌ SMS not sent — missing school ID.");
        return;
    }

    $settings = \App\Models\Addon\SmsSetting::where('school_id', $school_id)->first();

    if (!$settings) {
        \Log::warning("❌ SMS not sent — SMS settings not configured for school ID: {$school_id}");
        return;
    }

    // ✅ Hardcoded authKey (or pull from settings if preferred)
    $authKey      = '379724AJ9FzpGgvg62d539efP1';
    $senderId     = $settings->msg91_sender_id;
    $route        = $settings->msg91_route ?? '4';
    $country_code = $settings->msg91_country_code ?? '91';

    $payload = [
        "sender" => $senderId,
        "route" => $route,
        "country" => $country_code,
        "sms" => [
            [
                "message" => $message,
                "to" => [$phone]
            ]
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.msg91.com/api/v2/sendsms",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            "authkey: {$authKey}",
            "content-type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        \Log::error("❌ MSG91 cURL Error: $err");
    } else {
        \Log::info("✅ MSG91 SMS Sent to {$phone}: {$response}");
    }
}
*/





/*
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
*/
/*
    public function msg91($message , $phones) 
    {
    
        $curl = curl_init();
        
        curl_setopt_array($curl, [
          CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\n  \"template_id\": \"6300ca2fb587a61fd07c3793\",\n  \"short_url\": \"1\",\n  \"short_url_expiry\": \"1\",\n  \"realTimeResponse\": \"1\", \n  \"recipients\": [\n    {\n      \"mobiles\": \"919000249101\",\n      \"VAR1\": \"Prabhakar\",\n  \"VAR2\": \"0\",\n    \"VAR3\": \"0\"\n    }\n  ]\n}",
          CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authkey: 379724AJ9FzpGgvg62d539efP1",
            "content-type: application/json"
          ],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }
*/


//Shital
/*    public function msg91($mobiles, $studentName, $time, $date, $type = 'in')
    {
        $templateId = '6300ca2fb587a61fd07c3793'; // Replace with your real template ID
        $authKey = '379724AJ9FzpGgvg62d539efP1';

        $payload = [
            "template_id" => $templateId,
            "short_url" => "1",
            "short_url_expiry" => "1",
            "realTimeResponse" => "1",
            "recipients" => [
                [
                    "mobiles" => $mobiles,
                    "VAR1" => $studentName,
                    "VAR2" => $time,
                    "VAR3" => $date
                ]
            ]
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authkey: $authKey",
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            Log::error("MSG91 Flow Error: $err");
        } else {
            Log::info("✅ MSG91 Flow SMS Sent: $response");
        }
    }
*/

//shital1

public function msg91($mobiles, $studentName, $dateTime, $workedHours)
{
    $flowId = '6300ca2fb587a61fd07c3793'; // Your approved MSG91 Flow ID
    $sender = 'SWSATT'; // Your approved Sender ID
    $authKey = '379724AJ9FzpGgvg62d539efP1';

    $payload = [
        "flow_id" => $flowId,
        "sender" => $sender,
        "recipients" => [
            [
                "mobiles" => $mobiles,
                "name"    => $studentName,   // VAR1
                "time"    => $dateTime,      // VAR2
                "wtime"   => $workedHours    // VAR3
            ]
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.msg91.com/api/v5/flow/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            "authkey: $authKey",
            "content-type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        \Log::error("MSG91 Flow Error: $err");
    } else {
        \Log::info("✅ MSG91 Flow SMS Sent: $response");
    }
}



/*
    public function sendWatsappMessage(Request $request)
    {
        $sensorImei = $request->input('device_name');
        
        Log::info("Sending WhatsApp message to:" . $sensorImei);

    
        if (empty($sensorImei)) {
            return redirect()->back()->with('status', 'Please select a device to send WhatsApp message.');
        }
    
        // Fetch latest SmartbinData for selected device
        $latestData = SmartbinData::where('device_name', $sensorImei)
                                  ->orderBy('uplink_time', 'desc')
                                  ->first();
    
        if (!$latestData) {
            return redirect()->back()->with('status', 'No SmartBin data found for the selected device.');
        }
    
        // Fetch bin register info
        $register = SmartbinRegister::where('sensor_imei', $sensorImei)->first();
    
        if (!$register) {
            return redirect()->back()->with('status', 'No SmartBin register data found for the selected device.');
        }
    
        // Build values for the template
        $values = [
            $register->bin_number,                                // 1
            $register->bin_location_name,                        // 2
            $latestData->alarm_full ? 'Yes' : 'No',              // 3
            $latestData->alarm_fire ? 'Yes' : 'No',              // 4
            $latestData->alarm_fall ? 'Yes' : 'No',              // 5
            $latestData->alarm_battery ? 'Yes' : 'No',           // 6
            $latestData->per_filled . ' %',                      // 7
            $latestData->temperature . ' Centigrade',            // 8
            $latestData->tilt_angle . ' Degree',                 // 9
            $latestData->per_batt . ' %',                        // 10
            $latestData->device_name,                            // 11
            $register->sensor_mobile_no,                         // 12
            $latestData->latitude,                               // 13
            $latestData->longitude,                              // 14
            $latestData->location_url,                           // 15
            date('Y-m-d H:i:s', strtotime($latestData->scan_time)) . ' (YYYY-MM-DD)' // 16
        ];
    
        // Build components with body_1 to body_16
        $components = [];
        foreach ($values as $index => $value) {
            $components["body_" . ($index + 1)] = [
                "type" => "text",
                "value" => $value
            ];
        }
    
        // WhatsApp payload in MSG91 format
        $payload = [
            //"integrated_number" => "919642893089",
            "integrated_number" => "15557191149",
            "content_type" => "template",
            "payload" => [
                "messaging_product" => "whatsapp",
                "type" => "template",
                "template" => [
                    "name" => "smartbin_new",
                    "language" => [
                        "code" => "en_US", // ✅ Correct casing
                        "policy" => "deterministic"
                    ],
                    "namespace" => null,
                    "to_and_components" => [
                        [
                            "to" => [$register->bin_watsapp_no], // ✅ Ensure number is in 91XXXXXXXXXX format
                            "components" => $components
                        ]
                    ]
                ]
            ]
        ];
    
        // Optional: log payload for debugging
        // Log::info('WhatsApp Payload', ['payload' => $payload]);
    
        // Send WhatsApp Message via cURL
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => "https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authkey: 379724AJ9FzpGgvg62d539efP1",
                "content-type: application/json"
            ],
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
    
        if ($err) {
            return redirect()->back()->with('status', 'WhatsApp Error: ' . $err);
        } else {
            $responseData = json_decode($response, true);
    
            if (isset($responseData['status']) && $responseData['status'] == 'success') {
                return redirect()->back()->with('status', 'WhatsApp message sent successfully!');
            } else {
                return redirect()->back()->with('status', 'WhatsApp API Response: ' . $response);
            }
        }
    }
*/

public function sendWhatsappMessage($mobiles, $studentName, $dateTime, $workedHours)
{
    $payload = [
        "integrated_number" => "15557191149",
        "content_type" => "template",
        "payload" => [
            "messaging_product" => "whatsapp",
            "type" => "template",
            "template" => [
                "name" => "hrmattendance", // ✅ your approved template
                "language" => [
                    "code" => "en",
                    "policy" => "deterministic"
                ],
                "namespace" => null,
                "to_and_components" => [
                    [
                        "to" => [$mobiles], // ✅ use $mobiles which is passed to function
                        "components" => [
                            "body_1" => ["type" => "text", "value" => $studentName],
                            "body_2" => ["type" => "text", "value" => $dateTime],
                            "body_3" => ["type" => "text", "value" => $workedHours]
                        ]
                    ]
                ]
            ]
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/bulk/",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_SLASHES),
        CURLOPT_HTTPHEADER => [
            "accept: application/json",
            "authkey: 379724AJ9FzpGgvg62d539efP1",
            "content-type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        Log::error("❌ WhatsApp Error: " . $err);
    } else {
        Log::info("✅ WhatsApp message sent: " . $response);
    }
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
