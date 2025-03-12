<html>
<head>
    <!-- Viewport Meta Tag for Mobile Responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="icon" href="https://www.tutorialswebsite.com/wp-content/uploads/2022/02/cropped-favicon.png" type="image/x-icon" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title>QR Payment | tutorialswebsite.com</title>

    <style>
        /* General Page Layout */
        .content {
            position: relative;
            margin: 10px auto;
            width: 100%;
            max-width: 640px; /* Set maximum width for large screens */
            z-index: 1;
            text-align: center;
        }

        .clearfix {
            border-bottom: 1px dotted #ccc;
            margin-bottom: 5px;
        }

        .demo-title {
            background-color: #ff7127;
            border-color: #ff7127;
            color: #fff;
            padding: 10px;
            text-align: center;
        }

        .demo-title h4 {
            font-size: 22px;
            font-weight: 700;
            text-shadow: 2px 2px #ff7127;
        }

        /* Responsive Adjustments */
        @media (max-width: 767px) {
            .content {
                width: 90%; /* Allow more width on mobile */
            }
        }

        @media (max-width: 480px) {
            .content {
                width: 100%; /* Make it full width on very small screens */
            }
        }
    </style>
</head>

<body style="background-repeat: no-repeat;">

<div class="col-md-12">
    <div class="content">
        <div class="col-lg-12">
            <div class="col-xs-12 col-md-12" style="text-align:center;">
                <?php
                    if (isset($_GET['pid'])) {
                        $payid = $_GET['pid'];
                        include "auth.php";

                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://api.razorpay.com/v1/payments/qr_codes/' . $payid,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => array(
                                'Authorization: ' . $authAPIkey
                            ),
                        ));

                        $response = curl_exec($curl);

                        curl_close($curl);
                        $getQr = json_decode($response, true);

                        if (isset($getQr['id'])) {
                            $qrID = $getQr['id'];
                            $qrImage = $getQr['image_url'];
                            $imageData = base64_encode(file_get_contents($qrImage));
                            $src = 'data: ;base64,' . $imageData;
                            echo "<img src='" . $src . "' width='100%' style='max-width:300px;'>";
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    function checkStatus() {
        var request_url = "checkPaymentStatus.php";
        var formData = {
            qr_id: '<?php echo $payid;?>',
            action: 'checkPaymentStatus'
        }

        $.ajax({
            type: 'POST',
            url: request_url,
            data: formData,
            dataType: 'json',
            encode: true,
        }).done(function(data) {
            console.log(data);
            if (data.res == 'success') {
                window.location = "payment-success.php?payid=" + data.payid;
            }

            if (data.res == 'error') {
                window.location = "payment-failed.php?status=failed";
            }
        });
    }

    setInterval(checkStatus, 1000);
</script>

</body>
</html>
