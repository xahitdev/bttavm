<?php
include '../settings.php';
error_reporting(E_ALL & ~E_NOTICE);

session_start();
ob_start();

/* error_reporting(E_NOTICE); */

if (!isset($_SESSION['seller_id'])) {
	header("Location: ../seller-login.php");
	exit();
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
	<link href="../css/style.css" rel="stylesheet">
</head>

<body>
<?php
include '../navbar.php';
?>
	<!-- Breadcrumb Start -->
	<div class="breadcrumb-wrap">
		<div class="container-fluid">
			<ul class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">Home</a></li>
				<li class="breadcrumb-item"><a href="#">Seller</a></li>
				<li class="breadcrumb-item active">Seller Panel</li>
			</ul>
		</div>
	</div>
	<!-- Breadcrumb End -->

	<!-- My Account Start -->
	<div class="my-account">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<div class="nav flex-column nav-pills" role="tablist" aria-orientation="vertical">
						<a class="nav-link active" id="add-products-nav" data-toggle="pill" href="#add-products-tab"
							role="tab"><i class="fa fa-tachometer-alt"></i>Add Products</a>
						<a class="nav-link" id="orders-nav" data-toggle="pill" href="#orders-tab" role="tab"><i
								class="fa fa-shopping-bag"></i>Orders</a>
						<a class="nav-link" id="payment-nav" data-toggle="pill" href="#payment-tab" role="tab"><i
								class="fa fa-credit-card"></i>Payment Method</a>
						<a class="nav-link" id="address-nav" data-toggle="pill" href="#address-tab" role="tab"><i
								class="fa fa-map-marker-alt"></i>Address</a>
						<a class="nav-link" id="account-nav" data-toggle="pill" href="#account-tab" role="tab"><i
								class="fa fa-user"></i>Account Details</a>
						<a class="nav-link" href="index.php"><i class="fa fa-sign-out-alt"></i>Logout</a>
					</div>
				</div>
				<div class="col-md-9">
					<div class="tab-content">
						<div class="tab-pane fade show active" id="add-products-tab" role="tabpanel"
							aria-labelledby="add-products-nav">
							<h4>Add Products</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. In condimentum quam ac mi
								viverra dictum. In efficitur ipsum diam, at dignissim lorem tempor in. Vivamus tempor
								hendrerit finibus. Nulla tristique viverra nisl, sit amet bibendum ante suscipit non.
								Praesent in faucibus tellus, sed gravida lacus. Vivamus eu diam eros. Aliquam et sapien
								eget arcu rhoncus scelerisque.
							</p>
						</div>
						<div class="tab-pane fade" id="orders-tab" role="tabpanel" aria-labelledby="orders-nav">
							<div class="table-responsive">
								<table class="table table-bordered">

									<head class="thead-dark">
										<tr>
											<th>No</th>
											<th>Product</th>
											<th>Date</th>
											<th>Price</th>
											<th>Status</th>
											<th>Action</th>
										</tr>
									</head>

									<body>
										<tr>
											<td>1</td>
											<td>Product Name</td>
											<td>01 Jan 2020</td>
											<td>$99</td>
											<td>Approved</td>
											<td><button class="btn">View</button></td>
										</tr>
										<tr>
											<td>2</td>
											<td>Product Name</td>
											<td>01 Jan 2020</td>
											<td>$99</td>
											<td>Approved</td>
											<td><button class="btn">View</button></td>
										</tr>
										<tr>
											<td>3</td>
											<td>Product Name</td>
											<td>01 Jan 2020</td>
											<td>$99</td>
											<td>Approved</td>
											<td><button class="btn">View</button></td>
										</tr>
									</body>
								</table>
							</div>
						</div>
						<div class="tab-pane fade" id="payment-tab" role="tabpanel" aria-labelledby="payment-nav">
							<h4>Payment Method</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. In condimentum quam ac mi
								viverra dictum. In efficitur ipsum diam, at dignissim lorem tempor in. Vivamus tempor
								hendrerit finibus. Nulla tristique viverra nisl, sit amet bibendum ante suscipit non.
								Praesent in faucibus tellus, sed gravida lacus. Vivamus eu diam eros. Aliquam et sapien
								eget arcu rhoncus scelerisque.
							</p>
						</div>
						<div class="tab-pane fade" id="address-tab" role="tabpanel" aria-labelledby="address-nav">
							<h4>Address</h4>
							<div class="row">
								<div class="col-md-6">
									<h5>Payment Address</h5>
									<p>123 Payment Street, Los Angeles, CA</p>
									<p>Mobile: 012-345-6789</p>
									<button class="btn" id="address-nav" data-toggle="pill" href="#address-edit-tab"
										role="tab">Edit Address</button>
								</div>
								<div class="col-md-6">
									<h5>Shipping Address</h5>
									<p>123 Shipping Street, Los Angeles, CA</p>
									<p>Mobile: 012-345-6789</p>
									<button class="btn">Edit Address</button>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="address-edit-tab" role="tabpanel" aria-labelledby="address-nav">
							<div class="row">
								<form>
									<div class="mb-3">
										<label class="form-label">City:</label>
										<select id="city" class="form-select">
											<option value="">Select a city:</option>
											<?php
											$result = mysqli_query($conn, "SELECT * FROM iller");
											while ($row = mysqli_fetch_assoc($result)) {
												echo "<option value='" . $row['id'] . "'>" . $row['il_adi'] . "</option>";
											}
											?>
										</select>
									</div>
									<div class="mb-3">
										<label class="form-label">District:</label>
										<select id="district" class="form-select" disabled>
											<option value="">select a city first...</option>
										</select>
									</div>
									<div class="mb-3">
										<label class="form-label">Semt:</label>
										<select id="semt" class="form-select" disabled>
											<option value="">select a district first...</option>
										</select>
									</div>
								</form>
							</div>
						</div>
						<div class="tab-pane fade" id="account-tab" role="tabpanel" aria-labelledby="account-nav">
							<h4>Account Details</h4>
							<div class="row">
								<div class="col-md-6">
									<input class="form-control" type="text" placeholder="First Name">
								</div>
								<div class="col-md-6">
									<input class="form-control" type="text" placeholder="Last Name">
								</div>
								<div class="col-md-6">
									<input class="form-control" type="text" placeholder="Mobile">
								</div>
								<div class="col-md-6">
									<input class="form-control" type="text" placeholder="Email">
								</div>
								<div class="col-md-12">
									<input class="form-control" type="text" placeholder="Address">
								</div>
								<div class="col-md-12">
									<button class="btn">Update Account</button>
									<br><br>
								</div>
							</div>
							<h4>Password change</h4>
							<div class="row">
								<div class="col-md-12">
									<input class="form-control" type="password" placeholder="Current Password">
								</div>
								<div class="col-md-6">
									<input class="form-control" type="text" placeholder="New Password">
								</div>
								<div class="col-md-6">
									<input class="form-control" type="text" placeholder="Confirm Password">
								</div>
								<div class="col-md-12">
									<button class="btn">Save Changes</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- My Account End -->

	<?php include '../footer.php'; ?>
	

	<!-- Back to Top -->
	<a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

	<!-- JavaScript Libraries -->
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
	<script src="lib/easing/easing.min.js"></script>
	<script src="lib/slick/slick.min.js"></script>

	<!-- Template Javascript -->
	<script src="js/main.js"></script>
<script>
$(document).ready(function () {
	$("#city").change(function () {
		var cityId = $(this).val();
		$("#district").html('<option value="">İlçeler Yükleniyor...</option>').prop("disabled", true);

		if (cityId !== "") {
			$.ajax({
			url: "get_districts.php",
				type: "POST",
				data: { city_id: cityId },
				success: function (data) {
					$("#district").html(data).prop("disabled", false);
				}
		});
		}
	});
	$("#district").change(function () {
		var districtId = $(this).val();
		$("#semt").html('<option value="">Once Ilce Seciniz</option>').prop("disabled", true);

		if (districtId !== "") {
			$.ajax({
			url: "get_semt.php",
				type: "POST",
				data: { district_id: districtId },
				success: function (data) {
					$("#semt").html(data).prop("disabled", false);
				}
		});
		}
	});

});
</script>
</body>

</html>
