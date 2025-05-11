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

$productsSQL = "SELECT * FROM products WHERE seller_id = " . $seller_id . " AND is_deleted = 0";
$productResult = $conn->query($productsSQL);

//categorires SQL 
$categoriesSQL = "SELECT category_id, category_name FROM categories WHERE category_parent_id = 0";
$categoriesResult = $conn->query($categoriesSQL);

//edit products
if (isset($_POST['applyEditProductChanges'])) {
	$productid = $_POST['editProductID'];
	//al

	$sql = "SELECT * FROM products WHERE product_id = " . $productid . " AND seller_id = " . $seller_id;
	$result = $conn->query($sql);

	if ($result && $result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$productName = !empty($_POST['editProductName']) ? $_POST['editProductName'] : $row['product_name'];
		$productDescription = !empty($_POST['editProductDescription']) ? $_POST['editProductDescription'] : $row['product_description'];
		$productPrice = !empty($_POST['editProductPrice']) ? $_POST['editProductPrice'] : $row['price'];
		$productStock = !empty($_POST['editProductStock']) ? $_POST['editProductStock'] : $row['stock'];
		$productVisible = !empty($_POST['editProductVisible']) ? $_POST['editProductVisible'] : $row['is_active'];

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

// Form gönderildi mi kontrol et
if (isset($_POST['addProductButton'])) {

	// Form verilerini al
	$productName = $_POST['addProductName'];
	$productDescription = $_POST['addProductDescription'];
	$productPrice = floatval($_POST['addProductPrice']); // Kuruş olarak saklamak için
	$productStock = intval($_POST['addProductStock']);
	$categoryId = $_POST['category'][0]; // İlk kategori ID'sini al

	// Veri doğrulama
	$errors = [];

	if (empty($productName)) {
		$errors[] = "Ürün adı gereklidir.";
	}

	if (empty($productDescription)) {
		$errors[] = "Ürün açıklaması gereklidir.";
	}

	if ($productPrice <= 0) {
		$errors[] = "Geçerli bir fiyat giriniz.";
	}

	if ($productStock < 0) {
		$errors[] = "Stok miktarı negatif olamaz.";
	}

	if (empty($categoryId)) {
		$errors[] = "Kategori seçilmelidir.";
	}

	// En az bir resim yüklenmiş mi kontrol et
	if (empty($_FILES['productImage']['name'][0])) {
		$errors[] = "En az bir ürün resmi yüklemelisiniz.";
	}

	// Hata yoksa işleme devam et
	if (empty($errors)) {
		// Ürünü veritabanına ekle
		$stmt = $conn->prepare("INSERT INTO products (product_name, product_description, price, stock, category_id, seller_id, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
		$stmt->bind_param("ssdiii", $productName, $productDescription, $productPrice, $productStock, $categoryId, $seller_id);

		if ($stmt->execute()) {
			$productId = $conn->insert_id; // Yeni eklenen ürünün ID'sini al

			// Yükleme dizini
			// Yükleme dizini - dosya sistemi için
			$uploadDir = "../product-images/"; // Dosyaları kaydetmek için fiziksel yol
			$dbImageDir = "product-images/"; // Veritabanında saklamak için web yolu

			if (!file_exists($uploadDir)) {
				mkdir($uploadDir, 0777, true);
			}

			// Resimleri işle
			$uploadedImages = [];
			$totalImages = count($_FILES['productImage']['name']);

			for ($i = 0; $i < $totalImages; $i++) {
				if (!empty($_FILES['productImage']['name'][$i])) {
					$fileName = $_FILES['productImage']['name'][$i];
					$tmpName = $_FILES['productImage']['tmp_name'][$i];
					$fileSize = $_FILES['productImage']['size'][$i];
					$fileType = $_FILES['productImage']['type'][$i];

					// Dosya uzantısını al
					$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

					// İzin verilen uzantılar
					$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

					// Uzantı kontrolü
					if (in_array($fileExt, $allowedExtensions)) {
						// Benzersiz dosya adı oluştur
						$newFileName = uniqid() . '_' . time() . '.' . $fileExt;
						$targetFilePath = $uploadDir . $newFileName; // Dosyanın fiziksel yolu
						$dbFilePath = $dbImageDir . $newFileName;    // Veritabanında saklanacak yol

						// Dosyayı yükle
						if (move_uploaded_file($tmpName, $targetFilePath)) {
							// Veritabanı için web URL yolunu ekle (../product-images/ değil, product-images/)
							$uploadedImages[] = $dbFilePath;
						} else {
							$errors[] = "Resim yüklenirken bir hata oluştu: " . $fileName;
						}
					} else {
						$errors[] = "Sadece JPG, JPEG, PNG ve GIF dosyaları yükleyebilirsiniz.";
					}
				}
			}

			// Resim yollarını birleştir ve veritabanına kaydet
			if (!empty($uploadedImages)) {
				$imagesString = implode('#', $uploadedImages);

				$stmtImages = $conn->prepare("INSERT INTO product_images (product_images_url, product_id) VALUES (?, ?)");
				$stmtImages->bind_param("si", $imagesString, $productId);

				if ($stmtImages->execute()) {
					$success = "Ürün ve resimleri başarıyla eklendi.";

					// Sayfayı yeniden yükle veya yönlendir
					// header("Location: products.php");
					// exit;
				} else {
					$errors[] = "Resim bilgileri kaydedilirken bir hata oluştu: " . $stmtImages->error;
				}

				$stmtImages->close();
			}

		} else {
			$errors[] = "Ürün eklenirken bir hata oluştu: " . $stmt->error;
		}

		$stmt->close();
	}
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
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">

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
				<li class="breadcrumb-item"><a href="#">Ana Sayfa</a></li>
				<li class="breadcrumb-item"><a href="#">Satıcı Paneli</a></li>
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
						<a class="nav-link" id="orders-nav" data-toggle="pill" href="#orders-tab" role="tab"><i class="fa fa-shopping-cart"></i>Satışlarım</a>
						<a class="nav-link" id="edit-nav" data-toggle="pill" href="#edit-tab" role="tab"><i
								class="fa fa-shopping-bag"></i>Ürün Paneli</a>
						<a class="nav-link" id="account-nav" data-toggle="pill" href="#account-tab" role="tab"><i
								class="fa fa-user"></i>Hesap Detayları</a>
						<a class="nav-link" href="../logout.php"><i class="fa fa-sign-out-alt"></i>Çıkış Yap</a>
					</div>
				</div>
				<div class="col-md-9">
					<div class="tab-content">
					<div class="tab-pane fade" id="orders-tab" role="tabpanel" aria-labelledby="orders-nav">
							<h4>Satışlarım</h4>
							<?php
							// Satıcıya ait ürünlerin siparişlerini getir
							$ordersQuery = "SELECT DISTINCT o.*, c.customer_name, c.customer_mail
															FROM orders o
															JOIN order_details od ON o.order_id = od.order_id
															JOIN products p ON od.product_id = p.product_id
															JOIN customers c ON o.customer_id = c.customer_id
															WHERE p.seller_id = $seller_id
															ORDER BY o.created_at DESC";
							
							$ordersResult = $conn->query($ordersQuery);
							?>
							
							<div class="table-responsive">
									<table class="table table-bordered">
											<thead class="thead-dark">
													<tr>
															<th>Sipariş No</th>
															<th>Müşteri</th>
															<th>Tarih</th>
															<th>Miktar</th>
															<th>Durum</th>
															<th>İşlem</th>
													</tr>
											</thead>
											<tbody>
													<?php
													if ($ordersResult && $ordersResult->num_rows > 0) {
															while ($order = $ordersResult->fetch_assoc()) {
																	// Durum çevirileri
																	$status_labels = [
																			'pending' => '<span class="badge badge-warning">Beklemede</span>',
																			'processing' => '<span class="badge badge-info">İşleniyor</span>',
																			'shipped' => '<span class="badge badge-primary">Kargoda</span>',
																			'delivered' => '<span class="badge badge-success">Teslim Edildi</span>',
																			'cancelled' => '<span class="badge badge-danger">İptal Edildi</span>'
																	];
																	
																	$status = $status_labels[$order['order_status']] ?? $order['order_status'];
																	$date = date('d.m.Y H:i', strtotime($order['created_at']));
																	
																	// Sadece bu satıcıya ait ürünlerin toplam tutarını hesapla
																	$sellerTotalQuery = "SELECT SUM(od.total_price) as seller_total
																										 FROM order_details od
																										 JOIN products p ON od.product_id = p.product_id
																										 WHERE od.order_id = {$order['order_id']} 
																										 AND p.seller_id = $seller_id";
																	
																	$sellerTotalResult = $conn->query($sellerTotalQuery);
																	$sellerTotal = $sellerTotalResult->fetch_assoc()['seller_total'];
																	
																	echo "<tr>
																					<td>{$order['order_number']}</td>
																					<td>{$order['customer_name']}<br><small>{$order['customer_mail']}</small></td>
																					<td>$date</td>
																					<td>" . number_format($sellerTotal, 2) . " TL</td>
																					<td>$status</td>
																					<td>
																							<button class='btn btn-sm btn-primary view-order-details' data-order-id='{$order['order_id']}'>
																									<i class='fa fa-eye'></i> Detayları Gör 
																							</button>
																					</td>
																				</tr>";
															}
													} else {
															echo "<tr><td colspan='6' class='text-center'>No orders found for your products.</td></tr>";
													}
													?>
											</tbody>
									</table>
							</div>
							
							<!-- Order Details Modal -->
							<div class="modal fade" id="orderDetailsModal" tabindex="-1" role="dialog" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
									<div class="modal-dialog modal-lg" role="document">
											<div class="modal-content">
													<div class="modal-header">
															<h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
															<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																	<span aria-hidden="true">&times;</span>
															</button>
													</div>
													<div class="modal-body" id="orderDetailsContent">
															<!-- Order details will be loaded here -->
													</div>
													<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
													</div>
											</div>
									</div>
							</div>
						</div>
						<div class="tab-pane fade" id="edit-tab" role="tabpanel" aria-labelledby="edit-nav">
							<div class="table-responsive">
								<table class="table table-bordered">

									<head class="thead-dark">
										<tr>
											<th>Ürün ID</th>
											<th>Ürün İsim</th>
											<th>Açıklama</th>
											<th>Fiyat</th>
											<th>Stok</th>
											<th><a class="nav-link" id="add-product-nav" data-toggle="pill" href="#add-product-tab"
													role="tab"><i class="fa fa-plus"></i>Ekle</a></th>
										</tr>
									</head>

									<body>
										<form method="POST">
											<?php
											function limitHtmlText($html, $limit = 35)
											{
												// HTML etiketlerini kaldır
												$text = strip_tags($html);

												// Kısaltılacak metin uzunluğunu kontrol et
												if (strlen($text) > $limit) {
													// Metni kısalt ve "..." ekle
													$text = substr($text, 0, $limit) . "...";
												}

												return $text;
											}
											if ($productResult && $productResult->num_rows > 0) {
												while ($row = $productResult->fetch_assoc()) {
													$shortDescription = limitHtmlText($row['product_description'], 35);
													echo "<tr>";
													echo "<td> " . $row['product_id'] . " </td>";
													echo "<td> " . $row['product_name'] . " </td>";
													echo "<td> " . $shortDescription . " </td>";
													echo "<td> " . $row['price'] . " </td>";
													echo "<td> " . $row['stock'] . " </td>";
													echo "<td style='display: none'> " . $row['is_active'] . " </td>";
													echo '<td>
                <a href="edit-product.php?id=' . $row['product_id'] . '" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit"></i> Düzenle
                </a>
                <a href="delete-product.php?id=' . $row['product_id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'Are you sure you want to delete this product?\');">
                    <i class="fa fa-trash"></i> Sil
                </a>
              </td>';
													echo "</tr>";
												}
											} else {
												echo "<tr><td colspan='6' class='text-center'>No products found or query error.</td></tr>";
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
									<button class="btn" id="address-nav" data-toggle="pill" href="#address-edit-tab" role="tab">Edit
										Address</button>
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
								<?php
								// Mevcut satıcı bilgilerini getir
								$sellerQuery = "SELECT * FROM sellers WHERE seller_id = $seller_id";
								$sellerResult = $conn->query($sellerQuery);
								$seller = $sellerResult->fetch_assoc();
								?>
								
								<h4>Hesap Detayları</h4>
								
								<?php if (isset($_SESSION['update_success'])): ?>
										<div class="alert alert-success">
												<?php 
												echo $_SESSION['update_success'];
												unset($_SESSION['update_success']);
												?>
										</div>
								<?php endif; ?>
								
								<?php if (isset($_SESSION['update_error'])): ?>
										<div class="alert alert-danger">
												<?php 
												echo $_SESSION['update_error'];
												unset($_SESSION['update_error']);
												?>
										</div>
								<?php endif; ?>
								
								<form id="update-seller-form">
										<div class="row">
												<div class="col-md-6">
														<label>Satıcı isim</label>
														<input class="form-control" type="text" id="seller_name" value="<?php echo htmlspecialchars($seller['seller_name']); ?>" required>
												</div>
												<div class="col-md-6">
														<label>Email</label>
														<input class="form-control" type="email" id="seller_mail" value="<?php echo htmlspecialchars($seller['seller_mail']); ?>" required>
												</div>
												<div class="col-md-12 mt-3">
														<button type="submit" class="btn">Güncelle</button>
														<br><br>
												</div>
										</div>
								</form>
								
								<h4>Şifre Değiştir</h4>
								<form id="change-password-form">
										<div class="row">
												<div class="col-md-12">
														<label>Mevcut Şifre</label>
														<input class="form-control" type="password" id="current_password" placeholder="Mevcut şifre" required>
												</div>
												<div class="col-md-6">
														<label>Yeni Şifre</label>
														<input class="form-control" type="password" id="new_password" placeholder="Yeni şifre" required>
												</div>
												<div class="col-md-6">
														<label>Şifreyi Onayla</label>
														<input class="form-control" type="password" id="confirm_password" placeholder="Yeni şifre onayla" required>
												</div>
												<div class="col-md-12 mt-3">
														<button type="submit" class="btn">Kaydet</button>
												</div>
										</div>
								</form>
						</div>
						<div class="tab-pane fade" id="edit-product-tab" role="tabpanel" aria-labelledby="edit-product-nav">
							<h4>Edit Image of the Product</h4>
							<form method="POST">
								<div class="row">
									<div class="col-md-6 border-start" id="productImagePreview">

									</div>
									<div class="col-md-6 border-start">
										<!-- resim yukleme yeri -->
									</div>
								</div>
								<div class="row">
									<div class="col-md-6 border-start">
										<input class="form-control" id="edit_product_id" type="text" name="prepareChangeImage"
											value="<?php echo $_POST['editProductID'] ?>" hidden>
									</div>
								</div>
							</form>
							<h4>Edit Selected Product</h4>
							<form method="POST">
								<div class="row">
									<div class="col-md-6">
										<input class="form-control" id="edit_product_name" name="editProductName" type="text"
											placeholder="First Name">
									</div>
									<div class="col-md-6">
										<input class="form-control" id="edit_product_description" name="editProductDescription" type="text"
											placeholder="Last Name">
									</div>
									<div class="col-md-6">
										<input class="form-control" id="edit_price" name="editProductPrice" type="text"
											placeholder="Mobile">
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
										<input class="form-control" id="edit_product_id" name="editProductID" type="text"
											placeholder="First Name" hidden>
									</div>
									<div class="col-md-12">
										<button class="btn" type="submit" name="applyEditProductChanges">Apply Changes</button>
										<br><br>
									</div>
								</div>
							</form>
						</div>
						<div class="tab-pane fade" id="add-product-tab" role="tabpanel" aria-labelledby="add-product-nav">
							<h4 class="mb-4">Add a product...</h4>

							<?php if (!empty($errors)): ?>
								<div class="alert alert-danger">
									<ul class="mb-0">
										<?php foreach ($errors as $error): ?>
											<li><?php echo $error; ?></li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<?php if (isset($success)): ?>
								<div class="alert alert-success">
									<?php echo $success; ?>
								</div>
							<?php endif; ?>

							<form method="POST" enctype="multipart/form-data">
								<div class="row">
									<div class="col-md-6">
										<input class="form-control" name="addProductName" type="text" placeholder="Product Name" required>
									</div>
									<div class="col-md-12 mt-3">
										<label for="addProductDescription">Product Description</label>
										<textarea id="addProductDescription" name="addProductDescription"
											class="form-control summernote"></textarea>
									</div>
									<div class="col-md-6 mt-3">
										<input class="form-control" name="addProductPrice" type="text" placeholder="Price" required>
									</div>
									<div class="col-md-6 mt-3">
										<input class="form-control" name="addProductStock" type="text" placeholder="Stock" required>
									</div>

									<!-- Compact Image Upload Section -->
									<div class="col-md-12 mt-3">
										<label>Product Images</label>
										<div class="row">
											<div class="col-md-4 mb-2">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text">Main</span>
													</div>
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="productImage[]" accept="image/*"
															required>
														<label class="custom-file-label">Choose file</label>
													</div>
												</div>
											</div>
											<div class="col-md-4 mb-2">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text">Image 2</span>
													</div>
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="productImage[]" accept="image/*">
														<label class="custom-file-label">Choose file</label>
													</div>
												</div>
											</div>
											<div class="col-md-4 mb-2">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text">Image 3</span>
													</div>
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="productImage[]" accept="image/*">
														<label class="custom-file-label">Choose file</label>
													</div>
												</div>
											</div>
											<div class="col-md-4 mb-2">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text">Image 4</span>
													</div>
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="productImage[]" accept="image/*">
														<label class="custom-file-label">Choose file</label>
													</div>
												</div>
											</div>
											<div class="col-md-4 mb-2">
												<div class="input-group">
													<div class="input-group-prepend">
														<span class="input-group-text">Image 5</span>
													</div>
													<div class="custom-file">
														<input type="file" class="custom-file-input" name="productImage[]" accept="image/*">
														<label class="custom-file-label">Choose file</label>
													</div>
												</div>
											</div>
										</div>
										<small class="text-muted">First image will be the main product image.</small>
									</div>

									<!-- Categories area -->
									<div class="col-md-6 mt-3" id="category-selects">
										<select class="form-control" id="parent_category" name="category[]" required>
											<option value="">-- Select Category --</option>
											<?php if ($categoriesResult->num_rows > 0): ?>
												<?php while ($row = $categoriesResult->fetch_assoc()): ?>
													<?php if ($row['category_parent_id'] == 0): // Only parent categories ?>
														<option value="<?= htmlspecialchars($row['category_id']) ?>">
															<?= htmlspecialchars($row['category_name']) ?>
														</option>
													<?php endif; ?>
												<?php endwhile; ?>
											<?php endif; ?>
										</select>
									</div>
									<div class="col-md-12 mt-3">
										<button class="btn" type="submit" name="addProductButton">Add Product</button>
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
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

	<!-- Template Javascript -->
	<script src="js/main.js"></script>

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

		$(document).on('change', '#category-selects select', function () {
			var selectedCategoryId = $(this).val();

			// Remove all dropdowns after this one (if any)
			$(this).nextAll('select').remove();

			if (selectedCategoryId) {
				$.ajax({
					url: 'get-subcategories.php',
					type: 'POST',
					data: { parent_id: selectedCategoryId },
					success: function (response) {
						if (response.trim() !== '') {
							$('#category-selects').append(response);
						}
					}
				});
			}
		});
		$(document).ready(function () {
			// Initialize Summernote
			$('.summernote').summernote({
				placeholder: 'Enter detailed product description here...',
				height: 200,
				toolbar: [
					['style', ['style']],
					['font', ['bold', 'underline', 'clear']],
					['color', ['color']],
					['para', ['ul', 'ol', 'paragraph']],
					['table', ['table']],
					['insert', ['link']],
					['view', ['fullscreen', 'codeview', 'help']]
				]
			});

			// Display file name when selected
			$('.custom-file-input').on('change', function () {
				var fileName = $(this).val().split('\\').pop();
				$(this).next('.custom-file-label').html(fileName || 'Choose file');
			});
		});
	</script>
	<script>
	// View order details
	$(document).on('click', '.view-order-details', function() {
			var orderId = $(this).data('order-id');
			
			// Show loading
			$('#orderDetailsContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
			
			// Show modal
			$('#orderDetailsModal').modal('show');
			
			// Load order details
			$.ajax({
					url: 'get-seller-order-details.php',
					type: 'POST',
					data: { order_id: orderId },
					success: function(response) {
							$('#orderDetailsContent').html(response);
					},
					error: function() {
							$('#orderDetailsContent').html('<div class="alert alert-danger">Error loading order details.</div>');
					}
			});
	});
	</script>
	<script>
		// Satıcı bilgilerini güncelleme
		$('#update-seller-form').on('submit', function(e) {
				e.preventDefault();
				
				var formData = {
						seller_name: $('#seller_name').val(),
						seller_mail: $('#seller_mail').val(),
						seller_logo: $('#seller_logo').val(),
						address_id: $('#address_id').val()
				};
				
				$.ajax({
						url: 'update-seller.php',
						type: 'POST',
						data: formData,
						dataType: 'json',
						success: function(response) {
								if (response.status === 'success') {
										alert(response.message);
										// İsterseniz sayfayı yenileyebilirsiniz
										// location.reload();
								} else {
										alert(response.message);
								}
						},
						error: function() {
								alert('Bir hata oluştu. Lütfen tekrar deneyin.');
						}
				});
		});

		// Şifre değiştirme
		$('#change-password-form').on('submit', function(e) {
				e.preventDefault();
				
				var currentPassword = $('#current_password').val();
				var newPassword = $('#new_password').val();
				var confirmPassword = $('#confirm_password').val();
				
				// Validasyon
				if (newPassword !== confirmPassword) {
						alert('Yeni şifreler eşleşmiyor.');
						return;
				}
				
				if (newPassword.length < 6) {
						alert('Yeni şifre en az 6 karakter olmalıdır.');
						return;
				}
				
				$.ajax({
						url: 'change-seller-password.php',
						type: 'POST',
						data: {
								current_password: currentPassword,
								new_password: newPassword
						},
						dataType: 'json',
						success: function(response) {
								if (response.status === 'success') {
										alert(response.message);
										$('#change-password-form')[0].reset();
								} else {
										alert(response.message);
								}
						},
						error: function() {
								alert('Bir hata oluştu. Lütfen tekrar deneyin.');
						}
				});
		});
		</script>
</body>

</html>
