<form id="formAddOperatingList">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<input type="hidden" name="subdivision_id" value="" id="idSubdivisionIdAdd">
	<input type="hidden" name="complete_renovation_object_id" value="" id="idCompleteRenovationObjectIdAdd">
	<input type="hidden" name="specific_renovation_object_id" value="" id="idSpecificRenovationObjectIdAdd">
	<input type="hidden" name="place_id" value="" id="idPlaceIdAdd">
	<input type="hidden" name="passport_id" value="" id="idPassportIdAdd">
	<div class="row">
		<div class="col-md-12 mb-3">
			<label class="form-label">Підрозділ</label>
			<select class="form-select" disabled>
				<option value="">Оберіть підрозділ</option>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label class="form-label">Підстанція</label>
			<select class="form-select" disabled>
				<option value="">Оберіть підстанцію</option>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label class="form-label">Диспечерське найменування</label>
			<select class="form-select" disabled>
				<option value="">Оберіть диспечерське найменування</option>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label class="form-label">Місце встановлення</label>
			<select class="form-select" disabled>
				<option value="">Оберіть місце встановлення</option>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idServiceDateAdd" class="form-label">Дата обслуговування обладнання</label>
			<input type="text" class="form-control datemask datepicker" id="idServiceDateAdd" placeholder="Введіть дату обслуговування обладнання" name="service_date" autocomplete="on">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<!-- <label for="idTypeServiceIdAdd" class="form-label">Тип обслуговування</label>
			<select name="type_service_id" class="form-select" id="idTypeServiceIdAdd">
				<option value="">Оберіть тип обслуговування</option>
				<option value="1">Виконання капітального ремонту</option>
				<option value="2">Виконання поточного ремонту</option>
				<option value="3">Виконання технічного обслуговування</option>
				<option value="4">Виконання аварійно-відновлювальних робіт</option>
				<option value="5">Виконання в/в випробуваннь</option>
				<option value="6">Виконання відбору проб масла на ХАРГ</option>
				<option value="7">Виконання відбору проб масла на хімічний аналіз</option>
				<option value="8">Виконання повірки</option>
			</select>
			<div class="invalid-feedback"></div> -->

			<label for="idTypeServiceIdAdd" class="form-label">Тип обслуговування</label>
			<select name="type_service_id" class="form-select" id="idTypeServiceIdAdd">
				<option value="">Виберіть тип обслуговування</option>
				<?php foreach ($type_services as $item) :
				?>
					<option value="<?php echo $item->id ?>"><?php echo $item->name ?></option>
				<?php endforeach;
				?>
			</select>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idServiceDataAdd" class="form-label">
				<?php echo anchor_popup(site_url('passports/get_value/service_data', (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTPS'])) ? 'https' : 'http'), 'Дані з експлуатації обладнання', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" class="form-control" id="idServiceDataAdd" placeholder="Введіть дані з експлуатації обладнання" name="service_data" autocomplete="on" readonly>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="idExecutorAdd" class="form-label">
				<?php echo anchor_popup(site_url('passports/get_value/executor', (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTPS'])) ? 'https' : 'http'), 'Виконавець робіт', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" class="form-control" id="idExecutorAdd" placeholder="Введіть виконавця робіт" name="executor" autocomplete="on">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3 places">
		</div>
	</div>
</form>
