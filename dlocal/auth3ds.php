<?php
//print_r($_POST);
function sendDataToUrl($hostName,$destinyURL,$rest_body_array,$SECRET_KEY,$xDate) // function to send the data through cURL.
{
    //// header data ////
    //$payload = json_encode($rest_body_array); // encode request body into a json format
    $headerdata = array(
    'Content-Type: application/json',
    'X-Login: hcuHOSMn2O',
    'X-Trans-Key: AfdhG8eRKX',
    'X-Date: '.$xDate,
    'Authorization: V2-HMAC-SHA256, Signature: '.$SECRET_KEY,
    'X-Version: 2.1');
    $ch=curl_init($destinyURL); // set url
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // set post request
    curl_setopt($ch, CURLOPT_POSTFIELDS, $rest_body_array); // send the json format into the rest body
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$headerdata); // send the header data
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
    $result = curl_exec($ch); // excute the POST and obtain the result for the request (HEADER+BODY)
    //- separate the header from the body to excute access to the array.
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($result, 0, $header_size);
    $body = substr($result, $header_size);
    $headers = explode("\n", $header);
    //print_r($header); // print the header data
    curl_close($ch); // closes the curl
    return $body; // return the body request
}


$hostName = "sandbox.dlocal.com";
$destinyURL = "https://sandbox.dlocal.com/secure_payments";
$rest_body_array = '{"amount": 120.00,"currency" : "USD","country": "'.$_POST['country'].'","payment_method_id" : "CARD","payment_method_flow" : "DIRECT","payer":{"name" : "'.$_POST['card-holder'].'","email" : "mike@test.com","document" : "123456789","user_reference": "12345","address": {"state"  : "Rio de Janeiro","city" : "Volta Redonda","zip_code" : "27275-595","street" : "Servidao B-1","number" : "1106"}},"card":{"token": "'.$_POST["dlocalToken"].'"},"order_id": "657434343","description": "Testing Sandbox","notification_url": "http://merchant.com/notifications"}';
$secretKey ="ZXmvAQKQXhWls42RznmwyjdHFbzjczNKh";
$currentDate = date("Y-m-d\TH:i:s.Z\Z",time());
$xLOGIN = "hcuHOSMn2O";
$signature = hash_hmac("sha256", "$xLOGIN$currentDate$rest_body_array", $secretKey);

$respuesta_api = sendDataToUrl($hostName,$destinyURL,$rest_body_array,$signature,$currentDate);
echo $respuesta_api;
$json_string = json_decode($respuesta_api);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {
  font-family: Arial;
  font-size: 17px;
  padding: 8px;
  background-color: #EEEEEE;
}

* {
  box-sizing: border-box;
}

.row {
  display: -ms-flexbox; /* IE10 */
  display: flex;
  -ms-flex-wrap: wrap; /* IE10 */
  flex-wrap: wrap;
  margin: 0 -16px;
}

.col-25 {
  -ms-flex: 25%; /* IE10 */
  flex: 25%;
}

.col-50 {
  -ms-flex: 50%; /* IE10 */
  flex: 50%;
}

.col-75 {
  -ms-flex: 75%; /* IE10 */
  flex: 75%;
  margin-left:24%;
  margin-right:24%;
}

.col-25,
.col-50,
.col-75 {
  padding: 0 16px;
}

.container {
  background-color: #f2f2f2;
  padding: 5px 20px 15px 20px;
  border: 1px solid lightgrey;
  border-radius: 3px;
}

input[type=text] {
  width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}

label {
  margin-bottom: 10px;
  display: block;
}

.icon-container {
  margin-bottom: 20px;
  padding: 7px 0;
  font-size: 24px;
}

.btn {
  background-color: #04AA6D;
  color: white;
  padding: 12px;
  margin: 10px 0;
  border: none;
  width: 100%;
  border-radius: 3px;
  cursor: pointer;
  font-size: 17px;
}

.btn:hover {
  background-color: #45a049;
}

a {
  color: #2196F3;
}

hr {
  border: 1px solid lightgrey;
}

span.price {
  float: right;
  color: grey;
}
.DlocalField {
    width: 100%;
  margin-bottom: 20px;
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 3px;
}
/* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other (also change the direction - make the "cart" column go on top) */
@media (max-width: 800px) {
  .row {
    flex-direction: column-reverse;
  }
  .col-25 {
    margin-bottom: 20px;
  }
}
</style>
</head>
<body>
<div class="row">
  <div class="col-75">
    <div class="container">
      <form action="charge.php" method="post" id="payment-form">
        <div class="row">
          <h1 style="margin-left:10px;">Transaction Result</h1>
          <div class="imagen">
            <?php
            if($json_string->status!=null)
            {
                switch($json_string->status)
                {
                    case "PAID":
                        echo '<img src="img/check.png" height="100px" width="100px"><br>';
                        echo "<h3>Status: ".$json_string->status."</h3><br>";
                        echo $json_string->status_detail."<br>";
                        echo "<p>Se ha realizado una transaccion con la tarjeta".$json_string->card."</p>";
                        break;
                    case "REJECTED":
                        echo '<img src="img/declined.png" height="100px" width="100px"><br>';
                        echo "<h3>Status: ".$json_string->status."</h3>";
                        break;
                    case "PENDING":
                        echo '<img src="img/pending.jpg" height="100px" width="100px"><br>';
                        echo "<h3>Status: ".$json_string->status."</h3><br>";
                        echo $json_string->status_detail."<br>";
                        echo "<p>La transaccion está pendiente: ".$json_string->card."</p>";
                    default:
                        echo '<img src="img/declined.png" height="100px" width="100px"><br>';
                        echo "<h3>Status no comprendido: ".$json_string->status."</h3>";
                        break;     
                }
            }
            ?>
          </div>
        </div>
        <input type="submit" value="Continue to checkout" class="btn">
      </form>
    </div>
  </div>
</div>
</body>
</html>
