<?php
// Veritabanı bağlantısı
include '../settings.php';

// Oturum kontrolü (ihtiyaca göre)
session_start();
if (!isset($_SESSION['seller_id'])) {
    header("Location: login.php");
    exit;
}

$seller_id = $_SESSION['seller_id'];
$errors = [];
$success = '';

// ID kontrolü
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php?error=invalid_id");
    exit;
}

$product_id = intval($_GET['id']);

// Ürünün bu satıcıya ait olduğunu kontrol et
$checkSQL = "SELECT * FROM products WHERE product_id = ? AND seller_id = ?";
$stmt = $conn->prepare($checkSQL);
$stmt->bind_param("ii", $product_id, $seller_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php?error=unauthorized");
    exit;
}

// Ürün bilgilerini al
$product = $result->fetch_assoc();

// Ürün resimlerini al
$imageSQL = "SELECT product_images_url FROM product_images WHERE product_id = ?";
$stmtImg = $conn->prepare($imageSQL);
$stmtImg->bind_param("i", $product_id);
$stmtImg->execute();
$imgResult = $stmtImg->get_result();
$productImages = [];

if ($imgResult->num_rows > 0) {
    $imgRow = $imgResult->fetch_assoc();
    $imagesString = $imgRow['product_images_url'];
    if (!empty($imagesString)) {
        $productImages = explode('#', $imagesString);
    }
}

// Form gönderildi mi kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['updateProduct'])) {
    // Form verilerini al
    $productName = $_POST['productName'];
    $productDescription = $_POST['productDescription'];
    $productPrice = floatval($_POST['productPrice']) * 100; // Kuruş olarak saklamak için
    $productStock = intval($_POST['productStock']);
    $categoryId = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;

    // Veri doğrulama
    if (empty($productName)) {
        $errors[] = "Product name is required.";
    }

    if (empty($productDescription)) {
        $errors[] = "Product description is required.";
    }

    if ($productPrice <= 0) {
        $errors[] = "Please enter a valid price.";
    }

    if ($productStock < 0) {
        $errors[] = "Stock cannot be negative.";
    }

    if (empty($categoryId)) {
        $errors[] = "Category must be selected.";
    }

    // Hata yoksa güncelleme yap
    if (empty($errors)) {
        // Ürün bilgilerini güncelle
        $updateSQL = "UPDATE products SET 
                        product_name = ?, 
                        product_description = ?, 
                        price = ?, 
                        stock = ?, 
                        category_id = ? 
                      WHERE product_id = ?";

        $updateStmt = $conn->prepare($updateSQL);
        $updateStmt->bind_param("ssdiii", $productName, $productDescription, $productPrice, $productStock, $categoryId, $product_id);

        if ($updateStmt->execute()) {
            // Yeni resimler yüklendi mi kontrol et
            if (isset($_FILES['productImage']) && !empty($_FILES['productImage']['name'][0])) {
                $uploadDir = "../product-images";
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Yüklenen resimleri işle
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
                            $targetFilePath = $uploadDir . $newFileName;

                            // Dosyayı yükle
                            if (move_uploaded_file($tmpName, $targetFilePath)) {
                                $uploadedImages[] = $targetFilePath;
                            } else {
                                $errors[] = "Error uploading image: " . $fileName;
                            }
                        } else {
                            $errors[] = "Only JPG, JPEG, PNG and GIF files are allowed.";
                        }
                    }
                }

                // Yeni resimler varsa veritabanını güncelle
                if (!empty($uploadedImages)) {
                    $imagesString = implode('#', $uploadedImages);

                    // Önceki resim kaydını kontrol et
                    $checkImgSQL = "SELECT * FROM product_images WHERE product_id = ?";
                    $checkImgStmt = $conn->prepare($checkImgSQL);
                    $checkImgStmt->bind_param("i", $product_id);
                    $checkImgStmt->execute();
                    $checkImgResult = $checkImgStmt->get_result();

                    if ($checkImgResult->num_rows > 0) {
                        // Varolan kaydı güncelle
                        $updateImgSQL = "UPDATE product_images SET product_images_url = ? WHERE product_id = ?";
                        $updateImgStmt = $conn->prepare($updateImgSQL);
                        $updateImgStmt->bind_param("si", $imagesString, $product_id);
                        $updateImgStmt->execute();
                    } else {
                        // Yeni kayıt ekle
                        $insertImgSQL = "INSERT INTO product_images (product_images_url, product_id) VALUES (?, ?)";
                        $insertImgStmt = $conn->prepare($insertImgSQL);
                        $insertImgStmt->bind_param("si", $imagesString, $product_id);
                        $insertImgStmt->execute();
                    }
                }
            }

            $success = "Product successfully updated!";

            // Güncellenen ürün bilgilerini yeniden al
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();

            // Ürün resimlerini yeniden al
            $stmtImg->execute();
            $imgResult = $stmtImg->get_result();
            $productImages = [];

            if ($imgResult->num_rows > 0) {
                $imgRow = $imgResult->fetch_assoc();
                $imagesString = $imgRow['product_images_url'];
                if (!empty($imagesString)) {
                    $productImages = explode('#', $imagesString);
                }
            }
        } else {
            $errors[] = "Error updating product: " . $updateStmt->error;
        }
    }
}

