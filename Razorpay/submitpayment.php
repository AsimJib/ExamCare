<?php

if (isset($_POST['action']) && $_POST['action'] == 'payOrder') {

    include "auth.php";

    // Set transaction details
    $order_id = uniqid(); 

    $billing_name = $_POST['billing_name'];
    $billing_mobile = $_POST['billing_mobile'];
    $billing_email = $_POST['billing_email'];
    $payAmount = $_POST['payAmount'];

    $note = $billing_name . " account created";

    // Check if customer already exists using the mobile number or email
    $curl_check_customer = curl_init();

    curl_setopt_array($curl_check_customer, array(
        CURLOPT_URL => 'https://api.razorpay.com/v1/customers?contact=' . $billing_mobile,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: ' . $authAPIkey
        ),
    ));

    $response_check_customer = curl_exec($curl_check_customer);
    curl_close($curl_check_customer);
    $customer_check_res = json_decode($response_check_customer, true);

    if (isset($customer_check_res['items']) && count($customer_check_res['items']) > 0) {
        // Customer already exists, get the existing customer ID
        $customerId = $customer_check_res['items'][0]['id'];
    } else {
        // Customer does not exist, create a new customer
        $postdata = array(
            "name" => $billing_name,
            "email" => $billing_email,
            "contact" => $billing_mobile,
            "notes" => array(
                "notes_key_1" => $note,
                "notes_key_2" => ""
            )
        );

        $curl_create_customer = curl_init();

        curl_setopt_array($curl_create_customer, array(
            CURLOPT_URL => 'https://api.razorpay.com/v1/customers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postdata),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . $authAPIkey
            ),
        ));

        $response_create_customer = curl_exec($curl_create_customer);
        curl_close($curl_create_customer);
        $customerRes = json_decode($response_create_customer, true);

        if (isset($customerRes['error'])) {
            $errMessage = $customerRes['error']['description'];
            echo json_encode(['res' => 'error', 'message' => $errMessage]);
            exit;
        } else {
            $customerId = $customerRes['id'];
        }
    }

    /***** Create Razorpay QR code *****/
    $qrNote = "";
    $pdesc = "Pay to ExamCare";

    $qrpostData = array(
        "type" => "upi_qr",
        "name" => $billing_name,
        "usage" => "single_use",  // For reusability use "multiple_use"
        "fixed_amount" => true,
        "payment_amount" => $payAmount * 100,
        "description" => "Payment for " . $billing_name,
        "customer_id" => $customerId,
        "notes" => array(
            "purpose" => $qrNote
        )
    );

    $curl_qr = curl_init();

    curl_setopt_array($curl_qr, array(
        CURLOPT_URL => 'https://api.razorpay.com/v1/payments/qr_codes',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($qrpostData),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: ' . $authAPIkey
        ),
    ));

    $response_qr = curl_exec($curl_qr);
    curl_close($curl_qr);
    $qrRes = json_decode($response_qr, true);

    if (isset($qrRes['image_url'])) {
        $qrID = $qrRes['id'];
        $qrImage = $qrRes['image_url'];
        echo json_encode(['res' => 'success', 'customer_id' => $customerId, 'qr_id' => $qrID, 'qrImage' => $qrImage]);
        exit;
    } else {
        $error_message = isset($qrRes['error']) ? $qrRes['error']['description'] : 'QR code not generated';
        echo json_encode(['res' => 'error', 'message' => $error_message]);
        exit;
    }

}
?>
