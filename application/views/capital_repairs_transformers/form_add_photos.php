<form id="formAddPhotos">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<input type="hidden" name="subdivision_id" value="" id="idSubdivisionIdAddPhoto" />
	<input type="hidden" name="complete_renovation_object_id" value="" id="idCompleteRenovationObjectIdAddPhoto" />
	<input type="hidden" name="specific_renovation_object_id" value="" id="idSpecificRenovationObjectIdAddPhoto" />
	<input type="hidden" name="place_id" value="" id="idPlaceIdAddPhoto" />
	<input type="hidden" name="passport_id" value="" id="idPassportIdAddPhoto" />

	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="photoAlbumDate" class="form-label">Дата створення фотоальбому</label>
			<input type="text" name="photo_album_date" class="form-control datemask datepicker" id="photoAlbumDate" placeholder="Введіть дату створення фотоальбому">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="photoAlbumName" class="form-label">
				<?php echo anchor_popup(site_url('capital_repairs_transformers/get_value_photo_album/photo_album_name', (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) || isset($_SERVER['HTTPS'])) ? 'https' : 'http'), 'Назва фотоальбому', ['width' => 800, 'height' => 600, 'scrollbars'  => 'yes', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => 0, 'screeny' => 0, 'window_name' => '_blank']); ?>
			</label>
			<input type="text" name="photo_album_name" class="form-control" id="photoAlbumName" placeholder="Введіть назву фотоальбому" maxlength="255">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="photos" class="form-label">Фото</label>
			<input type="file" name="photo[]" class="form-control" id="photos" accept="image/jpeg" multiple>
			<div class="invalid-feedback"></div>
		</div>
	</div>
</form>
