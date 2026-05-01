<?php

$consumerKey = "FCo6C08lkPCjgMyXZ5ANrgI27DfAcOjTfFPHBEkxjQKifNcf";
$consumerSecret = "q287LFziG4lLpGubZ5MAQiS326949yZSe5VmJxoP4J0TE3GAvxECNvpqf6tcwudW";

$phone = $_POST['phone'];
$amount = $_POST['amount'];

file_put_contents("status.txt", "PENDING");

// TOKEN
$credentials = base64_encode($consumerKey . ":" . $consumerSecret);

$ch = curl_init("https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$res = curl_exec($ch);

$token = json_decode($res)->access_token;

// PASSWORD
$timestamp = date("YmdHis");
$passkey = "bfb279f9aa9bdbcf...";
$shortcode = "174379";

$password = base64_encode($shortcode . $passkey . $timestamp);

// REQUEST
$data = [
  "BusinessShortCode"=>$shortcode,
  "Password"=>$password,
  "Timestamp"=>$timestamp,
  "TransactionType"=>"CustomerPayBillOnline",
  "Amount"=>$amount,
  "PartyA"=>$phone,
  "PartyB"=>$shortcode,
  "PhoneNumber"=>$phone,
  "CallBackURL"=>"https://YOUR-RAILWAY-URL/callback.php",
  "AccountReference"=>"Fuel",
  "TransactionDesc"=>"Fuel Payment"
];

$ch = curl_init("https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $token",
  "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

echo curl_exec($ch);
?>