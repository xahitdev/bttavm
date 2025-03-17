<?php
if (isset($_GET["cid"])) {
    include "settings.php";

    $cid = htmlspecialchars($_GET["cid"]);
    $verified = "1";

    $query = "UPDATE customers SET is_active = ? WHERE customer_id = ?";
    $stmt = mysqli_prepare($connect, $query);

    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "si", $verified, $cid); 
        mysqli_stmt_execute($stmt);

    }

    header("Location: verification-status.php");
    exit();
}
?>
