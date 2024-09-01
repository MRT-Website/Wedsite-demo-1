<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 取得搜尋欄位的值
    $searchTerm1 = $_POST["searchTerm1"];
    $searchTerm2 = $_POST["searchTerm2"];

    // 資料要傳送的內容，以 JSON 格式傳遞
    $data_to_upload = '{"serial":"' . $searchTerm1 . '","number":"' . $searchTerm2 . '"}';

    // API 的 URL
    $api_url = "https://1c646r5g3i.execute-api.ap-southeast-2.amazonaws.com/default/MRTSearch";

    // 設置 HTTP 標頭
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => $data_to_upload,
        ),
    );
    $context = stream_context_create($options);

    // 發送 POST 請求並獲取回應
    $response = file_get_contents($api_url, false, $context);

    // 將回應直接回傳給前端
    echo $response;
}
?>