// Ana kategorileri al
$mainCategoriesSQL = "SELECT * FROM categories WHERE category_parent_id = 0 ORDER BY category_name";
$mainCategoriesResult = $conn->query($mainCategoriesSQL);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <!-- CSS Bağlantıları -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #fe6e61 !important">
                        <h4 class="mb-0">Edit Product</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <?php
                                echo $success;
                                header('Location: seller-panel.php');
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label">Product Name</label>
                                    <input type="text" class="form-control" id="productName" name="productName"
                                        value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                </div>

                                <!-- Kategori Seçimi - Hiyerarşik Yapı -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category</label>
                                    <div id="category-selects">
                                        <select class="form-control mb-2" id="parent_category" name="parent_category"
                                            required>
                                            <option value="">-- Select Category --</option>
                                            <?php if ($mainCategoriesResult && $mainCategoriesResult->num_rows > 0): ?>
                                                <?php while ($row = $mainCategoriesResult->fetch_assoc()): ?>
                                                    <option value="<?php echo $row['category_id']; ?>" <?php echo (getCategoryParent($conn, $product['category_id']) == $row['category_id']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($row['category_name']); ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>

                                        <div id="subcategory-container">
                                            <!-- Alt kategoriler AJAX ile yüklenecek -->
                                        </div>

                                        <!-- Gerçek kategori ID'si -->
                                        <input type="hidden" name="category_id" id="final_category_id"
                                            value="<?php echo $product['category_id']; ?>">
                                    </div>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="productDescription" class="form-label">Product Description</label>
                                    <textarea id="productDescription" name="productDescription"
                                        class="form-control summernote"><?php echo htmlspecialchars($product['product_description']); ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="productPrice" class="form-label">Price</label>
                                    <input type="text" class="form-control" id="productPrice" name="productPrice"
                                        value="<?php echo number_format($product['price'] / 100, 2); ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="productStock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="productStock" name="productStock"
                                        value="<?php echo $product['stock']; ?>" required>
                                </div>

                                <!-- Current Images -->
                                <?php if (!empty($productImages)): ?>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Current Images</label>
                                        <div class="row">
                                            <?php foreach ($productImages as $index => $image): ?>
                                                <div class="col-md-3 mb-2">
                                                    <div class="card">
                                                        <img src="<?php echo "../" . htmlspecialchars($image); ?>"
                                                            class="card-img-top" alt="Product Image"
                                                            style="height: 150px; object-fit: contain; border: 1px solid #ddd; padding: 5px;">
                                                        <div class="card-body p-2 text-center">
                                                            <small class="text-muted">Image <?php echo $index + 1; ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <small class="text-muted">If you upload new images, they will replace the current
                                            ones.</small>
                                    </div>
                                <?php endif; ?>
                                <!-- New Images Upload -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Upload New Images</label>
                                    <div class="row">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <div class="col-md-4 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">Image <?php echo $i + 1; ?></span>
                                                    </div>
                                                    <input type="file" class="form-control" name="productImage[]"
                                                        accept="image/*">
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted">Leave empty if you don't want to change the
                                        images.</small>
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" name="updateProduct" class="btn"
                                        style="background-color: #fe6e61 !important">Update
                                        Product</button>
                                    <a href="seller/seller-panel.php" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Bağlantıları -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <script>
        $(document).ready(function () {
            // Summernote Editörünü Başlat
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

            // Kategori değişikliğini izle
            $('#parent_category').on('change', function () {
                loadSubcategories($(this).val(), 0);
            });

            // Sayfa yüklendiğinde mevcut kategoriyi seç
            const currentCategoryId = <?php echo $product['category_id']; ?>;
            loadCategoryPath(currentCategoryId);

            // Kategori yolunu yükle
            function loadCategoryPath(categoryId) {
                $.ajax({
                    url: 'get_category_path.php',
                    type: 'GET',
                    data: {
                        category_id: categoryId
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            const path = response.path;

                            // Ana kategoriyi seç
                            if (path.length > 0) {
                                $('#parent_category').val(path[0]);

                                // Alt kategorileri sırayla yükle
                                let parentId = path[0];
                                for (let i = 1; i < path.length; i++) {
                                    const childId = path[i];
                                    loadSubcategoriesInPath(parentId, i, childId);
                                    parentId = childId;
                                }
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading category path:', error);
                    }
                });
            }

            // Kategori yolundaki alt kategorileri yükle
            function loadSubcategoriesInPath(parentId, level, selectedId) {
                $.ajax({
                    url: 'get_subcategories.php',
                    type: 'GET',
                    data: {
                        parent_id: parentId,
                        level: level
                    },
                    dataType: 'html',
                    success: function (response) {
                        if (level === 0) {
                            $('#subcategory-container').html(response);
                        } else {
                            $('#subcategory-level-' + (level - 1)).after(response);
                        }

                        // Doğru alt kategoriyi seç
                        $('#subcategory-level-' + level).val(selectedId);

                        // Son seçilen kategoriyi hidden input'a ata
                        $('#final_category_id').val(selectedId);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading subcategories:', error);
                    }
                });
            }

            // Alt kategorileri yükle
            function loadSubcategories(parentId, level) {
                if (!parentId) {
                    $('#subcategory-container').empty();
                    $('#final_category_id').val('');
                    return;
                }

                $.ajax({
                    url: 'get_subcategories.php',
                    type: 'GET',
                    data: {
                        parent_id: parentId,
                        level: level
                    },
                    dataType: 'html',
                    success: function (response) {
                        // Mevcut alt seviyeden sonraki tüm seçimleri temizle
                        if (level === 0) {
                            $('#subcategory-container').html(response);
                        } else {
                            $('#subcategory-level-' + (level - 1)).nextAll().remove();
                            $('#subcategory-level-' + (level - 1)).after(response);
                        }

                        // Yeni alt kategori seçimini dinle
                        $('#subcategory-level-' + level).on('change', function () {
                            const selectedId = $(this).val();
                            if (selectedId) {
                                loadSubcategories(selectedId, level + 1);
                                $('#final_category_id').val(selectedId);
                            } else {
                                $(this).nextAll().remove();
                                // Bir üst kategoriyi son kategori olarak ayarla
                                if (level > 0) {
                                    $('#final_category_id').val($('#subcategory-level-' + (level - 1)).val());
                                } else {
                                    $('#final_category_id').val($('#parent_category').val());
                                }
                            }
                        });

                        // Son seçilen kategoriyi hidden input'a ata
                        if ($('#subcategory-level-' + level).val()) {
                            $('#final_category_id').val($('#subcategory-level-' + level).val());
                        } else {
                            if (level > 0) {
                                $('#final_category_id').val($('#subcategory-level-' + (level - 1)).val());
                            } else {
                                $('#final_category_id').val(parentId);
                            }
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error loading subcategories:', error);
                    }
                });
            }
        });
    </script>
</body>

</html>

<?php
// Ana kategoriyi bul (üst kategori zincirini takip ederek)
function getCategoryParent($conn, $categoryId)
{
    $visited = [];

    while ($categoryId > 0 && !in_array($categoryId, $visited)) {
        $visited[] = $categoryId;

        $sql = "SELECT category_parent_id FROM categories WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if ($row['category_parent_id'] == 0) {
                return $categoryId; // Ana kategoriyi bulduk
            }
            $categoryId = $row['category_parent_id']; // Bir üst kategoriye çık
        } else {
            break; // Kategori bulunamadı
        }
    }

    return 0; // Ana kategori bulunamadı
}
?>