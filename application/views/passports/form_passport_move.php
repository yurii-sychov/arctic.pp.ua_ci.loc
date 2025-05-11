<form id="formMovePassport">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<input type="hidden" id="idMoveOld" name="id">
	<input type="hidden" id="equipmentIdMoveOld">
	<input type="hidden" id="voltageClassIdMoveOld">
	<div class="card mb-2">
		<h5 class="card-header">Нинішнє місце встановлення обладнання</h5>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="idSubdivisionMoveOld" class="form-label">Підрозділ</label>
					<input type="text" class="form-control text-warning" id="idSubdivisionMoveOld" disabled style="font-weight: 900;">
				</div>
				<div class="col-md-6 mb-3">
					<label for="idCompleteRenovationObjectMoveOld" class="form-label">Підстанція</label>
					<input type="text" class="form-control text-warning" id="idCompleteRenovationObjectMoveOld" disabled style="font-weight: 900;">
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="idSpecificRenovationObjectMoveOld" class="form-label">Диспечерське найменування</label>
					<input type="text" class="form-control text-warning" id="idSpecificRenovationObjectMoveOld" disabled style="font-weight: 900;">
				</div>
				<div class="col-md-6 mb-3">
					<label for="idPlaceMoveOld" class="form-label">Місце встановлення</label>
					<input type="text" class="form-control text-warning" id="idPlaceMoveOld" disabled style="font-weight: 900;">
				</div>
			</div>
		</div>
	</div>

	<div class="card mb-2">
		<h5 class="card-header">Нове місце встановлення обладнання</h5>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="idSubdivisionMove" class="form-label">Підрозділ</label>
					<select class="form-select" id="idSubdivisionMove" name="subdivision_id" onChange="getCompleteRenovationObjects(event)">
						<option value="" selected>Оберіть підрозділ</option>
					</select>
					<div class="invalid-feedback"></div>
				</div>
				<div class="col-md-6 mb-3">
					<label for="idCompleteRenovationObjectMove" class="form-label">Підстанція</label>
					<select class="form-select" id="idCompleteRenovationObjectMove" name="complete_renovation_object_id" onChange="getSpecificRenovationObjects(event)">
						<option value="" selected>Оберіть підстанцію</option>
					</select>
					<div class="invalid-feedback"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label for="idSpecificRenovationObjectMove" class="form-label">Диспечерське найменування</label>
					<select class="form-select" id="idSpecificRenovationObjectMove" name="specific_renovation_object_id">
						<option value="" selected>Оберіть диспечерське найменування</option>
					</select>
					<div class="invalid-feedback"></div>
				</div>
				<div class="col-md-6 mb-3">
					<label for="idPlaceMove" class="form-label">Місце встановлення</label>
					<select class="form-select" id="idPlaceMove" name="place_id">
						<option value="" selected>Оберіть місце встановлення</option>
						<?php foreach ($places as $item) : ?>
							<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
						<?php endforeach; ?>
					</select>
					<div class="invalid-feedback"></div>
				</div>
			</div>
		</div>
	</div>
</form>