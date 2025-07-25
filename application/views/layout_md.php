<!--
=========================================================
* Material Dashboard 3 - v3.2.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2024 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="apple-touch-icon" sizes="76x76" href="/templates/material-dashboard/assets/img/apple-icon.png">
	<link rel="icon" type="image/png" href="/templates/material-dashboard/assets/img/favicon.png">
	<title><?php echo $title; ?></title>
	<!-- Fonts and icons -->
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
	<!-- Nucleo Icons -->
	<link href="/templates/material-dashboard/assets/css/nucleo-icons.css" rel="stylesheet" />
	<link href="/templates/material-dashboard/assets/css/nucleo-svg.css" rel="stylesheet" />
	<!-- Font Awesome Icons -->
	<!-- <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script> -->
	<!-- Material Icons -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
	<!-- CSS Files -->
	<link id="pagestyle" href="/templates/material-dashboard/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

	<!-- Toastr -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css" integrity="sha512-6S2HWzVFxruDlZxI3sXOZZ4/eJ8AcxkQH1+JjSe/ONCEqR9L4Ysq5JdT5ipqtzU7WHalNwzwBv+iE51gNHJNqQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- DataTables -->
	<?php if (isset($datatables) && $datatables) : ?>
		<link href="https://cdn.datatables.net/2.3.1/css/dataTables.bootstrap5.min.css" rel="stylesheet" integrity="sha384-5hBbs6yhVjtqKk08rsxdk9xO80wJES15HnXHglWBQoj3cus3WT+qDJRpvs5rRP2c" crossorigin="anonymous">
	<?php endif; ?>
</head>

