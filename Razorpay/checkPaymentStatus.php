
<?php

if(isset($_POST['action']) && $_POST['action']='checkPaymentStatus'){

include "auth.php";


$qr_id=$_POST['qr_id'];

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.razorpay.com/v1/payments/qr_codes/'.$qr_id.'/payments',CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: '.$authAPIkey
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$paymentRes= json_decode($response,true);

if(isset($paymentRes['count']) && $paymentRes['count'] !='0'){
    $items=$paymentRes['items'];
    if(is_array($items) && !empty($items)){
        $payid=$items[0]['id'];
         $payStatus=$items[0]['status'];
         if($payStatus=='captured'){
           echo json_encode(['res'=>'success','payid'=>$payid]); exit;
         }else{
              echo json_encode(['res'=>'error','message'=>"Payment not captured"]); exit;
         }
    }else{
         echo json_encode(['res'=>'error','message'=>"Payment not captured"]); exit;
    }
}

}else{
    echo json_encode(['res'=>'error']); exit;
}
       ?>   
       