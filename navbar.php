<?php
require_once 'settings.php';
include 'functions.php';
error_reporting(E_ALL);

session_start();
ob_start();

$logoSQL = "SELECT * FROM logos WHERE logo_id = 1";
$logoResult = $conn->query($logoSQL);

if ($logoResult && $logoResult->num_rows > 0) {
	$logoRow = $logoResult->fetch_assoc();
	// now you can use $logoRow['column_name']
}

$current_file = $_SERVER['REQUEST_URI'];
$current_page = $_SERVER['PHP_SELF'];

function displayEmail()
{
	global $conn;
	if ($_SESSION['role'] == 'user') {
		echo $_SESSION['mail'];
	}
	if ($_SESSION['role'] == 'seller') {
		echo $_SESSION['seller_mail'];
	}
}

?>
<head>
	<style>
		/* Favori Popup Stilleri */
			.favorites-dropdown {
					position: relative;
					display: inline-block;
			}

			.favorites-popup {
					position: absolute;
					right: 0;
					top: 100%;
					width: 350px;
					background: #fff;
					border-radius: 5px;
					box-shadow: 0 0 15px rgba(0,0,0,0.2);
					z-index: 1000;
					margin-top: 10px;
					max-height: 500px;
					overflow: hidden;
			}

			.favorites-popup-header {
					padding: 10px 15px;
					border-bottom: 1px solid #eee;
					display: flex;
					justify-content: space-between;
					align-items: center;
			}

			.favorites-popup-header h5 {
					margin: 0;
					font-size: 16px;
					font-weight: 600;
			}

			.close-favorites {
					background: none;
					border: none;
					font-size: 20px;
					cursor: pointer;
					color: #999;
			}

			.favorites-popup-body {
					padding: 10px;
					max-height: 350px;
					overflow-y: auto;
			}

			.favorite-item {
					display: flex;
					padding: 10px;
					border-bottom: 1px solid #f5f5f5;
					align-items: center;
			}

			.favorite-item img {
					width: 60px;
					height: 60px;
					object-fit: cover;
					margin-right: 10px;
			}

			.favorite-item-details {
					flex: 1;
			}

			.favorite-item-details h6 {
					margin: 0 0 5px 0;
					font-size: 14px;
			}

			.favorite-item-details .price {
					color: #FF6F61;
					font-weight: 600;
			}

			.favorite-item-actions {
					display: flex;
					gap: 5px;
			}

			.favorite-item-actions button {
					border: none;
					background: none;
					padding: 5px;
					cursor: pointer;
					color: #555;
					font-size: 14px;
			}

			.favorite-item-actions button:hover {
					color: #FF6F61;
			}

			.favorites-popup-footer {
					padding: 10px 15px;
					border-top: 1px solid #eee;
					text-align: center;
			}

			.empty-favorites {
					padding: 20px;
					text-align: center;
					color: #999;
			}
	</style>
</head>
<!-- Top bar Start -->

<div class="top-bar bg-light py-2">
	<div class="container-fluid">
		<div class="row justify-content-center text-center">
			<div class="col-sm-6 d-flex align-items-center justify-content-center">
				<i class="fa fa-envelope me-2"></i> cahitatillab@gmail.com
			</div>
			<div class="col-sm-6 d-flex align-items-center justify-content-center">
				<i class="fa fa-phone-alt me-2"></i> +90 543 668 6435
			</div>
		</div>
	</div>
</div>

<!-- Top bar End -->

<!-- Nav Bar Start -->
<div class="nav">
	<div class="container">
		<nav class="navbar navbar-expand-md bg-dark navbar-dark">
			<a href="#" class="navbar-brand">MENU</a>
			<button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarCollapse">
				<div class="container-fluid">
					<div class="row w-100">
						<!-- 🌐 Left Nav Items -->
						<div class="col-md-8 d-flex flex-wrap align-items-center navbar-nav">
							<div class="nav-item dropdown">
								<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" style="padding: 10px 15px;">Satıcı
									mısın?</a>
								<div class="dropdown-menu"
									style="background-color: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 10px;">
									<?php if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])): ?>
										<a href="seller-login.php" class="dropdown-item" style="padding: 10px 15px; font-weight: 500;">Satıcı
											Giriş</a>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- 👤 Right Account/Login Section -->
						<div class="col-md-4 d-flex justify-content-md-end mt-3 mt-md-0 navbar-nav">
							<div class="nav-item dropdown">
								<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown"
									style="padding: 10px 15px; font-weight: 500;">
