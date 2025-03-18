<?php
require_once 'settings.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("UPDATE customers SET is_active = 1 WHERE token = ?");
    $stmt->bind_param("s", $token);
    
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "Your sign up is succesfull! You can log in now.";
    } else {
        echo "Invalid or expired token.";
    }
}
?>


