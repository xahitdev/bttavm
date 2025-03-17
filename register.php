<?php
require_once 'settings.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/PHPMailer/PHPMailer/src/Exception.php';
require 'vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require 'vendor/PHPMailer/PHPMailer/src/SMTP.php';

ob_start();
session_start();

$phpm = new PHPMailer();

if(isset($_POST['register'])){
	$name = trim($_POST['name'] . " " . trim($_POST['lastname']));
	$password = trim($_POST['password']);
	$birth = $_POST['dob'];
	$gender = $_POST['gender'];
	$mail = $_POST['mail'];
	$phone = $_POST['phone_number'];

	//cehcking if email address already exists.
	$sql = "SELECT customer_id FROM customers where customer_mail = ?";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, "s", $mail);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_store_result($stmt);

	if(mysqli_stmt_num_rows($stmt) > 0){
		$_SESSION['error'] = "This mail address is already exists. "; echo '<a href="login.php">Already have an account?</a>';
	}

	$sql = "INSERT INTO customers (customer_name, password_hashed, customer_birth, customer_gender, customer_mail, customer_phone) VALUES (?,?,?,?,?,?)";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, "ssssss", $name, md5($password), $birth, $gender, $mail, $phone);
	if (mysqli_stmt_execute($stmt)) {

		$targetid = "";

		$query = "SELECT * FROM customers WHERE customer_mail = ?";
		$stmt = mysqli_prepare($connect, $query);

		// Parametreyi bağla
		mysqli_stmt_bind_param($stmt, "s", $mail);

		// Sorguyu çalıştır
		mysqli_stmt_execute($stmt);

		// Sonuçları al
		$result = mysqli_stmt_get_result($stmt);

		if(mysqli_num_rows($result) > 0){
			while($user = mysqli_fetch_assoc($result)){
				$targetid = $user["customer_id"];
			}
		}

		// fix this section after pushing it into web server #fix
		$verification_url = "localhost/bttavm/verification-action.php?cid=".$targetid;

		$mailcontent = '
			<div dir="ltr" style="background-color:#f7f7f7;margin:0;padding:70px 0;width:100%" bgcolor="#f7f7f7" width="100%">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
			<tbody>
			<tr>
			<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="800" style="background-color:#fff;border:1px solid #dedede;border-radius:25px" bgcolor="#fff">
			<tbody>
			<tr>
			<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:black;color:#fff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:25px 25px 0 0" bgcolor="#a32bbc">
			<tbody>
			<tr>
			<td style="padding:20px 40px;display:block">
			<h1 style="font-weight: bolder; text-align:center" bgcolor="inherit">
			<b>Welcome to ESCLOT LONDON</b></h1>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td align="center" valign="top">
			<table border="0" cellpadding="0" cellspacing="0" width="600">
			<tbody>
			<tr>
			<td valign="top" style="background-color:#fff" bgcolor="#fff">

			<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tbody>
			<tr>
			<td valign="top" style="padding:40px 32px">
			<div style="color:#636363;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left" align="left">
			<h2 style="margin:0 0 16px; color:black;">Hi '.$name_surname.',</h2>
			<p style="margin:0 0 16px; color:black;">Thanks for creating an account on esclotlondon.com,<br> your verification link is
					<a href="'.$verification_url.'"><button style="margin:0px; padding: 0px; background-color: gray; border: 0px solid #FFF; border-radius: 8px; padding-top: 4px; padding-bottom: 4px; padding-left: 12px; padding-right: 12px; border: 1px solid black; cursor: pointer;"><b>Verify</b></button></a></p>
			</div>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>

			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:black;color:#fff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:0px 0px 25px 25px">
			<tbody>
			<tr>
			<td style="padding:20px 40px;display:block">

			</td>
			</tr>
			</tbody>
			</table>

			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			<tr>
			<td align="center" valign="top">
			<table border="0" cellpadding="10" cellspacing="0" width="600">
			<tbody>
			<tr>
			<td valign="top" style="padding:0;border-radius:6px">
			<table border="0" cellpadding="10" cellspacing="0" width="100%">
			<tbody>
			<tr>
			<td colspan="2" valign="middle" style="border-radius:6px;border:0;color:#8a8a8a;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:12px;line-height:150%;text-align:center;padding:24px 0" align="center">
			<p style="margin:0 0 16px">ESCLOT LONDON</p>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</td>
			</tr>
			</tbody>
			</table>
			</div>';

		try{
			$phpm -> isSMTP();
			$phpm -> CharSet = 'UTF-8';
			$phpm -> Host = 'smtp.gmail.com';
			$phpm -> SMTPAuth = true;
			$phpm -> Username = 'aslankoylu1071@gmail.com';
			$phpm -> Password = 'stkyijzdfjvwtbpd';
			$phpm -> Port = 587;
			$phpm -> setFrom("aslankoylu1071@gmail.com", "BTTAVM");
			$phpm -> addAddress($mail, $mail);
			$phpm -> isHTML(true);
			$phpm -> Subject = "BTTAVM | Mail Activation";
			$phpm -> Body = $mailcontent;
			$mbol = $phpm -> send();
		}catch(Exception $e){
			// Exception
		}

		$_SESSION["mail"] = $mail;

		echo "1";
	}else{
		echo "0";
		$_SESSION['error'] = "Something went wrong. Please try again.";
		header("Location: register.php");

	}
}
	

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>E Store - eCommerce HTML Template</title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="eCommerce HTML Template Free Download" name="keywords">
	<meta content="eCommerce HTML Template Free Download" name="description">

	<!-- Favicon -->
	<link href="img/favicon.ico" rel="icon">

	<!-- Google Fonts -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400|Source+Code+Pro:700,900&display=swap"
		rel="stylesheet">

	<!-- CSS Libraries -->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
	<link href="lib/slick/slick.css" rel="stylesheet">
	<link href="lib/slick/slick-theme.css" rel="stylesheet">

	<!-- Template Stylesheet -->
	<link href="css/style.css" rel="stylesheet">
