<?php

$data = file_get_contents("php://input");

file_put_contents("callback_log.txt", $data.PHP_EOL, FILE_APPEND);

$json = json_decode($data, true);

if(isset($json["Body"]["stkCallback"]["ResultCode"])){

    if($json["Body"]["stkCallback"]["ResultCode"] == 0){
        file_put_contents("status.txt","PAID");
    } else {
        file_put_contents("status.txt","FAILED");
    }
}

echo "OK";
?>