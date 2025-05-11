<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar-logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .sidebar-logo img {
            max-width: 150px;
            height: auto;
        }
        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-item {
            margin-bottom: 5px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-link:hover {
            background-color: #e9ecef;
            color: #0d6efd;
            text-decoration: none;
        }
        .sidebar-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar-icon {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        .sidebar-caption {
            padding: 10px 20px;
            font-size: 12px;
            text-transform: uppercase;
            color: #6c757d;
            font-weight: 600;
            margin-top: 15px;
        }
        .sidebar-dropdown {
            margin-left: 20px;
            display: none;
        }
        .sidebar-dropdown.show {
            display: block;
        }
        .sidebar-dropdown .sidebar-link {
            padding-left: 50px;
            font-size: 14px;
        }
        .dropdown-toggle::after {
            margin-left: auto;
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <!-- Logo Section -->
    <div class="sidebar-logo">
        <a href="dashboard.php">
            <img src="../img/logo-dark.png" alt="Logo" class="img-fluid">
        </a>
    </div>
    
    <!-- Menu Section -->
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <!-- Dashboard -->
            <li class="sidebar-item">
                <a href="dashboard.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt sidebar-icon"></i>
                    Dashboard
                </a>
            </li>
            
            <!-- Main Page Elements Section -->
            <li class="sidebar-caption">Main Page Elements</li>
            
            <!-- Kategori Düzenle -->
            <li class="sidebar-item">
                <a href="category-edit.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'category-edit.php' ? 'active' : ''; ?>">
                    <i class="fas fa-bookmark sidebar-icon"></i>
                    Kategori Düzenle
                </a>
            </li>
            
            <!-- Logo Düzenleme -->
            <li class="sidebar-item">
                <a href="edit-logos.php" class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'edit-logos.php' ? 'active' : ''; ?>">
                    <i class="fas fa-image sidebar-icon"></i>
                    Logo Düzenleme
                </a>
            </li>
            <li class="sidebar-item">
                <a href="page-images-change.php" class="sidebar-link">
                    <i class="fas fa-image sidebar-icon"></i>
										Ana Sayfa Resimleri
                </a>
            </li>
            <li class="sidebar-item">
                <a href="add-seller-profile.php" class="sidebar-link">
                    <i class="fas fa-user sidebar-icon"></i>
										Kullanıcı Profili Ekle
                </a>
            </li>
            <!-- Settings Section -->
            <li class="sidebar-caption">Ayarlar</li>
            
            <li class="sidebar-item">
                <a href="../logout.php" class="sidebar-link">
                    <i class="fas fa-sign-out-alt sidebar-icon"></i>
                    Çıkış Yap
                </a>
            </li>
        </ul>
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
