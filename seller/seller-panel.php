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

$seller_id = $_SESSION['seller_id']; // or set manually: $seller_id = 5;

$productsSQL = "SELECT * FROM products WHERE seller_id = " . $seller_id;
$productResult = $conn->query($productsSQL);

//categorires SQL 
$categoriesSQL = "SELECT category_id, category_name FROM categories WHERE category_parent_id = 0";
$categoriesResult = $conn->query($categoriesSQL);


//edit products

if (isset($_POST['applyEditProductChanges'])) {
	$productid = $_POST['editProductID'];

	$sql = "SELECT * FROM products WHERE product_id = ".$productid." AND seller_id = ".$seller_id;
	$result = $conn->query($sql); 

	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$productName        = !empty($_POST['editProductName'])        ? $_POST['editProductName']        : $row['product_name'];
		$productDescription = !empty($_POST['editProductDescription']) ? $_POST['editProductDescription'] : $row['product_description'];
		$productPrice       = !empty($_POST['editProductPrice'])       ? $_POST['editProductPrice']       : $row['price'];
		$productStock       = !empty($_POST['editProductStock'])       ? $_POST['editProductStock']       : $row['stock'];
		$productVisible = !empty($_POST['editProductVisible'])       ? $_POST['editProductVisible']       : $row['is_active'];

		$stmt = $conn->prepare("UPDATE products SET product_name=?, product_description=?, price=?, stock=?, is_active=? WHERE product_id=? AND seller_id=?");
		$stmt->bind_param("sssssss", $productName, $productDescription, $productPrice, $productStock, $productVisible, $productid, $seller_id);

		if ($stmt->execute()) {
		/* echo "<p style='color:green;'>Product updated successfully!</p>"; */
		} else {
		echo "<p style='color:red;'>Update failed: " . $stmt->error . "</p>";
		}
	} else {
	echo "<p style='color:red;'>Product not found or access denied.</p>";
	}
	header("Refresh:0");
}

//add products

