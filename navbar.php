<?php
require_once 'settings.php';
include 'functions.php';

$current_file = $_SERVER['REQUEST_URI'];
$image_src = (strpos($current_file, 'seller/seller-profile.php') !== false) 
	? "../img/logo.png" 
	: "/img/logo.png";

error_reporting(E_ALL & ~E_NOTICE);

session_start();
ob_start();

$loginType = "";

if(isset($_SESSION['seller_id'])){
	$loginType = 'seller';
} elseif(isset($_SESSION['user_id'])){
	$loginType = 'user';
}

?>

<!-- Top bar Start -->

<div class="top-bar bg-light py-2">
	<div class="container-fluid">
		<div class="row justify-content-center text-center">
			<div class="col-sm-6 d-flex align-items-center justify-content-center">
				<i class="fa fa-envelope me-2"></i> support@email.com
			</div>
			<div class="col-sm-6 d-flex align-items-center justify-content-center">
				<i class="fa fa-phone-alt me-2"></i> +012-345-6789
			</div>
		</div>
	</div>
</div>

<!-- Top bar End -->

<!-- Nav Bar Start -->
<div class="nav">
	<div class="container">
		<nav class="navbar navbar-expand-md bg-dark navbar-dark">
			<a href="#" class="navbar-brand">MENU</a>
			<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarCollapse">
				<div class="navbar-nav">
					<a href="index.php" class="nav-item nav-link active">Home</a>
					<a href="product-list.html" class="nav-item nav-link">Products</a>
					<a href="product-detail.html" class="nav-item nav-link">Product Detail</a>
					<a href="cart.html" class="nav-item nav-link">Cart</a>
					<a href="checkout.html" class="nav-item nav-link">Checkout</a>
					<a href="my-account.php" class="nav-item nav-link">My Account</a>
					<div class="nav-item dropdown">
						<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">More Pages</a>
						<div class="dropdown-menu">
							<a href="wishlist.html" class="dropdown-item">Wishlist</a>
<?php
if(!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])){
?>
								<a href="login.php" class="dropdown-item">Login & Register</a>
<?php
}
?>
							<a href="contact.html" class="dropdown-item">Contact Us</a>
<?php
if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])) {
?>
								<a href="sellerlogin.php" class="dropdown-item">Seller Login</a>
<?php
}
?>
						</div>
					</div>
				</div>
				<div class="navbar-nav mx-auto">
					<div class="nav-item dropdown">
						<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
<?php
if ($loginType != 'seller' && $loginType != 'user') {
?>
							Log in
<?php
}
?>
<?php
switch($loginType){
case 'seller': 
	echo "Seller Account ";
case 'user': 
	echo "User Account ";
}
?> </a>
						<div class="dropdown-menu">
<?php
if($loginType != ""){
	if($loginType == 'seller'){
?> 
									<a class="dropdown-item"> <?php echo $_SESSION['seller_mail']; ?> </a>
									<a href="logout.php" class="dropdown-item"> Log out </a>
<?php
	}elseif($loginType == 'user'){
?>
									<a class="dropdown-item"> <?php echo $_SESSION['username']; ?> </a>
									<a href="logout.php" class="dropdown-item"> Log out </a>
<?php
	}
}else{
?>
								<a href="login.php" class="dropdown-item">Log in</a>
								<a href="register.php" class="dropdown-item">Register</a>
<?php
}
?>
<?php
// BURAYI DUZELT #fix
?>
						</div>
					</div>
				</div>
			</div>
		</nav>
	</div>
</div>
<div class="bottom-bar">
	<div class="container ">
		<div class="row align-items-center">
			<div class="col-md-3">
				<div class="logo">
					<a href="index.php">
						<img src="<?php echo $image_src; ?>" alt="BTTAVM Logo">
					</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="search">
					<input type="text" placeholder="Search">
					<button><i class="fa fa-search"></i></button>
				</div>
			</div>
			<div class="col-md-3">
				<div class="user">
					<a href="wishlist.html" class="btn wishlist">
						<i class="fa fa-heart"></i>
						<span>(0)</span>
					</a>
					<a href="cart.html" class="btn cart">
						<i class="fa fa-shopping-cart"></i>
						<span>(0)</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- end of navbar -->
