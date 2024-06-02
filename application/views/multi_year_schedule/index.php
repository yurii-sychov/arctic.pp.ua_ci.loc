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
			<div class="col-sm-6 col-md-4 col-lg-2">
				<select class="form-select my-1" id="FilterStantion">
					<option value="" selected>Всі підстанції</option>
					<?php foreach ($stantions as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-2">
				<select class="form-select my-1" id="FilterEquipment">
					<option value="" selected>Все обладнання</option>
					<?php foreach ($equipments as $item) : ?>
						<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-2">
				<select class="form-select my-1" id="FilterInsulationType">
					<option value="" selected>Вид ізоляції</option>
					<?php foreach ($insulation_type as $item) :  ?>
						<option value="<?php echo htmlspecialchars($item->id);  ?>"><?php echo $item->insulation_type; ?></option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-2">
				<select class="form-select my-1" id="FilterTypeService">
					<option value="" selected>Всі види обслуговування</option>
					<?php foreach ($type_services as $item) :  ?>
						<option value="<?php echo $item->id;  ?>"><?php echo $item->name; ?></option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-2">
				<select class="form-select my-1" id="FilterVoltageClass">
					<option value="" selected>Всі класи напруги</option>
					<?php foreach ($voltage_class as $item) :  ?>
						<option value="<?php echo htmlspecialchars($item->id);  ?>"><?php echo $item->voltage / 1000; ?> кВ</option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-sm-6 col-md-4 col-lg-2">
				<select class="form-select my-1" id="FilterStatus">
					<option value="" selected>Всі статуси</option>
					<option value="1">Включено в обслуговування</option>
					<option value="0">Виключено з обслуговування</option>
					?>
				</select>
			</div>
		</div>
		<div class="row my-2">
			<div class="col-lg-12">
				<div class="d-grid gap-2 d-sm-block">
					<!-- <button class="btn btn-primary my-1" title="Скинути всі фільтри" id="ResetFilters"><i class="bi bi-x-square"></i></button> -->
					<button class="btn btn-success my-1" title="Очистити фільтри" data-bs-toggle="tooltip" id="clearLocalStorage"><i class="bi bi-x-square"></i></button>
					<!-- <a class="btn btn-danger my-1" href="/multi_year_schedule/get_schedule_kr" role="button" title="Tooltip on top">
						<i class="bi bi-file-earmark-pdf"></i> КР
					</a> -->

					<!-- <a class="btn btn-danger my-1" href="/multi_year_schedule/get_schedule_pr" role="button" title="Tooltip on top">
						<i class="bi bi-file-earmark-pdf"></i> ПР
					</a> -->

					<!-- <a class="btn btn-danger my-1" href="/multi_year_schedule/get_schedule_to" role="button" title="Tooltip on top">
						<i class="bi bi-file-earmark-pdf"></i> ТО
					</a> -->

					<!-- <a class="btn btn-info my-1" href="/uploads/multi_grafik_source.xlsx" role="button" title="Завантажити дані для БРПГ" data-bs-toggle="tooltip">
						<i class="bi bi-download"></i> Завантажити дані для БРПГ
					</a> -->

					<a class="btn btn-primary my-1" href="/multi_year_schedule/get_data_for_multi_schedule" role="button" title="Згенерувати дані для БРПГ" data-bs-toggle="tooltip">
						<i class="bi bi-file-earmark-excel"></i> Згенерувати дані для БРПГ
					</a>

					<!-- <a class="btn btn-success my-1" href="/uploads/grafik_source.xlsx" role="button" title="Завантажити дані для БРПГ" data-bs-toggle="tooltip">
						<i class="bi bi-download"></i> Завантажити дані для РПГ
					</a> -->

					<a class="btn btn-success my-1" href="/multi_year_schedule/get_data_for_schedule_sp" role="button" title="Згенерувати дані для БРПГ" data-bs-toggle="tooltip">
						<i class="bi bi-file-earmark-excel"></i> Згенерувати дані для РПГ СП
					</a>

					<a class="btn btn-warning my-1" href="/multi_year_schedule/get_data_for_schedule_srm" role="button" title="Згенерувати дані для БРПГ" data-bs-toggle="tooltip">
						<i class="bi bi-file-earmark-excel"></i> Згенерувати дані для РПГ СРМ
					</a>
				</div>
			</div>
		</div>

		<div class="loading text-center">
			<div class="spinner-border text-secondary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>

		<table class="table table-hover table-bordered" id="datatables">
			<caption>
				<ul>
					<li class="text-danger">Рожевим кольором виділено обладнання, що не планується ремонтувати у <?php echo date('Y'); ?> році.</li>
					<!-- <li class="text-info">Блакитним кольором виділено обладнання, що планується ремонтувати у <?php echo (date('Y') + 1); ?> році поза планом.</li> -->
				</ul>
			</caption>
		</table>

	</div>
</div>