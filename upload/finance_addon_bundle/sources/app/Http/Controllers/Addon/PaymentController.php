<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers\Addon;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Redirect;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Section;
use App\Models\Enrollment;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\StudentFeeManager;
use App\Models\Package;
use App\Models\PaymentHistory;
use App\Models\Payments;
use App\Models\GlobalSettings;
use App\Models\PaymentMethods;
use Omnipay\Omnipay;
use Illuminate\Support\Str;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Auth;
use Stripe;
use PaytmWallet;
use Session;
use Exception;

class PaymentController extends Controller
{
    /**
     * Show the offline payment.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    private $publicly_user_id;
    public function __construct()
    {
        if(isset($_GET['paymentId']))
        {

        }
        else
        {
            $this->middleware('auth');
            $this->middleware(function ($request, $next) {

                $this->id = Auth::user()->id;
                $this->role_id = Auth::user()->role_id;
                $this->publicly_user_id= $this->id;
                $this->school_id = Auth::user()->school_id;

                if($this->role_id==1)
                {
                    $this->superadmin_paytm_keys($this->id, $this->school_id);
                }
                else{
                    $this->paytm_keys($this->id,$this->school_id);
                }

                return $next($request);
            });

        }



    }



    public function pay(Request $request)
    {

        $gateway;
        $this->gateway = Omnipay::create('PayPal_Rest');

        $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
        $global_system_currency = $global_system_currency[0]['value'];

        $paypal = PaymentMethods::where(array('name' => 'paypal', 'school_id' => auth()->user()->school_id ))->first()->toArray();
        $paypal_keys = json_decode($paypal['payment_keys']);


        if ($paypal['mode'] == "test") {

            $this->gateway->setClientId($paypal_keys->test_client_id);
            $this->gateway->setSecret($paypal_keys->test_secret_key);
            $this->gateway->setTestMode(true);
        } elseif ($paypal['mode'] == "live") {
            $this->gateway->setClientId($paypal_keys->live_client_id);
            $this->gateway->setSecret($paypal_keys->live_secret_key);
            $this->gateway->setTestMode(false);
        }


        $user_data = $request->all();
        $success_url = $user_data['success_url'];
        $cancle_url = $user_data['cancle_url'];


        $user_data = implode(' ', array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($user_data), $user_data));

        try {

            $response = $this->gateway->purchase(array(
                'amount' => $request->amount,
                'currency' =>  $global_system_currency,
                'returnUrl' => route($success_url, ['user_data' => $user_data, 'response' => 'check']),
                'cancelUrl' => route($cancle_url, ['user_data' => $user_data, 'response' => 'check'])
            ))->send();


            if ($response->isRedirect()) {


                $response->redirect();
            } else {
                return $response->getMessage();
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }



    public function stripePost(Request $request)
    {

        $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
        $global_system_currency = $global_system_currency[0]['value'];

        $stripe = PaymentMethods::where(array('name' => 'stripe', 'school_id' => auth()->user()->school_id))->first()->toArray();
        $stripe_keys = json_decode($stripe['payment_keys']);
        $STRIPE_KEY;
        $STRIPE_SECRET;


        if ($stripe['mode'] == "test") {
            $STRIPE_KEY = $stripe_keys->test_key;
            $STRIPE_SECRET = $stripe_keys->test_secret_key;
        } elseif ($stripe['mode'] == "live") {
            $STRIPE_KEY = $stripe_keys->public_live_key;
            $STRIPE_SECRET = $stripe_keys->secret_live_key;
        }




        $user_data = $request->all();
        $expense_type = $user_data['expense_type'];



        $amount = $user_data['amount'] * 100;
        $success_url = $user_data['success_url'];
        $cancle_url = $user_data['cancle_url'];
        $user_data = implode(' ', array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($user_data), $user_data));

        try {

            Stripe\Stripe::setApiKey($STRIPE_SECRET);

            $session = \Stripe\Checkout\Session::create([
                'line_items' => [[
                    'price_data' => [
                        'currency' =>  $global_system_currency,
                        'product_data' => [
                            'name' =>  $expense_type,
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' =>  route($success_url, ['user_data' => $user_data, 'response' => 'check request->all() to get the response ']) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' =>  route($cancle_url, ['user_data' => $user_data, 'response' => 'check request->all() to get the response ']) . '?session_id={CHECKOUT_SESSION_ID}',
            ]);


            return redirect($session->url);

          } catch (\Exception $e) {

              return $e->getMessage();
          }


    }

    public function store(Request $request)
    {

        $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
        $global_system_currency = $global_system_currency[0]['value'];

        $razorpay = PaymentMethods::where(array('name' => 'razorpay', 'school_id' => auth()->user()->school_id))->first()->toArray();
        $razorpay_keys = json_decode($razorpay['payment_keys']);
        $RAZORPAY_KEY;
        $RAZORPAY_SECRET;


        if ($razorpay['mode'] == "test") {
            $RAZORPAY_KEY = $razorpay_keys->test_key;
            $RAZORPAY_SECRET = $razorpay_keys->test_secret_key;
        } elseif ($razorpay['mode'] == "live") {
            $RAZORPAY_KEY = $razorpay_keys->live_key;
            $RAZORPAY_SECRET = $razorpay_keys->live_secret_key;
        }

        $user_data = $request->all();
        $collected_response;
        $data_response;

        $success_url = $user_data['success_url'];
        $cancle_url = $user_data['cancle_url'];

        $user_data_info = implode(' ', array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($user_data), $user_data));

        $api = new Api($RAZORPAY_KEY, $RAZORPAY_SECRET);

        $payment = $api->payment->fetch($user_data['razorpay_payment_id']);

        if (count($user_data)  && !empty($user_data['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($user_data['razorpay_payment_id']);
                $response = $response->toArray();

                foreach ($response as $key => $value) {

                    if ($key != "notes" && $key != "acquirer_data") {

                        $collected_response[$key] = $value;
                    }
                }

                $data_response = implode(' ', array_map(function ($key, $value) {
                    if(!is_array($value)){
                        return "$key:$value";
                    }
                }, array_keys($collected_response), $collected_response));
            } catch (Exception $e) {
                // return  $e->getMessage();
                return redirect()->route($cancle_url, ['user_data' => $user_data_info, 'response' => $data_response]);
            }
        }

        return redirect()->route($success_url, ['user_data' => $user_data_info, 'response' => $data_response]);
    }


    public function paytm_keys($user_id,$school_id)
    {

        relogin_user($user_id);
        $paytm = PaymentMethods::where(array('name' => 'paytm', 'school_id' => $school_id))->first()->toArray();
        $paytm_keys = json_decode($paytm['payment_keys']);


        if ($paytm['mode'] == "test") {

            config(['services.paytm-wallet.env' => $paytm_keys->environment]);
            config(['services.paytm-wallet.merchant_id' => $paytm_keys->test_merchant_id]);
            config(['services.paytm-wallet.merchant_key' => $paytm_keys->test_merchant_key]);
            config(['services.paytm-wallet.merchant_website' => $paytm_keys->merchant_website]);
            config(['services.paytm-wallet.channel' => $paytm_keys->channel]);
            config(['services.paytm-wallet.industry_type' => $paytm_keys->industry_type]);
        } elseif ($paytm['mode'] == "live") {
            config(['services.paytm-wallet.env' => $paytm_keys->environment]);
            config(['services.paytm-wallet.merchant_id' => $paytm_keys->live_merchant_id]);
            config(['services.paytm-wallet.merchant_key' => $paytm_keys->live_merchant_key]);
            config(['services.paytm-wallet.merchant_website' => $paytm_keys->merchant_website]);
            config(['services.paytm-wallet.channel' => $paytm_keys->channel]);
            config(['services.paytm-wallet.industry_type' => $paytm_keys->industry_type]);
        }
    }

    public function paytmPayment(Request $request)
    {

        $user_data = $request->all();
        $success_url = $user_data['success_url'];
        $cancle_url = $user_data['cancle_url'];
        $user_data = implode(' ', array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($user_data), $user_data));

        $payment = PaytmWallet::with('receive');
        $payment->prepare([
            'order' => rand(),
            'user' => rand(10, 1000),
            'mobile_number' => '123456789',
            'email' => 'paytmtest@gmail.com',
            'amount' => $request->amount,

            'callback_url' => route('paytm.callback', ['success_url' => $success_url, 'cancle_url' => $cancle_url, 'user_data' => $user_data]),
        ]);

        relogin_user($this->publicly_user_id);
        return $payment->receive();
    }



    public function paytmCallback(Request $request, $success_url, $cancle_url, $user_data)
    {


        $transaction = PaytmWallet::with('receive');

        $response = $transaction->response();

        $r = implode(' ', array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($response), $response));

        $r = str_replace('/', '', $r);


        $transaction->getResponseMessage();
        $transaction->getOrderId();
        $transaction->getTransactionId();

        if ($transaction->isSuccessful()) {
            relogin_user($this->publicly_user_id);
            return redirect()->route($success_url, ['user_data' => $user_data, 'response' => $r]);
        } else if ($transaction->isFailed()) {
            relogin_user($this->publicly_user_id);
            return redirect()->route($cancle_url, ['user_data' => $user_data, 'response' => $r]);
        } else if ($transaction->isOpen()) {

            relogin_user($this->publicly_user_id);
            return redirect()->route($cancle_url, ['user_data' => $user_data, 'response' => $r]);
        }
    }

    public function string_to_array($user_data)
    {
        $user_data = explode(' ', $user_data);
        $recover_user_data = array();
        foreach ($user_data as $key => $value) {
            $length = strlen($value);
            $position = strpos($value, ':');
            $array_key = substr($value, 0, $position);
            $array_value = substr($value, $position + 1, $length);
            $recover_user_data[$array_key] = $array_value;
        }

        return $recover_user_data;
    }



    public function student_fee_success_payment_student(Request $request, $user_data, $response)
    {

        $user_data = $this->string_to_array($user_data);

        $Student_id_who_is_getting_paid = StudentFeeManager::find($user_data['expense_id'])->first()->toArray();
        $Student_id_who_is_getting_paid = $Student_id_who_is_getting_paid['student_id'];


        if ($user_data['payment_method'] == 'paypal') {


            $paypal_response_from_api = $request->all();

            $paypal_payment_response = json_encode($paypal_response_from_api);
            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $paypal_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);



            if ($status != "") {
                StudentFeeManager::where('id',  $user_data['expense_id'])->update([
                    'status' => 'paid',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'paid_amount' => $user_data['amount'],
                    'payment_method' => $user_data['payment_method'],
                ]);

                return redirect()->route('student.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {
                return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
            }
        }



        if ($user_data['payment_method'] == 'stripe') {
            $stripe = PaymentMethods::where(array('name' => 'stripe', 'school_id' => auth()->user()->school_id))->first()->toArray();
            $stripe_keys = json_decode($stripe['payment_keys']);
            $STRIPE_KEY;
            $STRIPE_SECRET;


            if ($stripe['mode'] == "test") {
                $STRIPE_KEY = $stripe_keys->test_key;
                $STRIPE_SECRET = $stripe_keys->test_secret_key;
            } elseif ($stripe['mode'] == "live") {
                $STRIPE_KEY = $stripe_keys->public_live_key;
                $STRIPE_SECRET = $stripe_keys->secret_live_key;
            }

            $stripe_api_response_session_id = $request->all();
            $stripe = new \Stripe\StripeClient($STRIPE_SECRET);
            $session_response = $stripe->checkout->sessions->retrieve($stripe_api_response_session_id['session_id'], []);
            $stripe_payment_intent = $session_response['payment_intent'];
            $stripe_session_id = $stripe_api_response_session_id['session_id'];

            $stripe_transaction_keys = array();
            $stripe_response['payment_intent']  = $stripe_payment_intent;
            $stripe_response['session_id'] = $stripe_session_id;
            $stripe_transaction_keys = $stripe_response;
            $stripe_payment_response = json_encode($stripe_transaction_keys);


            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $stripe_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);

            if ($status != "") {
                StudentFeeManager::where('id',  $user_data['expense_id'])->update([
                    'status' => 'paid',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'paid_amount' => $user_data['amount'],
                    'payment_method' => $user_data['payment_method']
                ]);

                return redirect()->route('student.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {
                return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
            }
        }

        if ($user_data['payment_method'] == 'razorpay') {

            $razorpay_api_response = $this->string_to_array($response);
            $razorpay_payment_response = json_encode($razorpay_api_response);

            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' =>  $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $razorpay_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);

            if ($status != "") {

                StudentFeeManager::where('id',  $user_data['expense_id'])->update(['status' => 'paid', 'paid_amount' => $user_data['amount'], 'updated_at' => date("Y-m-d H:i:s"), 'payment_method' => $user_data['payment_method'],]);

                return redirect()->route('student.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {
                return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
            }
        }

        if ($user_data['payment_method'] == 'paytm') {
            $paytm_api_response = $this->string_to_array($response);
            $paytm_payment_response = json_encode($paytm_api_response);
            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $paytm_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => $user_data['school_id'],
            ]);


            relogin_user($user_data['user_id']);

            if ($status != "") {

                StudentFeeManager::where('id',  $user_data['expense_id'])->update(['status' => 'paid', 'paid_amount' => $user_data['amount'], 'updated_at' => date("Y-m-d H:i:s"), 'payment_method' => $user_data['payment_method'],]);

                return redirect()->route('student.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {

                return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
            }
        }
    }

    public function student_fee_fail_payment_student(Request $request, $user_data, $response)
    {

        return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
    }



    public function student_fee_success_payment(Request $request, $user_data, $response)
    {

        $user_data = $this->string_to_array($user_data);

        $Student_id_who_is_getting_paid = StudentFeeManager::find($user_data['expense_id'])->first()->toArray();
        $Student_id_who_is_getting_paid = $Student_id_who_is_getting_paid['student_id'];



        if ($user_data['payment_method'] == 'paypal') {

            $paypal_response_from_api = $request->all();
            relogin_user($user_data['user_id']);
            $paypal_payment_response = json_encode($paypal_response_from_api);
            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $paypal_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);



            if ($status != "") {
                StudentFeeManager::where('id',  $user_data['expense_id'])->update([
                    'status' => 'paid',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'paid_amount' => $user_data['amount'],
                    'payment_method' => $user_data['payment_method'],
                ]);

                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {
                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
            }
        }



        if ($user_data['payment_method'] == 'stripe') {
            $stripe = PaymentMethods::where(array('name' => 'stripe', 'school_id' => auth()->user()->school_id))->first()->toArray();
            $stripe_keys = json_decode($stripe['payment_keys']);
            $STRIPE_KEY;
            $STRIPE_SECRET;


            if ($stripe['mode'] == "test") {
                $STRIPE_KEY = $stripe_keys->test_key;
                $STRIPE_SECRET = $stripe_keys->test_secret_key;
            } elseif ($stripe['mode'] == "live") {
                $STRIPE_KEY = $stripe_keys->public_live_key;
                $STRIPE_SECRET = $stripe_keys->secret_live_key;
            }

            $stripe_api_response_session_id = $request->all();
            $stripe = new \Stripe\StripeClient($STRIPE_SECRET);
            $session_response = $stripe->checkout->sessions->retrieve($stripe_api_response_session_id['session_id'], []);
            $stripe_payment_intent = $session_response['payment_intent'];
            $stripe_session_id = $stripe_api_response_session_id['session_id'];

            $stripe_transaction_keys = array();
            $stripe_response['payment_intent']  = $stripe_payment_intent;
            $stripe_response['session_id'] = $stripe_session_id;
            $stripe_transaction_keys = $stripe_response;
            $stripe_payment_response = json_encode($stripe_transaction_keys);


            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $stripe_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);

            if ($status != "") {
                StudentFeeManager::where('id',  $user_data['expense_id'])->update([
                    'status' => 'paid',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'paid_amount' => $user_data['amount'],
                    'payment_method' => $user_data['payment_method']
                ]);

                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {
                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
            }
        }

        if ($user_data['payment_method'] == 'razorpay') {

            $razorpay_api_response = $this->string_to_array($response);
            $razorpay_payment_response = json_encode($razorpay_api_response);

            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' =>  $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $razorpay_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);

            if ($status != "") {

                StudentFeeManager::where('id',  $user_data['expense_id'])->update(['status' => 'paid', 'paid_amount' => $user_data['amount'], 'updated_at' => date("Y-m-d H:i:s"), 'payment_method' => $user_data['payment_method'],]);

                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {
                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
            }
        }

        if ($user_data['payment_method'] == 'paytm') {
            $paytm_api_response = $this->string_to_array($response);
            $paytm_payment_response = json_encode($paytm_api_response);
            $status = Payments::create([
                'expense_type' => $user_data['expense_type'],
                'expense_id' => $user_data['expense_id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => $user_data['payment_method'],
                'amount' => $user_data['amount'],
                'status' => 'paid',
                'transaction_keys' => $paytm_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => $user_data['school_id'],
            ]);


            relogin_user($user_data['user_id']);

            if ($status != "") {

                StudentFeeManager::where('id',  $user_data['expense_id'])->update(['status' => 'paid', 'paid_amount' => $user_data['amount'], 'updated_at' => date("Y-m-d H:i:s"), 'payment_method' => $user_data['payment_method'],]);

                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment SuccessFul');
            } else {

                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
            }
        }
    }

    public function student_fee_fail_payment(Request $request, $user_data, $response)
    {

        return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
    }



     public function payWithPaypal_ForSubscription(Request $request)
     {
         $gateway;
         $this->gateway = Omnipay::create('PayPal_Rest');
         $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
         $global_system_currency = $global_system_currency[0]['value'];

         $paypal = get_settings('paypal');
         $paypal_keys = json_decode($paypal);




         if ($paypal_keys->mode == "test") {

             $this->gateway->setClientId($paypal_keys->test_client_id);
             $this->gateway->setSecret($paypal_keys->test_secret_key);
             $this->gateway->setTestMode(true);
         } elseif ($paypal_keys->mode == "live") {
             $this->gateway->setClientId($paypal_keys->live_client_id);
             $this->gateway->setSecret($paypal_keys->live_secret_key);
             $this->gateway->setTestMode(false);
         }


         $user_data = $request->all();
         $success_url = $user_data['success_url'];
         $cancle_url = $user_data['cancle_url'];

         $user_data = implode(' ', array_map(function ($key, $value) {
             return "$key:$value";
         }, array_keys($user_data), $user_data));


         try {

             $response = $this->gateway->purchase(array(
                 'amount' => $request->amount,
                 'currency' =>  $global_system_currency,
                 'returnUrl' => route($success_url, ['user_data' => $user_data, 'response' => 'ccheck_response_in_request->all()']),
                 'cancelUrl' => route($cancle_url, ['user_data' => $user_data, 'response' => 'ccheck_response_in_request->all()'])
             ))->send();


             if ($response->isRedirect()) {

                 $response->redirect();
             } else {
                 return $response->getMessage();
             }
         } catch (\Throwable $th) {
             return $th->getMessage();
         }
     }


     public function PayWithStripe_ForSubscription(Request $request)
     {

         $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
         $global_system_currency = $global_system_currency[0]['value'];

         $stripe =  get_settings('stripe');
         $stripe_keys = json_decode($stripe);

         $STRIPE_KEY;
         $STRIPE_SECRET;


         if ($stripe_keys->mode == "test") {
             $STRIPE_KEY = $stripe_keys->test_key;
             $STRIPE_SECRET = $stripe_keys->test_secret_key;
         } elseif ($stripe_keys->mode == "live") {
             $STRIPE_KEY = $stripe_keys->public_live_key;
             $STRIPE_SECRET = $stripe_keys->secret_live_key;
         }




         $user_data = $request->all();
         $expense_type = $user_data['expense_type'];



         $amount = $user_data['amount'] * 100;
         $success_url = $user_data['success_url'];
         $cancle_url = $user_data['cancle_url'];
         $user_data = implode(' ', array_map(function ($key, $value) {
             return "$key:$value";
         }, array_keys($user_data), $user_data));

         try {

             Stripe\Stripe::setApiKey($STRIPE_SECRET);

             $session = \Stripe\Checkout\Session::create([
                 'line_items' => [[
                     'price_data' => [
                         'currency' =>  $global_system_currency,
                         'product_data' => [
                             'name' =>  $expense_type,
                         ],
                         'unit_amount' => $amount,
                     ],
                     'quantity' => 1,
                 ]],
                 'mode' => 'payment',
                 'success_url' =>  route($success_url, ['user_data' => $user_data, 'response' => 'check request->all() to get the response ']) . '?session_id={CHECKOUT_SESSION_ID}',
                 'cancel_url' =>  route($cancle_url, ['user_data' => $user_data, 'response' => 'check request->all() to get the response ']) . '?session_id={CHECKOUT_SESSION_ID}',
             ]);


             return redirect($session->url);
         } catch (\Exception $e) {

             return $e->getMessage();
         }
     }

     public function PayWithRazorpay_ForSubscription(Request $request)
     {

         $global_system_currency = GlobalSettings::where('key', 'system_currency')->get()->toArray();
         $global_system_currency = $global_system_currency[0]['value'];

         $razorpay =  get_settings('razorpay');
         $razorpay_keys = json_decode($razorpay);
         $RAZORPAY_KEY;
         $RAZORPAY_SECRET;


         if ($razorpay_keys->mode == "test") {
             $RAZORPAY_KEY = $razorpay_keys->test_key;
             $RAZORPAY_SECRET = $razorpay_keys->test_secret_key;
         } elseif ($razorpay_keys->mode == "live") {
             $RAZORPAY_KEY = $razorpay_keys->live_key;
             $RAZORPAY_SECRET = $razorpay_keys->live_secret_key;
         }

         $user_data = $request->all();
         $collected_response;
         $data_response;

         $success_url = $user_data['success_url'];
         $cancle_url = $user_data['cancle_url'];

         $user_data_info = implode(' ', array_map(function ($key, $value) {
             return "$key:$value";
         }, array_keys($user_data), $user_data));

         $api = new Api($RAZORPAY_KEY, $RAZORPAY_SECRET);

         $payment = $api->payment->fetch($user_data['razorpay_payment_id']);

         if (count($user_data)  && !empty($user_data['razorpay_payment_id'])) {
             try {
                 $response = $api->payment->fetch($user_data['razorpay_payment_id']);
                 $response = $response->toArray();

                 foreach ($response as $key => $value) {

                     if ($key != "notes" && $key != "acquirer_data") {

                         $collected_response[$key] = $value;
                     }
                 }

                 $data_response = implode(' ', array_map(function ($key, $value) {
                    if(!is_array($value)){
                        return "$key:$value";
                    }
                 }, array_keys($collected_response), $collected_response));
             } catch (Exception $e) {
                 return  $e->getMessage();
                 return redirect()->route($cancle_url, ['user_data' => $user_data_info, 'response' => $data_response]);
             }
         }

         return redirect()->route($success_url, ['user_data' => $user_data_info, 'response' => $data_response]);
     }

     public function superadmin_paytm_keys($user_id, $school_id)
     {

         relogin_user($user_id);
         $paytm = get_settings('paytm');
         $paytm_keys = json_decode($paytm);


         if ($paytm_keys->mode == "test") {

             config(['services.paytm-wallet.env' => $paytm_keys->environment]);
             config(['services.paytm-wallet.merchant_id' => $paytm_keys->test_merchant_id]);
             config(['services.paytm-wallet.merchant_key' => $paytm_keys->test_merchant_key]);
             config(['services.paytm-wallet.merchant_website' => $paytm_keys->merchant_website]);
             config(['services.paytm-wallet.channel' => $paytm_keys->channel]);
             config(['services.paytm-wallet.industry_type' => $paytm_keys->industry_type]);
         } elseif ($paytm_keys->mode == "live") {
             config(['services.paytm-wallet.env' => $paytm_keys->environment]);
             config(['services.paytm-wallet.merchant_id' => $paytm_keys->live_merchant_id]);
             config(['services.paytm-wallet.merchant_key' => $paytm_keys->live_merchant_key]);
             config(['services.paytm-wallet.merchant_website' => $paytm_keys->merchant_website]);
             config(['services.paytm-wallet.channel' => $paytm_keys->channel]);
             config(['services.paytm-wallet.industry_type' => $paytm_keys->industry_type]);
         }
     }

     public function PayWithPaytm_ForSubscription(Request $request)
     {

         $user_data = $request->all();
         $success_url = $user_data['success_url'];
         $cancle_url = $user_data['cancle_url'];
         $user_data = implode(' ', array_map(function ($key, $value) {
             return "$key:$value";
         }, array_keys($user_data), $user_data));

         $payment = PaytmWallet::with('receive');
         $payment->prepare([
             'order' => rand(),
             'user' => rand(10, 1000),
             'mobile_number' => '123456789',
             'email' => 'paytmtest@gmail.com',
             'amount' => $request->amount,

             'callback_url' => route('superadmin.paytm.callback', ['success_url' => $success_url, 'cancle_url' => $cancle_url, 'user_data' => $user_data]),
         ]);

         relogin_user($this->publicly_user_id);
         return $payment->receive();
     }



     public function Subcription_PaytmCallback(Request $request, $success_url, $cancle_url, $user_data)
     {


         $transaction = PaytmWallet::with('receive');

         $response = $transaction->response();

         $r = implode(' ', array_map(function ($key, $value) {
             return "$key:$value";
         }, array_keys($response), $response));

         $r = str_replace('/', '', $r);


         $transaction->getResponseMessage();
         $transaction->getOrderId();
         $transaction->getTransactionId();

         if ($transaction->isSuccessful()) {
             relogin_user($this->publicly_user_id);
             return redirect()->route($success_url, ['user_data' => $user_data, 'response' => $r]);
         } else if ($transaction->isFailed()) {
             relogin_user($this->publicly_user_id);
             return redirect()->route($cancle_url, ['user_data' => $user_data, 'response' => $r]);
         } else if ($transaction->isOpen()) {

             relogin_user($this->publicly_user_id);
             return redirect()->route($cancle_url, ['user_data' => $user_data, 'response' => $r]);
         }
     }

     public function PayWithFlutterwave_ForSubscription($user_id="", $package_id="", Request $request)
     {
        relogin_user($user_id);
        $flutterwave_response_from_api = $request->all();

        $r = implode(' ', array_map(function ($key, $value) {
            return "$key:$value";
        }, array_keys($flutterwave_response_from_api), $flutterwave_response_from_api));

        $r = str_replace('/', '', $r);

        $user_data['payment_method'] = 'flutterwave';
        $user_data['expense_id'] = $package_id;
        $package = Package::find($package_id);
        $user_data['amount'] = $package['price'];
        $user_data['user_id'] = $user_id;
        $user_data = implode(' ', array_map(function ($key, $value) {
             return "$key:$value";
         }, array_keys($user_data), $user_data));


        if($flutterwave_response_from_api['status'] == 'successful') {
            return redirect()->route('admin_subscription_fee_success_payment', ['user_data' => $user_data, 'response' => $r]);
        } else{
            return redirect()->route('admin.subscription')->with('message', 'Payment Failed');
        }
     }



     public function admin_subscription_fee_success_payment(Request $request, $user_data, $response)
     {

         $user_data = $this->string_to_array($user_data);


         $last_package=Subscription::where('school_id',auth()->user()->school_id)->orderBy('id', 'desc')->first();

         $package = Package::find($user_data['expense_id']);
            if(strtolower($package->interval)=='days')
                {
                    $expire_date = strtotime('+'.$package->days.' days', strtotime(date("Y-m-d H:i:s")) );

                }
            elseif(strtolower($package->interval)=='monthly')
                {
                    $monthly=$package->days*30;
                    $expire_date = strtotime('+'.$monthly.' days', strtotime(date("Y-m-d H:i:s")) );

                }
            elseif(strtolower($package->interval)=='yearly')
                {
                    $yearly=$package->days*365;
                    $expire_date = strtotime('+'.$yearly.' days', strtotime(date("Y-m-d H:i:s")) );

                }

         if ($user_data['payment_method'] == 'paypal') {

             $paypal_response_from_api = $request->all();
             $paypal_payment_response = json_encode($paypal_response_from_api);
             $status = Subscription::create([
                 'package_id' => $user_data['expense_id'],
                 'school_id' => auth()->user()->school_id,
                 'paid_amount' => $user_data['amount'],
                 'payment_method' => $user_data['payment_method'],
                 'transaction_keys' => $paypal_payment_response,
                 'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                 'expire_date' => $expire_date,
                 'status' => '1',
                 'active' => '1',
             ]);

             if(!empty($last_package))
             {
                 $last_package= $last_package->toArray();


                 Subscription::where('id',  $last_package['id'])->update([
                     'active' => 0,
                 ]);


             }



                 return redirect()->route('admin.subscription')->with('message', 'Payment Successfull');

         }



         if ($user_data['payment_method'] == 'stripe') {
             $stripe =  get_settings('stripe');
             $stripe_keys = json_decode($stripe);
             $STRIPE_KEY;
             $STRIPE_SECRET;


             if ($stripe_keys->mode == "test") {
                 $STRIPE_KEY = $stripe_keys->test_key;
                 $STRIPE_SECRET = $stripe_keys->test_secret_key;
             } elseif ($stripe_keys->mode == "live") {
                 $STRIPE_KEY = $stripe_keys->public_live_key;
                 $STRIPE_SECRET = $stripe_keys->secret_live_key;
             }

             $stripe_api_response_session_id = $request->all();
             $stripe = new \Stripe\StripeClient($STRIPE_SECRET);
             $session_response = $stripe->checkout->sessions->retrieve($stripe_api_response_session_id['session_id'], []);
             $stripe_payment_intent = $session_response['payment_intent'];
             $stripe_session_id = $stripe_api_response_session_id['session_id'];

             $stripe_transaction_keys = array();
             $stripe_response['payment_intent']  = $stripe_payment_intent;
             $stripe_response['session_id'] = $stripe_session_id;
             $stripe_transaction_keys = $stripe_response;
             $stripe_payment_response = json_encode($stripe_transaction_keys);


             $status = Subscription::create([
                 'package_id' => $user_data['expense_id'],
                 'school_id' => auth()->user()->school_id,
                 'paid_amount' => $user_data['amount'],
                 'payment_method' => $user_data['payment_method'],
                 'transaction_keys' => $stripe_payment_response,
                 'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                 'expire_date' => $expire_date,
                 'status' => '1',
                 'active' => '1',
             ]);

             if(!empty($last_package))
             {
                 $last_package= $last_package->toArray();

                 Subscription::where('id',  $last_package['id'])->update([
                     'active' => 0,
                 ]);


             }

             return redirect()->route('admin.subscription')->with('message', 'Payment Successfull');
         }

         if ($user_data['payment_method'] == 'razorpay') {

             $razorpay_api_response = $this->string_to_array($response);
             $razorpay_payment_response = json_encode($razorpay_api_response);

             $status = Subscription::create([
                 'package_id' => $user_data['expense_id'],
                 'school_id' => auth()->user()->school_id,
                 'paid_amount' => $user_data['amount'],
                 'payment_method' => $user_data['payment_method'],
                 'transaction_keys' => $razorpay_payment_response,
                 'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                 'expire_date' => $expire_date,
                 'status' => '1',
                 'active' => '1',
             ]);

             if(!empty($last_package))
             {
                 $last_package= $last_package->toArray();

                 Subscription::where('id',  $last_package['id'])->update([
                     'active' => 0,
                 ]);


             }

             return redirect()->route('admin.subscription')->with('message', 'Payment Successfull');
         }

         if ($user_data['payment_method'] == 'paytm') {
             $paytm_api_response = $this->string_to_array($response);
             $paytm_payment_response = json_encode($paytm_api_response);
             $status = Subscription::create([
                 'package_id' => $user_data['expense_id'],
                 'school_id' => auth()->user()->school_id,
                 'paid_amount' => $user_data['amount'],
                 'payment_method' => $user_data['payment_method'],
                 'transaction_keys' => $paytm_payment_response,
                 'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                 'expire_date' => $expire_date,
                 'status' => '1',
                 'active' => '1',
             ]);

             if(!empty($last_package))
             {
                 $last_package= $last_package->toArray();

                 Subscription::where('id',  $last_package['id'])->update([
                     'active' => 0,
                 ]);


             }


              relogin_user($user_data['user_id']);

              return redirect()->route('admin.subscription')->with('message', 'Payment Successfull');
         }


         if ($user_data['payment_method'] == 'flutterwave') {
             $flutterwave_api_response = $this->string_to_array($response);
             $flutterwave_payment_response = json_encode($flutterwave_api_response);
             
             $status = Subscription::create([
                 'package_id' => $user_data['expense_id'],
                 'school_id' => auth()->user()->school_id,
                 'paid_amount' => $user_data['amount'],
                 'payment_method' => $user_data['payment_method'],
                 'transaction_keys' => $flutterwave_payment_response,
                 'date_added' =>  strtotime(date("Y-m-d H:i:s")),
                 'expire_date' => $expire_date,
                 'status' => '1',
                 'active' => '1',
             ]);

             if(!empty($last_package))
             {
                 $last_package= $last_package->toArray();

                 Subscription::where('id',  $last_package['id'])->update([
                     'active' => 0,
                 ]);


             }


              relogin_user($user_data['user_id']);

              return redirect()->route('admin.subscription')->with('message', 'Payment Successfull');
         }




     }

     public function admin_subscription_fee_fail_payment(Request $request, $user_data, $response)
     {

         return redirect()->route('admin.subscription')->with('message', 'Payment Failed');

     }

    public function flutterwavePayment($user_id="", $expense_id="", Request $request)
    {
        $fee_details = StudentFeeManager::find($expense_id)->toArray();
        $Student_id_who_is_getting_paid = $fee_details['student_id'];


        $flutterwave_response_from_api = $request->all();

        if($flutterwave_response_from_api['status'] == 'successful') {

            relogin_user($this->publicly_user_id);
            $flutterwave_payment_response = json_encode($flutterwave_response_from_api);
            $status = Payments::create([
                'expense_type' => $fee_details['title'],
                'expense_id' => $fee_details['id'],
                'user_id' => $Student_id_who_is_getting_paid,
                'payment_method' => "flutterwave",
                'amount' => $fee_details['total_amount'],
                'status' => 'paid',
                'transaction_keys' => $flutterwave_payment_response,
                'created_at' => strtotime(date('d-M-Y')),
                'school_id' => auth()->user()->school_id,
            ]);



            if ($status != "") {
                StudentFeeManager::where('id',  $fee_details['id'])->update([
                    'status' => 'paid',
                    'updated_at' => date("Y-m-d H:i:s"),
                    'paid_amount' => $fee_details['total_amount'],
                    'payment_method' => "flutterwave",
                ]);

                if(auth()->user()->role_id =='6') {
                    return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Successful');
                }else {
                    return redirect()->route('student.fee_manager.list')->with('message', 'Payment Successful');
                }

            } else {

                if(auth()->user()->role_id =='6') {
                    return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
                }else {
                    return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
                }

            }

        } else {
            if(auth()->user()->role_id =='6') {
                return redirect()->route('parent.fee_manager.list')->with('message', 'Payment Failed');
            }else {
                return redirect()->route('student.fee_manager.list')->with('message', 'Payment Failed');
            }
        }

    }



}
