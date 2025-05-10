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

// Sepetteki ürünleri getir
$query = "SELECT c.cart_id, c.product_id, p.product_name, p.price, c.quantity, p.stock
          FROM cart c
          JOIN products p ON c.product_id = p.product_id
          WHERE c.customer_id = $user_id
          ORDER BY c.created_at DESC";

$result = $conn->query($query);

// Sepet toplamını hesapla
$totalQuery = "SELECT SUM(c.quantity * p.price) AS subtotal 
              FROM cart c 
              JOIN products p ON c.product_id = p.product_id 
              WHERE c.customer_id = $user_id";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$subtotal = $totalRow['subtotal'] ?? 0;

// Kargo ücreti (örnek bir değer)
$shippingCost = $subtotal > 0 ? 10 : 0; // 10 TL kargo ücreti, sepet boşsa 0

// Toplam tutar
$grandTotal = $subtotal + $shippingCost;
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
        
        <!-- Breadcrumb Start -->
        <div class="breadcrumb-wrap">
            <div class="container-fluid">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
                    <li class="breadcrumb-item"><a href="products.php">Ürünler</a></li>
                    <li class="breadcrumb-item active">Sepetim</li>
                </ul>
            </div>
        </div>
        <!-- Breadcrumb End -->
        
        <!-- Cart Start -->
        <div class="cart-page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-page-inner">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Ürün</th>
                                            <th>Fiyat</th>
                                            <th>Adet</th>
                                            <th>Toplam</th>
                                            <th>Kaldır</th>
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
                                                
                                                // Ürün ara toplamı hesapla
                                                $itemTotal = $row['price'] * $row['quantity'];
                                                
                                                echo '<tr>
                                                    <td>
                                                        <div class="img">
                                                            <a href="product-detail.php?id=' . $row['product_id'] . '"><img src="' . $productImage . '" alt="' . $row['product_name'] . '"></a>
                                                            <p>' . $row['product_name'] . '</p>
                                                        </div>
                                                    </td>
                                                    <td>' . $row['price'] . ' TL</td>
                                                    <td>
                                                        <div class="qty">
                                                            <button class="btn-minus" data-product-id="' . $row['product_id'] . '"><i class="fa fa-minus"></i></button>
                                                            <input type="text" class="quantity-input" value="' . $row['quantity'] . '" data-product-id="' . $row['product_id'] . '" data-price="' . $row['price'] . '">
                                                            <button class="btn-plus" data-product-id="' . $row['product_id'] . '"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </td>
                                                    <td class="item-total" data-product-id="' . $row['product_id'] . '">' . $itemTotal . ' TL</td>
                                                    <td><button class="remove-from-cart" data-product-id="' . $row['product_id'] . '"><i class="fa fa-trash"></i></button></td>
                                                </tr>';
                                            }
                                        } else {
                                            echo '<tr><td colspan="5" class="text-center">Sepetinizde henüz ürün bulunmuyor.</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="cart-page-inner">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="coupon">
                                        <input type="text" placeholder="Kupon Kodu">
                                        <button>Kuponu Uygula</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="cart-summary">
                                        <div class="cart-content">
                                            <h1>Sepet Özeti</h1>
                                            <p>Ara Toplam<span id="cart-subtotal"><?php echo $subtotal; ?> TL</span></p>
                                            <p>Kargo Ücreti<span id="shipping-cost"><?php echo $shippingCost; ?> TL</span></p>
                                            <h2>Genel Toplam<span id="cart-total"><?php echo $grandTotal; ?> TL</span></h2>
                                        </div>
                                        <div class="cart-btn">
                                            <button id="update-cart">Sepeti Güncelle</button>
                                            <button id="checkout" onclick="window.location.href='cart.php'">Ödeme Yap</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart End -->
        
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
        
        <!-- Sepet İşlemleri JavaScript -->
				<script>
				// Cart.php içindeki JavaScript'e eklenecek
				$('#checkout').on('click', function(e) {
						e.preventDefault();
						
						if (confirm('Siparişinizi onaylıyor musunuz?')) {
								$.ajax({
										url: 'create-order.php',
										type: 'POST',
										dataType: 'json',
										success: function(response) {
												if (response.status === 'success') {
														alert('Siparişiniz başarıyla oluşturuldu! Sipariş numaranız: ' + response.order_number);
														window.location.href = 'my-account.php?tab=orders';
												} else {
														alert(response.message || 'Sipariş oluşturulurken bir hata oluştu.');
												}
										},
										error: function() {
												alert('Bir hata oluştu. Lütfen tekrar deneyin.');
										}
								});
						}
				});
				</script>
				<script>
				$(document).ready(function() {
						// Miktar arttırma butonu
						$('.btn-plus').off('click').on('click', function(e) {
								e.preventDefault();
								e.stopPropagation();
								
								var productId = $(this).data('product-id');
								var input = $(this).siblings('.quantity-input');
								var currentValue = parseInt(input.val());
								input.val(currentValue + 1);
								updateItemTotal(input);
						});
						
						// Miktar azaltma butonu
						$('.btn-minus').off('click').on('click', function(e) {
								e.preventDefault();
								e.stopPropagation();
								
								var productId = $(this).data('product-id');
								var input = $(this).siblings('.quantity-input');
								var currentValue = parseInt(input.val());
								if (currentValue > 1) {
										input.val(currentValue - 1);
										updateItemTotal(input);
								}
						});
						
						// Miktar input değiştiğinde
						$('.quantity-input').off('change').on('change', function() {
								var newValue = parseInt($(this).val());
								
								// Negatif veya NaN değerleri düzelt
								if (isNaN(newValue) || newValue < 1) {
										newValue = 1;
										$(this).val(1);
								}
								
								updateItemTotal($(this));
						});
						
						// Ürün ara toplamını güncelle
						function updateItemTotal(input) {
								var quantity = parseInt(input.val());
								var price = parseFloat(input.data('price'));
								var total = quantity * price;
								var productId = input.data('product-id');
								
								// Ürün ara toplamını göster
								$('.item-total[data-product-id="' + productId + '"]').text(total.toFixed(2) + ' TL');
								
								// Sepet toplamını güncelle
								updateCartTotal();
						}
						
						// Sepet toplamını güncelle
						function updateCartTotal() {
								var subtotal = 0;
								
								// Tüm ürünlerin ara toplamlarını topla
								$('.item-total').each(function() {
										var itemTotal = parseFloat($(this).text().replace(' TL', ''));
										subtotal += itemTotal;
								});
								
								// Kargo ücreti
								var shippingCost = subtotal > 0 ? 10 : 0;
								
								// Genel toplam
								var grandTotal = subtotal + shippingCost;
								
								// Değerleri güncelle
								$('#cart-subtotal').text(subtotal.toFixed(2) + ' TL');
								$('#shipping-cost').text(shippingCost.toFixed(2) + ' TL');
								$('#cart-total').text(grandTotal.toFixed(2) + ' TL');
						}
						
						// Sepetten kaldır butonu
						$('.remove-from-cart').off('click').on('click', function(e) {
								e.preventDefault();
								
								var productId = $(this).data('product-id');
								var tableRow = $(this).closest('tr');
								
								if (confirm('Bu ürünü sepetten kaldırmak istediğinize emin misiniz?')) {
										$.ajax({
												url: 'remove-from-cart.php',
												type: 'POST',
												data: {
														product_id: productId
												},
												dataType: 'json',
												success: function(response) {
														if(response.status === 'success') {
																// Navbar'daki sepet sayısını güncelle
																$('.cart span').text('(' + response.cart_count + ')');
																
																// Tablodan satırı kaldır
																tableRow.fadeOut(300, function() {
																		$(this).remove();
																		
																		// Eğer tablo boşsa mesaj göster
																		if ($('tbody tr').length === 0) {
																				$('tbody').html('<tr><td colspan="5" class="text-center">Sepetinizde henüz ürün bulunmuyor.</td></tr>');
																				
																				// Sepet özetini sıfırla
																				$('#cart-subtotal').text('0.00 TL');
																				$('#shipping-cost').text('0.00 TL');
																				$('#cart-total').text('0.00 TL');
																		} else {
																				// Sepet toplamını güncelle
																				updateCartTotal();
																		}
																});
																
																// Başarılı mesajı
																alert('Ürün sepetten kaldırıldı.');
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
						
						// Sepeti güncelle butonu
						$('#update-cart').off('click').on('click', function(e) {
								e.preventDefault();
								
								var updates = [];
								
								// Tüm ürünlerin güncel miktarlarını al
								$('.quantity-input').each(function() {
										var productId = $(this).data('product-id');
										var quantity = parseInt($(this).val());
										
										updates.push({
												product_id: productId,
												quantity: quantity
										});
								});
								
								// Tüm güncellemeleri tek bir istekte gönder
								$.ajax({
										url: 'update-cart.php',
										type: 'POST',
										data: {
												updates: JSON.stringify(updates)
										},
										dataType: 'json',
										success: function(response) {
												if(response.status === 'success') {
														// Navbar'daki sepet sayısını güncelle
														if (response.cart_count !== undefined) {
																$('.cart span').text('(' + response.cart_count + ')');
														}
														
														// Başarılı mesajı
														alert('Sepet güncellendi.');
														
														// Sayfayı yenilemek yerine dinamik güncelleme yapabiliriz
														// location.reload(); // Bu satırı kaldırdık
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
        
        <!-- Template Javascript -->
        <script src="js/main.js"></script>
    </body>
</html>
