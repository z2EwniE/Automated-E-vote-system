<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="author" content="qoshima">
	<meta name="keywords" content=" Admin, dashboard, responsive, css, sass, html, theme, front-end">
	
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="img/icons/icon-48x48.png" />
	<title>E-Vote System</title>

	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

	<!-- Choose your prefered color scheme -->
	 <link href="css/light.css" rel="stylesheet">

	<style>
		body {
			opacity: 0;
		}
	</style>
</head>


<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
	<div class="wrapper">

        <?php
            include_once 'includes/sidebar.php';
        ?>

		<div class="main">

            <?php
                include_once 'includes/navbar.php';
            ?>
			<main class="content">
				<div class="container-fluid p-0">

					<div class="row mb-2 mb-xl-3">
						<div class="col-auto d-none d-sm-block">
							<h3><strong>Profile</strong></h3>
						</div>

                        <div class="row justify-content-center">
    <div class="col-md-3 col-xl-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Details</h5>
            </div>
            <div class="card-body text-center">
                <img src="img/avatars/qoshima.jpg" alt="qoshima" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                <h5 class="card-title mb-0">qoshima</h5>
                <div class="text-muted mb-2">Lead ajax</div>
            
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Skills</h5>
                <a href="#" class="badge bg-primary me-1 my-1">Tamang pindot lang</a>
                
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">About</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><span data-feather="home" class="feather-sm me-1"></span> Lives in <a href="#">Jan lang sa gedli</a></li>
                    <li class="mb-1"><span data-feather="briefcase" class="feather-sm me-1"></span> Works at <a href="#">PH.com</a></li>
                    <li class="mb-1"><span data-feather="map-pin" class="feather-sm me-1"></span> From <a href="#">Under the sea</a></li>
                </ul>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Elsewhere</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><span class="fas fa-globe fa-fw me-1"></span> <a href="#">staciehall.co</a></li>
                    <li class="mb-1"><span class="fab fa-twitter fa-fw me-1"></span> <a href="#">Twitter</a></li>
                    <li class="mb-1"><span class="fab fa-facebook fa-fw me-1"></span> <a href="#">Facebook</a></li>
                    <li class="mb-1"><span class="fab fa-instagram fa-fw me-1"></span> <a href="#">Instagram</a></li>
                </ul>
            </div>
        </div>
    </div>

          <div class="col-md-4 col-xl-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Profile Details</h5>
            </div>
            <div class="card-body text-center">
                <img src="img/avatars/qoshima.jpg" alt="qoshima" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                <h5 class="card-title mb-0">qoshima</h5>
                <div class="text-muted mb-2">Lead ajax</div>
              
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Skills</h5>
                <a href="#" class="badge bg-primary me-1 my-1">Attention seeker</a>
     
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">About</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><span data-feather="home" class="feather-sm me-1"></span> Lives in <a href="#">Jan lang sa gedli</a></li>
                    <li class="mb-1"><span data-feather="briefcase" class="feather-sm me-1"></span> Works at <a href="#">PH.com</a></li>
                    <li class="mb-1"><span data-feather="map-pin" class="feather-sm me-1"></span> From <a href="#">Under the sea</a></li>
                </ul>
            </div>
            <hr class="my-0" />
            <div class="card-body">
                <h5 class="h6 card-title">Elsewhere</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><span class="fas fa-globe fa-fw me-1"></span> <a href="#">staciehall.co</a></li>
                    <li class="mb-1"><span class="fab fa-twitter fa-fw me-1"></span> <a href="#">Twitter</a></li>
                    <li class="mb-1"><span class="fab fa-facebook fa-fw me-1"></span> <a href="#">Facebook</a></li>
                    <li class="mb-1"><span class="fab fa-instagram fa-fw me-1"></span> <a href="#">Instagram</a></li>
                   </ul>
                  </div>
              </div>
                 </div>
            </div>





                    <!--end of pages -->

				</div>
			</main>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0">
								<a href="https://github.com/z2EwniE" target="_blank" class="text-muted"><strong>Calvskie</strong></a> &copy;
							</p>
						</div>
						<div class="col-6 text-end">
							<ul class="list-inline">
								<li class="list-inline-item">
                                <a target="_blank" class="text-muted"><strong>eVote System</strong></a> &copy;
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                <a class="text-muted" href="#">ISPSC - Tagudin Campus</a>
                                </li>
							</ul>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>
	<script src="js/app.js"></script>
</body>

</html>