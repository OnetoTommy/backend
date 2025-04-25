<?php
// CORS 处理
header("Access-Control-Allow-Origin: https://bespoke-halva-fdb945.netlify.app"); // ← 根据你的Netlify地址改
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

// 预检请求快速返回
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 开启错误日志
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

// 调试日志
file_put_contents("log.txt", print_r($_POST, true), FILE_APPEND);

include 'connect.php';
ob_start();

$response = ['status' => 'error', 'message' => 'Unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'signUp') {
        $firstName = $_POST['fName'] ?? '';
        $lastName = $_POST['lName'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$firstName || !$lastName || !$email || !$password) {
            $response['message'] = "Missing required fields.";
            echo json_encode($response); exit();
        }

        $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $checkEmail->bindParam(':email', $email);
        $checkEmail->execute();

        if ($checkEmail->rowCount() > 0) {
            $response['message'] = "Email already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = $conn->prepare("INSERT INTO users (firstName, lastName, email, password) VALUES (:firstName, :lastName, :email, :password)");
            $insertQuery->bindParam(':firstName', $firstName);
            $insertQuery->bindParam(':lastName', $lastName);
            $insertQuery->bindParam(':email', $email);
            $insertQuery->bindParam(':password', $hashedPassword);

            if ($insertQuery->execute()) {
                $response = ['status' => 'success', 'message' => 'User registered successfully.'];
            } else {
                $response['message'] = "Failed to register user: " . $insertQuery->errorInfo()[2];
            }
        }
    }
}

ob_clean();
echo json_encode($response);
exit();
?>
