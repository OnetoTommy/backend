<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

// ===== CORS 设置 =====
$allowed_origins = [
    "https://bespoke-halva-fdb945.netlify.app",
    "https://stirring-hummingbird-de4c7f.netlify.app",
    "https://super-hotteok-14c488.netlify.app",
    "http://localhost:3000"
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
}

header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// ===== 主逻辑 =====
include 'connect.php';

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
        } else {
            try {
                $check = $conn->prepare("SELECT * FROM users WHERE email = :email");
                $check->bindParam(':email', $email);
                $check->execute();

                if ($check->rowCount() > 0) {
                    $response['message'] = "Email already exists.";
                } else {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $insert = $conn->prepare("INSERT INTO users (firstName, lastName, email, password) VALUES (:f, :l, :e, :p)");
                    $insert->bindParam(':f', $firstName);
                    $insert->bindParam(':l', $lastName);
                    $insert->bindParam(':e', $email);
                    $insert->bindParam(':p', $hashed);

                    if ($insert->execute()) {
                        $response = ['status' => 'success', 'message' => 'User registered successfully'];
                    } else {
                        $response['message'] = "Failed to insert: " . $insert->errorInfo()[2];
                    }
                }
            } catch (PDOException $e) {
                $response['message'] = "PDO error: " . $e->getMessage();
            }
        }
    }
}

ob_end_clean();
echo json_encode($response);
exit();
?>
