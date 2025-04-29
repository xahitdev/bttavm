<?php
require_once 'settings.php';
/* include 'functions.php'; */
error_reporting(E_ALL);


$featuredSQL = "SELECT * FROM products ORDER BY RAND() LIMIT 5";
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
		}

		.category-icon-ozel:hover {
			background-color: rgba(0, 123, 255, 1); 
			transform: scale(1.1); 
		}
</style> 
	<div class="container mt-4 mb-4">
	<div class="category-container-ozel d-flex" style="padding-top: 10px !important;">
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(0, 123, 255, 0.6);"><i class="fas fa-shopping-cart"></i></div>
			<div class="category-name-ozel">Alışveriş</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(40, 167, 69, 0.6);"><i class="fas fa-utensils"></i></div>
			<div class="category-name-ozel">Yemek</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(220, 53, 69, 0.6);"><i class="fas fa-film"></i></div>
			<div class="category-name-ozel">Eğlence</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(255, 193, 7, 0.6);"><i class="fas fa-dumbbell"></i></div>
			<div class="category-name-ozel">Spor</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(23, 162, 184, 0.6);"><i class="fas fa-gamepad"></i></div>
			<div class="category-name-ozel">Oyun</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(111, 66, 193, 0.6);"><i class="fas fa-music"></i></div>
			<div class="category-name-ozel">Müzik</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(108, 117, 125, 0.6);"><i class="fas fa-book"></i></div>
			<div class="category-name-ozel">Kitap</div>
		</div>
		<div class="category-ozel">
			<div class="category-icon-ozel" style="background-color: rgba(52, 58, 64, 0.6);"><i class="fas fa-plane"></i></div>
			<div class="category-name-ozel">Seyahat</div>
		</div>
	</div>
