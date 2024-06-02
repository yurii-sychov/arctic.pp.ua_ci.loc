<form id="formAddDocument" enctype="multipart/form-data">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="subdivision_id" value="" id="idSubdivisionIdAddDocument" />
	<input type="hidden" name="complete_renovation_object_id" value="" id="idCompleteRenovationObjectIdAddDocument" />
	<input type="hidden" name="specific_renovation_object_id" value="" id="idSpecificRenovationObjectIdAddDocument" />
	<input type="hidden" name="place_id" value="" id="idPlaceIdAddDocument" />
	<input type="hidden" name="passport_id" value="" id="idPassportIdAddDocument" />

	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="documentDate" class="form-label">Дата створення документу</label>
			<input type="text" name="document_date" class="form-control datemask datepicker" id="documentDate" placeholder="Введіть дату створення документу">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="documentDescription" class="form-label">
				<?php echo anchor_popup(site_url('capital_repairs_transformers/get_value_document/document_description', (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTPS'])) ? 'https' : 'http'), 'Короткий опис документу', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" name="document_description" class="form-control" id="documentDescription" placeholder="Введіть короткий опис документу" maxlength="255">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="documentScan" class="form-label">Скан документу</label>
			<input type="file" name="document_scan" class="form-control" id="documentScan" accept=".pdf">
			<div class="invalid-feedback"></div>
		</div>
	</div>
</form>
