<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo $title; ?></title>

	<link rel="icon" href="data:;base64,=">
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome Icons -->
	<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/fontawesome-free/css/all.min.css">

	<!-- Toastr -->
	<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/toastr/toastr.min.css">

	<?php if (isset($forms) && $forms) : ?>
		<!-- Select2 -->
		<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/select2/css/select2.min.css">
		<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
		<!-- Tempusdominus Bootstrap 4 -->
		<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
	<?php endif; ?>

	<!-- DataTables -->
	<?php if (isset($datatables) && $datatables) : ?>
		<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
		<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
		<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
	<?php endif; ?>
	<!-- Theme style -->
	<link rel="stylesheet" href="/vendor/almasaeed2010/adminlte/dist/css/adminlte.min.css">
</head>

<body class="hold-transition sidebar-mini layout-navbar-fixed">
	<div class="wrapper">

		<!-- Navbar -->
		<nav class="main-header navbar navbar-expand navbar-white navbar-light">
			<!-- Left navbar links -->
			<ul class="navbar-nav">
				<li class="nav-item">
					<a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="/" class="nav-link">Головна</a>
				</li>
				<li class="nav-item d-none d-sm-inline-block">
					<a href="#" class="nav-link">Контакт</a>
				</li>
			</ul>

			<!-- Right navbar links -->
			<ul class="navbar-nav ml-auto">
				<!-- Navbar Search -->
				<li class="nav-item">
					<a class="nav-link" data-widget="navbar-search" href="#" role="button">
						<i class="fas fa-search"></i>
					</a>
					<div class="navbar-search-block">
						<form class="form-inline">
							<div class="input-group input-group-sm">
								<input name="search" class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search" />
								<div class="input-group-append">
									<button class="btn btn-navbar" type="submit">
										<i class="fas fa-search"></i>
									</button>
									<button class="btn btn-navbar" type="button" data-widget="navbar-search">
										<i class="fas fa-times"></i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</li>

				<!-- Messages Dropdown Menu -->
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#">
						<i class="far fa-comments"></i>
						<span class="badge badge-danger navbar-badge">3</span>
					</a>
					<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<a href="#" class="dropdown-item">
							<!-- Message Start -->
							<div class="media">
								<img src="/vendor/almasaeed2010/adminlte/dist/img/user1-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle">
								<div class="media-body">
									<h3 class="dropdown-item-title">
										Brad Diesel
										<span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
									</h3>
									<p class="text-sm">Call me whenever you can...</p>
									<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
								</div>
							</div>
							<!-- Message End -->
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<!-- Message Start -->
							<div class="media">
								<img src="/vendor/almasaeed2010/adminlte/dist/img/user8-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
								<div class="media-body">
									<h3 class="dropdown-item-title">
										John Pierce
										<span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
									</h3>
									<p class="text-sm">I got your message bro</p>
									<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
								</div>
							</div>
							<!-- Message End -->
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<!-- Message Start -->
							<div class="media">
								<img src="/vendor/almasaeed2010/adminlte/dist/img/user3-128x128.jpg" alt="User Avatar" class="img-size-50 img-circle mr-3">
								<div class="media-body">
									<h3 class="dropdown-item-title">
										Nora Silvester
										<span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
									</h3>
									<p class="text-sm">The subject goes here</p>
									<p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
								</div>
							</div>
							<!-- Message End -->
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
					</div>
				</li>
				<!-- Notifications Dropdown Menu -->
				<li class="nav-item dropdown">
					<a class="nav-link" data-toggle="dropdown" href="#">
						<i class="far fa-bell"></i>
						<span class="badge badge-warning navbar-badge">15</span>
					</a>
					<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<span class="dropdown-header">15 Notifications</span>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-envelope mr-2"></i> 4 new messages
							<span class="float-right text-muted text-sm">3 mins</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-users mr-2"></i> 8 friend requests
							<span class="float-right text-muted text-sm">12 hours</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item">
							<i class="fas fa-file mr-2"></i> 3 new reports
							<span class="float-right text-muted text-sm">2 days</span>
						</a>
						<div class="dropdown-divider"></div>
						<a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
					</div>
				</li>
				<li class="nav-item dropdown user-menu">
					<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
						<img src="/vendor/almasaeed2010/adminlte/dist/img/user2-160x160.jpg" class="user-image img-circle elevation-2" alt="User Image">
						<span class="d-none d-md-inline"><?php echo isset($this->session->user) ? $this->session->user->name . ' ' . $this->session->user->surname : 'User User'; ?></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
						<!-- User image -->
						<li class="user-header bg-primary">
							<img src="/vendor/almasaeed2010/adminlte/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">

							<p>
								<?php echo isset($this->session->user) ? $this->session->user->name . ' ' . $this->session->user->surname . ' - ' . $this->session->user->position : 'User User Position'; ?>
								<small>Member since Nov. 2012</small>
							</p>
						</li>
						<!-- Menu Body -->
						<li class="user-body d-none">
							<div class="row">
								<div class="col-4 text-center">
									<a href="#">Followers</a>
								</div>
								<div class="col-4 text-center">
									<a href="#">Sales</a>
								</div>
								<div class="col-4 text-center">
									<a href="#">Friends</a>
								</div>
							</div>
							<!-- /.row -->
						</li>
						<!-- Menu Footer-->
						<li class="user-footer">
							<a href="/profile" class="btn btn-default btn-flat">Профіль</a>
							<a href="/authentication/logout" class="btn btn-default btn-flat float-right">Вийти</a>
						</li>
					</ul>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-widget="fullscreen" href="#" role="button">
						<i class="fas fa-expand-arrows-alt"></i>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
						<i class="fas fa-th-large"></i>
					</a>
				</li>
			</ul>
		</nav>
		<!-- /.navbar -->

		<!-- Main Sidebar Container -->
		<aside class="main-sidebar sidebar-dark-primary elevation-4">
			<!-- Brand Logo -->
			<a href="/" class="brand-link">
				<img src="/vendor/almasaeed2010/adminlte/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
				<span class="brand-text font-weight-light">SP</span>
			</a>

			<!-- Sidebar -->
			<div class="sidebar">
				<!-- Sidebar user panel (optional) -->
				<div class="user-panel mt-3 pb-3 mb-3 d-flex">
					<div class="image">
						<img src="/vendor/almasaeed2010/adminlte/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
					</div>
					<div class="info">
						<a href="/profile" class="d-block"><?php echo isset($this->session->user) ? $this->session->user->name . ' ' . $this->session->user->surname : 'User User'; ?></a>
					</div>
				</div>

				<!-- SidebarSearch Form -->
				<div class="form-inline">
					<div class="input-group" data-widget="sidebar-search">
						<input name="search" class="form-control form-control-sidebar" type="search" placeholder="Пошук" aria-label="Search" />
						<div class="input-group-append">
							<button class="btn btn-sidebar">
								<i class="fas fa-search fa-fw"></i>
							</button>
						</div>
					</div>
				</div>

				<!-- Sidebar Menu -->
				<nav class="mt-2">
					<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
						<!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
						<li class="nav-item <?php echo ($page === 'subdivisions' || $page === 'complete_renovation_objects' || $page === 'specific_renovation_objects' || $page === 'passports') ? 'menu-is-opening menu-open' : NULL; ?>">
							<a href="#" class="nav-link <?php if ($page === 'subdivisions' || $page === 'complete_renovation_objects' || $page === 'specific_renovation_objects') echo 'active' ?>">
								<i class="nav-icon fas fa-folder-open"></i>
								<p>
									Довідники
									<i class="right fas fa-angle-left"></i>
								</p>
							</a>
							<ul class="nav nav-treeview">
								<li class="nav-item">
									<a href="/subdivisions" class="nav-link <?php if ($page === 'subdivisions') echo 'active'; ?>">
										<i class="far fa-circle nav-icon text-danger"></i>
										<p>Підрозділи</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="/complete_renovation_objects" class="nav-link <?php if ($page === 'complete_renovation_objects') echo 'active'; ?>">
										<i class="far fa-circle nav-icon text-info"></i>
										<p>Енергетичні об'єкти</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="/specific_renovation_objects" class="nav-link <?php if ($page === 'specific_renovation_objects') echo 'active'; ?>">
										<i class="far fa-circle nav-icon text-warning"></i>
										<p>ДНО</p>
									</a>
								</li>
								<li class="nav-item">
									<a href="/passports" class="nav-link <?php if ($page === 'passports') echo 'active'; ?>">
										<i class="far fa-circle nav-icon text-primary"></i>
										<p>Паспорти</p>
									</a>
								</li>
							</ul>
						</li>
						<li class="nav-item">
							<a href="/users" class="nav-link">
								<i class=" nav-icon fas fa-users"></i>
								<p>Користувачі</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="/acts" class="nav-link <?php if ($page === 'acts') echo 'active'; ?>">
								<i class="nav-icon fas fa-file"></i>
								<p>
									Акти приймання
									<span class="right badge badge-secondary">New</span>
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="/documentations" class="nav-link <?php if ($page === 'sp_instructions') echo 'active'; ?>">
								<i class="nav-icon fas fa-file"></i>
								<p>
									Документація
									<span class="right badge badge-info">New</span>
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="/sp_instructions" class="nav-link <?php if ($page === 'sp_instructions') echo 'active'; ?>">
								<i class="nav-icon fas fa-file"></i>
								<p>
									Інструкції з ОП
									<span class="right badge badge-warning">New</span>
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="/fs_instructions" class="nav-link <?php if ($page === 'fs_instructions') echo 'active'; ?>">
								<i class="nav-icon fas fa-file"></i>
								<p>
									Інструкції з ПБ
									<span class="right badge badge-success">New</span>
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="/fire_extinguishers" class="nav-link <?php if ($page === 'fire_extinguishers') echo 'active'; ?>">
								<i class="nav-icon fas fa-fire-extinguisher"></i>
								<p>
									Вогнегасники
									<span class="right badge badge-warning">New</span>
								</p>
							</a>
						</li>
						<li class="nav-item">
							<a href="/protective_arsenals" class="nav-link <?php if ($page === 'protective_arsenals') echo 'active'; ?>">
								<i class="nav-icon fas fa-mitten"></i>
								<p>
									Захисні засоби
									<span class="right badge badge-danger">New</span>
								</p>
							</a>
						</li>
					</ul>
				</nav>
				<!-- /.sidebar-menu -->
			</div>
			<!-- /.sidebar -->
		</aside>

		<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
			<!-- Content Header (Page header) -->
			<section class="content-header">
				<div class="container-fluid">
					<div class="row mb-2">
						<div class="col-sm-6">
							<h1 class="m-0"><?php echo $title_heading; ?></h1>
						</div><!-- /.col -->
						<div class="col-sm-6">
							<ol class="breadcrumb float-sm-right">
								<li class="breadcrumb-item"><a href="#">Головна</a></li>
								<li class="breadcrumb-item active"><?php echo $title_heading; ?></li>
							</ol>
						</div><!-- /.col -->
					</div><!-- /.row -->
				</div><!-- /.container-fluid -->
			</section>
			<!-- /.content-header -->

			<!-- Main content -->
			<section class="content">
				<?php $this->load->view($content); ?>
			</section>
			<!-- /.content -->
		</div>
		<!-- /.content-wrapper -->

		<!-- Control Sidebar -->
		<aside class="control-sidebar control-sidebar-dark">
			<!-- Control sidebar content goes here -->
			<div class="p-3">
				<h5>Title</h5>
				<p>Sidebar content</p>
			</div>
		</aside>
		<!-- /.control-sidebar -->

		<!-- Main Footer -->
		<footer class="main-footer">
			<!-- To the right -->
			<div class="float-right d-none d-sm-inline">
				Anything you want
			</div>
			<!-- Default to the left -->
			<strong>Copyright &copy; 2014-2021 <a href="https://adminlte.io">AdminLTE.io</a>.</strong> All rights reserved.
		</footer>
	</div>
	<!-- ./wrapper -->

	<?php if (isset($this->session->user) && $this->session->user->id == 1) : ?>
		<?php //$this->output->enable_profiler(TRUE);
		?>
	<?php endif; ?>

	<!-- REQUIRED SCRIPTS -->

	<!-- jQuery -->
	<script src="/vendor/almasaeed2010/adminlte/plugins/jquery/jquery.min.js"></script>
	<!-- Bootstrap 4 -->
	<script src="/vendor/almasaeed2010/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- Toastr -->
	<script src="/vendor/almasaeed2010/adminlte/plugins/toastr/toastr.min.js"></script>
	<!-- DataTables  & Plugins -->
	<?php if (isset($datatables) && $datatables) : ?>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/jszip/jszip.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/pdfmake/pdfmake.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/pdfmake/vfs_fonts.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.print.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
		<script src="/assets/js/datatable_lte.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<?php endif; ?>

	<?php if (isset($forms) && $forms) : ?>
		<!-- Select2 -->
		<script src="/vendor/almasaeed2010/adminlte/plugins/select2/js/select2.full.min.js"></script>
		<!-- InputMask -->
		<script src="/vendor/almasaeed2010/adminlte/plugins/moment/moment.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/inputmask/jquery.inputmask.min.js"></script>
		<!-- Tempusdominus Bootstrap 4 -->
		<script src="/vendor/almasaeed2010/adminlte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
		<!-- jquery-validation -->
		<script src="/vendor/almasaeed2010/adminlte/plugins/jquery-validation/jquery.validate.min.js"></script>
		<script src="/vendor/almasaeed2010/adminlte/plugins/jquery-validation/additional-methods.min.js"></script>
	<?php endif; ?>

	<!-- AdminLTE App -->
	<script src="/vendor/almasaeed2010/adminlte/dist/js/adminlte.min.js"></script>
	<!-- AdminLTE for demo purposes -->
	<!-- <script src="/vendor/almasaeed2010/adminlte/dist/js/demo.js"></script> -->

	<!-- CustomJS -->
	<script src="/assets/js/custom.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<!-- Page specific script -->

	<?php if (isset($ag_grid) && $ag_grid) : ?>
		<script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>
	<?php endif; ?>

	<?php if (isset($page_js) && $page_js) : ?>
		<script src="/assets/js/pages/<?php echo $page_js; ?>.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<?php endif; ?>
</body>

</html>