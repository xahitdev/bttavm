<?php

if (count(debug_backtrace()) == 0) {
    exit();
}

ob_start();
session_start();

require_once 'settings.php';

$accountStatus = false;
$accountVerification = false;
$accountData = "";

if(isset($_SESSION["email"]) ){

	$mail = htmlspecialchars($_SESSION["email"]);

	$query = "SELECT * FROM customers WHERE customers_mail = ? AND customers_subid = ?";
	$stmt = mysqli_prepare($connect, $query);

	if ($stmt) {
		mysqli_stmt_bind_param($stmt, "ss", $mail); 
		mysqli_stmt_execute($stmt);

		$result = mysqli_stmt_get_result($stmt);

		if (mysqli_num_rows($result) > 0) {
			$accountStatus = true;

			while ($user = mysqli_fetch_assoc($result)) {
				$accountData = $user;
				if ($user["is_active"] === "1") {
					$accountVerification = true;
				}
				break; 
			}
		} else {
			header("Location: logout.php");
			exit();
		}
	}
}

?>