<body class="g-sidenav-show  bg-gray-100">
	<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2" id="sidenav-main">
		<div class="sidenav-header">
			<i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
			<a class="navbar-brand px-4 py-3 m-0" href="/">
				<img src="/templates/material-dashboard/assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26" alt="main_logo">
				<span class="ms-1 text-sm text-dark">SP Repair</span>
			</a>
		</div>
		<hr class="horizontal dark mt-0 mb-2">
		<div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">dashboard</i>
						<span class="nav-link-text ms-1">Статистика</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php echo $page === 'documentations' ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="/documentations">
						<i class="material-symbols-rounded opacity-5">table_view</i>
						<span class="nav-link-text ms-1">Документація</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link <?php echo $page === 'my_documentations' ? 'active bg-gradient-dark text-white' : 'text-dark'; ?>" href="/documentations/my">
						<i class="material-symbols-rounded opacity-5">receipt_long</i>
						<span class="nav-link-text ms-1">Моя документація</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">view_in_ar</i>
						<span class="nav-link-text ms-1">Virtual Reality</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">format_textdirection_r_to_l</i>
						<span class="nav-link-text ms-1">RTL</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">notifications</i>
						<span class="nav-link-text ms-1">Notifications</span>
					</a>
				</li>
				<li class="nav-item mt-3">
					<h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Account pages</h6>
				</li>
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">person</i>
						<span class="nav-link-text ms-1">Profile</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">login</i>
						<span class="nav-link-text ms-1">Sign In</span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link text-dark" href="javascript:void(0);">
						<i class="material-symbols-rounded opacity-5">assignment</i>
						<span class="nav-link-text ms-1">Sign Up</span>
					</a>
				</li>
			</ul>
		</div>
		<div class="sidenav-footer position-absolute w-100 bottom-0 ">
			<div class="mx-3">
				<a class="btn btn-outline-dark mt-4 w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard?ref=sidebarfree" type="button">Documentation</a>
				<a class="btn bg-gradient-dark w-100" href="https://www.creative-tim.com/product/material-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
			</div>
		</div>
	</aside>
	<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
		<!-- Navbar -->
		<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
			<div class="container-fluid py-1 px-3">
				<nav aria-label="breadcrumb">
					<ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
						<li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/">Головна</a></li>
						<li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?php echo $title_heading; ?></li>
					</ol>
				</nav>
				<div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
					<div class="ms-md-auto pe-md-3 d-flex align-items-center d-none d-lg-block">
						<div class="input-group input-group-outline">
							<label class="form-label" for="search">Пошук...</label>
							<input type="text" class="form-control" id="search">
						</div>
					</div>
					<ul class="navbar-nav d-flex align-items-center  justify-content-end">
						<li class="nav-item d-flex align-items-center">
							<?php if (isset($button['name']) && $button['name'] === 'button_add'): ?>
								<a class="btn btn-outline-primary btn-sm mb-0 me-3 <?php echo $button['class']; ?>" href="javascript:void(0);" <?php echo $button['method']; ?>>Створити</a>
							<?php endif; ?>
						</li>
						<li class="mt-1 d-none d-lg-block">
							<a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
						</li>
						<li class="nav-item d-xl-none ps-3 d-flex align-items-center">
							<a href="javascript:void(0);" class="nav-link text-body p-0" id="iconNavbarSidenav">
								<div class="sidenav-toggler-inner">
									<i class="sidenav-toggler-line"></i>
									<i class="sidenav-toggler-line"></i>
									<i class="sidenav-toggler-line"></i>
								</div>
							</a>
						</li>
						<li class="nav-item px-3 d-flex align-items-center">
							<a href="javascript:void(0);" class="nav-link text-body p-0">
								<i class="material-symbols-rounded fixed-plugin-button-nav">settings</i>
							</a>
						</li>
						<li class="nav-item dropdown pe-3 d-flex align-items-center">
							<a href="javascript:void(0);" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
								<i class="material-symbols-rounded">notifications</i>
							</a>
							<ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
								<li class="mb-2">
									<a class="dropdown-item border-radius-md" href="javascript:void(0);">
										<div class="d-flex py-1">
											<div class="my-auto">
												<img src="/templates/material-dashboard/assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
											</div>
											<div class="d-flex flex-column justify-content-center">
												<h6 class="text-sm font-weight-normal mb-1">
													<span class="font-weight-bold">New message</span> from Laur
												</h6>
												<p class="text-xs text-secondary mb-0">
													<i class="fa fa-clock me-1"></i>
													13 minutes ago
												</p>
											</div>
										</div>
									</a>
								</li>
								<li class="mb-2">
									<a class="dropdown-item border-radius-md" href="javascript:void(0);">
										<div class="d-flex py-1">
											<div class="my-auto">
												<img src="/templates/material-dashboard/assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark  me-3 ">
											</div>
											<div class="d-flex flex-column justify-content-center">
												<h6 class="text-sm font-weight-normal mb-1">
													<span class="font-weight-bold">New album</span> by Travis Scott
												</h6>
												<p class="text-xs text-secondary mb-0">
													<i class="fa fa-clock me-1"></i>
													1 day
												</p>
											</div>
										</div>
									</a>
								</li>
								<li>
									<a class="dropdown-item border-radius-md" href="javascript:void(0);">
										<div class="d-flex py-1">
											<div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
												<svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
													<title>credit-card</title>
													<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
														<g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
															<g transform="translate(1716.000000, 291.000000)">
																<g transform="translate(453.000000, 454.000000)">
																	<path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
																	<path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
																</g>
															</g>
														</g>
													</g>
												</svg>
											</div>
											<div class="d-flex flex-column justify-content-center">
												<h6 class="text-sm font-weight-normal mb-1">
													Payment successfully completed
												</h6>
												<p class="text-xs text-secondary mb-0">
													<i class="fa fa-clock me-1"></i>
													2 days
												</p>
											</div>
										</div>
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item d-flex align-items-center">
							<a href="/auth/logout" class="nav-link text-body font-weight-bold px-0">
								<i class="material-symbols-rounded">account_circle</i>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<!-- End Navbar -->
		<div class="container-fluid py-2">
			<?php $this->load->view($content); ?>
			<footer class="footer py-4  ">
				<div class="container-fluid">
					<div class="row align-items-center justify-content-lg-between">
						<div class="col-lg-6 mb-lg-0 mb-4">
							<div class="copyright text-center text-sm text-muted text-lg-start">
								© <script>
									document.write(new Date().getFullYear())
								</script>,
								made with <i class="fa fa-heart"></i> by
								<a href="/" class="font-weight-bold" target="_blank">Repair SP</a>
								for a better web.
							</div>
						</div>
						<div class="col-lg-6">
							<ul class="nav nav-footer justify-content-center justify-content-lg-end">
								<li class="nav-item">
									<a href="/" class="nav-link text-muted" target="_blank">Repair SP</a>
								</li>
								<li class="nav-item">
									<a href="/about" class="nav-link text-muted" target="_blank">Про нас</a>
								</li>
								<li class="nav-item">
									<a href="/blog" class="nav-link text-muted" target="_blank">Блог</a>
								</li>
								<li class="nav-item">
									<a href="/license" class="nav-link pe-0 text-muted" target="_blank">Ліцензія</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</main>
	<div class="fixed-plugin">
		<a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
			<i class="material-symbols-rounded py-2">settings</i>
		</a>
		<div class="card shadow-lg">
			<div class="card-header pb-0 pt-3">
				<div class="float-start">
					<h5 class="mt-3 mb-0">Material UI Configurator</h5>
					<p>See our dashboard options.</p>
				</div>
				<div class="float-end mt-4">
					<button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
						<i class="material-symbols-rounded">clear</i>
					</button>
				</div>
				<!-- End Toggle Button -->
			</div>
			<hr class="horizontal dark my-1">
			<div class="card-body pt-sm-3 pt-0">
				<!-- Sidebar Backgrounds -->
				<div>
					<h6 class="mb-0">Sidebar Colors</h6>
				</div>
				<a href="javascript:void(0);" class="switch-trigger background-color">
					<div class="badge-colors my-2 text-start">
						<span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
						<span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
						<span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
						<span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
						<span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
						<span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
					</div>
				</a>
				<!-- Sidenav Type -->
				<div class="mt-3">
					<h6 class="mb-0">Sidenav Type</h6>
					<p class="text-sm">Choose between different sidenav types.</p>
				</div>
				<div class="d-flex">
					<button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
					<button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
					<button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
				</div>
				<p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
				<!-- Navbar Fixed -->
				<div class="mt-3 d-flex">
					<h6 class="mb-0">Navbar Fixed</h6>
					<div class="form-check form-switch ps-0 ms-auto my-auto">
						<input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
					</div>
				</div>
				<hr class="horizontal dark my-3">
				<div class="mt-2 d-flex">
					<h6 class="mb-0">Light / Dark</h6>
					<div class="form-check form-switch ps-0 ms-auto my-auto">
						<input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
					</div>
				</div>
				<hr class="horizontal dark my-sm-4">
				<a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/material-dashboard-pro">Free Download</a>
				<a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View documentation</a>
				<div class="w-100 text-center">
					<a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
					<h6 class="mt-3">Thank you for sharing!</h6>
					<a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
						<i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
					</a>
					<a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
						<i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
					</a>
				</div>
			</div>
		</div>
	</div>
	<!--   Core JS Files   -->
	<script src="/templates/material-dashboard/assets/js/core/popper.min.js"></script>
	<script src="/templates/material-dashboard/assets/js/core/bootstrap.min.js"></script>
	<script src="/templates/material-dashboard/assets/js/plugins/perfect-scrollbar.min.js"></script>
	<script src="/templates/material-dashboard/assets/js/plugins/smooth-scrollbar.min.js"></script>
	<script>
		var win = navigator.platform.indexOf('Win') > -1;
		if (win && document.querySelector('#sidenav-scrollbar')) {
			var options = {
				damping: '0.5'
			}
			Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
		}
	</script>
	<!-- Github buttons -->
	<script async defer src="https://buttons.github.io/buttons.js"></script>
	<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
	<script src="/templates/material-dashboard/assets/js/material-dashboard.min.js?v=3.2.0"></script>

	<!-- Toastr -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- DataTables  & Plugins -->
	<?php if (isset($datatables) && $datatables) : ?>
		<script src="https://cdn.datatables.net/2.3.1/js/dataTables.min.js" integrity="sha384-LiV1KhVIIiAY/+IrQtQib29gCaonfR5MgtWzPCTBVtEVJ7uYd0u8jFmf4xka4WVy" crossorigin="anonymous"></script>
		<script src="https://cdn.datatables.net/2.3.1/js/dataTables.bootstrap5.min.js" integrity="sha384-G85lmdZCo2WkHaZ8U1ZceHekzKcg37sFrs4St2+u/r2UtfvSDQmQrkMsEx4Cgv/W" crossorigin="anonymous"></script>
		<script src="/assets/js/datatable_md.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<?php endif; ?>

	<?php if (isset($page_js) && $page_js) : ?>
		<script src="/assets/js/pages/<?php echo $page_js; ?>.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<?php endif; ?>
</body>

</html>