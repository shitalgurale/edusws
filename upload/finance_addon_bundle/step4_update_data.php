$paypal_details = DB::table('global_settings')->where('key', 'paypal')->get();

if(count($paypal_details) == 0){
	DB::table('global_settings')->insert([
		'key' => 'paypal',
		'value' => '{"status":"0","mode":"test","test_client_id":"snd_cl_id_xxxxxxxxxxxxx","test_secret_key":"snd_cl_sid_xxxxxxxxxxxx","live_client_id":"lv_cl_id_xxxxxxxxxxxxxxx","live_secret_key":"lv_cl_sid_xxxxxxxxxxxxxx"}',
	]);
}

$stripe_details = DB::table('global_settings')->where('key', 'stripe')->get();

if(count($stripe_details) == 0){
	DB::table('global_settings')->insert([
		'key' => 'stripe',
		'value' => '{"status":"0","mode":"test","test_key":"pk_test_xxxxxxxxxxxxx","test_secret_key":"sk_test_xxxxxxxxxxxxxx","public_live_key":"pk_live_xxxxxxxxxxxxxx","secret_live_key":"sk_live_xxxxxxxxxxxxxx"}',
	]);
}

$razorpay_details = DB::table('global_settings')->where('key', 'razorpay')->get();

if(count($razorpay_details) == 0){
	DB::table('global_settings')->insert([
		'key' => 'razorpay',
		'value' => '{"status":"0","mode":"test","test_key":"rzp_test_xxxxxxxxxxxxx","test_secret_key":"rzs_test_xxxxxxxxxxxxx","live_key":"rzp_live_xxxxxxxxxxxxx","live_secret_key":"rzs_live_xxxxxxxxxxxxx","theme_color":"#c7a600"}',
	]);
}

$paytm_details = DB::table('global_settings')->where('key', 'paytm')->get();

if(count($paytm_details) == 0){
	DB::table('global_settings')->insert([
		'key' => 'paytm',
		'value' => '{"status":"0","mode":"test","test_merchant_id":"tm_id_xxxxxxxxxxxx","test_merchant_key":"tm_key_xxxxxxxxxx","live_merchant_id":"lv_mid_xxxxxxxxxxx","live_merchant_key":"lv_key_xxxxxxxxxxx","environment":"provide-a-environment","merchant_website":"merchant-website","channel":"provide-channel-type","industry_type":"provide-industry-type"}',
	]);
}

$flutterwave_details = DB::table('global_settings')->where('key', 'flutterwave')->get();

if(count($flutterwave_details) == 0){
	DB::table('global_settings')->insert([
		'key' => 'flutterwave',
		'value' => '{"status":"0","mode":"test","test_key":"flwp_test_xxxxxxxxxxxxx","test_secret_key":"flws_test_xxxxxxxxxxxxx","test_encryption_key":"flwe_test_xxxxxxxxxxxxx","public_live_key":"flwp_live_xxxxxxxxxxxxxx","secret_live_key":"flws_live_xxxxxxxxxxxxxx","encryption_live_key":"flwe_live_xxxxxxxxxxxxxx"}',
	]);
}

$schools = DB::table('schools')->where('status', 1)->get();

foreach($schools as $school){
	$flutterwave_details = DB::table('payment_methods')->where('school_id', $school->id)->where('name', 'flutterwave')->get();

	if(count($flutterwave_details) == 0){
		DB::table('payment_methods')->insert([
			'name' => 'flutterwave',
			'payment_keys' => '{"test_key":"flwp_test_xxxxxxxxxxxxx","test_secret_key":"flws_test_xxxxxxxxxxxxx","test_encryption_key":"flwe_test_xxxxxxxxxxxxx","public_live_key":"flwp_live_xxxxxxxxxxxxxx","secret_live_key":"flws_live_xxxxxxxxxxxxxx","encryption_live_key":"flwe_live_xxxxxxxxxxxxxx"}',
			'image' => 'flutterwave.png',
			'status' => 1,
			'mode' => 'test',
			'school_id' => $school->id,
		]);
	}
}