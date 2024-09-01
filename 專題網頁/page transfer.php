<?php
session_start();
// 假設用戶已經在mrt登入 2.php中成功登入，並且帳號和員工編號已經存入session中
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>頁面跳轉</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>頁面選擇</h1>
        <a href="0421_test1.php" class="button">搜尋資料藍圖</a>
        <a href="upload.php" class="button">上傳Excel檔案</a>
        <a href="s3.php" class="button">上傳PDF圖檔</a>
        <a href="change password.php" class="button">更改密碼</a>
    </div>
</body>
</html>