<?php
ob_start();
session_start();
include "../settings.php";

if (isset($_POST["loginAction"])) {
  $admin_username = $_POST['username'];
  $admin_password = $_POST['password'];

  $sql = "SELECT * FROM admin WHERE username = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $admin_username);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    if (md5($admin_password) == $row['password_hashed']) {
      session_start();
      $_SESSION['admin_id'] = $row['admin_id'];
      $_SESSION['admin_mail'] = $row['username'];
      header("Location: dashboard.php");
      // exit();
    } else {
      $_SESSION['login_error'] = "Invalid password.";
    }
  } else {
    $_SESSION['login_error'] = "User not found.";
  }

  // header("Location: index.php");
  // exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>BTT-AVM Panel</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="">

  <!-- [Favicon] icon -->
  <link rel="icon" href="../assets/images/favicon.svg" type="image/x-icon"> <!-- [Google Font] Family -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
    id="main-font-link">
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="../assets/fonts/feather.css">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="../assets/fonts/material.css">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
  <link rel="stylesheet" href="../assets/css/style-preset.css">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->

  <div class="auth-main">
    <div class="auth-wrapper v3">
      <div class="auth-form">

        <div class="auth-header">
          <!--
          <a href="#"><img src="../assets/images/logo-dark.svg" alt="img"></a>
          -->
        </div>
        <div class="card my-5">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-end mb-4">
              <h3 class="mb-0"><b>Login</b></h3>
              <!-- <a href="#" class="link-primary">Don't have an account?</a> -->
            </div>

            <!-- LOGIN FORM X-Y -->
            <form action="" method="POST">
              <div class="form-group mb-3">
                <label class="form-label">Email Address</label>
                <input name="username" type="email" required class="form-control" placeholder="Email Address">
              </div>
              <div class="form-group mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" required class="form-control" placeholder="Password">
              </div>
              <div class="d-grid mt-4">
                <button name="loginAction" type="submit" class="btn btn-dark">Log in</button>
              </div>
            </form>

          </div>
        </div>
        <div class="auth-footer row">
          <!-- <div class=""> -->
          <div class="col my-1">
            <p class="m-0">Copyright © <a style="color: black;" href="#">BTT-AVM</a></p>
          </div>
          <!--
            <div class="col-auto my-1">
              <ul class="list-inline footer-link mb-0">
                <li class="list-inline-item"><a href="#">Home</a></li>
                <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
                <li class="list-inline-item"><a href="#">Contact us</a></li>
              </ul>
            </div>
            -->
          <!-- </div> -->
        </div>
      </div>
    </div>
  </div>
  <!-- [ Main Content ] end -->
  <!-- Required Js -->
  <script src="../assets/js/plugins/popper.min.js"></script>
  <script src="../assets/js/plugins/simplebar.min.js"></script>
  <script src="../assets/js/plugins/bootstrap.min.js"></script>
  <script src="../assets/js/fonts/custom-font.js"></script>
  <script src="../assets/js/pcoded.js"></script>
  <script src="../assets/js/plugins/feather.min.js"></script>





  <script>layout_change('light');</script>




  <script>change_box_container('false');</script>



  <script>layout_rtl_change('false');</script>


  <script>preset_change("preset-1");</script>


  <script>font_change("Public-Sans");</script>



</body>
<!-- [Body] end -->

</html>
