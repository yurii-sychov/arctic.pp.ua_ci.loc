<!doctype html>
<html lang="en">

<head>
	<title><?php echo $title; ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="/assets/themes/login/css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
	<link rel="icon" href="data:;base64,=">
</head>

<body>
	<section class="ftco-section" style="padding: 2em 0;">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12 text-center">
					<h2 class="heading-section"><?php echo strtoupper('repair.pp.ua'); ?></h2>
				</div>
			</div>
			<noscript>
				<div class="row justify-content-center">
					<div class="col-md-7 col-lg-5 text-center">
						<div class="alert alert-danger" role="alert">
							Вам необхідно увімкнути в налаштуваннях сайту підтримку JavaScript
						</div>
					</div>
				</div>
			</noscript>
			<div class="row justify-content-center" id="infoBlock" style="display: none;">
				<div class="col-md-7 col-lg-5 text-left">
					<div class="alert alert-danger" role="alert" id="infoBlockMessage"></div>
				</div>
			</div>
			<?php $this->load->view($content); ?>
		</div>
	</section>
	<script src="/assets/themes/login/js/jquery.min.js"></script>
	<?php if (isset($page) && $page === 'signin') : ?>
		<script src="/assets/js/pages/signin.js"></script>
	<?php endif; ?>
	<?php if (isset($page) && $page === 'signup') : ?>
		<script src="/assets/js/pages/signup.js"></script>
	<?php endif; ?>
	<?php if (isset($page) && $page === 'forgot') : ?>
		<script src="/assets/js/pages/forgot.js"></script>
	<?php endif; ?>
</body>

</html>