<?php
require_once 'settings.php';
include 'functions.php';
error_reporting(E_ALL);

session_start();
ob_start();

$logoSQL = "SELECT * FROM logos WHERE logo_id = 1";
$logoResult = $conn->query($logoSQL);

if ($logoResult && $logoResult->num_rows > 0) {
	$logoRow = $logoResult->fetch_assoc();
	// now you can use $logoRow['column_name']
}

$current_file = $_SERVER['REQUEST_URI'];
$current_page = $_SERVER['PHP_SELF'];

function displayEmail()
{
	global $conn;
	if ($_SESSION['role'] == 'user') {
		echo $_SESSION['mail'];
	}
	if ($_SESSION['role'] == 'seller') {
		echo $_SESSION['seller_mail'];
	}
}

// Kullanıcı giriş yapmışsa favori sayısını al
$favorites_count = 0;
if (isset($_SESSION['user_id'])) {
	$user_id = $_SESSION['user_id'];
	$countQuery = "SELECT * FROM favorites WHERE customer_id = ?";
	$countStmt = $conn->prepare($countQuery);
	$countStmt->bind_param("i", $user_id); 
	$countStmt->execute();
	$result = $countStmt->get_result();
	$favorites_count = $result->num_rows;
}

?>

<!-- Top bar Start -->

<div class="top-bar bg-light py-2">
	<div class="container-fluid">
		<div class="row justify-content-center text-center">
			<div class="col-sm-6 d-flex align-items-center justify-content-center">
				<i class="fa fa-envelope me-2"></i> cahitatillab@gmail.com
			</div>
			<div class="col-sm-6 d-flex align-items-center justify-content-center">
				<i class="fa fa-phone-alt me-2"></i> +90 543 668 6435
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
						<!-- 🌐 Left Nav Items -->
						<div class="col-md-8 d-flex flex-wrap align-items-center navbar-nav">
							<div class="nav-item dropdown">
								<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="padding: 10px 15px;">Satıcı
									mısın?</a>
								<div class="dropdown-menu"
									style="background-color: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 10px;">
									<?php if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])): ?>
										<a href="seller-login.php" class="dropdown-item" style="padding: 10px 15px; font-weight: 500;">Satıcı
											Giriş</a>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- 👤 Right Account/Login Section -->
						<div class="col-md-4 d-flex justify-content-md-end mt-3 mt-md-0 navbar-nav">
							<div class="nav-item dropdown">
								<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"
									style="padding: 10px 15px; font-weight: 500;">
<?php
if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])) {
	echo "Giriş Yap";
} else {
	displayEmail();
}
?>
								</a>
								<div class="dropdown-menu dropdown-menu-right"
									style="background-color: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 10px; min-width: 180px;">
									<?php if (isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])) { ?>
										<a href="logout.php" class="dropdown-item"
											style="padding: 10px 15px; color: #333; font-weight: 500; border-radius: 6px; transition: background 0.3s;"
											onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
											Çıkış Yap
										</a>
									<?php } else { ?>
										<a href="login.php" class="dropdown-item"
											style="padding: 10px 15px; color: #333; font-weight: 500; border-radius: 6px; transition: background 0.3s;"
											onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
											Giriş Yap
										</a>
										<a href="register.php" class="dropdown-item"
											style="padding: 10px 15px; color: #333; font-weight: 500; border-radius: 6px; transition: background 0.3s;"
											onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
											Kayıt Ol
										</a>
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
				<a href="<?php if (file_exists("index.php")) {
				echo "index.php";
} else {
	echo "../index.php";
}
?>">
						<img src="<?php echo $logoRow['navbar_logo']; ?>" alt="BTTAVM Logo">
					</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="search">
					<form action="product-list.php" method="GET">
						<input type="text" name="search_query" placeholder="Search">
						<button><i class="fa fa-search"></i></button>
					</form>
				</div>
			</div>
			<div class="col-md-3">
				<div class="user">
				<a href="<?php echo $_SESSION['role'] == 'user'
				? 'my-account.php'
				: (strpos($_SERVER['PHP_SELF'], 'seller/seller-panel.php') !== false ? 'seller-panel.php' : 'seller/seller-panel.php');
?>" class="btn btn-light wishlist" style="border: 0px;">
						<i class="fa fa-user"></i>
					</a>
					<a href="favorites.php" class="btn btn-light favorites" style="border: 0px;">
							<i class="fa fa-heart"></i>
							<span>(<?php echo $favorites_count; ?>)</span>
					</a>
					<a href="cart.php" class="btn btn-light cart" style="border: 0px;">
						<i class="fa fa-shopping-cart"></i>
						<span>(0)</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- end of navbar -->
