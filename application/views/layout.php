<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

?>

<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf_test_name" content="<?php echo $this->security->get_csrf_hash(); ?>">

	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<!-- <link href="/assets/css/lib/bootstrap/cosmo.min.css" rel="stylesheet"> -->

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

	<?php if (isset($datatables) && $datatables) : ?>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
	<?php endif; ?>
	<?php if (isset($datatables_button) && $datatables_button) : ?>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
	<?php endif; ?>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
	<link rel="stylesheet" type="text/css" href="/assets/css/custom.css">

	<?php if ($page === 'capital_repairs_transformers/index' || $page === 'capital_repairs_transformers/sdzp' || $page === 'resources') : ?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhsPDQY9QnKE0Wqag9y38OIgEvb88cA==" crossorigin="anonymous" referrerpolicy="no-referrer">
	<?php endif; ?>

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/css/perfect-scrollbar.min.css" integrity="sha512-ygIxOy3hmN2fzGeNqys7ymuBgwSCet0LVfqQbWY10AszPMn2rB9JY0eoG0m1pySicu+nvORrBmhHVSt7+GI9VA==" crossorigin="anonymous" referrerpolicy="no-referrer">

	<!-- Select2 -->
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" /> -->
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> -->
	<!-- End Select2 -->

	<link rel="icon" href="/assets/images/favicon.png">

	<title>
		<?php echo $title; ?>
	</title>
</head>

