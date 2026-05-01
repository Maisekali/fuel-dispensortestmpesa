<?php

require "config.php";

$phone = $_POST['phone'] ?? '';
$amount = $_POST['amount'] ?? '';

if (!$phone || !$amount) {
    echo json_encode(["error" => "Missing input"]);
    exit;
}

// Format phone
if (substr($phone, 0, 1) == "0") {
    $phone = "254" . substr($phone, 1);
}
if (substr($phone, 0, 4) == "+254") {
    $phone = substr($phone, 1);
}

// Reset status
file_put_contents("status.txt", "PENDING");

// ================= TOKEN =================
$credentials = base64_encode(CONSUMER_KEY . ":" . CONSUMER_SECRET);

$ch = curl_init("https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$token = json_decode($response)->access_token;

// ================= PASSWORD =================
$timestamp = date("YmdHis");
$password = base64_encode(SHORTCODE . PASSKEY . $timestamp);

// ================= STK PUSH =================
$data = [
  "BusinessShortCode" => SHORTCODE,
  "Password" => $password,
  "Timestamp" => $timestamp,
  "TransactionType" => "CustomerPayBillOnline",
  "Amount" => (int)$amount,
  "PartyA" => $phone,
  "PartyB" => SHORTCODE,
  "PhoneNumber" => $phone,

  // ✅ YOUR LIVE CALLBACK
  "CallBackURL" => "https://fuel-dispensortestmpesa-production.up.railway.app/callback.php",

  "AccountReference" => "FuelStation",
  "TransactionDesc" => "Fuel Payment"
];

$ch = curl_init("https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer $token",
  "Content-Type: application/json"
]);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

// Debug log
file_put_contents("stk_response.txt", $result . PHP_EOL, FILE_APPEND);

echo $result;
?>
