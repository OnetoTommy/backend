<?php
$host = "localhost";
$dbname = "shopping_ai";
$dbusername = "root";
$dbpassword = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Successful";

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