</head>

<body>
	<!-- Top bar Start -->
	<div class="top-bar">
		<div class="container-fluid">
			<div class="row">
				<div class="col-sm-6">
					<i class="fa fa-envelope"></i>
					support@email.com
				</div>
				<div class="col-sm-6">
					<i class="fa fa-phone-alt"></i>
					+012-345-6789
				</div>
			</div>
		</div>
	</div>
	<!-- Top bar End -->

	<!-- Nav Bar Start -->
<?php
include 'navbar.php';
?>
	<!-- Nav Bar End -->


	<!-- Breadcrumb Start -->
	<div class="breadcrumb-wrap">
		<div class="container-fluid">
			<ul class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">Home</a></li>
				<li class="breadcrumb-item"><a href="#">Products</a></li>
				<li class="breadcrumb-item active">Login & Register</li>
			</ul>
		</div>
	</div>
	<!-- Breadcrumb End -->

	<!-- Login Start -->
	<div class="login">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-4">
				<div class="register-form rounded shadow">
					<h3 class="text-center mb-4">Let's sign you up.</h3>
					<form id="registerForm" action="register.php" method="POST">
						<!-- Step 1: First Name -->
						<div class="step active">
							<label class="form-label">First Name:</label>
							<input type="text" name="name" class="form-control" id="name" required>
							<button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(2)">Next</button>
						</div>

						<!-- Step 2: Last Name -->
						<div class="step d-none">
							<label class="form-label">Last Name:</label>
							<input type="text" name="lastname" class="form-control" id="lastname" required>
							<button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(1)">Back</button>
							<button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(3)">Next</button>
						</div>

						<!-- Step 3: Mail Address -->
						<div class="step d-none">
							<label class="form-label">Mail Address:</label>
							<input type="email" name="mail" class="form-control" id="mail" required>
							<button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(2)">Back</button>
							<button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(4)">Next</button>
						</div>

						<!-- Step 4: Password -->
						<div class="step d-none">
							<label class="form-label">Choose a Password:</label>
							<input type="password" name="password" class="form-control" id="password" required>
							<button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(3)">Back</button>
							<button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(5)">Next</button>
						</div>

						<!-- Step 5: Phone Number -->
						<div class="step d-none">
							<label class="form-label">Phone Number:</label>
							<input type="text" name="phone_number" class="form-control" id="phone_number" required>
							<button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(4)">Back</button>
							<button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(6)">Next</button>
						</div>

						<!-- Step 6: Date of Birth -->
						<div class="step d-none">
							<label class="form-label">Select Date of Birth:</label>
							<input type="date" name="dob" class="form-control" id="dob" required>
							<button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(5)">Back</button>
							<button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(7)">Next</button>
						</div>

						<!-- Step 7: Gender Selection -->
						<div class="step d-none">
							<label class="form-label">Select Gender:</label>
							<select name="gender" class="form-select" id="gender" required>
								<option value="">Select Gender</option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
								<option value="Do not want to answer">Do not want to answer</option>
							</select>
							<button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(6)">Back</button>
							<input class="btn btn-success mt-3 w-100" type="submit" name="register" value="Register">
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
let currentStep = 0;
const steps = document.querySelectorAll('.step');

