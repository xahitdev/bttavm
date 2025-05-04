<?php
error_reporting(E_ALL);

$index_image_id = 1;

/* $indexImagesQuery = $connect -> prepare("SELECT * FROM index_images WHERE index_image_id = :index_image_id"); */
/* $indexImagesQuery-> bindParam(":index_image_id",$index_image_id,PDO::PARAM_STR); */
/* $indexImagesQuery-> fetchAll(PDO::FETCH_ASSOC); */
/* $indexImagesQuery-> execute(); */
/* if($indexImagesQuery-> rowCount()){ */
/* 	foreach($indexImagesQuery as $i){ */
/* 		$index_image_id = $i["index_image_id"]; */
/* 		$indexImageTop = $i["index_image_top"]; */
/* 		$indexImageBottom = $i["index_image_bottom"]; */
/* 		$isBottomImageVisible = $i["index_image_bottom_visible"]; */
/* 	} */
/* } */

if (isset($_POST['changeImages'])) {
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

	if (isset($_FILES['topImage']) && $_FILES['topImage']['error'] === UPLOAD_ERR_OK) {
        $topImagePath = processImage($_FILES['topImage'], 'Top Image');
    } else {
        $topImagePath = $indexImageTop;
    }

	if (isset($_FILES['bottomImage']) && $_FILES['bottomImage']['error'] === UPLOAD_ERR_OK) {
        $bottomImagePath = processImage($_FILES['bottomImage'], 'Bottom Image');
    } else {
        $bottomImagePath = $indexImageBottom;
    }

    $bottomVisible = isset($_POST['bottomImageVisible']) ? 1 : 0;

    if (empty($errors)) {
        $query = $connect->prepare("
            UPDATE index_images
            SET 
                index_image_top = :top,
                index_image_bottom = :bottom,
                index_image_bottom_visible = :visible
            WHERE index_image_id = :id
        ");
        $query->execute([
            ':top' => $topImagePath,
            ':bottom' => $bottomImagePath,
            ':visible' => $bottomVisible,
            ':id' => $index_image_id
        ]);

        header("Refresh:0"); // Sayfayı yenile
    } else {
        foreach ($errors as $err) {
            echo "<p style='color:red;'>$err</p>";
        }
    }
}


$index_content_id = 1;

/* $headerQuery = $connect->prepare("SELECT * FROM index_content WHERE index_content_id = :index_content_id"); */
/* $headerQuery->bindParam(":index_content_id", $index_content_id, PDO::PARAM_STR); */
/* $headerQuery->fetchAll(PDO::FETCH_ASSOC); */
/* $headerQuery->execute(); */
/* if ($headerQuery->rowCount()) { */
/* 	foreach ($headerQuery as $i) { */
/* 		$indexHeader = $i["index_content_header"]; */
/* 		$indexText = $i["index_content_subtext"]; */
/* 		$isIndexContentVisible = $i["index_content_visible"]; */
/* 	} */
/* } */

if (isset($_POST['changeHeader'])) {
	$changedHeader = $_POST['headerText'] ?? $indexHeader;
	$changedText = $_POST['subtextText'] ?? $indexText;
	$changedVisible = isset($_POST['sectionVisible']) ? 1 : 0; // checkbox handling

	$changeHeaderQuery = $connect->prepare("
		UPDATE index_content 
		SET 
			index_content_header = :changedHeader, 
			index_content_subtext = :changedText, 
			index_content_visible = :changedVisible 
		WHERE 
			index_content_id = :index_content_id
	");

	$changeHeaderQuery->execute([
		':changedHeader' => $changedHeader,
		':changedText' => $changedText,
		':changedVisible' => $changedVisible,
		':index_content_id' => $index_content_id
	]);
	header("Refresh:0");
}


?>

<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
	<title>BTTAVM Panel</title>
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
														<h2 class="mb-0">Edit Texts</h2>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="card">
										<div class="card-body">
											<form action="" method="post">
												<div class="form-group">
													<label class="form-label">Visibility: </label>
													<input class="form-check-input" type="checkbox" role="switch"
														name="sectionVisible" <?php echo $isIndexContentVisible == 1 ? 'checked' : ''; ?>>
												</div>
												<div class="form-group">
													<label class="form-label">Header Text</label>
													<input name="headerText" type="text" class="form-control"
														value="<?php echo $indexHeader; ?>" required>
												</div>
												<div class="form-group">
													<label class="form-label">Text</label>
													<input name="subtextText" type="text" class="form-control"
														value="<?php echo $indexText; ?>" required>
												</div>
										</div>
										<div class="text-end btn-page mb-0 mt-4">
											<!-- <button class="btn btn-outline-secondary">Cancel</button> -->
											<button name="changeHeader" type="submit" class="btn btn-primary">Apply
												Changes</button>
										</div>
										</form>
									</div>
								</div>
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
												<div class="form-group">
													<label class="form-label">Top Image:</label>
													<input name="topImage" type="file" accept="image/*" class="form-control" >
													<label class="form-label">Preview:</label>
													<div class="col-md-7 px-0">
													  <div class="img-box" style="padding:10px;">
													  <img src="<?php echo "../" . $indexImageTop; ?>" class="box_img" alt="about img" style="width: 20%">
													  </div>
													</div>
												</div>
												<div class="form-group">
													<label class="form-label">Bottom Image:</label>
													<input name="bottomImage" type="file" accept="image/*" class="form-control" >
													<label class="form-label">Preview:</label>
													<div class="col-md-7 px-0">
													  <div class="img-box" style="padding:10px;">
													  <img src="<?php echo "../" . $indexImageBottom; ?>" class="box_img" alt="about img" style="width: 20%">
													  </div>
													</div>
												</div>
												<div class="form-group">
													<label class="form-label">Bottom Image Visibility: </label>
													<input class="form-check-input" type="checkbox" role="switch"
														name="bottomImageVisible" <?php echo $isBottomImageVisible == 1 ? 'checked' : ''; ?>>
												</div>
											</div>
											<div class="text-end btn-page mb-0 mt-4">
												<!-- <button class="btn btn-outline-secondary">Cancel</button> -->
												<button name="changeImages" type="submit" class="btn btn-primary">Apply Changes</button>
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
