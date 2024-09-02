<?php
// 檢查是否有傳遞 PDF 的 URL
if (!isset($_GET['fileUrl']) || empty($_GET['fileUrl'])) {
    die("無效的請求");
}

// 取得 URL
$s3Url = $_GET['fileUrl'];

// 檢查 URL 是否有效
if (filter_var($s3Url, FILTER_VALIDATE_URL) === FALSE) {
    die("無效的 URL");
}

// 從 URL 取得文件名稱
$filename = basename(parse_url($s3Url, PHP_URL_PATH));

// 設置 HTTP 標頭以顯示 PDF
header("Content-Type: application/pdf");
header("Content-Disposition: inline; filename=\"" . urlencode($filename) . "\"");

// 讀取並輸出 S3 文件內容
readfile($s3Url);
?>