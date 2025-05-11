<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row my-2">
			<div class="col-lg-12">
				<select class="form-select my-1" id="FilterSubdivision">
					<option value="" selected>Оберіть підрозділ</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="row my-2">
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterStantion">
					<option value="" selected>Всі підстанції</option>
					<?php foreach ($stantions as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterEquipment">
					<option value="" selected>Все обладнання</option>
					<?php foreach ($equipments as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterInsulationType">
					<option value="" selected>Вид ізоляції</option>
					<?php foreach ($insulation_type as $item) :  ?>
						<option value="<?php echo htmlspecialchars($item->id);  ?>"><?php echo $item->insulation_type; ?></option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterVoltageClass">
					<option value="" selected>Всі класи напруги</option>
					<?php foreach ($voltage_class as $item) :  ?>
						<option value="<?php echo htmlspecialchars($item->id);  ?>"><?php echo $item->voltage / 1000; ?> кВ</option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-lg-2">
				<select class="form-select my-1" id="FilterIsPhoto">
					<option value="" selected>Оберіть паспорти з фото</option>
					<option value="1">З фото</option>
					<option value="0">Без фото</option>
				</select>
			</div>
			<?php if ($this->session->user->group === 'admin') : ?>
				<div class="col-lg-2">
					<select class="form-select my-1" id="OrderUpdateAt">
						<option value="" selected>Сортувати по даті оновлення</option>
						<!-- <option value="asc">Сортувати за зростанням</option> -->
						<option value="desc">Сортувати за спаданням</option>
					</select>
				</div>
			<?php endif; ?>
		</div>
		<div class="row my-2">
			<div class="col-lg-12">
				<div class="d-grid gap-2 d-sm-block">
					<button class="btn btn-success my-1" data-bs-toggle="tooltip" data-bs-placement="top" title="Очистити сховище" id="clearLocalStorage"><i class="bi bi-x-square"></i></button>
					<?php if ($this->session->user->id == 1 or $this->session->user->group === 'engineer') : ?>
						<a class="btn btn-primary my-1" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addPassportModal">Додати паспорт</a>
					<?php else : ?>
						<a class="btn btn-primary my-1 disabled" href="javascript:void(0);">Додати паспорт</a>
					<?php endif; ?>
					<button class="btn btn-danger my-1 dropdown-toggle" type="button" id="dropdownMenuButtonObjects" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="bi bi-file-earmark-pdf"></i> Паспорт підстанції
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonObjects">
						<?php foreach ($stantions as $item) : ?>
							<li>
								<a class="dropdown-item" href="/passports/gen_passport_object_pdf/<?php echo $item->id; ?>" target="_blank">
									<?php echo $item->name; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<button class="btn btn-warning my-1 dropdown-toggle" type="button" id="dropdownMenuButtonStantions" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="bi bi-file-earmark-pdf"></i> Експлуатаційна відомість
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButtonStantions">
						<?php foreach ($stantions as $item) : ?>
							<li>
								<a class="dropdown-item" href="/passports/gen_operating_list_object_pdf/<?php echo $item->id . '/' . date('Y'); ?>" target="_blank">
									<?php echo $item->name; ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php if ($this->session->user->group === 'admin') : ?>
						<button class="btn btn-secondary my-1" onClick="actionAjax(event, 'passports', 'action_ajax', 'get_subdivisions');">Get Data Ajax</button>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="loading text-center">
			<div class="spinner-border text-secondary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>

		<table class="table table-hover table-bordered" id="datatables"></table>

	</div>
</div>

<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button> -->

<!-- Modal Form Add Passport -->
<?php if ($this->session->user->id == 1 || $this->session->user->group === 'engineer') : ?>
	<div class="modal fade" id="addPassportModal" tabindex="-1" aria-labelledby="addPassportModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addPassportModalLabel">Додавання паспорту обладнання</h5>
				</div>
				<div class="modal-body">
					<?php $this->load->view('passports/form_passport_add'); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
					<button type="button" class="btn btn-primary action" onclick="addPassport(event);">Зберегти</button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Modal Form Edit Passport -->
<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer' or $this->session->user->group === 'master') : ?>
	<div class="modal fade" id="editPassportModal" tabindex="-1" aria-labelledby="editPassportModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editPassportModalLabel">Редагування паспорту обладнання</h5>
				</div>
				<div class="modal-body">
					<?php $this->load->view('passports/form_passport_edit'); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити вікно</button>
					<button type="button" class="btn btn-primary action" onclick="editPassport(event);">Змінити паспортні дані</button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Modal Form Move Passport -->
<?php //if ($this->session->user->id == 1) :
?>
<div class="modal fade" id="movePassportModal" tabindex="-1" aria-labelledby="movePassportModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="movePassportModalLabel">Переміщення паспорту обладнання</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('passports/form_passport_move'); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити вікно</button>
				<button type="button" class="btn btn-primary action" onclick="movePassport(event);">Перемістити паспорт</button>
			</div>
		</div>
	</div>
</div>
<?php //endif;
?>

<!-- Modal Form Add Properties -->
<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer' or $this->session->user->group === 'master') : ?>
	<div class="modal fade" id="addPropertiesModal" tabindex="-1" aria-labelledby="addPropertiesModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addPropertiesModalLabel">Додавання характеристик обладнання</h5>
				</div>
				<div class="modal-body">
					<?php $this->load->view('passports/form_properties'); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
					<button type="button" class="btn btn-primary action" onclick="addProperties(event);">Зберегти</button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Modal Form Add Operating List -->
<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer' or $this->session->user->group === 'master') : ?>
	<div class="modal fade" id="addOperatingListModal" tabindex="-1" aria-labelledby="addOperatingListModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addOperatingListModalLabel">Додавання експлуатаційних даних</h5>
				</div>
				<div class="modal-body">
					<?php $this->load->view('passports/form_operating_list'); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-info disabled">Розблокувати неактивні поля</button>
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
					<button type="button" class="btn btn-primary action" onclick="addOperatingList(event);">Зберегти</button>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>