<?php
include 'settings.php';

if (isset($_GET['id'])) {
	$id = intval($_GET['id']);

	$sql = "SELECT * FROM products WHERE product_id=" . $id . " AND is_deleted=0 AND is_active=1";
	$result = $conn->query($sql);

	$getImagesSQL = "SELECT * FROM product_images WHERE product_id=" . $id;
	$getImageResult = $conn->query($getImagesSQL);
	$productDetailImage = $getImageResult->fetch_assoc();
	$productImageData = $productDetailImage['product_images_url'];
	$productImageArray = explode('#', $productImageData);

	if ($result->num_rows > 0) {
		$product = $result->fetch_assoc();
	} else {
		header("Location: index.php");
	}
}

$productPrice = number_format(doubleval($product['price']), 2, '.', ',');
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
	<?php include 'navbar.php'; ?>
	<!-- Product Detail Start -->
	<div class="product-detail">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-8">
					<div class="product-detail-top" style="background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); padding: 30px; margin-top: 20px;">
						<div class="row align-items-center">
							<div class="col-md-5">
								<div class="product-image-wrapper" style="position: relative;">
									<!-- Yeni ürün, indirim badge'leri için -->
									<div class="product-badges" style="position: absolute; top: 10px; left: 10px; z-index: 10;">
										<?php if(isset($product['is_new']) && $product['is_new'] == 1): ?>
											<span style="background: #28a745; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px; margin-right: 5px;">YENİ</span>
										<?php endif; ?>
										<?php if(isset($product['discount']) && $product['discount'] > 0): ?>
											<span style="background: #dc3545; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px;">%<?php echo $product['discount']; ?> İNDİRİM</span>
										<?php endif; ?>
									</div>
									
									<div class="product-slider-single normal-slider" style="border-radius: 10px; overflow: hidden;">
										<?php
										foreach ($productImageArray as $image) {
											if (!empty($image)) {
												echo '<img src="' . $image . '" alt="Product Image" style="width: 100%; height: 500px; object-fit: cover;">';
											}
										}
										?>
									</div>
									<div class="product-slider-single-nav normal-slider" style="margin-top: 15px; border: none !important; box-shadow: none !important; background: transparent !important;">
										<?php
										foreach ($productImageArray as $image) {
											if (!empty($image)) {
												echo '<div class="slider-nav-img" style="margin: 0 5px; cursor: pointer; border-radius: 8px; overflow: hidden; transition: all 0.3s; border: none !important;">
												<img src="' . $image . '" alt="Product Image" style="width: 100%; height: 80px; object-fit: cover; border-radius: 8px; border: none !important;" 
												onmouseover="this.style.opacity=\'0.8\';" 
												onmouseout="this.style.opacity=\'1\';">
												</div>';
											}
										}
										?>
									</div>
								</div>
							</div>
							<div class="col-md-7">
								<div class="product-content" style="padding-left: 30px;">
									<div class="title" style="margin-bottom: 15px;">
										<h2 style="font-size: 32px; font-weight: 600; color: #333; margin: 0;"><?php echo $product['product_name'] ?></h2>
									</div>
									
									<div class="ratting" style="margin-bottom: 20px;">
										<i class="fa fa-star" style="color: #ffc107; font-size: 18px;"></i>
										<i class="fa fa-star" style="color: #ffc107; font-size: 18px;"></i>
										<i class="fa fa-star" style="color: #ffc107; font-size: 18px;"></i>
										<i class="fa fa-star" style="color: #ffc107; font-size: 18px;"></i>
										<i class="fa fa-star" style="color: #ffc107; font-size: 18px;"></i>
										<span style="color: #666; margin-left: 10px;">(4.8) 125 Değerlendirme</span>
									</div>
									
									<div class="price" style="margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
										<h4 style="margin: 0; font-size: 16px; color: #666; margin-bottom: 5px;">Fiyat:</h4>
										<div style="display: flex; align-items: baseline; gap: 15px;">
											<p style="margin: 0; font-size: 36px; font-weight: 700; color: #007bff;"><?php echo $productPrice; ?><span style="font-size: 24px; margin-left: 5px;">TL</span></p>
											<?php if(isset($product['old_price']) && $product['old_price'] > $product['price']): ?>
												<p style="margin: 0; font-size: 18px; color: #999; text-decoration: line-through;"><?php echo number_format($product['old_price'], 2); ?> TL</p>
											<?php endif; ?>
										</div>
									</div>
									
									<div class="quantity" style="margin-bottom: 25px;">
										<h4 style="margin: 0; font-size: 16px; color: #666; margin-bottom: 10px;">Miktar:</h4>
										<div class="qty" style="display: inline-flex; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
											<button class="btn-minus" style="border: none; background: #f8f9fa; padding: 10px 15px; cursor: pointer; transition: all 0.3s;"
												onmouseover="this.style.background='#e9ecef'" 
												onmouseout="this.style.background='#f8f9fa'">
												<i class="fa fa-minus"></i>
											</button>
											<input type="text" value="1" id="product-quantity" style="border: none; width: 60px; text-align: center; font-size: 18px; font-weight: 600;">
											<button class="btn-plus" style="border: none; background: #f8f9fa; padding: 10px 15px; cursor: pointer; transition: all 0.3s;"
												onmouseover="this.style.background='#e9ecef'" 
												onmouseout="this.style.background='#f8f9fa'">
												<i class="fa fa-plus"></i>
											</button>
										</div>
										<span style="margin-left: 15px; color: #666;">Stok: <?php echo isset($product['stock']) ? $product['stock'] : '100'; ?> adet</span>
									</div>
									
									<div class="action" style="display: flex; gap: 15px; margin-bottom: 25px;">
										<a class="btn add-to-cart" href="#" data-product-id="<?php echo $product['product_id']; ?>" 
											style="background: #007bff; color: white; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-size: 18px; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s; flex: 1; justify-content: center;"
											onmouseover="this.style.background='#0056b3'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 5px 15px rgba(0, 123, 255, 0.3)'" 
											onmouseout="this.style.background='#007bff'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
											<i class="fa fa-shopping-cart"></i>Sepete Ekle
										</a>
										<a class="btn add-to-favorites" href="#" data-product-id="<?php echo $product['product_id']; ?>" 
											style="background: #fff; color: #dc3545; border: 2px solid #dc3545; padding: 15px; border-radius: 8px; text-decoration: none; font-size: 18px; display: inline-flex; align-items: center; justify-content: center; width: 60px; transition: all 0.3s;"
											onmouseover="this.style.background='#dc3545'; this.style.color='white'; this.style.transform='translateY(-2px)'" 
											onmouseout="this.style.background='#fff'; this.style.color='#dc3545'; this.style.transform='translateY(0)'">
											<i class="fa fa-heart"></i>
										</a>
									</div>
									
									<!-- Ek bilgiler -->
									<div class="product-features" style="border-top: 1px solid #e0e0e0; padding-top: 20px;">
										<div style="display: flex; gap: 40px; margin-bottom: 15px; flex-wrap: wrap;">
											<div style="display: flex; align-items: center; gap: 10px;">
												<i class="fa fa-truck" style="color: #28a745; font-size: 20px;"></i>
												<span>Ücretsiz Kargo</span>
											</div>
											<div style="display: flex; align-items: center; gap: 10px;">
												<i class="fa fa-undo" style="color: #007bff; font-size: 20px;"></i>
												<span>14 Gün İade Garantisi</span>
											</div>
										</div>
										<div style="display: flex; gap: 40px; flex-wrap: wrap;">
											<div style="display: flex; align-items: center; gap: 10px;">
												<i class="fa fa-shield-alt" style="color: #6c757d; font-size: 20px;"></i>
												<span>Güvenli Ödeme</span>
											</div>
											<div style="display: flex; align-items: center; gap: 10px;">
												<i class="fa fa-star" style="color: #ffc107; font-size: 20px;"></i>
												<span>Orijinal Ürün</span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row product-detail-bottom">
						<div class="col-lg-12">
							<ul class="nav nav-pills nav-justified">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="pill" href="#description">Description</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="pill" href="#reviews">Reviews (1)</a>
								</li>
							</ul>

							<div class="tab-content">
								<div id="description" class="container tab-pane active">
									<h4>Product description</h4>
									<p>
										<?php echo $product['product_description']; ?>
									</p>
								</div>
								<div id="reviews" class="container tab-pane fade">
									<div class="reviews-submitted">
										<div class="reviewer">Phasellus Gravida - <span>01 Jan 2020</span></div>
										<div class="ratting">
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
											<i class="fa fa-star"></i>
										</div>
										<p>
											Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium,
											totam rem aperiam.
										</p>
									</div>
									<div class="reviews-submit">
										<h4>Give your Review:</h4>
										<div class="ratting">
											<i class="far fa-star"></i>
											<i class="far fa-star"></i>
											<i class="far fa-star"></i>
											<i class="far fa-star"></i>
											<i class="far fa-star"></i>
										</div>
										<div class="row form">
											<div class="col-sm-6">
												<input type="text" placeholder="Name">
											</div>
											<div class="col-sm-6">
												<input type="email" placeholder="Email">
											</div>
											<div class="col-sm-12">
												<textarea placeholder="Review"></textarea>
											</div>
											<div class="col-sm-12">
												<button>Submit</button>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="product">
						<div class="section-header">
							<h1>Related Products</h1>
						</div>

						<div class="row align-items-stretch product-slider product-slider-3">
							<?php
							// Related products için rastgele 5 ürün getir (mevcut ürün hariç)
							$relatedProductsSQL = "SELECT * FROM products 
													WHERE is_active = 1 
													AND is_deleted = 0 
													AND product_id != " . $id . " 
													ORDER BY RAND() 
													LIMIT 5";
							$relatedProductsResult = $conn->query($relatedProductsSQL);
							
							if ($relatedProductsResult && $relatedProductsResult->num_rows > 0) {
								while ($relatedProduct = $relatedProductsResult->fetch_assoc()) {
									// Ürün resimlerini getir
									$productImageSQL = "SELECT product_images_url FROM product_images WHERE product_id = " . $relatedProduct['product_id'];
									$productImageResult = $conn->query($productImageSQL);
									$productImageRow = $productImageResult->fetch_assoc();
									
									$productImage = "img/no-image.jpg"; // Varsayılan resim
									if ($productImageRow && !empty($productImageRow['product_images_url'])) {
										$imageArray = explode('#', $productImageRow['product_images_url']);
										if (!empty($imageArray[0])) {
											$productImage = $imageArray[0];
										}
									}
									
									// Ürün adını kısalt
									$productName = strlen($relatedProduct['product_name']) > 30 ? substr($relatedProduct['product_name'], 0, 30) . '...' : $relatedProduct['product_name'];
									?>
									<div class="col-lg-3" style="display: flex; padding: 10px; margin-bottom: 20px;">
										<div class="product-item" style="display: flex; flex-direction: column; width: 100%; min-height: 450px; border: 1px solid #eee; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: white;">
											<div class="product-title" style="height: 80px; overflow: hidden; margin-bottom: 10px;">
												<a href="product-detail.php?id=<?php echo $relatedProduct['product_id']; ?>" style="color: black; text-decoration: none; font-weight: 500; display: block; line-height: 1.3; margin-bottom: 8px;"><?php echo $productName; ?></a>
												<div class="ratting">
													<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
													<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
													<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
													<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
													<i class="fa fa-star" style="color: #ffc107; font-size: 12px;"></i>
												</div>
											</div>
											<div class="product-image" style="position: relative; height: 250px; margin-bottom: 15px; overflow: hidden; border-radius: 5px;">
												<a href="product-detail.php?id=<?php echo $relatedProduct['product_id']; ?>" style="display: block; height: 100%;">
													<img src="<?php echo $productImage; ?>" alt="<?php echo $relatedProduct['product_name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
												</a>
												<div class="product-action" style="position: absolute; bottom: 10px; right: 10px; display: flex; gap: 5px;">
													<a href="#" class="add-to-cart" data-product-id="<?php echo $relatedProduct['product_id']; ?>" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-cart-plus"></i></a>
													<a href="#" class="add-to-favorites" data-product-id="<?php echo $relatedProduct['product_id']; ?>" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-heart"></i></a>
													<a href="product-detail.php?id=<?php echo $relatedProduct['product_id']; ?>" style="background: rgba(255,255,255,0.9); width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #333; text-decoration: none; transition: all 0.3s;"><i class="fa fa-search"></i></a>
												</div>
											</div>
											<div class="product-price" style="margin-top: auto; padding-top: 15px; border-top: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; height: 60px;">
												<h3 style="margin: 0; font-size: 20px; color: #333; font-weight: 600;"><span style="font-size: 14px;">TL</span><?php echo number_format($relatedProduct['price'], 2); ?></h3>
												<a class="btn add-to-cart-button" href="#" data-product-id="<?php echo $relatedProduct['product_id']; ?>" style="background: #007bff; color: white; padding: 8px 15px; border-radius: 5px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;"><i class="fa fa-shopping-cart"></i>Sepete Ekle</a>
											</div>
										</div>
									</div>
									<?php
								}
							} else {
								// Eğer ürün yoksa boş bir mesaj göster
								echo '<div class="col-lg-12"><p class="text-center">İlgili ürün bulunamadı.</p></div>';
							}
							?>
						</div>
					</div>
				</div>

				<!-- Side Bar Start -->
