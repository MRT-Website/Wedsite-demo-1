<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 讀取前端傳遞的 JSON 資料
    $data_to_upload = file_get_contents('php://input');

    if (empty($data_to_upload)) {
        // 沒有接收到資料
        http_response_code(400);
        echo json_encode(array("error" => "No data received"));
        exit();
    }

    // API 的 URL
    $api_url = "https://m7y072zeib.execute-api.ap-southeast-2.amazonaws.com/default/12";

    // 設置 HTTP 標頭
    $options = array(
        'http' => array(
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data_to_upload,
        ),
    );
    $context = stream_context_create($options);

    // 發送 POST 請求並獲取回應
    $response = @file_get_contents($api_url, false, $context);

    if ($response === FALSE) {
        // 處理錯誤
        http_response_code(500);
        echo json_encode(array("error" => "Failed to fetch data from API."));
    } else {
        // 將回應直接回傳給前端
        echo $response;
    }
} else {
    // 如果不是 POST 請求
    http_response_code(405);
    echo json_encode(array("error" => "Method Not Allowed"));
}
?>