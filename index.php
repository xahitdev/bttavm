<?php
require_once 'settings.php';
/* include 'functions.php'; */
error_reporting(E_ALL);

$featuredSQL = "SELECT * FROM products WHERE is_active=1 AND is_deleted=0 ORDER BY RAND() LIMIT 5";
$featuredResult = $conn->query($featuredSQL);

$sliderImageSQL = "SELECT * FROM index_images";
$sliderImageResult = $conn->query($sliderImageSQL);
$sliderImage = $sliderImageResult->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>BTT AVM - En İyi Fırsatlar</title>
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
	<?php
	include 'navbar.php';
	?>
	<!-- Main Slider Start -->
	<style>
		.category-container-ozel {
			overflow-x: auto;
			white-space: nowrap;
			padding-bottom: 10px;
		}

		.category-ozel {
			text-align: center;
			display: inline-block;
			width: 100px;
			margin: 0 10px;
		}

		.category-icon-ozel {
			width: 60px;
			height: 60px;
			background-color: rgba(0, 123, 255, 0.6);
			color: white;
			display: flex;
			align-items: center;
			justify-content: center;
			border-radius: 50%;
			font-size: 24px;
			margin: auto;
			transition: background-color 0.3s, transform 0.2s;
		}

		.category-name-ozel {
			margin-top: 8px;
			font-size: 14px;
			font-weight: 500;
			white-space: normal;
			color: black !important;
		}

		.category-icon-ozel:hover {
			background-color: rgba(0, 123, 255, 1);
			transform: scale(1.1);
		}
	</style>
	<?php
	$categoriesNavSQL = "SELECT * FROM categories WHERE category_parent_id=0";
	$categoriesNavResult = $conn->query($categoriesNavSQL);
	?>
	<div class="container mt-4 mb-4">
		<div class="category-container-ozel d-flex justify-content-center" style="padding-top: 10px !important;">
			<?php while ($row = $categoriesNavResult->fetch_assoc()) {
				echo ' 
		<a href="product-list.php?id=' . $row['category_id'] . '">
			<div class="category-ozel">
				<div class="category-icon-ozel" style="background-color: ' . $row['category_icon_color'] . ';"><i class="fas ' . $row['category_icon'] . '"></i></div>
				<div class="category-name-ozel">' . $row['category_name'] . '</div>
			</div>
		</a>
		';
			} ?>
		</div>
	</div>
	<div class="header" style="width: 100%; display: flex; justify-content: center;">
		<div class="container-fluid" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 15px;">
			<div class="row" style="margin: 0;">
				<div class="col-md-12 mt-4">
					<div id="carouselExampleControls" class="carousel slide" data-ride="carousel" style="width: 100%;">
						<div class="carousel-inner">
							<div class="carousel-item active">
								<img class="d-block w-100" src="<?php echo $sliderImage['index_slider_image'] ?>" alt="First slide" style="max-height: 500px; object-fit: cover;">
							</div>
							<div class="carousel-item">
								<img class="d-block w-100" src="<?php echo $sliderImage['index_slider_image_2'] ?>" alt="Second slide" style="max-height: 500px; object-fit: cover;">
							</div>
							<div class="carousel-item">
								<img class="d-block w-100" src="<?php echo $sliderImage['index_slider_image_3'] ?>" alt="Third slide" style="max-height: 500px; object-fit: cover;">
							</div>
						</div>
						<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
							<span class="carousel-control-prev-icon" aria-hidden="true"></span>
							<span class="sr-only">Previous</span>
						</a>
						<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
							<span class="carousel-control-next-icon" aria-hidden="true"></span>
							<span class="sr-only">Next</span>
						</a>
					</div>
				</div>
			</div>
			<div class="row" style="margin: 0; display: flex; justify-content: center; flex-wrap: wrap; margin-top: 20px;">
				<div class="col-md-6">
					<div class="header-img" style="width: 100%; margin-bottom: 20px;">
						<div class="img-item" style="position: relative; overflow: hidden;">
							<img src="<?php echo $sliderImage['card_image']; ?>" style="width: 100%; height: auto; display: block;">
							<a class="img-text" href="">
								<p>Some text goes here that describes the image</p>
							</a>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="header-img" style="width: 100%; margin-bottom: 20px;">
						<div class="img-item" style="position: relative; overflow: hidden;">
							<img src="<?php echo $sliderImage['card_image2']; ?>" style="width: 100%; height: auto; display: block;">
							<a class="img-text" href="">
								<p>Some text goes here that describes the image</p>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Main Slider End -->

	<!-- Brand Start -->
