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
						// Sayfanın en üstünde arama parametresini kontrol edin
						$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

						// Eğer arama sorgusu varsa
						if (!empty($search_query)) {
							// Güvenlik için escape
							$search_query = $conn->real_escape_string($search_query);

							// Arama sorgusu ile ürünleri getir
							$productsSQL = "SELECT * FROM products WHERE (product_name LIKE '%$search_query%' OR product_description LIKE '%$search_query%') AND is_active = 1";
							$productsResult = $conn->query($productsSQL);

							// Sonuçları göster
							if ($productsResult && $productsResult->num_rows > 0) {
								?>
								<div class="container pt-4 pb-3">
									<div class="text-center mb-4">
										<h2 class="section-title px-5"><span class="px-2">Arama Sonuçları:
												"<?php echo htmlspecialchars($search_query); ?>"</span></h2>
									</div>
									<div class="row px-xl-5">
										<?php
										while ($row = $productsResult->fetch_assoc()) {
											$productPicturesSQL = "SELECT * FROM product_images WHERE product_id=" . $row['product_id'];
											$productPicturesResult = $conn->query($productPicturesSQL);
											$pictureRow = $productPicturesResult->fetch_assoc();

											// Resim kontrolü - eğer resim yoksa varsayılan resim kullan
											$productImagePreview = isset($pictureRow['product_images_url']) ? $pictureRow['product_images_url'] : '';
											$productImagePreviewArray = !empty($productImagePreview) ? explode('#', $productImagePreview) : ['placeholder.jpg'];

											$productName = limitText($row['product_name'], 25);
											?>
											<div class="col-lg-4 col-md-6 col-sm-12 pb-1">
												<div class="card product-item border-0 mb-4"
													style="border-radius: 0.5rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
													<div class="card-header product-title bg-transparent border-0 pt-3 pb-0" style="min-height: 80px;">
														<h6 class="text-truncate text-center m-0">
															<a href="product-detail.php?id=<?php echo $row['product_id']; ?>"
																class="text-decoration-none text-dark">
																<?php echo $productName; ?>
															</a>
														</h6>
														<div class="d-flex justify-content-center mt-2">
															<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
															<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
															<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
															<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
															<small class="text-warning"><i class="fa fa-star"></i></small>
														</div>
													</div>
													<div class="card-body p-0 overflow-hidden position-relative" style="height: 250px;">
														<a href="product-detail.php?id=<?php echo $row['product_id']; ?>" class="text-decoration-none">
															<img class="img-fluid w-100"
																src="<?php echo isset($productImagePreviewArray[0]) ? $productImagePreviewArray[0] : 'placeholder.jpg'; ?>"
																alt="<?php echo $row['product_name']; ?>" style="height: 250px; object-fit: cover;">
														</a>
														<div class="product-action"
															style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.1); opacity: 0; transition: 0.5s; z-index: 1;">
															<div>
																<a class="btn btn-outline-dark btn-sm rounded-circle"
																	href="add-to-cart.php?id=<?php echo $row['product_id']; ?>"
																	style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
																	<i class="fa fa-cart-plus"></i>
																</a>
																<a class="btn btn-outline-dark btn-sm rounded-circle"
																	href="add-to-wishlist.php?id=<?php echo $row['product_id']; ?>"
																	style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
																	<i class="fa fa-heart"></i>
																</a>
																<a class="btn btn-outline-dark btn-sm rounded-circle"
																	href="product-detail.php?id=<?php echo $row['product_id']; ?>"
																	style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
																	<i class="fa fa-search"></i>
																</a>
															</div>
														</div>
													</div>
													<div class="card-footer bg-transparent border-top text-center pt-3 pb-3"
														style="background-color: rgba(0, 0, 0, 0.02);">
														<div class="d-flex justify-content-between align-items-center">
															<h6 class="m-0 font-weight-semi-bold">
																<span>$</span><?php echo number_format($row['price'] / 100, 2); ?>
															</h6>
															<a href="add-to-cart.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-primary">
																<i class="fa fa-shopping-cart mr-1"></i>Buy Now
															</a>
														</div>
													</div>
												</div>
											</div>
											<?php
										}
										?>
									</div>
								</div>

								<style>
									.product-item:hover .product-action {
										opacity: 1;
									}

									.product-item:hover {
										box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
										transition: .5s;
									}
								</style>
								<?php
							} else {
								?>
								<div class="container-fluid pt-5">
									<div class="alert alert-info text-center" role="alert">
										Aramanız için sonuç bulunamadı: "<?php echo htmlspecialchars($search_query); ?>"
									</div>
								</div>
								<?php
							}
						} else {
							// Arama sorgusu yoksa normal kategori listesi kodunu çalıştır
							if (isset($_GET['id'])) {
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
										?>
										<div class="row px-xl-5">
											<?php
											while ($row = $productsResult->fetch_assoc()) {
												$productPicturesSQL = "SELECT * FROM product_images WHERE product_id=" . $row['product_id'];
												$productPicturesResult = $conn->query($productPicturesSQL);
												$pictureRow = $productPicturesResult->fetch_assoc();

												// Resim kontrolü - eğer resim yoksa varsayılan resim kullan
												$productImagePreview = isset($pictureRow['product_images_url']) ? $pictureRow['product_images_url'] : '';
												$productImagePreviewArray = !empty($productImagePreview) ? explode('#', $productImagePreview) : ['placeholder.jpg'];

												$productName = limitText($row['product_name'], 25);
												?>
												<div class="col-lg-4 col-md-6 col-sm-12 pb-1">
													<div class="card product-item border-0 mb-4"
														style="border-radius: 0.5rem; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);">
														<div class="card-header product-title bg-transparent border-0 pt-3 pb-0" style="min-height: 80px;">
															<h6 class="text-truncate text-center m-0">
																<a href="product-detail.php?id=<?php echo $row['product_id']; ?>"
																	class="text-decoration-none text-dark">
																	<?php echo $productName; ?>
																</a>
															</h6>
															<div class="d-flex justify-content-center mt-2">
																<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
																<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
																<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
																<small class="text-warning mr-1"><i class="fa fa-star"></i></small>
																<small class="text-warning"><i class="fa fa-star"></i></small>
															</div>
														</div>
														<div class="card-body p-0 overflow-hidden position-relative" style="height: 250px;">
															<a href="product-detail.php?id=<?php echo $row['product_id']; ?>" class="text-decoration-none">
																<img class="img-fluid w-100"
																	src="<?php echo isset($productImagePreviewArray[0]) ? $productImagePreviewArray[0] : 'placeholder.jpg'; ?>"
																	alt="<?php echo $row['product_name']; ?>" style="height: 250px; object-fit: cover;">
															</a>
															<div class="product-action"
																style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0, 0, 0, 0.1); opacity: 0; transition: 0.5s; z-index: 1;">
																<div>
																	<a class="btn btn-outline-dark btn-sm rounded-circle"
																		href="add-to-cart.php?id=<?php echo $row['product_id']; ?>"
																		style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
																		<i class="fa fa-cart-plus"></i>
																	</a>
																	<a class="btn btn-outline-dark btn-sm rounded-circle"
																		href="add-to-wishlist.php?id=<?php echo $row['product_id']; ?>"
																		style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
																		<i class="fa fa-heart"></i>
																	</a>
																	<a class="btn btn-outline-dark btn-sm rounded-circle"
																		href="product-detail.php?id=<?php echo $row['product_id']; ?>"
																		style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
																		<i class="fa fa-search"></i>
																	</a>
																</div>
															</div>
														</div>
														<div class="card-footer bg-transparent border-top text-center pt-3 pb-3"
															style="background-color: rgba(0, 0, 0, 0.02);">
															<div class="d-flex justify-content-between align-items-center">
																<h6 class="m-0 font-weight-semi-bold">
																	<span>$</span><?php echo number_format($row['price'] / 100, 2); ?>
																</h6>
																<a href="add-to-cart.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-primary">
																	<i class="fa fa-shopping-cart mr-1"></i>Buy Now
																</a>
															</div>
														</div>
													</div>
												</div>
												<?php
											}
											?>
										</div>

										<style>
											.product-item:hover .product-action {
												opacity: 1;
											}

											.product-item:hover {
												box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
												transition: .5s;
											}
										</style>
										<?php
									} else {
										?>
										<div class="alert alert-info text-center" role="alert">
											Bu kategoride ürün bulunamadı.
										</div>
										<?php
									}
								}
							}
						}
						?>
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
										<?php
										// Rastgele 3 ürün çekme sorgusu
										$randomProductsSQL = "SELECT * FROM products WHERE is_active = 1 ORDER BY RAND() LIMIT 3";
										$randomProductsResult = $conn->query($randomProductsSQL);
										
										if ($randomProductsResult && $randomProductsResult->num_rows > 0) {
												while ($product = $randomProductsResult->fetch_assoc()) {
														// Her ürün için ayrı bir sorgu ile resimleri al
														$productImageSQL = "SELECT product_images_url FROM product_images WHERE product_id = " . $product['product_id'];
														$productImageResult = $conn->query($productImageSQL);
														$productImageRow = $productImageResult->fetch_assoc();
														
														// Resim URL'lerini al
														$productImagePreview = isset($productImageRow['product_images_url']) ? $productImageRow['product_images_url'] : '';
														$productImagePreviewArray = !empty($productImagePreview) ? explode('#', $productImagePreview) : ['placeholder.jpg'];
														
														// Ürün adını limitle
														$productName = limitText($product['product_name'], 25);
										?>
										<div class="product-item">
												<div class="product-title">
														<a href="product-detail.php?id=<?php echo $product['product_id']; ?>"><?php echo $productName; ?></a>
														<div class="ratting">
																<i class="fa fa-star"></i>
																<i class="fa fa-star"></i>
																<i class="fa fa-star"></i>
																<i class="fa fa-star"></i>
																<i class="fa fa-star"></i>
														</div>
												</div>
												<div class="product-image">
														<a href="product-detail.php?id=<?php echo $product['product_id']; ?>">
																<img src="<?php echo isset($productImagePreviewArray[0]) ? $productImagePreviewArray[0] : 'placeholder.jpg'; ?>" alt="<?php echo $product['product_name']; ?>">
														</a>
														<div class="product-action">
																<a href="add-to-cart.php?id=<?php echo $product['product_id']; ?>"><i class="fa fa-cart-plus"></i></a>
																<a href="add-to-wishlist.php?id=<?php echo $product['product_id']; ?>"><i class="fa fa-heart"></i></a>
																<a href="product-detail.php?id=<?php echo $product['product_id']; ?>"><i class="fa fa-search"></i></a>
														</div>
												</div>
												<div class="product-price">
														<h3><span>$</span><?php echo number_format($product['price'] / 100, 2); ?></h3>
														<a class="btn" href="add-to-cart.php?id=<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i>Buy Now</a>
												</div>
										</div>
										<?php
												}
										} else {
												echo '<div class="product-item"><div class="product-title"><p>No products found</p></div></div>';
										}
										?>
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
