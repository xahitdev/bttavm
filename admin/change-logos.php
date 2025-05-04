<?php
error_reporting(E_ALL);
include "dbcon.php";
include "safe-protocol.php";

$logo_id = 1;

$logoQuery = $connect -> prepare("SELECT * FROM logos WHERE	logo_id = :logo_id");
$logoQuery-> bindParam(":logo_id",$index_image_id,PDO::PARAM_STR);
$logoQuery-> fetchAll(PDO::FETCH_ASSOC);
$logoQuery-> execute();
if($logoQuery-> rowCount()){
	foreach($logoQuery as $i){
		$logo_id = $i["logo_id"];
		$indexLogoTop = $i["index_logo_top"];
		$tailoringLogoTop = $i["tailoring_logo_top"];
		$sidePagesLogos = $i["sidepages_logo"];
		$logoBottom = $i["logo_bottom"];
	}
}

if (isset($_POST['changeLogo'])) {
    $uploadDir = 'images/';
    $errors = [];

    function processImage($file, $name){
		$uploadFilePath = "images";
		$tmp_name = $file['tmp_name'];
		$name = $file['name'];
		$size = $file['size'];
		$type = $file['type'];
		$uz = substr($name,-4,4);
		$randOne = rand(10000,50000);
		$randTwo = rand(10000,50000);
		$date = date("Ymdhms");
		$imageName = $randOne.$date.$randTwo.$uz;

		if (strlen($name) == 0) {
            $errors[] = "$name chosen image is not appropriate.";
            return false;
		}

		if ($type != 'image/jpeg' && $type != 'image/png' && $uz != '.jpg') {
			$nc = "Chosen file can only be JPG, JPEG or PNG";
            return false;
		}

		if ($size > (102410243)) {
			$nc = "Too much file size...";
			return false;
		}

		$demoU = $uploadFilePath."/".$imageName;

		if(move_uploaded_file($tmp_name, "../$uploadFilePath/$imageName")){
			return $demoU;
		} else {
			$errors[] = "$name could not be uploaded.";
            return false;
        }
    }

	if (isset($_FILES['indexLogoTop']) && $_FILES['indexLogoTop']['error'] === UPLOAD_ERR_OK) {
        $indexLogoTopPath = processImage($_FILES['indexLogoTop'], 'Index Logo Top');
    } else {
        $indexLogoTopPath = $indexLogoTop;
    }

	if (isset($_FILES['tailoringLogoTop']) && $_FILES['tailoringLogoTop']['error'] === UPLOAD_ERR_OK) {
        $tailoringLogoPath = processImage($_FILES['tailoringLogoTop'], 'Tailoring Logo');
    } else {
        $tailoringLogoPath = $tailoringLogoTop;
    }

	if (isset($_FILES['sidePagesLogos']) && $_FILES['sidePagesLogos']['error'] === UPLOAD_ERR_OK) {
        $sidePagesLogosPath = processImage($_FILES['sidePagesLogos'], 'Side Pages Logos');
    } else {
        $sidePagesLogosPath = $sidePagesLogos;
    }

	if (isset($_FILES['logoBottom']) && $_FILES['logoBottom']['error'] === UPLOAD_ERR_OK) {
        $logoBottomPath = processImage($_FILES['logoBottom'], 'Bottom Logo');
    } else {
        $logoBottomPath = $logoBottom;
    }

    /* $bottomVisible = isset($_POST['bottomImageVisible']) ? 1 : 0; */

    if (empty($errors)) {
        $query = $connect->prepare("
            UPDATE logos
            SET 
                index_logo_top = :index_top_logo,
                tailoring_logo_top = :tailoring_logo,
                sidepages_logo = :side_pages_logo,
                logo_bottom = :bottom_logo_overall
            WHERE logo_id = :id
        ");
        $query->execute([
			':id' => $logo_id,
            ':index_logo_top' => $indexLogoTopPath,
            ':tailoring_logo' => $tailoringLogoPath,
            ':side_pages_logo' => $sidePagesLogosPath,
            ':bottom_logo_overall' => $logoBottomPath
        ]);

        header("Refresh:0"); // Sayfayı yenile
    } else {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>$err</p>";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
	<title>Esclot Panel</title>
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

<body data-pc-preset="preset-1" data-pc-direction="ltr" data-pc-theme="light">
	<!-- [ Pre-loader ] start -->
	<div class="loader-bg">
		<div class="loader-track">
			<div class="loader-fill"></div>
		</div>
	</div>
	<!-- [ Pre-loader ] End -->
	<!-- [ Sidebar Menu ] start -->
	<?php include "sidebar-components.php"; ?>
	<!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
	<?php include "header-components.php"; ?>
	<!-- [ Header ] end -->

	<?php include "feedback-components.php"; ?>

	<!-- [ Main Content ] start -->
	<div class="pc-container">
		<div class="pc-content">

			<div class="page-header">
				<div class="page-block">
					<div class="row align-items-center">
						<div class="col-md-12">
							<div class="page-header-title">
								<h5 class="m-b-10">Size</h5>
							</div>
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
								<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
								<li class="breadcrumb-item"><a href="size-add.php">Size</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>

			<!-- [ breadcrumb ] start -->
			<div class="page-header">
				<div class="page-block">
					<div class="row align-items-center">
						<div class="col-md-12">
							<div class="page-header-title">
								<h2 class="mb-0">Change Main Page Elements</h2>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- [ breadcrumb ] end -->
			<!-- [ Main Content ] start -->
			<div class="row">
				<!-- [ sample-page ] start -->
				<div class="col-sm-12">
					<div class="card">
						<div class="card-body">
						   <div class="row">
							  <div class="col-md-12">
								<div class="page-header">
									<div class="page-block">
										<div class="row align-items-center">
											<div class="col-md-12">
												<div class="page-header-title">
													<h2 class="mb-0">Edit Images</h2>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12">
									<div class="card">
										<div class="card-body">
											<form action="" method="post" enctype="multipart/form-data">
												<!-- curr -->
												<div class="form-group">
													<label class="form-label">Main Page Top Logo</label>
													<input name="indexLogoTop" type="file" accept="image/*" class="form-control" >
													<label class="form-label">Preview:</label>
													<div class="col-md-7 px-0">
													  <div class="img-box" style="padding:10px;">
													  <img src="<?php echo "../" . $indexLogoTop; ?>" class="box_img" alt="about img" style="width: 20%">
													  </div>
													</div>
												</div>
												<div class="form-group">
													<label class="form-label">Tailoring Top Logo</label>
													<input name="tailoringLogoTop" type="file" accept="image/*" class="form-control" >
													<label class="form-label">Preview:</label>
													<div class="col-md-7 px-0">
													  <div class="img-box" style="padding:10px;">
													  <img src="<?php echo "../" . $tailoringLogoTop; ?>" class="box_img" alt="about img" style="width: 20%">
													  </div>
													</div>
												</div>
												<div class="form-group">
													<label class="form-label">Side Pages Logos</label>
													<input name="sidePagesLogos" type="file" accept="image/*" class="form-control" >
													<label class="form-label">Preview:</label>
													<div class="col-md-7 px-0">
													  <div class="img-box" style="padding:10px;">
													  <img src="<?php echo "../" . $sidePagesLogos; ?>" class="box_img" alt="about img" style="width: 20%">
													  </div>
													</div>
												</div>
												<div class="form-group">
													<label class="form-label">Bottom Logo</label>
													<input name="logoBottom" type="file" accept="image/*" class="form-control" >
													<label class="form-label">Preview:</label>
													<div class="col-md-7 px-0">
													  <div class="img-box" style="padding:10px;">
													  <img src="<?php echo "../" . $logoBottom; ?>" class="box_img" alt="about img" style="width: 20%">
													  </div>
													</div>
												</div>
												<!--
												<div class="form-group">
													<label class="form-label">Bottom Image Visibility: </label>
													<input class="form-check-input" type="checkbox" role="switch"
														name="bottomImageVisible" <?php ?>echo $isBottomImageVisible == 1 ? 'checked' : ''; ?>>
												</div>
												-->
											</div>
											<div class="text-end btn-page mb-0 mt-4">
												<!-- <button class="btn btn-outline-secondary">Cancel</button> -->
												<button name="changeLogo" type="submit" class="btn btn-primary">Apply Changes</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Language - Comma Decimal Place table start -->
		
		<!-- Language - Comma Decimal Place table end -->
		<!-- [ sample-page ] end -->
	</div>
	<!-- [ Main Content ] end -->
	</div>
	</div>
	<!-- [ Main Content ] end -->

	<footer class="pc-footer">
		<div class="footer-wrapper container-fluid">
			<div class="row">
				<div class="col-auto my-1">
					<ul class="list-inline footer-link mb-0">

					</ul>
				</div>
			</div>
		</div>
	</footer>



	<!-- [Page Specific JS] start -->
	<script src="../assets/js/plugins/apexcharts.min.js"></script>
	<script src="../assets/js/pages/dashboard-default.js"></script>
	<!-- [Page Specific JS] end -->
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


	<script>preset_change("preset-2");</script>


	<script>font_change("Public-Sans");</script>



</body>
<!-- [Body] end -->

</html>
