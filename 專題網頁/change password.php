<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['account']) || !isset($_SESSION['employeeID'])) {
    header("Location: mrt登入 2.php");
    exit();
}

// Handle password change request
if (isset($_POST["action"]) && $_POST["action"] == "changePassword") {
    $new_password = $_POST["newPassword"];
    $confirm_password = $_POST["confirmPassword"];

    if ($new_password !== $confirm_password) {
        $error_message = "兩次輸入的密碼不一致！";
    } elseif (strlen($new_password) < 5) {
        $error_message = "密碼長度必須至少5個字符！";
    } else {
        // Construct data to be sent
        $change_password_data = array(
            "account" => $_SESSION['account'],
            "employeeID" => $_SESSION['employeeID'],
            "newPassword" => $new_password
        );

        // Convert data to JSON format
        $json_data = json_encode($change_password_data);

        // Set up cURL request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://12thuntxc4.execute-api.ap-southeast-2.amazonaws.com/default/MRT_Password_Change");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute request and get response
        $response = curl_exec($ch);

        // Check for errors
        if ($response === false) {
            $error_message = 'Curl error: ' . curl_error($ch);
        } else {
            // Decode JSON response
            $json_response = json_decode($response, true);
            
            if (isset($json_response["success"]) && $json_response["success"] === true) {
                $success_message = "密碼修改成功！";
            } else {
                $error_message = "密碼修改失敗，" . ($json_response["error"] ?? "未知錯誤。");
            }
        }

        // Close cURL resource
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>修改密碼</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input {
            display: block;
            margin: 10px 0;
            padding: 5px;
            width: 200px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>修改密碼</h2>
        <p>帳號: <?php echo htmlspecialchars($_SESSION['account']); ?></p>
        <p>員工編號: <?php echo htmlspecialchars($_SESSION['employeeID']); ?></p>
        <?php
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        if (isset($success_message)) {
            echo "<p class='success'>$success_message</p>";
        }
        ?>
        <form method="post">
            <input type="password" name="newPassword" placeholder="新密碼" required>
            <input type="password" name="confirmPassword" placeholder="確認新密碼" required>
            <input type="hidden" name="action" value="changePassword">
            <button type="submit">修改密碼</button>
        </form>
    </div> 
</body>
</html>