<div class="col-lg-4 sidebar">
					<div class="sidebar-widget category">
						<h2 class="title">Category</h2>
						<nav class="navbar bg-light">
							<ul class="navbar-nav">
								<?php
								// Ana kategorileri getir (parent_id = 0)
								$categoriesSQL = "SELECT * FROM categories WHERE category_parent_id = 0";
								$categoriesResult = $conn->query($categoriesSQL);
								
								if ($categoriesResult && $categoriesResult->num_rows > 0) {
									while ($category = $categoriesResult->fetch_assoc()) {
										?>
										<li class="nav-item">
											<a class="nav-link" href="product-list.php?id=<?php echo $category['category_id']; ?>">
												<i class="fas <?php echo $category['category_icon']; ?>" style="color: <?php echo $category['category_icon_color']; ?>;"></i>
												<?php echo $category['category_name']; ?>
											</a>
										</li>
										<?php
									}
								}
								?>
							</ul>
						</nav>
					</div>
					<div class="sidebar-widget widget-slider">
						<div class="sidebar-slider normal-slider">
							<?php
							// Rastgele 3 ürün çekme sorgusu
							$randomProductsSQL = "SELECT * FROM products WHERE is_active = 1 AND is_deleted = 0 ORDER BY RAND() LIMIT 3";
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
									$productName = strlen($product['product_name']) > 25 ? substr($product['product_name'], 0, 25) . '...' : $product['product_name'];
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
												<img
													src="<?php echo isset($productImagePreviewArray[0]) ? $productImagePreviewArray[0] : 'placeholder.jpg'; ?>"
													alt="<?php echo $product['product_name']; ?>">
											</a>
											<div class="product-action">
												<a href="#" class="add-to-cart" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-cart-plus"></i></a>
												<a href="#" class="add-to-favorites" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-heart"></i></a>
												<a href="product-detail.php?id=<?php echo $product['product_id']; ?>"><i class="fa fa-search"></i></a>
											</div>
										</div>
										<div class="product-price">
											<h3><span>TL</span><?php echo number_format($product['price'], 2); ?></h3>
											<a class="btn add-to-cart-button" href="#" data-product-id="<?php echo $product['product_id']; ?>"><i class="fa fa-shopping-cart"></i>Sepete Ekle</a>
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
				</div>
				<!-- Side Bar End -->
			</div>
		</div>
	</div>
	<!-- Product Detail End -->

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
	<?php include 'footer.php'; ?>
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
<script>
$(document).ready(function() {
	// Önce mevcut event listener'ları temizle
	$('.btn-minus').off('click');
	$('.btn-plus').off('click');
	$('.add-to-cart').off('click');
	$('.add-to-favorites').off('click');
	$('.add-to-cart-button').off('click');
	
	// Miktar artırma/azaltma
	$('.btn-minus').on('click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var value = parseInt($('#product-quantity').val());
		if(value > 1) {
			$('#product-quantity').val(value - 1);
		}
	});
	
	$('.btn-plus').on('click', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		var value = parseInt($('#product-quantity').val());
		$('#product-quantity').val(value + 1);
	});
	
	// Sepete ekleme butonları (hem yuvarlak hem de mavi buton için)
	$(document).on('click', '.add-to-cart, .add-to-cart-button', function(e) {
		e.preventDefault();
		e.stopImmediatePropagation();
		
		var productId = $(this).data('product-id');
		var quantity = 1; // Default quantity
		
		// Eğer ürün detay sayfasındaysak ve quantity input varsa onu kullan
		if ($('#product-quantity').length) {
			quantity = parseInt($('#product-quantity').val());
		}
		
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
					// Sepet sayısını güncelle
					if ($('.cart-count').length) {
						$('.cart-count').text(response.cart_count);
					}
					
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
});
</script>
</body>

</html>