<?php
if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])) {
	echo "Giriş Yap";
} else {
	displayEmail();
}
?>
								</a>
								<div class="dropdown-menu dropdown-menu-right"
									style="background-color: #fff; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 10px; min-width: 180px;">
									<?php if (isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])) { ?>
										<a href="logout.php" class="dropdown-item"
											style="padding: 10px 15px; color: #333; font-weight: 500; border-radius: 6px; transition: background 0.3s;"
											onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
											Çıkış Yap
										</a>
									<?php } else { ?>
										<a href="login.php" class="dropdown-item"
											style="padding: 10px 15px; color: #333; font-weight: 500; border-radius: 6px; transition: background 0.3s;"
											onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
											Giriş Yap
										</a>
										<a href="register.php" class="dropdown-item"
											style="padding: 10px 15px; color: #333; font-weight: 500; border-radius: 6px; transition: background 0.3s;"
											onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">
											Kayıt Ol
										</a>
									<?php } ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>


		</nav>
	</div>
</div>
<div class="bottom-bar">
	<div class="container ">
		<div class="row align-items-center">
			<div class="col-md-3">
				<div class="logo">
				<a href="<?php if (file_exists("index.php")) {
				echo "index.php";
} else {
	echo "../index.php";
}
?>">
						<img src="<?php echo $logoRow['navbar_logo']; ?>" alt="BTTAVM Logo">
					</a>
				</div>
			</div>
			<div class="col-md-6">
				<div class="search">
					<form action="product-list.php" method="GET">
						<input type="text" name="search_query" placeholder="Search">
						<button><i class="fa fa-search"></i></button>
					</form>
				</div>
			</div>
			<div class="col-md-3">
				<div class="user">
				<a href="<?php echo $_SESSION['role'] == 'user'
				? 'my-account.php'
				: (strpos($_SERVER['PHP_SELF'], 'seller/seller-panel.php') !== false ? 'seller-panel.php' : 'seller/seller-panel.php');
?>" class="btn btn-light wishlist" style="border: 0px;">
						<i class="fa fa-user"></i>
					</a>
					<?php
					// Favori sayısını hesapla
					$favorites_count = 0;
					if (isset($_SESSION['user_id'])) {
							$user_id = $_SESSION['user_id'];
							
							// Favori sayısını al
							$countQuery = "SELECT COUNT(*) AS count FROM favorites WHERE customer_id = $user_id";
							$countResult = $conn->query($countQuery);
							
							if ($countResult && $countResult->num_rows > 0) {
									$countRow = $countResult->fetch_assoc();
									$favorites_count = $countRow['count'];
							}
					}
					?>
					<div class="favorites-dropdown">
							<a href="javascript:void(0);" class="btn btn-light favorites" id="show-favorites" style="border: 0px;">
									<i class="fa fa-heart"></i>
									<span>(<?php echo $favorites_count; ?>)</span>
							</a>
							
							<!-- Favori Popup -->
							<div class="favorites-popup" id="favorites-popup" style="display: none;">
									<div class="favorites-popup-header">
											<h5>Favorilerim</h5>
											<button type="button" class="close-favorites" id="close-favorites">&times;</button>
									</div>
									<div class="favorites-popup-body">
											<!-- Burada favoriler AJAX ile yüklenecek -->
											<div id="favorites-list"></div>
									</div>
									<div class="favorites-popup-footer">
											<a href="all-favorites.php" class="btn btn-sm btn-outline-primary">Tüm Favorileri Görüntüle</a>
									</div>
							</div>
					</div>
					<?php
					// Sepetteki ürün sayısını hesapla
					$cart_count = 0;
					if (isset($_SESSION['user_id'])) {
							$user_id = $_SESSION['user_id'];
							
							// Sepet sayısını al
							$countQuery = "SELECT COUNT(*) AS count FROM cart WHERE customer_id = $user_id";
							$countResult = $conn->query($countQuery);
							
							if ($countResult && $countResult->num_rows > 0) {
									$countRow = $countResult->fetch_assoc();
									$cart_count = $countRow['count'];
							}
					}
					?>

					<a href="cart.php" class="btn btn-light cart" style="border: 0px;">
							<i class="fa fa-shopping-cart"></i>
							<span>(<?php echo $cart_count; ?>)</span>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Navbar'daki sepet sayısını güncelle
