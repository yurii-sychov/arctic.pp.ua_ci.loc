<form id="formAddPassport">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idCompleteRenovationObjectAdd" class="form-label">Підстанція</label>
			<select class="form-select" id="idCompleteRenovationObjectAdd" name="complete_renovation_object_id">
				<option value="" selected>Оберіть підстанцію</option>
				<?php foreach ($stantions as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idEquipmentAdd" class="form-label">Вид обладнання</label>
			<select class="form-select" id="idEquipmentAdd" name="equipment_id">
				<option value="" selected>Оберіть вид обладнання</option>
				<?php foreach ($equipments as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idInsulationTypeAdd" class="form-label">Вид ізоляції</label>
			<select class="form-select" id="idInsulationTypeAdd" name="insulation_type_id">
				<option value="" selected>Оберіть вид ізоляції</option>
				<?php foreach ($insulation_type as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->insulation_type; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idPlaceAdd" class="form-label">Місце встановлення</label>
			<select class="form-select" id="idPlaceAdd" name="place_id">
				<option value="" selected>Оберіть місце встановлення</option>
				<?php foreach ($places as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idVoltageClassAdd" class="form-label">Клас напруги</label>
			<select class="form-select" id="idVoltageClassAdd" name="voltage_class_id">
				<option value="" selected>Оберіть клас напруги</option>
				<?php foreach ($voltage_class as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo ($item->voltage / 1000); ?> кВ</option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idSpecificRenovationObjectAdd" class="form-label">Диспечерське найменування</label>
			<input type="text" class="form-control" id="idSpecificRenovationObjectAdd" placeholder="Введіть диспечерське найменування" name="specific_renovation_object" autocomplete="off">
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idTypeAdd" class="form-label">Тип обладнання</label>
			<input type="text" class="form-control" id="idTypeAdd" placeholder="Введіть тип обладнання" name="type" autocomplete="off">
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idProductionDateAdd" class="form-label">Дата виготовлення</label>
			<input type="text" class="form-control datemask datepicker" id="idProductionDateAdd" placeholder="Введіть дату виготовлення" name="production_date" value="" autocomplete="off">
			<div class="invalid-feedback"></div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6 mb-3">
			<label for="idNumberAdd" class="form-label">Номер</label>
			<input type="text" class="form-control" id="idNumberAdd" placeholder="Введіть номер" name="number" autocomplete="off">
			<div class="invalid-feedback"></div>
		</div>
		<div class="col-md-6 mb-3">
			<label for="idDocumentScan" class="form-label">Документація</label>
			<input class="form-control" type="file" id="idDocumentScan" disabled>
		</div>
		<div class="invalid-feedback"></div>
	</div>
</form>