if (isset($_POST['addProductButton'])) {
    $productName = $_POST['addProductName'];
    $productDesc = $_POST['addProductDescription'];
    $productPrice = $_POST['addProductPrice'];
    $productStock = $_POST['addProductStock'];
    $productCategory = $_POST['addProductCategory'];
	$isActive = 1;

    $stmt = $conn->prepare("INSERT INTO products (product_name, product_description, price, stock, category_id, seller_id, is_active) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssss", $productName, $productDesc, $productPrice, $productStock, $productCategory, $seller_id, $isActive);
    
    if ($stmt->execute()) {
        /* echo "Product added successfully!"; */
		header("Refresh:0");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
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
								class="fa fa-shopping-bag"></i>Product Manager</a>
						<a class="nav-link" id="payment-nav" data-toggle="pill" href="#payment-tab" role="tab"><i
								class="fa fa-credit-card"></i>Payment Method</a>
						<a class="nav-link" id="address-nav" data-toggle="pill" href="#address-tab" role="tab"><i
								class="fa fa-map-marker-alt"></i>Address</a>
						<a class="nav-link" id="account-nav" data-toggle="pill" href="#account-tab" role="tab"><i
								class="fa fa-user"></i>Account Details</a>
						<a class="nav-link" href="bttavm/index.php"><i class="fa fa-sign-out-alt"></i>Logout</a>
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
											<th>Product ID</th>
											<th>Product Name</th>
											<th>Description</th>
											<th>Price</th>
											<th>Stock</th>
											<th><a class="nav-link" id="add-product-nav" data-toggle="pill" href="#add-product-tab" role="tab"><i class="fa fa-plus"></i>Add</a></th>
										</tr>
									</head>
									<body>
									<form method="POST">
									<?php
									if ($productResult && $productResult->num_rows > 0) {
									    while($row = $productResult->fetch_assoc()) {
										echo "<tr>";
											echo "<td> " . $row['product_id'] . " </td>";
											echo "<td> " . $row['product_name'] . " </td>";
											echo "<td> " . $row['product_description'] . " </td>";
											echo "<td> " . $row['price'] . " </td>";
											echo "<td> " . $row['stock'] . " </td>";
											echo "<td style='display: none'> " . $row['is_active'] . " </td>";
											echo '<td><a data-productid="'. $row['product_id'] .'" class="nav-link edit-product-veri" id="edit-product-nav" data-toggle="pill" href="#edit-product-tab" role="tab"><i></i>Edit</a></td>';
										echo "</tr>";
									    }
									} else {
									    echo "No products found or query error.";
									}
									?>
									</form>
									</body>
								</table>
							</div>
						</div>
						<div class="tab-pane fade" id="" role="tabpanel" aria-labelledby="payment-nav">
							<h4>Payment Method</h4>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. In condimentum quam ac mi
								viverra dictum. In efficitur ipsum diam, at dignissim lorem tempor in. Vivamus tempor
								hendrerit finibus. Nulla tristique viverra nisl, sit amet bibendum ante suscipit non.
								Praesent in faucibus tellus, sed gravida lacus. Vivamus eu diam eros. Aliquam et sapien
								eget arcu rhoncus scelerisque.
							</p>
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
						<?php
						?>
						<div class="tab-pane fade" id="edit-product-tab" role="tabpanel" aria-labelledby="edit-product-nav">
							<h4>Edit Image of the Product</h4>
							<form method="POST">
								<div class="row">
									<div class="col-md-6">
									<input class="form-control" id="edit_product_id" type="text" name="prepareChangeImage" placeholder="<?php echo $_POST['editProductID'] ?>">
									</div>
								</div>
							</form>
							<h4>Edit Selected Product</h4>
							<form method="POST">
								<div class="row">
									<div class="col-md-6">
										<input class="form-control" id="edit_product_name" name="editProductName" type="text" placeholder="First Name">
									</div>
									<div class="col-md-6">
										<input class="form-control" id="edit_product_description" name="editProductDescription" type="text" placeholder="Last Name">
									</div>
									<div class="col-md-6">
										<input class="form-control" id="edit_price" name="editProductPrice" type="text" placeholder="Mobile">
									</div>
									<div class="col-md-6">
										<input class="form-control" id="edit_stock" name="editProductStock" type="text" placeholder="Email">
									</div>
									<div class="col-md-6">
										<select class="form-control" id="edit_visible" name="editProductVisible">
											<option value="0">Not for Sale</option>
											<option value="1">On Sale</option>
										</select>
									</div>
									<div class="col-md-6">
										<input class="form-control" id="edit_product_id" name="editProductID" type="text" placeholder="First Name" hidden>
									</div>
									<div class="col-md-12">
										<button class="btn" type="submit" name="applyEditProductChanges">Apply Changes</button>
										<br><br>
									</div>
								</div>
							</form>
						</div>
						<div class="tab-pane fade" id="add-product-tab" role="tabpanel" aria-labelledby="add-product-nav">
						  <h4>Add Products</h4>
						  <form method="POST">
							<div class="row">
							  <div class="col-md-6">
								<input class="form-control" name="addProductName" type="text" placeholder="Product Name" required>
							  </div>
							  <div class="col-md-6">
								<input class="form-control" name="addProductDescription" type="text" placeholder="Description" required>
							  </div>
							  <div class="col-md-6">
								<input class="form-control" name="addProductPrice" type="text" placeholder="Price" required>
							  </div>
							  <div class="col-md-6">
								<input class="form-control" name="addProductStock" type="text" placeholder="Stock" required>
							  </div>

							  <!-- Categories area -->
							  <div class="col-md-6" id="category-selects">
								<select class="form-control" id="parent_category" name="category[]" required>
								  <option value="">-- Select Category --</option>
								  <?php if($categoriesResult->num_rows > 0): ?>
									<?php while($row = $categoriesResult->fetch_assoc()): ?>
									  <?php if($row['category_parent_id'] == 0): // Only parent categories ?>
										<option value="<?= htmlspecialchars($row['category_id']) ?>">
										  <?= htmlspecialchars($row['category_name']) ?>
										</option>
									  <?php endif; ?>
									<?php endwhile; ?>
								  <?php endif; ?>
								</select>
							  </div>

							  <div class="col-md-12">
								<br>
								<button class="btn" type="submit" name="addProductButton">Add Product</button>
								<br><br>
							  </div>
							</div>
						  </form>
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
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
	<script src="lib/easing/easing.min.js"></script>
	<script src="lib/slick/slick.min.js"></script>

	<!-- Template Javascript -->
	<script src="js/main.js"></script>
	<script>
	$(document).ready(function () {
    $('.edit-product-veri').on("click", function () {
        var currentTr = $(this).closest('tr'); // Bulunduğu satır

        // td'leri sırayla al
        var productId = currentTr.find('td:eq(0)').text().trim();
        var productName = currentTr.find('td:eq(1)').text().trim();
        var productDescription = currentTr.find('td:eq(2)').text().trim();
        var price = currentTr.find('td:eq(3)').text().trim();
        var stock = currentTr.find('td:eq(4)').text().trim();
        var visible = currentTr.find('td:eq(5)').text().trim();

        // Konsola yazdıralım
        console.log("ID: " + productId);
        console.log("Ad: " + productName);
        console.log("Açıklama: " + productDescription);
        console.log("Fiyat: " + price);
        console.log("Stok: " + stock);
        console.log("Visible: " + visible);

        // Örnek: form alanlarına atama (eğer varsa)
        $('#edit_product_id').val(productId);
        $('#edit_product_name').val(productName);
        $('#edit_product_description').val(productDescription);
        $('#edit_price').val(price);
        $('#edit_stock').val(stock);
        $('#edit_visible').val(visible);
		});
	});
	</script>
<script>
$(document).ready(function () {
	$("#city").change(function () {
		var cityId = $(this).val();
		$("#district").html('<option value="">İlçeler Yükleniyor...</option>').prop("disabled", true);

		if (cityId !== "") {
			$.ajax({
			url: "../get_districts.php",
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
			url: "../get_semt.php",
				type: "POST",
				data: { district_id: districtId },
				success: function (data) {
					$("#semt").html(data).prop("disabled", false);
				}
		});
		}
	});

});

$(document).on('change', '#category-selects select', function() {
  var selectedCategoryId = $(this).val();

  // Remove all dropdowns after this one (if any)
  $(this).nextAll('select').remove();

  if (selectedCategoryId) {
    $.ajax({
      url: 'get-subcategories.php',
      type: 'POST',
      data: { parent_id: selectedCategoryId },
      success: function(response) {
        if (response.trim() !== '') {
          $('#category-selects').append(response);
        }
      }
    });
  }
});
</script>
</body>

</html>
