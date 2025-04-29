<?php
include '../../settings.php';

// Get and escape input
$newsletterMail = mysqli_real_escape_string($conn, $_POST['newsletter_email']);

// Check if email already exists
$sql = "SELECT * FROM newsletter WHERE newsletter_email = '$newsletterMail'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Email already exists
} else {
    // Insert new email (is_active = 1)
    $sql = "INSERT INTO newsletter (newsletter_email, is_active) VALUES ('$newsletterMail', '1')";
    $conn->query($sql);
}

// Redirect
header('Location: ../../index.php');
?>

