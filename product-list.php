<?php
require_once 'settings.php';
/* echo $_GET['id']; */
// Tüm alt kategorileri recursive olarak bulan fonksiyon 
function getAllChildCategories($conn, $categoryId, &$resultArray = [])
{
	// Ana kategoriyi sonuç dizisine ekleyelim (eğer daha önce eklenmemişse)
	if (!in_array($categoryId, $resultArray)) {
		$resultArray[] = $categoryId;
	}

	// Bu kategorinin doğrudan alt kategorilerini bulalım
	$categoryId = (int) $categoryId; // SQL injection'a karşı koruma
	$sql = "SELECT category_id FROM categories WHERE category_parent_id = $categoryId";
	$result = $conn->query($sql);

	// Alt kategori yoksa, mevcut sonuçları döndürelim
	if ($result->num_rows == 0) {
		return $resultArray;
	}

	// Her bir alt kategori için, onun da alt kategorilerini bulalım
	while ($row = $result->fetch_assoc()) {
		$childId = (int) $row['category_id']; // SQL injection'a karşı koruma
		// Sonsuz döngüye girmemek için kontrol
		if (!in_array($childId, $resultArray)) {
			$resultArray[] = $childId;
			// Recursive olarak alt kategorileri bulalım
			getAllChildCategories($conn, $childId, $resultArray);
		}
	}

	return $resultArray;
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
	<!-- Nav Bar Start -->
	<?php
	include 'navbar.php';
	?>
	<!-- Bottom Bar End -->

	<!-- Breadcrumb Start -->
	<div class="breadcrumb-wrap">
		<div class="container-fluid">
			<ul class="breadcrumb">
				<li class="breadcrumb-item"><a href="#">Home</a></li>
				<li class="breadcrumb-item"><a href="#">Products</a></li>
				<li class="breadcrumb-item active">Product List</li>
			</ul>
		</div>
	</div>
	<!-- Breadcrumb End -->

	<!-- Product List Start -->
	<div class="product-view">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-8">
					<div class="row">
						<div class="col-md-12">
							<div class="product-view-top">
								<div class="row">
									<div class="col-md-4">
										<div class="product-search">
											<input type="email" value="Search">
											<button><i class="fa fa-search"></i></button>
										</div>
									</div>
									<div class="col-md-4">
										<div class="product-short">
											<div class="dropdown">
												<div class="dropdown-toggle" data-toggle="dropdown">Product short by
												</div>
												<div class="dropdown-menu dropdown-menu-right">
													<a href="#" class="dropdown-item">Newest</a>
													<a href="#" class="dropdown-item">Popular</a>
													<a href="#" class="dropdown-item">Most sale</a>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-4">
										<div class="product-price-range">
											<div class="dropdown">
												<div class="dropdown-toggle" data-toggle="dropdown">Product price range
												</div>
												<div class="dropdown-menu dropdown-menu-right">
													<a href="#" class="dropdown-item">$0 to $50</a>
													<a href="#" class="dropdown-item">$51 to $100</a>
													<a href="#" class="dropdown-item">$101 to $150</a>
													<a href="#" class="dropdown-item">$151 to $200</a>
													<a href="#" class="dropdown-item">$201 to $250</a>
													<a href="#" class="dropdown-item">$251 to $300</a>
													<a href="#" class="dropdown-item">$301 to $350</a>
													<a href="#" class="dropdown-item">$351 to $400</a>
													<a href="#" class="dropdown-item">$401 to $450</a>
													<a href="#" class="dropdown-item">$451 to $500</a>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?php
						// Önce tüm kategorileri bulalım (üstteki fonksiyonu kullanarak)
						$getCategoryId = (int) $_GET['id'];
						$categoryIds = getAllChildCategories($conn, $getCategoryId);

						// Güvenli kategori ID'leri dizisi oluştur
						if (!empty($categoryIds)) {
							$safeCategories = array_map('intval', $categoryIds);
							$categoryIdsList = implode(',', $safeCategories);

							// Ürünleri getirelim
							$productsSQL = "SELECT * FROM products WHERE category_id IN ($categoryIdsList) AND is_active = 1";
							$productsResult = $conn->query($productsSQL);

							if ($productsResult && $productsResult->num_rows > 0) {
								while ($row = $productsResult->fetch_assoc()) {
									$productPicturesSQL = "SELECT * FROM product_images WHERE product_id=" . $row['product_id'];
									$productPicturesResult = $conn->query($productPicturesSQL);
									$pictureRow = $productPicturesResult->fetch_assoc();
									$productImagePreview = $pictureRow['product_images_url'];
									$productImagePreviewArray = explode('#', $productImagePreview);

									$productName = limitText($row['product_name'], 25);
									// Her bir ürün için HTML şablonunu dolduralım
									?>
									<div class="col-md-4">
										<div class="product-item">
											<div class="product-title">
												<a href="product-detail.php?id=<?php echo $row['product_id']; ?>"><?php echo $productName; ?></a>
												<div class="ratting">
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
													<i class="fa fa-star"></i>
												</div>
											</div>
											<div class="product-image">
												<a href="product-detail.php?id=<?php echo $row['product_id']; ?>">
													<img src="<?php echo $productImagePreviewArray[0] ?>" alt="<?php echo $row['product_name']; ?>"
														style="width: 100%; height: 250px; object-fit: cover; !important">
												</a>
												<div class="product-action">
													<a href="add-to-cart.php?id=<?php echo $row['product_id']; ?>"><i class="fa fa-cart-plus"></i></a>
													<a href="add-to-wishlist.php?id=<?php echo $row['product_id']; ?>"><i class="fa fa-heart"></i></a>
													<a href="product-detail.php?id=<?php echo $row['product_id']; ?>"><i class="fa fa-search"></i></a>
												</div>
											</div>
											<div class="product-price">
												<h3><span>$</span><?php echo number_format($row['price'] / 100, 2); ?></h3>
												<a class="btn" href="add-to-cart.php?id=<?php echo $row['product_id']; ?>"><i
														class="fa fa-shopping-cart"></i>Buy Now</a>
											</div>
										</div>
									</div>
									<?php
								}
							} else {
								echo "<div class='col-12'><p class='text-center'>Bu kategoride ürün bulunamadı.</p></div>";
							}
						}
						?>
					</div>

					<!-- Pagination Start -->
					<div class="col-md-12">
						<nav aria-label="Page navigation example">
							<ul class="pagination justify-content-center">
								<li class="page-item disabled">
									<a class="page-link" href="#" tabindex="-1">Previous</a>
								</li>
								<li class="page-item active"><a class="page-link" href="#">1</a></li>
								<li class="page-item"><a class="page-link" href="#">2</a></li>
								<li class="page-item"><a class="page-link" href="#">3</a></li>
								<li class="page-item">
									<a class="page-link" href="#">Next</a>
								</li>
							</ul>
						</nav>
					</div>
					<!-- Pagination Start -->
				</div>

				<!-- Side Bar Start -->
				<div class="col-lg-4 sidebar">
					<div class="sidebar-widget category">
						<h2 class="title">Category</h2>
						<nav class="navbar bg-light">
							<ul class="navbar-nav">
								<li class="nav-item">
									<a class="nav-link" href="#"><i class="fa fa-female"></i>Fashion & Beauty</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#"><i class="fa fa-child"></i>Kids & Babies Clothes</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#"><i class="fa fa-tshirt"></i>Men & Women Clothes</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#"><i class="fa fa-mobile-alt"></i>Gadgets &
										Accessories</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="#"><i class="fa fa-microchip"></i>Electronics &
										Accessories</a>
								</li>
							</ul>
						</nav>
					</div>

					<div class="sidebar-widget widget-slider">
						<div class="sidebar-slider normal-slider">
							<div class="product-item">
								<div class="product-title">
									<a href="#">Product Name</a>
									<div class="ratting">
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
									</div>
								</div>
								<div class="product-image">
									<a href="product-detail.html">
										<img src="img/product-10.jpg" alt="Product Image">
									</a>
									<div class="product-action">
										<a href="#"><i class="fa fa-cart-plus"></i></a>
										<a href="#"><i class="fa fa-heart"></i></a>
										<a href="#"><i class="fa fa-search"></i></a>
									</div>
								</div>
								<div class="product-price">
									<h3><span>$</span>99</h3>
									<a class="btn" href=""><i class="fa fa-shopping-cart"></i>Buy Now</a>
								</div>
							</div>
							<div class="product-item">
								<div class="product-title">
									<a href="#">Product Name</a>
									<div class="ratting">
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
									</div>
								</div>
								<div class="product-image">
									<a href="product-detail.html">
										<img src="img/product-9.jpg" alt="Product Image">
									</a>
									<div class="product-action">
										<a href="#"><i class="fa fa-cart-plus"></i></a>
										<a href="#"><i class="fa fa-heart"></i></a>
										<a href="#"><i class="fa fa-search"></i></a>
									</div>
								</div>
								<div class="product-price">
									<h3><span>$</span>99</h3>
									<a class="btn" href=""><i class="fa fa-shopping-cart"></i>Buy Now</a>
								</div>
							</div>
							<div class="product-item">
								<div class="product-title">
									<a href="#">Product Name</a>
									<div class="ratting">
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
									</div>
								</div>
								<div class="product-image">
									<a href="product-detail.html">
										<img src="img/product-8.jpg" alt="Product Image">
									</a>
									<div class="product-action">
										<a href="#"><i class="fa fa-cart-plus"></i></a>
										<a href="#"><i class="fa fa-heart"></i></a>
										<a href="#"><i class="fa fa-search"></i></a>
									</div>
								</div>
								<div class="product-price">
									<h3><span>$</span>99</h3>
									<a class="btn" href=""><i class="fa fa-shopping-cart"></i>Buy Now</a>
								</div>
							</div>
						</div>
					</div>

					<div class="sidebar-widget brands">
						<h2 class="title">Our Brands</h2>
						<ul>
							<li><a href="#">Nulla </a><span>(45)</span></li>
							<li><a href="#">Curabitur </a><span>(34)</span></li>
							<li><a href="#">Nunc </a><span>(67)</span></li>
							<li><a href="#">Ullamcorper</a><span>(74)</span></li>
							<li><a href="#">Fusce </a><span>(89)</span></li>
							<li><a href="#">Sagittis</a><span>(28)</span></li>
						</ul>
					</div>

					<div class="sidebar-widget tag">
						<h2 class="title">Tags Cloud</h2>
						<a href="#">Lorem ipsum</a>
						<a href="#">Vivamus</a>
						<a href="#">Phasellus</a>
						<a href="#">pulvinar</a>
						<a href="#">Curabitur</a>
						<a href="#">Fusce</a>
						<a href="#">Sem quis</a>
						<a href="#">Mollis metus</a>
						<a href="#">Sit amet</a>
						<a href="#">Vel posuere</a>
						<a href="#">orci luctus</a>
						<a href="#">Nam lorem</a>
					</div>
				</div>
				<!-- Side Bar End -->
			</div>
		</div>
	</div>
	<!-- Product List End -->

	<!-- Brand Start -->
	<div class="brand">
		<div class="container-fluid">
			<div class="brand-slider">
				<div class="brand-item"><img src="img/brand-1.png" alt=""></div>
				<div class="brand-item"><img src="img/brand-2.png" alt=""></div>
				<div class="brand-item"><img src="img/brand-3.png" alt=""></div>
				<div class="brand-item"><img src="img/brand-4.png" alt=""></div>
				<div class="brand-item"><img src="img/brand-5.png" alt=""></div>
				<div class="brand-item"><img src="img/brand-6.png" alt=""></div>
			</div>
		</div>
	</div>
	<!-- Brand End -->

	<!-- Footer Start -->
	<?php
	include 'footer.php';
	?>
	<!-- Footer End -->

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