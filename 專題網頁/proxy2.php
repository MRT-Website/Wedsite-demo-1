<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_FILES['files'])) {
        http_response_code(400);
        echo json_encode(array("error" => "No files uploaded"));
        exit();
    }

    $responses = [];
    foreach ($_FILES['files']['tmp_name'] as $index => $tmpName) {
        $fileData = base64_encode(file_get_contents($tmpName));
        $fileName = $_FILES['files']['name'][$index];
        $api_url = "https://7ujoxhv7u6.execute-api.ap-southeast-2.amazonaws.com/default/MRTPicture";

        // 創建 JSON 請求數據
        $postData = json_encode([
            'fileName' => $fileName,
            'fileContent' => $fileData
        ]);

        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => $postData,
            ),
        );
        $context = stream_context_create($options);

        $response = @file_get_contents($api_url, false, $context);
        $responses[] = array(
            'name' => $fileName,
            'status' => $response === FALSE ? "Failed to upload file " . $fileName : "Uploaded successfully"
        );
    }

    echo json_encode($responses);
} else {
    http_response_code(405);
    echo json_encode(array("error" => "Method Not Allowed"));
}
?>