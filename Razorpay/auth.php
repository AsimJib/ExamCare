<?php


$razorpay_mode='live';

$razorpay_test_key='rzp_test_Biw7k35dT7rhHS';
$razorpay_test_secret_key='wi12BaB80vknDWnUBjQr4FX0';
$razorpay_live_key='rzp_live_8YOmfybg2mEz3i';
$razorpay_live_secret_key='vw3M0pw4bpK9ZwX8dSktK2Ko';

if($razorpay_mode=='test'){
    
    $razorpay_key=$razorpay_test_key;
    
$authAPIkey="Basic ".base64_encode($razorpay_test_key.":".$razorpay_test_secret_key);

}else{
    
	$authAPIkey="Basic ".base64_encode($razorpay_live_key.":".$razorpay_live_secret_key);
	
	$razorpay_key=$razorpay_live_key;

}