function nextStep(step) {
	// Validate current step before allowing the next step
	const inputs = steps[currentStep].querySelectorAll("input, select");
	let isValid = true;

	inputs.forEach(input => {
	if (!input.value) {
		isValid = false;
		input.classList.add("is-invalid");
	} else {
		input.classList.remove("is-invalid");
	}
	});

	if (isValid) {
		steps[currentStep].classList.add('d-none');
		steps[step - 1].classList.remove('d-none');
		currentStep = step - 1;
	}
}

function prevStep(step) {
	steps[currentStep].classList.add('d-none');
	steps[step - 1].classList.remove('d-none');
	currentStep = step - 1;
}
</script>

	<!--
	<div class="login">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<div class="register-form">
						<form action="" method="POST" name="user_register">
						<div class="row">
							<div class="col-md-6">
								<label>First Name</label>
								<input class="form-control" type="text" placeholder="First Name" name="name" required>
							</div>
							<div class="col-md-6">
								<label>Last Name</label>
								<input class="form-control" type="text" placeholder="Last Name" name="lastname" required>
							</div>
							<div class="col-md-6">
								<label>E-mail</label>
								<input class="form-control" type="text" placeholder="E-mail" name="mail" required>
							</div>
							<div class="col-md-6">
								<label>Phone Number (Without +)</label>
								<input class="form-control" type="text" placeholder="Phone Number" name="phone_number" required>
							</div>
							<div class="col-md-6">
								<label>Password</label>
								<input class="form-control" type="password" placeholder="Password" name="password" required>
							</div>
							<div class="col-md-6">
								<label>Retype Password</label>
								<input class="form-control" type="password" placeholder="Password, again..." name="password_check" required>
							</div>
							<div class="col-md-6">
								<label>Date of Birth</label>
								<input class="form-control" type="date" name="date_of_birth" required>
							</div>
							<div class="col-md-6">
								<label>Gender</label>
								<select class="form-control" name="gender" required>
								  <option value="Male">Male</option>
								  <option value="Female">Female</option>
								  <option value="Do not want to answer">Do not want to answer</option>
								</select>
							</div>
							<div class="col-md-12">
								<button class="btn">Register</button>
							</div>
						</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	--!>

	<!-- Login End -->

	<!-- Footer Start -->
<?php
include 'footer.php';
?>
	<!-- Footer Bottom End -->

	<!-- Back to Top -->
	<a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

	<!-- JavaScript Libraries -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
	<script src="lib/easing/easing.min.js"></script>
	<script src="lib/slick/slick.min.js"></script>

	<!-- Template Javascript -->
	<script src="js/main.js"></script>
</body>

</html>
