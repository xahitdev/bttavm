<?php
require_once 'settings.php';
include 'functions.php';
error_reporting(E_ALL & ~E_NOTICE);

session_start();
ob_start();

$current_file = $_SERVER['REQUEST_URI'];

function displayEmail(){
	global $conn;
	if($_SESSION['role'] == 'user'){
		echo $_SESSION['user_mail'];
	}
	if($_SESSION['role'] == 'seller'){
		echo $_SESSION['seller_mail'];
	}
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
    <div class="container-fluid">
        <div class="row w-100">
            <!-- Left Nav Items -->
            <div class="col-md-8 d-flex flex-wrap align-items-center navbar-nav">
                <a href="categories.php" class="nav-item nav-link active">Categories</a>
                <a href="cart.html" class="nav-item nav-link">Cart</a>
                <a href="checkout.html" class="nav-item nav-link">Checkout</a>
				<?php if(isset($_SESSION['role']) == 'seller'){ ?>
                <a href="seller/seller-panel.php" class="nav-item nav-link">Seller Panel</a>
				<?php } ?>
                <?php if (!isset($_SESSION['role'])): ?>
                    <a href="my-account.php" class="nav-item nav-link">My Account</a>
                <?php endif; ?>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">More Pages</a>
                    <div class="dropdown-menu">
                        <a href="wishlist.html" class="dropdown-item">Wishlist</a>
                        <?php if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])): ?>
                            <a href="login.php" class="dropdown-item">Login & Register</a>
                            <a href="seller-login.php" class="dropdown-item">Seller Login</a>
                        <?php endif; ?>
                        <a href="contact.html" class="dropdown-item">Contact Us</a>
                    </div>
                </div>
            </div>

            <!-- Right Account/Login Section -->
            <div class="col-md-4 d-flex justify-content-md-end mt-3 mt-md-0 navbar-nav">
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                        <?php
                            if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])) {
                                echo "Log in";
                            } else {
                                echo $_SESSION['user_mail'] ?? $_SESSION['seller_mail'];
                            }
                        ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
						<?php if(isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])){ ?>
						<a class="dropdown-item"><?php echo displayEmail(); ?></a>
						<a href="logout.php" class="dropdown-item">Log out</a>
						<?php }else{?>
						<a href="login.php" class="dropdown-item">Log in</a>
						<a href="register.php" class="dropdown-item">Register</a>
						<?php } ?>
                    </div>
                </div>
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
						<img src="/bttavm/img/logo.png" alt="BTTAVM Logo">
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
