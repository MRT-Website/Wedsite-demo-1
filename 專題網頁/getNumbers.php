<?php
// 獲取圖號數據的函數
function getNumberOptions() {
    $numberApiUrl = "https://zlwdtn5iw3.execute-api.ap-southeast-2.amazonaws.com/default/MRTSearch1";

    // 設置 HTTP 標頭
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json\r\n",
            'method' => 'GET',
        ),
    );
    $context = stream_context_create($options);

    // 發送 GET 請求並獲取回應
    $response = file_get_contents($numberApiUrl, false, $context);

    if ($response === FALSE) {
        return json_encode(array()); // 返回空數組以防錯誤
    }

    // 解析 JSON 並排序數據
    $data = json_decode($response, true);
    sort($data); // 排序圖號選項

    return json_encode($data);
}

// 獲取圖號數據並生成下拉選單
$numberOptionsJson = getNumberOptions();
$numberOptions = json_decode($numberOptionsJson, true);

header('Content-Type: application/json');
echo json_encode($numberOptions);
?>