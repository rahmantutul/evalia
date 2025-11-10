<?php
$curl = curl_init();
$data = json_encode(array(
    "text" => "",
    "voiceId" => "",
    "webhookUrl" => "",
    "webhookAuth" => array(
        "authKey" => "",
        "authSecret" => ""
     ),
));
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.tryhamsa.com/v1/jobs/text-to-speech",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
        "Authorization: Token ********************************d9b4",
        "Content-Type: application/json"
    ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}
?>