$('.cart span').text('(' + response.cart_count + ')');
</script>
<script>
$(document).ready(function() {
    // Favori ikonuna tıklandığında popupı göster
    $('#show-favorites').on('click', function(e) {
        e.preventDefault();
        loadFavorites();
        $('#favorites-popup').fadeToggle(200);
        
        // Popup dışına tıklandığında kapat
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.favorites-dropdown').length) {
                $('#favorites-popup').fadeOut(200);
                $(document).unbind('click.favorites-outside');
            }
        });
        
        e.stopPropagation();
    });
    
    // Kapat butonuna tıklandığında
    $('#close-favorites').on('click', function() {
        $('#favorites-popup').fadeOut(200);
    });
    
    // Favorileri yükle
    function loadFavorites() {
        $.ajax({
            url: 'get-favorites.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    var items = response.items;
                    var html = '';
                    
                    if(items.length === 0) {
                        html = '<div class="empty-favorites">Favori listenizde henüz ürün bulunmuyor.</div>';
                    } else {
                        for(var i = 0; i < items.length; i++) {
                            html += '<div class="favorite-item">';
                            html += '<img src="' + items[i].image + '" alt="' + items[i].name + '">';
                            html += '<div class="favorite-item-details">';
                            html += '<h6><a href="product-detail.php?id=' + items[i].id + '">' + items[i].name + '</a></h6>';
                            html += '<div class="price">' + items[i].price + ' TL</div>';
                            html += '</div>';
                            html += '<div class="favorite-item-actions">';
                            html += '<button class="add-to-cart-popup" data-product-id="' + items[i].id + '"><i class="fa fa-cart-plus"></i></button>';
                            html += '<button class="remove-from-favorites-popup" data-product-id="' + items[i].id + '"><i class="fa fa-trash"></i></button>';
                            html += '</div>';
                            html += '</div>';
                        }
                    }
                    
                    $('#favorites-list').html(html);
                    
                    // Favorilerden kaldır butonu tıklandığında
                    $('.remove-from-favorites-popup').on('click', removeFavorite);
                    
                    // Sepete ekle butonu tıklandığında
                    $('.add-to-cart-popup').on('click', addToCart);
                }
            },
            error: function() {
                $('#favorites-list').html('<div class="empty-favorites">Favorileri yüklerken bir hata oluştu.</div>');
            }
        });
    }
    
    function removeFavorite() {
        var productId = $(this).data('product-id');
        var item = $(this).closest('.favorite-item');
        
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
                    
                    // Listeden ögeyi kaldır
                    item.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Eğer liste boşsa mesaj göster
                        if ($('.favorite-item').length === 0) {
                            $('#favorites-list').html('<div class="empty-favorites">Favori listenizde henüz ürün bulunmuyor.</div>');
                        }
                    });
                } else {
                    alert(response.message || 'Bir hata oluştu.');
                }
            },
            error: function() {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        });
    }
    
    // Sepete ekleme fonksiyonu güncellenmiş hali
    function addToCart() {
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
                    // Navbar'daki sepet sayısını güncelle - ÖNEMLİ KISIM
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
    }
});
</script>
<!-- end of navbar -->
