<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Razorpay QR Payment</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body style="background-repeat: no-repeat;">
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">Student Details Form</h4>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>Exam Name</label>
                        <input type="text" class="form-control" name="billing_name" id="billing_name_unique" placeholder="Enter exam name" required="" autofocus="">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="billing_email" id="billing_email_unique" placeholder="Enter email" required="">
                    </div>
                    <div class="form-group">
                        <label>Mobile Number</label>
                        <input type="number" class="form-control" name="billing_mobile" id="billing_mobile_unique" min-length="10" max-length="10" placeholder="Enter Mobile Number" required="" autofocus="">
                    </div>
                    <div class="form-group">
                        <label>Payment Amount</label>
                        <input type="text" class="form-control" name="payAmount" id="payAmount_unique" placeholder="Enter Amount" required="" autofocus="">
                    </div>
                    <!-- submit button -->
                    <button id="PayNow" class="btn btn-success btn-lg btn-block">Submit & Pay</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //Pay Amount
    jQuery(document).ready(function($){
        jQuery('#PayNow').click(function(e){
            $(this).text('Please Wait..');

            let billing_name = $('#billing_name_unique').val();
            let billing_mobile = $('#billing_mobile_unique').val();
            let billing_email = $('#billing_email_unique').val();
            var payAmount = $('#payAmount_unique').val();

            var request_url="submitpayment.php";
            var formData = {
                billing_name:billing_name,
                billing_mobile:billing_mobile,
                billing_email:billing_email,
                payAmount:payAmount,
                action:'payOrder'
            }

            $.ajax({
                type: 'POST',
                url:request_url,
                data:formData,
                dataType: 'json',
                encode:true,
            }).done(function(data){
                if(data.res=='success'){
                    window.location="payNow.php?pid="+data.qr_id;
                    e.preventDefault();
                }
                if(data.res=='error'){
                    $('#result').text(data.message);
                    $(this).text('Submit & Pay');
  
              }
            });
        });
    });
</script>
</body>
</html>
