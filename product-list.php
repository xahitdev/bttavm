<?php
require_once 'settings.php';

// Fiyat aralığı ve sıralama parametreleri
$price_min = isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (int)$_GET['price_max'] : PHP_INT_MAX;
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : '';

// SQL için ek koşullar
$price_condition = " AND price BETWEEN " . $price_min . " AND " . $price_max;
$order_by = "";

switch($sort_by) {
    case 'newest':
        $order_by = " ORDER BY product_id DESC";
        break;
    case 'price_low':
        $order_by = " ORDER BY price ASC";
        break;
    case 'price_high':
        $order_by = " ORDER BY price DESC";
        break;
    default:
        $order_by = " ORDER BY product_id DESC";
}

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
    
    <style>
        .product-price-range form {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .product-price-range .input-group {
            margin: 0;
        }

        .product-price-range input.form-control {
            width: 70px !important;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            margin-right: 5px;
        }

        .product-price-range .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .product-item:hover .product-action {
            opacity: 1;
        }

        .product-item:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transition: .5s;
        }
    </style>
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
                <li class="breadcrumb-item"><a href="index.php">Anasayfa</a></li>
                <li class="breadcrumb-item"><a href="#">Ürünler</a></li>
                <li class="breadcrumb-item active">Ürün Listesi</li>
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
                                            <form action="product-list.php" method="get">
                                                <input type="text" name="search_query" placeholder="Ürün Ara" value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                                                <?php if(isset($_GET['id'])): ?>
                                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                                <?php endif; ?>
                                                <button type="submit"><i class="fa fa-search"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="product-short">
                                            <div class="dropdown">
                                                <div class="dropdown-toggle" data-toggle="dropdown">Sıralama</div>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="?sort=newest<?php echo isset($_GET['id']) ? '&id='.$_GET['id'] : ''; ?><?php echo isset($_GET['search_query']) ? '&search_query='.$_GET['search_query'] : ''; ?>" class="dropdown-item">En Yeni</a>
                                                    <a href="?sort=price_low<?php echo isset($_GET['id']) ? '&id='.$_GET['id'] : ''; ?><?php echo isset($_GET['search_query']) ? '&search_query='.$_GET['search_query'] : ''; ?>" class="dropdown-item">Fiyat: Düşükten Yükseğe</a>
                                                    <a href="?sort=price_high<?php echo isset($_GET['id']) ? '&id='.$_GET['id'] : ''; ?><?php echo isset($_GET['search_query']) ? '&search_query='.$_GET['search_query'] : ''; ?>" class="dropdown-item">Fiyat: Yüksekten Düşüğe</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="product-price-range">
                                            <form action="product-list.php" method="get" class="form-inline">
                                                <?php if(isset($_GET['id'])): ?>
                                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                                <?php endif; ?>
                                                <?php if(isset($_GET['search_query'])): ?>
                                                    <input type="hidden" name="search_query" value="<?php echo htmlspecialchars($_GET['search_query']); ?>">
                                                <?php endif; ?>
                                                <?php if(isset($_GET['sort'])): ?>
                                                    <input type="hidden" name="sort" value="<?php echo $_GET['sort']; ?>">
                                                <?php endif; ?>
                                                
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="price_min" class="form-control" placeholder="Min TL" value="<?php echo isset($_GET['price_min']) ? $_GET['price_min'] : ''; ?>">
                                                    <input type="number" name="price_max" class="form-control" placeholder="Max TL" value="<?php echo isset($_GET['price_max']) ? $_GET['price_max'] : ''; ?>">
                                                    <div class="input-group-append">
                                                        <button type="submit" class="btn btn-sm btn-primary">Filtrele</button>
                                                    </div>
                                                </div>
                                            </form>
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
                            $productsSQL = "SELECT * FROM products WHERE (product_name LIKE '%$search_query%' OR product_description LIKE '%$search_query%') AND is_active = 1 AND is_deleted=0" . $price_condition . $order_by;
                            $productsResult = $conn->query($productsSQL);

                            // Sonuçları göster
                            if ($productsResult && $productsResult->num_rows > 0) {
                                ?>
                                <div class="container pt-4 pb-3">
                                    <div class="text-center mb-4">
                                        <h2 class="section-title px-5"><span class="px-2">Arama Sonuçları: "<?php echo htmlspecialchars($search_query); ?>"</span></h2>
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
                                                                <a class="btn btn-outline-dark btn-sm rounded-circle add-to-cart"
                                                                    href="#" data-product-id="<?php echo $row['product_id']; ?>"
                                                                    style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
                                                                    <i class="fa fa-cart-plus"></i>
                                                                </a>
                                                                <a class="btn btn-outline-dark btn-sm rounded-circle add-to-favorites"
                                                                    href="#" data-product-id="<?php echo $row['product_id']; ?>"
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
                                                                <span>TL</span><?php echo number_format($row['price'], 2); ?>
                                                            </h6>
                                                            <a href="#" class="btn btn-sm btn-primary add-to-cart" data-product-id="<?php echo $row['product_id']; ?>">
                                                                <i class="fa fa-shopping-cart mr-1"></i>Satın Al
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
                                <?php
                            } else {
                                ?>
                                <div class="container-fluid pt-5">
                                    <div class="alert alert-info text-center" role="alert">
                                        "<?php echo htmlspecialchars($search_query); ?>" için sonuç bulunamadı.
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
                                    $productsSQL = "SELECT * FROM products WHERE category_id IN ($categoryIdsList) AND is_active = 1 AND is_deleted=0" . $price_condition . $order_by;
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
                                                                    <a class="btn btn-outline-dark btn-sm rounded-circle add-to-cart"
                                                                        href="#" data-product-id="<?php echo $row['product_id']; ?>"
                                                                        style="width: 40px; height: 40px; line-height: 40px; display: inline-flex; align-items: center; justify-content: center; margin: 0 3px;">
                                                                        <i class="fa fa-cart-plus"></i>
                                                                    </a>
                                                                    <a class="btn btn-outline-dark btn-sm rounded-circle add-to-favorites"
                                                                        href="#" data-product-id="<?php echo $row['product_id']; ?>"
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
                                                                    <span>TL</span><?php echo number_format($row['price'], 2); ?>
                                                                </h6>
                                                                <a href="#" class="btn btn-sm btn-primary add-to-cart" data-product-id="<?php echo $row['product_id']; ?>">
                                                                    <i class="fa fa-shopping-cart mr-1"></i>Satın Al
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
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
                                        <a class="page-link" href="#" tabindex="-1">Önceki</a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">Sonraki</a>
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
                        <h2 class="title">Kategoriler</h2>
                        <nav class="navbar bg-light">
                            <ul class="navbar-nav">
                                <?php
                                // Ana kategorileri getir
                                $mainCategoriesSQL = "SELECT * FROM categories WHERE category_parent_id = 0 LIMIT 5";
                                $mainCategoriesResult = $conn->query($mainCategoriesSQL);
                                
                                if ($mainCategoriesResult && $mainCategoriesResult->num_rows > 0) {
                                    while ($category = $mainCategoriesResult->fetch_assoc()) {
                                        echo '<li class="nav-item">
                                                <a class="nav-link" href="product-list.php?id=' . $category['category_id'] . '">
                                                    <i class="fa fa-angle-right"></i>' . $category['category_name'] . '
                                                </a>
                                              </li>';
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
																$productName = limitText($product['product_name'], 25);
																?>
																<div class="product-item" style="height: 400px; width: 100%; display: flex; flex-direction: column;">
																		<div class="product-title" style="height: 80px; overflow: hidden;">
																				<a href="product-detail.php?id=<?php echo $product['product_id']; ?>" style="color: black;"><?php echo $productName; ?></a>
																				<div class="ratting">
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																				</div>
																		</div>
																		<div class="product-image" style="height: 220px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
																				<a href="product-detail.php?id=<?php echo $product['product_id']; ?>">
																						<img
																								src="<?php echo isset($productImagePreviewArray[0]) ? $productImagePreviewArray[0] : 'placeholder.jpg'; ?>"
																								alt="<?php echo $product['product_name']; ?>"
																								style="width: 100%; height: 100%; object-fit: contain;">
																				</a>
																				<div class="product-action">
																						<a href="#" class="add-to-cart" data-product-id="<?php echo $product['product_id']; ?>" style="background-color: #ffffff; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#ffbe33'" onmouseout="this.style.backgroundColor='#ffffff'"><i class="fa fa-cart-plus"></i></a>
																						<a href="#" class="add-to-favorites" data-product-id="<?php echo $product['product_id']; ?>" style="background-color: #ffffff; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#ffbe33'" onmouseout="this.style.backgroundColor='#ffffff'"><i class="fa fa-heart"></i></a>
																						<a href="product-detail.php?id=<?php echo $product['product_id']; ?>" style="background-color: #ffffff; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#ffbe33'" onmouseout="this.style.backgroundColor='#ffffff'"><i class="fa fa-search"></i></a>
																				</div>
																		</div>
																		<div class="product-price" style="height: 100px; display: flex; flex-direction: column; justify-content: center;">
																				<h3 style="color: black;"><span>TL</span><?php echo number_format($product['price'], 2); ?></h3>
																				<a class="btn add-to-cart" href="#" data-product-id="<?php echo $product['product_id']; ?>" style="color: black; background-color: #ffffff; transition: background-color 0.3s ease;" onmouseover="this.style.backgroundColor='#ffbe33'" onmouseout="this.style.backgroundColor='#ffffff'"><i class="fa fa-shopping-cart"></i>Satın Al</a>
																		</div>
																</div>
																<?php
														}
												} else {
														echo '<div class="product-item"><div class="product-title"><p>Ürün bulunamadı</p></div></div>';
												}
												?>
										</div>
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

<!-- Product List JavaScript -->
<script>
$(document).ready(function() {
    // Sepete ekleme
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        
        var productId = $(this).data('product-id');
        var quantity = 1; // Varsayılan miktar
        
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
    
    // Favorilere ekleme
    $(document).on('click', '.add-to-favorites', function(e) {
        e.preventDefault();
        
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
                    
                    // İkonu değiştir
                    $(this).find('i').removeClass('fa-heart').addClass('fa-heart text-danger');
                    
                    // Başarılı mesajı göster
                    alert('Ürün favorilere eklendi!');
                } else {
                    alert(response.message || 'Bir hata oluştu.');
                }
            }.bind(this),
            error: function() {
                alert('Bir hata oluştu. Lütfen tekrar deneyin.');
            }
        });
    });
});
</script>
</body>
</html>

