<?php
session_start();
ob_start();
?>
<!-- Nav Bar Start -->
<div class="nav">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-md bg-dark navbar-dark">
            <a href="#" class="navbar-brand">MENU</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav mr-auto">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
                    <a href="product-list.html" class="nav-item nav-link">Products</a>
                    <a href="product-detail.html" class="nav-item nav-link">Product Detail</a>
                    <a href="cart.html" class="nav-item nav-link">Cart</a>
                    <a href="checkout.html" class="nav-item nav-link">Checkout</a>
                    <a href="my-account.php" class="nav-item nav-link">My Account</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">More Pages</a>
                        <div class="dropdown-menu">
                            <a href="wishlist.html" class="dropdown-item">Wishlist</a>
                            <a href="login.php" class="dropdown-item">Login & Register</a>
                            <a href="contact.html" class="dropdown-item">Contact Us</a>
                            <?php
                            if (!isset($_SESSION['seller_id']) && !isset($_SESSION['user_id'])) {
                                ?>
                                <a href="sellerlogin.php" class="dropdown-item">Seller Login</a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="navbar-nav ml-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <?php
                            if (!isset($_SESSION['user_id']) && !isset($_SESSION['seller_id'])) {
                                ?>
								Log in
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($_SESSION['seller_id'])) {
                                echo "Seller Account";
                            } elseif (isset($_SESSION['user_id'])) {
                                echo "User Account";
                            }
                            ?> </a>
                        <div class="dropdown-menu">
                            <?php
                            if (isset($_SESSION['seller_id'])) {
                                ?>
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($_SESSION['user_id'])) {
								echo $_SESSION['username'];
								?>
                                <a href="logout.php" class="btn">Logout</a>
								<?php
                            } elseif (isset($_SESSION['seller_id'])) {
                                echo $_SESSION['seller_mail'];
                                ?>
                                <a href="logout.php" class="btn">Logout</a>
                            <?php } else {
                                ?>
                                <a href="login.php" class="dropdown-item">Login</a>
                                <a href="register.php" class="dropdown-item">Register</a>
                                <?php
                            }
                            ?>
                            <!-- <a href="login.php" class="dropdown-item">Login</a>
                            <a href="register.php" class="dropdown-item">Register</a> -->
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>
    <div class="bottom-bar">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="logo">
                        <a href="index.php">
                            <img src="img/logo.png" alt="Logo">
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="search">
                        <input type="text" placeholder="Search">
                        <button><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="user">
                        <a href="wishlist.html" class="btn wishlist">
                            <i class="fa fa-heart"></i>
                            <span>(0)</span>
                        </a>
                        <a href="cart.html" class="btn cart">
                            <i class="fa fa-shopping-cart"></i>
                            <span>(0)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

