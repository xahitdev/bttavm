<?php
require_once 'settings.php';

if(isset($_POST['register'])){
    $name = trim($_POST['name'] . " " . trim($_POST['lastname']));
    $password = trim($_POST['password']);
    $birth = $_POST['dob'];
    $gender = $_POST['gender'];
    $mail = $_POST['mail'];
    $phone = $_POST['phone_number'];
    
    //cehcking if email address already exists.
    $sql = "SELECT id FROM customers where customer_mail = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $mail);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if(mysqli_stmt_num_rows($stmt) > 0){
        $_SESSION['error'] = "This mail address is already exists. "; echo '<a href="login.php">Already have an account?</a>';
    }
    
    $sql = "INSERT INTO customers (customer_name, password_hashed, customer_birth, customer_gender, customer_mail, customer_phone) VALUES (?,?,?,?,?,?)";
	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, "ssssss", $name, md5($password), $birth, $gender, $mail, $phone);
	if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Registration successful! You can now log in.";
        header("Location: login.php"); // Redirect to login page
    } else {
        $_SESSION['error'] = "Something went wrong. Please try again.";
        header("Location: register.php");
    }
    exit();
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
</head>

<body>
    <!-- Top bar Start -->
    <div class="top-bar">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <i class="fa fa-envelope"></i>
                    support@email.com
                </div>
                <div class="col-sm-6">
                    <i class="fa fa-phone-alt"></i>
                    +012-345-6789
                </div>
            </div>
        </div>
    </div>
    <!-- Top bar End -->

    <!-- Nav Bar Start -->
    <?php
    include 'navbar.php';
    ?>
    <!-- Nav Bar End -->


    <!-- Breadcrumb Start -->
    <div class="breadcrumb-wrap">
        <div class="container-fluid">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item"><a href="#">Products</a></li>
                <li class="breadcrumb-item active">Login & Register</li>
            </ul>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Login Start -->
    <div class="login">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4">
                <div class="register-form rounded shadow">
                    <h3 class="text-center mb-4">Let's sign you up.</h3>
                    <form id="registerForm" action="register.php" method="POST">
                        <!-- Step 1: First Name -->
                        <div class="step active">
                            <label class="form-label">First Name:</label>
                            <input type="text" name="name" class="form-control" id="name" required>
                            <button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(2)">Next</button>
                        </div>

                        <!-- Step 2: Last Name -->
                        <div class="step d-none">
                            <label class="form-label">Last Name:</label>
                            <input type="text" name="lastname" class="form-control" id="lastname" required>
                            <button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(1)">Back</button>
                            <button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(3)">Next</button>
                        </div>

                        <!-- Step 3: Mail Address -->
                        <div class="step d-none">
                            <label class="form-label">Mail Address:</label>
                            <input type="email" name="mail" class="form-control" id="mail" required>
                            <button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(2)">Back</button>
                            <button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(4)">Next</button>
                        </div>

                        <!-- Step 4: Password -->
                        <div class="step d-none">
                            <label class="form-label">Choose a Password:</label>
                            <input type="password" name="password" class="form-control" id="password" required>
                            <button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(3)">Back</button>
                            <button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(5)">Next</button>
                        </div>

                        <!-- Step 5: Phone Number -->
                        <div class="step d-none">
                            <label class="form-label">Phone Number:</label>
                            <input type="text" name="phone_number" class="form-control" id="phone_number" required>
                            <button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(4)">Back</button>
                            <button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(6)">Next</button>
                        </div>

                        <!-- Step 6: Date of Birth -->
                        <div class="step d-none">
                            <label class="form-label">Select Date of Birth:</label>
                            <input type="date" name="dob" class="form-control" id="dob" required>
                            <button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(5)">Back</button>
                            <button type="button" class="btn btn-primary mt-3 w-100" onclick="nextStep(7)">Next</button>
                        </div>

                        <!-- Step 7: Gender Selection -->
                        <div class="step d-none">
                            <label class="form-label">Select Gender:</label>
                            <select name="gender" class="form-select" id="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Do not want to answer">Do not want to answer</option>
                            </select>
                            <button type="button" class="btn btn-secondary mt-3 w-100" onclick="prevStep(6)">Back</button>
                            <input class="btn btn-success mt-3 w-100" type="submit" name="register" value="Register">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 0;
    const steps = document.querySelectorAll('.step');

    function nextStep(step) {
        // Validate current step before allowing the next step
        const inputs = steps[currentStep].querySelectorAll("input, select");
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value) {
                isValid = false;
                input.classList.add("is-invalid");
            } else {
                input.classList.remove("is-invalid");
            }
        });

        if (isValid) {
            steps[currentStep].classList.add('d-none');
            steps[step - 1].classList.remove('d-none');
            currentStep = step - 1;
        }
    }

    function prevStep(step) {
        steps[currentStep].classList.add('d-none');
        steps[step - 1].classList.remove('d-none');
        currentStep = step - 1;
    }
</script>

    <!--
    <div class="login">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="register-form">
                        <form action="" method="POST" name="user_register">
                        <div class="row">
                            <div class="col-md-6">
                                <label>First Name</label>
                                <input class="form-control" type="text" placeholder="First Name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label>Last Name</label>
                                <input class="form-control" type="text" placeholder="Last Name" name="lastname" required>
                            </div>
                            <div class="col-md-6">
                                <label>E-mail</label>
                                <input class="form-control" type="text" placeholder="E-mail" name="mail" required>
                            </div>
                            <div class="col-md-6">
                                <label>Phone Number (Without +)</label>
                                <input class="form-control" type="text" placeholder="Phone Number" name="phone_number" required>
                            </div>
                            <div class="col-md-6">
                                <label>Password</label>
                                <input class="form-control" type="password" placeholder="Password" name="password" required>
                            </div>
                            <div class="col-md-6">
                                <label>Retype Password</label>
                                <input class="form-control" type="password" placeholder="Password, again..." name="password_check" required>
                            </div>
                            <div class="col-md-6">
                                <label>Date of Birth</label>
                                <input class="form-control" type="date" name="date_of_birth" required>
                            </div>
                            <div class="col-md-6">
                                <label>Gender</label>
                                <select class="form-control" name="gender" required>
                                  <option value="Male">Male</option>
                                  <option value="Female">Female</option>
                                  <option value="Do not want to answer">Do not want to answer</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button class="btn">Register</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    --!>
    
    <!-- Login End -->

    <!-- Footer Start -->
    <?php
    include 'footer.php';
    ?>
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
