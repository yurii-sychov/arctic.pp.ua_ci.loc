<div class="card text-dark bg-light border-info mb-2">
	<div class="card-header text-dark bg-info">
		<h5>Новини додатку</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-2 mb-4">
				<div id="SinoptikInformer" class="SinoptikInformer type1">
					<div class="siHeader">
						<div class="siLh">
							<div class="siMh"><a onmousedown="siClickCount();" class="siLogo" href="https://ua.sinoptik.ua/" target="_blank" rel="nofollow" title="Погода"> </a>Погода <span id="siHeader"></span></div>
						</div>
					</div>
					<div class="siBody"><a onmousedown="siClickCount();" href="https://ua.sinoptik.ua/погода-кропивницький" title="Погода у Кропивницькому" target="_blank">
							<div class="siCity">
								<div class="siCityName"><span>Кропивницький</span></div>
								<div id="siCont0" class="siBodyContent">
									<div class="siLeft">
										<div class="siTerm"></div>
										<div class="siT" id="siT0"></div>
										<div id="weatherIco0"></div>
									</div>
									<div class="siInf">
										<p>вологість: <span id="vl0"></span></p>
										<p>тиск: <span id="dav0"></span></p>
										<p>вітер: <span id="wind0"></span></p>
									</div>
								</div>
							</div>
						</a>
						<div class="siLinks">Погода на 10 днів від <a href="https://ua.sinoptik.ua/погода-кропивницький/10-днів" title="Погода на 10 днів" target="_blank" onmousedown="siClickCount();">sinoptik.ua</a></div>
					</div>
					<div class="siFooter">
						<div class="siLf">
							<div class="siMf"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-10">
				<div style="height: 200px; position: relative; overflow: auto;">
					<div class="scrollbar-container" style="display: none; position: relative; height: 100%;">
						<div class="table-responsive">
							<table class="table table-bordered align-middle">
								<thead>
									<tr>
										<th>Дата</th>
										<th>Тип</th>
										<th>Зміст</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($news as $item) : ?>
										<?php $text_color_random = array_rand($text_color, 1); ?>
										<tr class="<?php echo $text_color[$text_color_random]; ?>">
											<td><?php echo $item->date_created; ?></td>
											<td><?php echo $item->title; ?></td>
											<td><?php echo $item->description; ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<script type="text/javascript" charset="UTF-8" src="//sinoptik.ua/informers_js.php?title=4&amp;wind=3&amp;cities=303010892&amp;lang=ua"></script>
		</div>
	</div>
</div>

<div class="card text-dark bg-light border-light mb-2">
	<div class="card-header">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<?php if ($this->session->flashdata('message')) : ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?php echo $this->session->flashdata('message');  ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>
		<div class="row my-2">
			<div class="col-lg-4 text-center">
				<img src="<?php echo $user->gender == 1 ? '/assets/images/avatar_male.webp' :  '/assets/images/avatar_female.webp'; ?>" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
				<h5 class="my-3"><?php echo $user->name . " " . $user->surname; ?></h5>
				<p class="text-muted mb-1"><?php echo $user->position; ?></p>
				<p class="text-muted mb-4">Україна</p>
				<div class="mb-2 d-grid gap-2 d-md-block">
					<a href="/profile/update/<?php echo $user->id; ?>" class="btn btn-primary">Редагувати</a>
					<a href="/profile/send_message" class="btn btn-outline-success ms-1">Повідомлення</a>
				</div>
			</div>
			<div class="col-lg-8">
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Прізвище ім'я по батькові </p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0"><?php echo $user->surname . ' ' . $user->name . ' ' . $user->patronymic; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Підрозділ</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0">
							<?php foreach ($subdivisions as $item) : ?>
								<?php if ($item->id == $user->subdivision_id) : ?>
									<?php echo $item->name; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						</p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Стать</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0"><?php echo ($user->gender == 1) ? 'Чоловік' : 'Жінка'; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Email</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0"><?php echo $user->email; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Телефон</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0"><?php echo $user->phone; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Мобільний</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0"><?php echo $user->phone_mobile; ?></p>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Скількі рядків показувати на сторінці?</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0"><?php echo $user->rows; ?></p>
					</div>
				</div>
				<hr>
				<!-- <div class="row">
					<div class="col-sm-4">
						<p class="mb-0">Адреса</p>
					</div>
					<div class="col-sm-8">
						<p class="text-muted mb-0">Україна</p>
					</div>
				</div> -->
			</div>
		</div>
	</div>
</div>

<script>

</script>