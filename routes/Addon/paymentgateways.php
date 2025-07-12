<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Addon\PaymentController;



Auth::routes();

    // Online Payment Gateways
Route::controller(PaymentController::class)->group(function () {

    // Paypal
    Route::post('PayWithPaypal','pay')->name('payment.paypal.pay');
    // Stripe
    Route::post('stripes', 'stripePost')->name('stripe.post');
    // Razorpay
    Route::post('razorpay-payment', 'store')->name('razorpay.payment.store');
    // Paytm
    Route::post('paytm-payment', 'paytmPayment')->name('paytm.payment');
    Route::post('paytm-callback/{success_url}/{cancle_url}/{user_data}', 'paytmCallback')->name('paytm.callback');
    // Stripe
    Route::get('flutterwave/{user_id}/{expense_id}', 'flutterwavePayment')->name('flutterwave.payment');

    // Student Fee payment By Student
    Route::get('student/payment/success/{user_data}/{response}', 'student_fee_success_payment_student')->name('student.student_fee_success_payment_student');
    Route::get('student/payment/fail/{user_data}/{response}', 'student_fee_fail_payment_student')->name('student.student_fee_fail_payment_student');

    // Student Fee Payment By Parent
    Route::get('parent/payment/success/{user_data}/{response}', 'student_fee_success_payment')->name('parent.student_fee_success_payment');
    Route::get('parent/payment/fail/{user_data}/{response}', 'student_fee_fail_payment')->name('parent.student_fee_fail_payment');

    //superadmin Payment Gateways for subscription
    // Paypal
    Route::post('PayWithPaypal/subscription', 'payWithPaypal_ForSubscription')->name('superadmin.paypal.subscription');
    // Stripe
    Route::post('PayWithStripe/subscription', 'PayWithStripe_ForSubscription')->name('superadmin.stripe.subscription');
    // Razorpay
    Route::post('PayWithRazorpay/subscription', 'PayWithRazorpay_ForSubscription')->name('superadmin.razorpay.subscription');
    // Paytm
    Route::post('PayWithPaytm/subscription', 'PayWithPaytm_ForSubscription')->name('superadmin.paytm.subscription');
    Route::post('paytm-callback/subscription/{success_url}/{cancle_url}/{user_data}', 'Subcription_PaytmCallback')->name('superadmin.paytm.callback');
    //Flutterwave
    Route::get('PayWithFlutterwave/subscription/{user_id}/{package_id}', 'PayWithFlutterwave_ForSubscription')->name('superadmin.flutterwave.subscription');

    // Subcription Payment by Admin
    Route::get('admin/subscription/payment/success/{user_data}/{response}',  'admin_subscription_fee_success_payment')->name('admin_subscription_fee_success_payment');
    Route::get('admin/subscription/payment/fail/{user_data}/{response}', 'admin_subscription_fee_fail_payment')->name('admin_subscription_fee_fail_payment');


});