<!-- Brand Start -->
<div class="brand" style="width: 100%; display: flex; justify-content: center;">
	<div class="container-fluid" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 15px;">
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

<!-- Feature Start-->
<div class="feature" style="width: 100%; display: flex; justify-content: center;">
	<div class="container-fluid" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 15px;">
		<div class="row align-items-center" style="margin: 0;">
			<div class="col-lg-3 col-md-6 feature-col">
				<div class="feature-content">
					<i class="fab fa-cc-mastercard"></i>
					<h2>Güvensiz Ödeme</h2>
					<p>
						Lorem ipsum dolor sit amet consectetur elit
					</p>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 feature-col">
				<div class="feature-content">
					<i class="fa fa-truck"></i>
					<h2>Ülke içi teslim</h2>
					<p>
						Lorem ipsum dolor sit amet consectetur elit
					</p>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 feature-col">
				<div class="feature-content">
					<i class="fa fa-sync-alt"></i>
					<h2>İade Yok</h2>
					<p>
						Lorem ipsum dolor sit amet consectetur elit
					</p>
				</div>
			</div>
			<div class="col-lg-3 col-md-6 feature-col">
				<div class="feature-content">
					<i class="fa fa-comments"></i>
					<h2>24/7 Destek</h2>
					<p>
						Bize mail atın.
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Feature End-->

<!-- Featured Product Start -->
<div class="featured-product product" style="width: 100%; display: flex; justify-content: center;">
	<div class="container-fluid" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 15px;">
		<div class="section-header">
			<h1>Öne Çıkan Ürünler</h1>
		</div>
		<div class="row align-items-stretch product-slider product-slider-4" style="margin: 0; display: flex; flex-wrap: wrap;">
			<?php if ($featuredResult->num_rows > 0) {
				while ($row = $featuredResult->fetch_assoc()) {
					$productName = strlen($row['product_name']) > 35 ? substr($row['product_name'], 0, 35) . '...' : $row['product_name'];
					$productImagesSQL = "SELECT * FROM product_images WHERE product_id=" . $row['product_id'] . "";
					$productImagesResult = $conn->query($productImagesSQL);
					$productImage = $productImagesResult->fetch_assoc();
					$productImageData = $productImage['product_images_url'];
					$productImageArray = explode('#', $productImageData);
					echo '
				<div class="col-lg-3" style="display: flex; padding: 10px; margin-bottom: 20px;">
					<div class="product-item" style="display: flex; flex-direction: column; width: 100%; min-height: 450px; border: 1px solid #eee; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: white;">
						<div class="product-title" style="height: 80px; overflow: hidden; margin-bottom: 10px;">
							<a href="product-detail.php?id=' . $row['product_id'] . '" style="color: black; text-decoration: none; font-weight: 500; display: block; line-height: 1.3; margin-bottom: 8px;">' . $productName . '</a>
							<div class="ratting">
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
							</div>
						</div>
						<div class="product-image" style="position: relative; height: 250px; margin-bottom: 15px; overflow: hidden; border-radius: 5px;">
							<a href="product-detail.html" style="display: block; height: 100%;">
								<img src="' . $productImageArray[0] . '" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover;">
							</a>
							<div class="product-action" style="position: absolute; bottom: 10px; right: 10px; display: flex; gap: 5px;">
								<a href="#" class="add-to-cart" data-product-id="' . $row['product_id'] . '" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-cart-plus"></i></a>
								<a href="#" class="add-to-favorites" data-product-id="' . $row['product_id'] . '" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-heart"></i></a>
								<a href="product-detail.php?id=' . $row['product_id'] . '" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-search"></i></a>
							</div>
						</div>
						<div class="product-price" style="margin-top: auto; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; height: 60px;">
							<h3 style="margin: 0; font-size: 20px; color: #333; font-weight: 600;"><span style="font-size: 14px;">TL</span>' . $row['price'] . '</h3>
							<a class="btn add-to-cart-button" href="#" data-product-id="' . $row['product_id'] . '" style="background: #007bff; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;"><i class="fa fa-shopping-cart"></i>Ekle</a>

						</div>
					</div>
				</div>
			';
				}
			} ?>
		</div>
	</div>
