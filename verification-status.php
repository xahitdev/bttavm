<?php
require_once 'settings.php';

if(!isset($_SESSION['mail'])){
	header("Location:index.php");
	exit();
}
?>
<!DOCTYPE html>
<html>
<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="images/fevicon.png" type="image/x-icon">
  <title>BTTAVM - Online Shop</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha256-mLBIhmBvigTFWPSCtvdu6a76T+3Xyt+K571hupeFLg4=" crossorigin="anonymous" />
  <!-- font awesome style -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Tenor+Sans&display=swap" rel="stylesheet">


  <!-- Custom styles for this template -->
  <link href="css/style.css" rel="stylesheet" />
  <!-- responsive style -->
  <link href="css/responsive.css" rel="stylesheet" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>


<body>



<div style="margin-top: 17vh;"></div>

<div>
	<div class="container-fluid d-flex justify-content-center align-items-center" style="height:60vh;">
		<div class="text-center tenor-sans-text col-lg-6">
			<h2 class="tenor-sans-text my-3" style="font-size:1.4rem; font-weight: bold;">
			  <?php if(!$accountVerification){ ?>
				The email verification for <i><?php echo htmlspecialchars($_SESSION["mail"]) ?></i> is required.<br><br>
<?php
if(isset($_GET["s"]) && htmlspecialchars($_GET["s"]) == "200"){ ?>
					<span style="color: green;">Mail has been sent</span>
					<br><br>
<?php }
?>
				<div class="row" style="justify-content: center;">
				  <a href="control/again-send-mail.php" class="mr-3 mb-3">
					<button class="btn btn-outline-dark"><i class="fa fa-send" aria-hidden="true"></i> I didn't receive verification mail</button>
				  </a>
				  <a href="control/logout.php">
					<button class="btn btn-outline-dark"><i class="fa fa-sign-out " aria-hidden="true"></i> Log out</button>
				  </a>
				</div>

			  <?php }else{ ?>
				The email verification for <i><?php echo htmlspecialchars($_SESSION["mail"]) ?></i> has been successfully completed.<br><br>
				<a href="index.php"><button class="btn btn-outline-dark"><i class="fa fa-home" aria-hidden="true"></i> Go to Main Page</button></a>
			  <?php } ?>
			</h2>
		</div>
	</div>
</div>


<div style="margin-top: 100px;"></div>

  <script src="js/log-in-page-support.js"></script>
  <!-- jQery -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <!-- popper js -->
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <!-- bootstrap js -->
  <script src="js/bootstrap.js"></script>
  <!-- owl slider -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
  <!-- nice select -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/js/jquery.nice-select.min.js" integrity="sha256-Zr3vByTlMGQhvMfgkQ5BtWRSKBGa2QlspKYJnkjZTmo=" crossorigin="anonymous"></script>
  <!-- custom js -->
  <script src="js/custom.js"></script>
  <!-- Google Map -->
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCh39n5U-4IoWpsVGUHWdqB6puEkhRLdmI&callback=myMap"></script>
  <!-- End Google Map -->
  <script src="js/sidebar.js"></script>
</body>

</html>