</div>
	<div class="header">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
				<div class="header-slider normal-slider" style="height: 500px;"> <!-- Set container height -->
					  <div class="header-slider-item" style="
						position: relative;
						height: 100%;
						display: flex;
						justify-content: center;
						align-items: center;
					  ">
					  <img src="<?php echo $sliderImage['index_slider_image'] ?>" alt="Slider Image" style="
						  max-width: 100%;
						  max-height: 100%;
						  object-fit: contain;
						">
						<div class="header-slider-caption" style="
						  position: absolute;
						  bottom: 20px;
						  left: 50%;
						  transform: translateX(-50%);
						  text-align: center;
						">
						  <p>Find the best prices!</p>
						  <a class="btn" href=""><i class="fa fa-shopping-cart"></i>Shop Now</a>
						</div>
					  </div>

					  <!-- Repeat for other slider items -->
					  <div class="header-slider-item" style="
						position: relative;
						height: 100%;
						display: flex;
						justify-content: center;
						align-items: center;
					  ">
						<img src="<?php echo $sliderImage['index_slider_image_2'] ?>" alt="Slider Image" style="
						  max-width: 100%;
						  max-height: 100%;
						  object-fit: contain;
						">
						<!-- ... caption ... -->
					  </div>

					  <div class="header-slider-item" style="
						position: relative;
						height: 100%;
						display: flex;
						justify-content: center;
						align-items: center;
					  ">
						<img src="<?php echo $sliderImage['index_slider_image_3'] ?>" alt="Slider Image" style="
						  max-width: 100%;
						  max-height: 100%;
						  object-fit: contain;
						">
						<!-- ... caption ... -->
					  </div>
					</div>
				</div>
				<div class="row w-100">
					<div class="col-md-6">
						<div class="header-img">
							<div class="img-item">
								<img src="<?php echo $sliderImage['card_image']; ?>">
								<a class="img-text" href="">
									<p>Some text goes here that describes the image</p>
								</a>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="header-img">
							<div class="img-item">
								<img src="<?php echo $sliderImage['card_image2']; ?>">
								<a class="img-text" href="">
									<p>Some text goes here that describes the image</p>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Main Slider End -->

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

	<!-- Feature Start-->
	<div class="feature">
		<div class="container-fluid">
			<div class="row align-items-center">
				<div class="col-lg-3 col-md-6 feature-col">
					<div class="feature-content">
						<i class="fab fa-cc-mastercard"></i>
						<h2>Secure Payment</h2>
						<p>
							Lorem ipsum dolor sit amet consectetur elit
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 feature-col">
					<div class="feature-content">
						<i class="fa fa-truck"></i>
						<h2>Worldwide Delivery</h2>
						<p>
							Lorem ipsum dolor sit amet consectetur elit
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 feature-col">
					<div class="feature-content">
						<i class="fa fa-sync-alt"></i>
						<h2>90 Days Return</h2>
						<p>
							Lorem ipsum dolor sit amet consectetur elit
						</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 feature-col">
					<div class="feature-content">
						<i class="fa fa-comments"></i>
						<h2>24/7 Support</h2>
						<p>
							Lorem ipsum dolor sit amet consectetur elit
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Feature End-->

	<!-- Category Start-->
	<!--
	<div class="category">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<div class="category-item ch-400">
						<img src="img/category-3.jpg" />
						<a class="category-name" href="">
							<p>Some text goes here that describes the image</p>
						</a>
					</div>
				</div>
				<div class="col-md-3">
					<div class="category-item ch-250">
						<img src="img/category-4.jpg" />
						<a class="category-name" href="">
							<p>Some text goes here that describes the image</p>
						</a>
					</div>
					<div class="category-item ch-150">
						<img src="img/category-5.jpg" />
						<a class="category-name" href="">
							<p>Some text goes here that describes the image</p>
						</a>
					</div>
				</div>
				<div class="col-md-3">
					<div class="category-item ch-150">
						<img src="img/category-6.jpg" />
						<a class="category-name" href="">
							<p>Some text goes here that describes the image</p>
						</a>
					</div>
					<div class="category-item ch-250">
						<img src="img/category-7.jpg" />
						<a class="category-name" href="">
							<p>Some text goes here that describes the image</p>
						</a>
					</div>
				</div>
				<div class="col-md-3">
					<div class="category-item ch-400">
						<img src="img/category-8.jpg" />
						<a class="category-name" href="">
							<p>Some text goes here that describes the image</p>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	-->
	<!-- Category End-->

	<!-- Call to Action Start -->
	<div class="call-to-action">
		<div class="container-fluid">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h1>call us for any queries</h1>
				</div>
				<div class="col-md-6">
					<a href="tel:0123456789">+012-345-6789</a>
				</div>
			</div>
		</div>
	</div>
	<!-- Call to Action End -->

	<!-- Featured Product Start -->
	<div class="featured-product product">
		<div class="container-fluid">
			<div class="section-header">
				<h1>Featured Product</h1>
			</div>
			<div class="row align-items-center product-slider product-slider-4">
			<?php if($featuredResult->num_rows > 0){
				while($row = $featuredResult->fetch_assoc()){
					$productName = strlen($row['product_name']) > 30 ? substr($row['product_name'], 0, 30) . '...' : $row['product_name'];

					$productImagesSQL = "SELECT * FROM product_images WHERE product_id=".$row['product_id']."";
					$productImagesResult = $conn->query($productImagesSQL);
					$productImage = $productImagesResult->fetch_assoc();
					$productImageData = $productImage['product_images_url'];
					$productImageArray = explode('#', $productImageData);
					
					echo '
					<div class="col-lg-3">
						<div class="product-item">
							<div class="product-title">
								<a href="product-detail.php?id='.$row['product_id'].'">'.$productName.'</a>
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
									<img src="'.$productImageArray[0].'" alt="Product Image" style="width: 100%; height: 250px; object-fit: cover; !important">
								</a>
								<div class="product-action">
									<a href="#"><i class="fa fa-cart-plus"></i></a>
									<a href="#"><i class="fa fa-heart"></i></a>
									<a href="#"><i class="fa fa-search"></i></a>
								</div>
							</div>
							<div class="product-price">
								<h3><span>TL</span>'.$row['price'].'</h3>
								<a class="btn" href=""><i class="fa fa-shopping-cart"></i>Buy</a>
							</div>
						</div>
					</div>
				';}} ?>
			</div>
		</div>
	</div>
	<!-- Featured Product End -->

	<!-- Newsletter Start -->
	<div class="newsletter py-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="newsletter-title">Subscribe Our Newsletter</h1>
            </div>
            <div class="col-md-6">
                <div class="form">
                    <form method="POST" action="assets/php/newsletter.php">
                        <div class="row">
                            <div class="col-md-8 mb-2 mb-md-0">
                                <input type="email" class="form-control" placeholder="Your email here" name="newsletter_email" required>
                            </div>
                            <div class="col-md-4">
                                <input type="submit" class="btn btn-dark w-100" value="Subscribe" name="newsletterSend">
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
		$featuredSQL = "SELECT * FROM products ORDER BY product_id DESC";
		$featuredResult = $conn->query($featuredSQL);
	?>
	<!-- Recent Product Start -->
	<div class="recent-product product">
		<div class="container-fluid">
			<div class="section-header">
				<h1>Recent Product</h1>
			</div>
			<div class="row align-items-center product-slider product-slider-4">
			<?php if($featuredResult->num_rows > 0){
				while($row = $featuredResult->fetch_assoc()){
					$productName = strlen($row['product_name']) > 30 ? substr($row['product_name'], 0, 30) . '...' : $row['product_name'];

					$productImagesSQL = "SELECT * FROM product_images WHERE product_id=".$row['product_id']."";
					$productImagesResult = $conn->query($productImagesSQL);
					$productImage = $productImagesResult->fetch_assoc();
					$productImageData = $productImage['product_images_url'];
					$productImageArray = explode('#', $productImageData);
					
					echo '
					<div class="col-lg-3">
						<div class="product-item">
							<div class="product-title">
								<a href="product-detail.php?id='.$row['product_id'].'">'.$productName.'</a>
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
									<img src="'.$productImageArray[0].'" alt="Product Image" style="width: 100%; height: 250px; object-fit: cover; !important">
								</a>
								<div class="product-action">
									<a href="#"><i class="fa fa-cart-plus"></i></a>
									<a href="#"><i class="fa fa-heart"></i></a>
									<a href="#"><i class="fa fa-search"></i></a>
								</div>
							</div>
							<div class="product-price">
								<h3><span>TL</span>'.$row['price'].'</h3>
								<a class="btn" href=""><i class="fa fa-shopping-cart"></i>Buy</a>
							</div>
						</div>
					</div>
				';}} ?>
			</div>
		</div>
	</div>
	<!-- Recent Product End -->

	<!-- Review Start -->
	<!--
	<div class="review">
		<div class="container-fluid">
			<div class="row align-items-center review-slider normal-slider">
				<div class="col-md-6">
					<div class="review-slider-item">
						<div class="review-img">
							<img src="img/review-1.jpg" alt="Image">
						</div>
						<div class="review-text">
							<h2>Customer Name</h2>
							<h3>Profession</h3>
							<div class="ratting">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur vitae nunc eget leo
								finibus luctus et vitae lorem
							</p>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="review-slider-item">
						<div class="review-img">
							<img src="img/review-2.jpg" alt="Image">
						</div>
						<div class="review-text">
							<h2>Customer Name</h2>
							<h3>Profession</h3>
							<div class="ratting">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur vitae nunc eget leo
								finibus luctus et vitae lorem
							</p>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="review-slider-item">
						<div class="review-img">
							<img src="img/review-3.jpg" alt="Image">
						</div>
						<div class="review-text">
							<h2>Customer Name</h2>
							<h3>Profession</h3>
							<div class="ratting">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<p>
								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur vitae nunc eget leo
								finibus luctus et vitae lorem
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	-->
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
</body>

</html>
