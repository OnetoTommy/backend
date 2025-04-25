<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: http://localhost:3003"); // ✅ Important for Frontend site
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

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
            echo json_encode($response);
            exit();
        }

        // Check if email exists
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
                $response['message'] = "Failed to register user.";
            }
        }
    } elseif ($action === 'signIn') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $sql = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $sql->bindParam(':email', $email);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $user = $sql->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['email'] = $user['email'];
                $response = ['status' => 'success', 'message' => 'Login successful.'];
            } else {
                $response['message'] = "Incorrect password.";
            }
        } else {
            $response['message'] = "Email not found.";
        }
    }
}

ob_clean();
echo json_encode($response);
exit();
?>
