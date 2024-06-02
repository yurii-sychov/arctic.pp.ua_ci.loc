<form id="formAddOperatingListObject">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="subdivision_id" value="" id="idSubdivisionIdAdd" />
	<input type="hidden" name="complete_renovation_object_id" value="" id="idCompleteRenovationObjectIdAdd" />
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idServiceDateAdd" class="form-label">Дата обслуговування об`єкту</label>
			<input type="text" class="form-control datemask datepicker" id="idServiceDateAdd" placeholder="Введіть дату обслуговування об`єкту" name="service_date" autocomplete="on">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idTypeServiceIdAdd" class="form-label">Тип обслуговування</label>
			<select name="type_service_id" class="form-select" id="idTypeServiceIdAdd">
				<option value="">Виберіть тип обслуговування</option>
				<?php foreach ($type_services as $item) : ?>
					<option value="<?php echo $item->id ?>"><?php echo $item->name ?></option>
				<?php endforeach; ?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idActNumber" class="form-label">Номер документу</label>
			<input type="text" class="form-control" id="idActNumber" placeholder="Введіть номер документу" name="document_number" autocomplete="on" maxlength="20">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idServiceDataAdd" class="form-label">
				<?php echo anchor_popup(site_url('complete_renovation_objects/get_value/service_data', (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTPS'])) ? 'https' : 'http'), 'Дані з експлуатації по об`єкту', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" class="form-control" id="idServiceDataAdd" placeholder="Введіть дані з експлуатації по об`єкту" name="service_data" autocomplete="on" maxlength="255">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idExecutorAdd" class="form-label">
				<?php echo anchor_popup(site_url('complete_renovation_objects/get_value/executor', (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTPS'])) ? 'https' : 'http'), 'Виконавець робіт', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" class="form-control" id="idExecutorAdd" placeholder="Введіть виконавця робіт" name="executor" autocomplete="on" maxlength="50">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<hr />
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idDocumentScanAdd" class="form-label">Сканкопія документу в форматі PDF</label>
			<input type="file" name="document_scan" class="form-control" id="idDocumentScanAdd" accept=".pdf">
			<div class="invalid-feedback"></div>
		</div>
	</div>
</form>
