<?php
session_start();
require_once 'settings.php';

// Kullanıcı giriş yapmış mı kontrol et
if (!isset($_SESSION['user_id'])) {
    // Giriş yapmamışsa giriş sayfasına yönlendir
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Sayfalama için değişkenler
$limit = 10; // Sayfa başına gösterilecek ürün sayısı
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Toplam favori sayısını al
$countQuery = "SELECT COUNT(*) AS total FROM favorites WHERE customer_id = $user_id";
$countResult = $conn->query($countQuery);
$totalRow = $countResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

// Favori ürünleri getir - sayfalı olarak
$query = "SELECT f.favorite_id, f.product_id, p.product_name, p.price, p.stock, f.created_at
          FROM favorites f
          JOIN products p ON f.product_id = p.product_id
          WHERE f.customer_id = $user_id
          ORDER BY f.created_at DESC
          LIMIT $start, $limit";

$result = $conn->query($query);

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
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400|Source+Code+Pro:700,900&display=swap" rel="stylesheet">

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
        <!-- Top bar Start -->
        <!-- Top bar End -->
        
        <!-- Nav Bar Start -->
        <!-- Nav Bar End -->      
        
        <!-- Bottom Bar Start -->
        <!-- Bottom Bar End -->
        
        <!-- Breadcrumb Start -->
        <div class="breadcrumb-wrap">
            <div class="container-fluid">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Products</a></li>
                    <li class="breadcrumb-item active">Cart</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->
        
        <!-- Cart Start -->
				<!-- Favorites Start -->
				<div class="favorites-page">
						<div class="container">
								<div class="row justify-content-center">
										<div class="col-lg-12">
												<div class="favorites-page-inner">
														<div class="table-responsive">
																<table class="table table-bordered">
																		<thead class="thead-dark">
																				<tr>
																						<th>Ürün</th>
																						<th>Fiyat</th>
																						<th>Stok Durumu</th>
																						<th>İşlem</th>
																				</tr>
																		</thead>
																		<tbody class="align-middle">
																				<?php
																				if ($result && $result->num_rows > 0) {
																						while ($row = $result->fetch_assoc()) {
																								// Her ürün için product_images tablosundan resim al
																								$productId = $row['product_id'];
																								$imageQuery = "SELECT product_images_url FROM product_images WHERE product_id = $productId LIMIT 1";
																								$imageResult = $conn->query($imageQuery);
																								
																								$productImage = "img/no-image.jpg"; // Varsayılan resim
																								
																								if ($imageResult && $imageResult->num_rows > 0) {
																										$imageRow = $imageResult->fetch_assoc();
																										if (!empty($imageRow['product_images_url'])) {
																												// # karakterinden bölerek ilk resmi al
																												$imageArray = explode('#', $imageRow['product_images_url']);
																												if (!empty($imageArray[0])) {
																														$productImage = $imageArray[0]; // İlk resmi kullan
																												}
																										}
																								}
																								
																								// Stok durumu kontrol
																								$stockStatus = ($row['stock'] > 0) ? 'Stokta' : 'Stokta Yok';
																								$stockClass = ($row['stock'] > 0) ? 'text-success' : 'text-danger';
																								
																								echo '<tr>
																										<td>
																												<div class="img">
																														<a href="product-detail.php?id=' . $row['product_id'] . '"><img src="' . $productImage . '" alt="' . $row['product_name'] . '" style="max-width: 80px;"></a>
																														<p>' . $row['product_name'] . '</p>
																												</div>
																										</td>
																										<td>' . $row['price'] . ' TL</td>
																										<td class="' . $stockClass . '">' . $stockStatus . '</td>
																										<td>
																												<button class="btn btn-sm add-to-cart" data-product-id="' . $row['product_id'] . '"><i class="fa fa-cart-plus"></i></button>
																												<button class="btn btn-sm remove-from-favorites" data-product-id="' . $row['product_id'] . '"><i class="fa fa-trash"></i></button>
																										</td>
																								</tr>';
																						}
																				} else {
																						echo '<tr><td colspan="4" class="text-center">Favori listenizde henüz ürün bulunmuyor.</td></tr>';
																				}
																				?>
																		</tbody>
																</table>
														</div>
														
														<?php if ($totalPages > 1): ?>
																<!-- Sayfalama -->
																<div class="pagination flex-m flex-w p-t-26">
																		<?php if ($page > 1): ?>
																				<a href="?page=<?php echo $page - 1; ?>" class="item-pagination flex-c-m trans-0-4 active-pagination">
																						<i class="fa fa-angle-left"></i>
																				</a>
																		<?php endif; ?>
																		
																		<?php for ($i = 1; $i <= $totalPages; $i++): ?>
																				<a href="?page=<?php echo $i; ?>" class="item-pagination flex-c-m trans-0-4 <?php echo ($i == $page) ? 'active-pagination' : ''; ?>">
																						<?php echo $i; ?>
																				</a>
																		<?php endfor; ?>
																		
																		<?php if ($page < $totalPages): ?>
																				<a href="?page=<?php echo $page + 1; ?>" class="item-pagination flex-c-m trans-0-4 active-pagination">
																						<i class="fa fa-angle-right"></i>
																				</a>
																		<?php endif; ?>
																</div>
														<?php endif; ?>
												</div>
										</div>
								</div>
						</div>
				</div>
        <!-- Cart End -->
        
				<script>
$(document).ready(function() {
    // Favorilerden kaldır butonu tıklandığında
    $('.remove-from-favorites').on('click', function() {
        var productId = $(this).data('product-id');
        var tableRow = $(this).closest('tr');
        
        if (confirm('Bu ürünü favorilerinizden kaldırmak istediğinize emin misiniz?')) {
            $.ajax({
                url: 'remove-from-favorites.php',
                type: 'POST',
                data: {
                    product_id: productId,
                    action: 'remove'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        // Favori sayısını güncelle
                        $('.favorites span').text('(' + response.favorites_count + ')');
                        
                        // Tablodan satırı kaldır
                        tableRow.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Eğer tablo boşsa mesaj göster
                            if ($('tbody tr').length === 0) {
                                $('tbody').html('<tr><td colspan="4" class="text-center">Favori listenizde henüz ürün bulunmuyor.</td></tr>');
                            }
                            
                            // Sayfa yenileme
                            if ($('tbody tr').length === 0 && '<?php echo $page; ?>' > 1) {
                                window.location.href = 'all-favorites.php?page=<?php echo max(1, $page - 1); ?>';
                            }
                        });
                        
                        // Başarılı mesajı göster
                        alert('Ürün favorilerden kaldırıldı.');
                    } else {
                        alert(response.message || 'Bir hata oluştu.');
                    }
                },
                error: function() {
                    alert('Bir hata oluştu. Lütfen tekrar deneyin.');
                }
            });
        }
    });
    
    // Sepete ekle butonu tıklandığında
    $('.add-to-cart').on('click', function() {
								var productId = $(this).data('product-id');
								
								$.ajax({
										url: 'add-to-cart.php',
										type: 'POST',
										data: {
												product_id: productId,
												quantity: 1
										},
										dataType: 'json',
										success: function(response) {
												if(response.status === 'success') {
														// Sepet sayısını güncelle (eğer varsa)
														if ($('.cart-count').length) {
																$('.cart-count').text(response.cart_count);
														}
														
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

				<style>
				/* Favoriler sayfası için ek stiller */
				.favorites-page {
						padding: 30px 0;
				}

				.favorites-page-inner {
						background: #ffffff;
						padding: 30px;
						margin: 0 auto; /* Ortalama için eklendi */
						max-width: 1000px; /* Maksimum genişlik ekleyerek daha iyi görünmesini sağlar */
						box-shadow: 0 0 10px rgba(0, 0, 0, 0.05); /* İsteğe bağlı: hafif bir gölge ekler */
				}

				.table-responsive {
						overflow-x: auto;
				}

				.table-bordered {
						border: 1px solid #dee2e6;
						margin: 0 auto; /* Tabloyu merkeze almak için */
						width: 100%; /* Tam genişlik kullanarak container'a göre ortalama */
				}

				.table thead th {
						vertical-align: bottom;
						border-bottom: 2px solid #dee2e6;
						text-align: center; /* Başlıkları ortala */
				}

				.thead-dark th {
						color: #fff;
						background-color: #343a40;
						border-color: #454d55;
				}

				.table-bordered td, .table-bordered th {
						border: 1px solid #dee2e6;
				}

				.table td, .table th {
						padding: .75rem;
						vertical-align: middle;
						border-top: 1px solid #dee2e6;
				}

				.img {
						display: flex;
						align-items: center;
				}

				.img img {
						max-width: 80px;
						margin-right: 15px;
				}

				.img p {
						margin: 0;
						font-weight: 500;
				}

				.btn-sm {
						padding: .25rem .5rem;
						font-size: .875rem;
						line-height: 1.5;
						border-radius: .2rem;
				}

				.btn-sm i {
						font-size: 1rem;
				}

				.remove-from-favorites {
						color: #e74c3c;
						margin-left: 5px;
				}

				.add-to-cart {
						color: #3498db;
				}

				.text-success {
						color: #28a745!important;
				}

				.text-danger {
						color: #dc3545!important;
				}

				/* Sayfalama stilleri */
				.pagination {
						display: flex;
						justify-content: center;
						margin-top: 20px;
				}

				.item-pagination {
						display: flex;
						justify-content: center;
						align-items: center;
						width: 36px;
						height: 36px;
						font-size: 14px;
						color: #555;
						background-color: #fff;
						border: 1px solid #e6e6e6;
						border-radius: 3px;
						margin: 0 5px;
						text-decoration: none;
						transition: all 0.3s;
				}

				.item-pagination:hover, .active-pagination {
						color: #fff;
						background-color: #333;
						border-color: #333;
				}

				/* Boş favori listesi için ortalamalı mesaj */
				.text-center {
						text-align: center;
				}
				</style>

        <!-- Footer Start -->
				<?php
				include 'footer.php';
				?>
        <!-- Footer End -->
        
        <!-- Footer Bottom Start -->
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
