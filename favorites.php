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

// Favori ürünleri getir - Tablolarınıza uygun SQL sorgusu
$query = "SELECT f.favorite_id, f.product_id, p.product_name, p.price, p.category_id, p.stock
          FROM favorites f
          JOIN products p ON f.product_id = p.product_id
          WHERE f.customer_id = $user_id
          ORDER BY f.created_at DESC";

$result = $conn->query($query);

// Header dahil et
include 'includes/header.php';
?>

<!-- Breadcrumb Start -->
<div class="breadcrumb-wrap">
    <div class="container-fluid">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
            <li class="breadcrumb-item active">Favorilerim</li>
        </ul>
    </div>
</div>
<!-- Breadcrumb End -->

<!-- Favorites Start -->
<div class="wishlist-page">
    <div class="container-fluid">
        <div class="wishlist-page-inner">
            <div class="row">
                <div class="col-md-12">
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
                                        // Ürün resmi için - Ürün ID'sine göre ilk ürün resmini bulmaya çalışalım
                                        $productId = $row['product_id'];
                                        $imageQuery = "SELECT photo1 FROM product_images WHERE product_id = $productId LIMIT 1";
                                        $imageResult = $conn->query($imageQuery);
                                        
                                        $productImage = "img/no-image.jpg"; // Varsayılan resim
                                        
                                        if ($imageResult && $imageResult->num_rows > 0) {
                                            $imageRow = $imageResult->fetch_assoc();
                                            if (!empty($imageRow['photo1'])) {
                                                $productImage = $imageRow['photo1'];
                                            }
                                        }
                                        
                                        // Stok durumu kontrol
                                        $stockStatus = ($row['stock'] > 0) ? 'Stokta' : 'Stokta Yok';
                                        $stockClass = ($row['stock'] > 0) ? 'text-success' : 'text-danger';
                                        
                                        echo '<tr>
                                            <td>
                                                <div class="img">
                                                    <a href="product-detail.php?id=' . $row['product_id'] . '">
                                                        <img src="' . $productImage . '" alt="Ürün Resmi" style="max-width: 80px;">
                                                    </a>
                                                    <p>' . $row['product_name'] . '</p>
                                                </div>
                                            </td>
                                            <td>' . $row['price'] . ' TL</td>
                                            <td class="' . $stockClass . '">' . $stockStatus . '</td>
                                            <td>
                                                <button class="btn add-to-cart" data-product-id="' . $row['product_id'] . '"><i class="fa fa-cart-plus"></i></button>
                                                <button class="btn remove-from-favorites" data-product-id="' . $row['product_id'] . '"><i class="fa fa-trash"></i></button>
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Favorites End -->

<script>
$(document).ready(function() {
    // Favorilerden kaldır butonu tıklandığında
    $('.remove-from-favorites').on('click', function() {
        var productId = $(this).data('product-id');
        var tableRow = $(this).closest('tr');
        
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
                    $('.wishlist span').text('(' + response.favorites_count + ')');
                    
                    // Tablodan satırı kaldır
                    tableRow.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Eğer tablo boşsa mesaj göster
                        if ($('.table tbody tr').length === 0) {
                            $('.table tbody').html('<tr><td colspan="4" class="text-center">Favori listenizde henüz ürün bulunmuyor.</td></tr>');
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
    });
    
    // Sepete ekle butonu tıklandığında
    $('.add-to-cart').on('click', function() {
        var productId = $(this).data('product-id');
        
        $.ajax({
            url: 'add-to-cart.php', // Bu dosyayı oluşturmanız gerekecek
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

<?php
// Footer dahil et
include 'includes/footer.php';
?>