</div>
<!-- Featured Product End -->

<!-- Newsletter Start -->
<div class="newsletter py-4" style="width: 100%; display: flex; justify-content: center;">
	<div class="container-fluid" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 15px;">
		<div class="row align-items-center" style="margin: 0;">
			<div class="col-md-6">
				<h1 class="newsletter-title">Bültenimize abone olun</h1>
			</div>
			<div class="col-md-6">
				<div class="form">
					<form method="POST" action="assets/php/newsletter.php">
						<div class="row" style="margin: 0;">
							<div class="col-md-8 mb-2 mb-md-0">
								<input type="email" class="form-control" placeholder="E-postanızı girin..." name="newsletter_email"
									required>
							</div>
							<div class="col-md-4">
								<input type="submit" class="btn btn-dark w-100" value="Abone ol" name="newsletterSend">
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Newsletter End -->

<?php
$featuredSQL = "SELECT * FROM products WHERE is_active=1 AND is_deleted=0 ORDER BY product_id DESC";
$featuredResult = $conn->query($featuredSQL);
?>
<!-- Recent Product Start -->
<div class="recent-product product" style="width: 100%; display: flex; justify-content: center;">
	<div class="container-fluid" style="max-width: 1200px; width: 100%; margin: 0 auto; padding: 0 15px;">
		<div class="section-header">
			<h1>Son Ürünler</h1>
		</div>
		<div class="row align-items-stretch product-slider product-slider-4" style="margin: 0; display: flex; flex-wrap: wrap;">
			<?php if ($featuredResult->num_rows > 0) {
				while ($row = $featuredResult->fetch_assoc()) {
					$productName = strlen($row['product_name']) > 30 ? substr($row['product_name'], 0, 30) . '...' : $row['product_name'];
					$productImagesSQL = "SELECT * FROM product_images WHERE product_id=" . $row['product_id'] . "";
					$productImagesResult = $conn->query($productImagesSQL);
					$productImage = $productImagesResult->fetch_assoc();
					$productImageData = $productImage['product_images_url'];
					$productImageArray = explode('#', $productImageData);
					echo '
				<div class="col-lg-3" style="display: flex; padding: 10px; margin-bottom: 20px;">
					<div class="product-item" style="display: flex; flex-direction: column; width: 100%; min-height: 450px; border: 1px solid #eee; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: white;">
						<div class="product-title" style="height: 80px; overflow: hidden; margin-bottom: 10px;">
							<a href="product-detail.php?id=' . $row['product_id'] . '" style="color: black; text-decoration: none; font-weight: 500; display: block; line-height: 1.3; margin-bottom: 8px;">' . $productName . '</a>
							<div class="ratting">
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
								<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
							</div>
						</div>
						<div class="product-image" style="position: relative; height: 250px; margin-bottom: 15px; overflow: hidden; border-radius: 5px;">
							<a href="product-detail.php?id=' . $row['product_id'] . '" style="display: block; height: 100%;">
								<img src="' . $productImageArray[0] . '" alt="Product Image" style="width: 100%; height: 100%; object-fit: cover;">
							</a>
							<div class="product-action" style="position: absolute; bottom: 10px; right: 10px; display: flex; gap: 5px;">
								<a href="#" class="add-to-cart" data-product-id="' . $row['product_id'] . '" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-cart-plus"></i></a>
								<a href="#" class="add-to-favorites" data-product-id="' . $row['product_id'] . '" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-heart"></i></a>
								<a href="product-detail.php?id=' . $row['product_id'] . '" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-search"></i></a>
							</div>
						</div>
						<div class="product-price" style="margin-top: auto; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; height: 60px;">
							<h3 style="margin: 0; font-size: 20px; color: #333; font-weight: 600;"><span style="font-size: 14px;">TL</span>' . $row['price'] . '</h3>
							<a class="btn add-to-cart-button" href="#" data-product-id="' . $row['product_id'] . '" style="background: #007bff; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;"><i class="fa fa-shopping-cart"></i>Ekle</a>
						</div>
					</div>
				</div>
			';
				}
			} ?>
		</div>
	</div>