<body>
	<?php $this->benchmark->mark('code_start'); ?>
	<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top d-print-none">
		<div class="container-fluid">
			<a class="navbar-brand" href="/">REPAIR.PP.UA</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer' || $this->session->user->group === 'master') : ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle <?php if ($page === 'multi_year_schedule' || $page === 'schedules') : ?>active<?php endif; ?>" href="#" id="scheduleDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Графіки</a>
							<ul class="dropdown-menu" aria-labelledby="scheduleDropdown">
								<li>
									<a class="dropdown-item disable <?php if ($page === 'multi_year_schedule') : ?>active<?php endif; ?>" href="<?php echo '/multi_year_schedule'; ?>">
										Виконання
									</a>
								</li>
								<li>
									<a class="dropdown-item disable <?php if ($page === 'schedules') : ?>active<?php endif; ?>" href="<?php echo '/schedules'; ?>">
										Планування
									</a>
								</li>
								<li>
									<a class="dropdown-item disable <?php if ($page === 'realization') : ?>active<?php endif; ?>" href="<?php echo '/realization'; ?>">
										Виконання поточного року
									</a>
								</li>
								<li>
									<a class="dropdown-item disable <?php if ($page === 'schedules/materials') : ?>active<?php endif; ?>" href="<?php echo '/schedules/materials'; ?>">
										Матеріали на <?php echo (date('Y') + 1) ?> рік
									</a>
								</li>
								<li>
									<a class="dropdown-item disable <?php if ($page === 'passports_r3') : ?>active<?php endif; ?>" href="<?php echo '/passports_r3'; ?>">
										Зв'язка з R3
									</a>
								</li>
								<li>
									<a class="dropdown-item disable <?php if ($page === 'materials/extra_materials') : ?>active<?php endif; ?>" href="<?php echo '/materials/extra_materials'; ?>">
										Специфічна робота з матеріалами
									</a>
								</li>
							</ul>
						</li>
					<?php endif; ?>
					<?php if (($this->session->user->group === 'admin' || $this->session->user->group === 'engineer' || $this->session->user->group === 'master') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp' || $this->session->user->group !== 'head')) :
					?>
						<?php //if (($this->session->user->group === 'admin') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp' || $this->session->user->group !== 'head')) :
						?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'complete_renovation_objects/index') : ?>active<?php endif; ?>" aria-current="page" href="/complete_renovation_objects">Енергетичні об`єкти</a>
						</li>
					<?php endif; ?>

					<?php if (($this->session->user->group === 'admin' || $this->session->user->group === 'engineer' || $this->session->user->group === 'master' || $this->session->user->group === 'user' || $this->session->user->group === 'head') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp')) : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'passports') : ?>active<?php endif; ?>" aria-current="page" href="/passports/index_old">Паспорти</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer' || $this->session->user->group === 'master' || $this->session->user->group === 'user') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'estimates') : ?>active<?php endif; ?>" aria-current="page" href="/estimates">Кошториси</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'sp' || $this->session->user->group === 'sdzp' || $this->session->user->group === 'head') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'capital_repairs_transformers/index') : ?>active<?php endif; ?>" aria-current="page" href="/capital_repairs_transformers">КРСТ</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'sp' || $this->session->user->group === 'sdzp' || $this->session->user->group === 'head') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'capital_repairs_transformers/sdzp') : ?>active<?php endif; ?>" aria-current="page" href="/capital_repairs_transformers/sdzp">КРСТ_1</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'resources/index') : ?>active<?php endif; ?>" aria-current="page" href="/resources">Ресурси</a>
						</li>
					<?php endif; ?>

					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle <?php if ($page === 'equipments/index/' . $this->uri->segment(3)) : ?>active<?php endif; ?>" href="#" id="oborudDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Обладнання</a>
						<ul class="dropdown-menu" aria-labelledby="oborudDropdown">
							<?php if (function_exists('get_equipments_for_menu')) : ?>
								<?php foreach (get_equipments_for_menu() as $item) : ?>
									<li><a class="dropdown-item disable <?php if ($page === 'equipments/index/' . $item->id) : ?>active<?php endif; ?>" href="<?php echo '/equipments/index/' . $item->id; ?>">
											<?php echo $item->plural_name ? $item->plural_name : $item->name; ?>
										</a></li>
								<?php endforeach; ?>
							<?php else : ?>
								<li><a class="dropdown-item disable" href="javascript:void(0);">Список не знайдено</a></li>
							<?php endif; ?>
						</ul>
					</li>

					<!-- <li class="nav-item">
						<a class="nav-link <?php //if ($page === 'buildings/index') :
											?>active<?php //endif;
													?>"
							aria-current="page" href="/buildings">Будівлі та споруди</a>
					</li> -->

					<?php if (($this->session->user->group === 'admin' || $this->session->user->group === 'master') && ($this->session->user->group !== 'sp' || $this->session->user->group !== 'sdzp' || $this->session->user->group !== 'head')) : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'protective_arsenal/index') : ?>active<?php endif; ?>" aria-current="page" href="/protective_arsenal">Захисні засоби</a>
						</li>
					<?php endif; ?>

					<?php if ($this->session->user->group === 'admin') : ?>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'logs/index') : ?>active<?php endif; ?>" aria-current="page" href="/logs">Логи</a>
						</li>
						<li class="nav-item">
							<a class="nav-link <?php if ($page === 'users/index') : ?>active<?php endif; ?>" aria-current="page" href="/users">Користувачі</a>
						</li>
					<?php endif; ?>
					<li class="nav-item">
						<a class="nav-link <?php if ($page === 'dashboard/index') : ?>active<?php endif; ?>" aria-current="page" href="/dashboard">Статистика</a>
					</li>
					<li class="nav-item">
						<a class="nav-link <?php if ($page === 'passports/index') : ?>active<?php endif; ?> position-relative" aria-current="page" href="/passports/index/1/1">
							Паспорти
							<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
								New
								<span class="visually-hidden">New</span>
							</span>
						</a>
					</li>
				</ul>
				<!-- <form class="d-flex"> -->
				<!-- <input class="form-control me-2" type="search" placeholder="Пошук" aria-label="Search" disabled> -->
				<!-- <button class="btn btn-outline-success disabled" type="submit">Пошук</button> -->
				<!-- </form> -->
				<ul class="navbar-nav mb-2 mb-lg-0 d-flex">
					<li class="nav-item dropdown">
						<a class="nav-link" href="javascript:void(0);" id="navbarDropdownBell" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-bell" style="font-size: 24px"></i>
						</a>
						<div class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="navbarDropdownBell">
							Bells
						</div>
					</li>
				</ul>
				<?php echo $this->session->user->name . ' ' . $this->session->user->surname; ?>
				<ul class="navbar-nav mb-2 mb-lg-0 d-flex">
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="javascript:void(0);" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-person-circle" style="font-size: 24px"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
							<li>
								<a class="dropdown-item <?php if ($page === 'profile/index') : ?>active<?php endif; ?>" href="/profile">
									Профіль
								</a>
							</li>
							<li>
								<hr class="dropdown-divider">
							</li>
							<li><a class="dropdown-item" href="/authentication/logout">Вийти</a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>

	<div class="container-fluid">
		<div class="row my-2">
			<div class="col-lg-4 text-start">
				<h3>
					<?php echo isset($title_heading) ? $title_heading : 'TITLE_HEADING'; ?>
				</h3>
			</div>
			<div class="col-lg-8 text-end">
				<?php //if (isset($button_group)):
				?>
				<div class="d-grid gap-2 d-md-block">
					<?php if (isset($custom_button) && $custom_button) : ?>
						<?php foreach ($custom_button as $btn) : ?>
							<button type="button" class="btn <?php echo isset($btn['class']) ? $btn['class'] : 'btn-outline-secondary'; ?>" <?php echo isset($btn['action']) ? 'onClick="' . $btn['action'] . ';"' : NULL; ?>><i class="<?php echo isset($btn['icon']) ? $btn['icon'] : NULL; ?>"></i> <?php echo isset($btn['title']) ? $btn['title'] : 'NULL'; ?></button>
						<?php endforeach; ?>
					<?php endif; ?>
					<?php if (isset($export_to_excel) && $export_to_excel) : ?>
						<button type="button" class="btn btn-outline-success" onClick="exportToExcel(event)"><i class="bi bi-file-earmark-excel"></i> Експортувати в EXCEL</button>
					<?php endif; ?>
					<?php if (isset($export_to_word) && $export_to_word) : ?>
						<button type="button" class="btn btn-outline-primary" onClick="exportToWord(event)"><i class="bi bi-file-earmark-word"></i> Експортувати в WORD</button>
					<?php endif; ?>
					<?php if (isset($export_to_pdf) && $export_to_pdf) : ?>
						<button type="button" class="btn btn-outline-danger" onClick="exportToPDF(event)"><i class="bi bi-file-earmark-pdf"></i> Експортувати в PDF</button>
					<?php endif; ?>
					<?php if (isset($upload_photo) && $upload_photo) : ?>
						<button type="button" class="btn btn-outline-warning" onClick="uploadPhoto(event)"><i class="bi bi-file-image"></i> Завантажити фото</button>
					<?php endif; ?>
				</div>
				<?php //endif;
				?>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12">
				<?php $this->load->view($content); ?>
			</div>
		</div>
	</div>

	<?php if ($this->session->user->id == 1) : ?>
		<?php //$this->output->enable_profiler(TRUE);
		?>
	<?php endif; ?>


	<!-- Jquery -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

	<?php if (isset($datatables) && $datatables) : ?>
		<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
		<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
	<?php endif; ?>
	<?php if (isset($datatables_button) && $datatables_button) : ?>
		<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
		<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
	<?php endif; ?>

	<?php if ($page === 'capital_repairs_transformers/index' || $page === 'capital_repairs_transformers/sdzp' || $page === 'resources') : ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js" integrity="sha512-k2GFCTbp9rQU412BStrcD/rlwv1PYec9SNrkbQlo6RZCf75l6KcC3UwDY8H5n5hl4v77IDtIPwOk9Dqjs/mMBQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<?php endif; ?>

	<?php if ($page === 'complete_renovation_objects/edit') : ?>
		<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
	<?php endif; ?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.min.js" integrity="sha512-d4KkQohk+HswGs6A1d6Gak6Bb9rMWtxjOa0IiY49Q3TeFd5xAzjWXDCBW9RS7m86FQ4RzM2BdHmdJnnKRYknxw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/perfect-scrollbar/1.5.5/perfect-scrollbar.min.js" integrity="sha512-X41/A5OSxoi5uqtS6Krhqz8QyyD8E/ZbN7B4IaBSgqPLRbWVuXJXr9UwOujstj71SoVxh5vxgy7kmtd17xrJRw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script src="/assets/js/custom.js?v=<?php echo date("Y-m-d"); ?>"></script>

	<?php if (isset($page_js) && $page_js) : ?>
		<script src="/assets/js/pages/<?php echo $page_js; ?>.js?v=<?php echo date("Y-m-d"); ?>"></script>
	<?php endif; ?>

	<!-- Select2 -->
	<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
	<!-- End Select2 -->

	<div class="container-fluid mt-5">
		<div class="row">
			<div class="col-md-12 text-center">
				<?php $this->benchmark->mark('code_end'); ?>
				<strong class="text-secondary"><?php echo mb_strtoupper('Час виконання коду:'); ?> <?php echo mb_strtoupper($this->benchmark->elapsed_time('code_start', 'code_end')); ?> <?php echo mb_strtoupper('секунд'); ?></strong>
			</div>
		</div>
	</div>
</body>

</html>