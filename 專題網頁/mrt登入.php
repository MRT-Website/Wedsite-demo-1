<?php
session_start();

// 确保是登录动作
if (isset($_POST["action"]) && $_POST["action"] == "login") {
    // 构造要发送的数据
    $login_data = array(
        "account" => $_POST["uaccount"],
        "employeeID" => $_POST["uemployee"],
		"password"=>$_POST["upass"]
    );

    // 将数据转换为 JSON 格式
    $json_data = json_encode($login_data);

    // 设置 cURL 请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://12thuntxc4.execute-api.ap-southeast-2.amazonaws.com/default/MRTlogin");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // 执行请求并获取返回结果
    $response = curl_exec($ch);

    // 检查是否有错误发生
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch);
    }

    // 关闭 cURL 资源
    curl_close($ch);

    // 输出 API 返回的消息
    echo $response;

    // 解析 JSON 数据
    $json_response = json_decode($response, true);

    // 在登入腳本中，登入成功後添加以下代碼
    if (isset($json_response["authenticated"]) && $json_response["authenticated"] === true) {
        // 開始會話
        session_start();
        // 儲存用戶資訊到 session
        $_SESSION['account'] = $_POST["uaccount"];
        $_SESSION['employeeID'] = $_POST["uemployee"];
        // 登錄成功，跳轉到指定頁面
        header("Location:page transfer.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
   <meta charset="UTF-8">
   <title>MRT登入頁面</title>
   <style>
     body {
        text-align: center;
		background-image: url('mrt圖.png');
		background-size: cover; /* 根據容器大小自適應調整背景圖片大小 */
     }
     div.welcome {
        color: green;
     }
     div.error {
        color: red;
     }
     .container {
        display: flex;
        justify-content: space-evenly;
        margin-top: 20px; /* 調整上方間距 */
        margin-bottom: 20px; /* 新增下方間距 */
     }
     form {
        text-align: left;
        width: 300px;
     }
     form input[type="text"],
     form input[type="password"],
     form input[type="tel"] {
        width: 100%;
        margin-bottom: 10px;
     }
   </style>
</head>
<body>
<h1>MRT登入</h1> 
<div class="container">
    <div>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            帳號：<input type="text" name="uaccount"><br>
			員工編號：<input type="text" name="uemployee"><br>
            密碼：<input type="password" name="upass"><br>
            <input type="hidden" name="action" value="login"> 
			<!-- <a href="mrt註冊.php"><button type="button">前往註冊</button></a> -->
			 <span style="margin-left: 250px;"> <!-- 这里调整空格的距离 -->
            <input type="submit" value="登入">
			
        </form>
    </div>
</div>
</body>
</html>