</div>
	<!-- Recent Product End -->

	<!-- Review Start -->
	<!-- Review End -->

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
	<script>
	$(document).ready(function() {
			// Önce mevcut event listener'ları temizleyelim
			$('.add-to-cart').off('click');
			$('.add-to-favorites').off('click');
			$('.add-to-cart-button').off('click');
			
			// Sepete ekleme butonu (küçük yuvarlak buton)
			$(document).on('click', '.add-to-cart', function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					
					var productId = $(this).data('product-id');
					var quantity = 1;
					
					$.ajax({
							url: 'add-to-cart.php',
							type: 'POST',
							data: {
									product_id: productId,
									quantity: quantity
							},
							dataType: 'json',
							success: function(response) {
									if(response.status === 'success') {
											// Navbar'daki sepet sayısını güncelle
											$('.cart span').text('(' + response.cart_count + ')');
											
											// Başarılı mesajı göster
											alert('Ürün sepete eklendi.');
									} else {
											alert(response.message || 'Bir hata oluştu.');
									}
							},
							error: function() {
									alert('Bir hata oluştu. Lütfen tekrar deneyin.');
							}
					});
			});
			
			// Favorilere ekleme butonu
			$(document).on('click', '.add-to-favorites', function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					
					var productId = $(this).data('product-id');
					
					$.ajax({
							url: 'add-to-favorites.php',
							type: 'POST',
							data: {
									product_id: productId,
									action: 'add'
							},
							dataType: 'json',
							success: function(response) {
									if(response.status === 'success') {
											// Navbar'daki favori sayısını güncelleme
											$('.favorites span').text('(' + response.favorites_count + ')');
											
											// Kullanıcıya bildirim gösterme
											alert('Ürün favorilere eklendi!');
									} else {
											alert(response.message || 'İşlem başarısız oldu.');
									}
							},
							error: function() {
									alert('Bir hata oluştu. Lütfen tekrar deneyin.');
							}
					});
			});
			
			// Sepete Ekle butonu (alt kısımdaki mavi buton)
			$(document).on('click', '.add-to-cart-button', function(e) {
					e.preventDefault();
					e.stopImmediatePropagation();
					
					var productId = $(this).data('product-id');
					var quantity = 1;
					
					$.ajax({
							url: 'add-to-cart.php',
							type: 'POST',
							data: {
									product_id: productId,
									quantity: quantity
							},
							dataType: 'json',
							success: function(response) {
									if(response.status === 'success') {
											// Navbar'daki sepet sayısını güncelle
											$('.cart span').text('(' + response.cart_count + ')');
											
											// Başarılı mesajı göster
											alert('Ürün sepete eklendi.');
									} else {
											alert(response.message || 'Bir hata oluştu.');
									}
							},
							error: function() {
									alert('Bir hata oluştu. Lütfen tekrar deneyin.');
							}
					});
			});
	});
	</script>
</body>

</html>
