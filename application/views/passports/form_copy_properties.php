<form id="formCopyProperties">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<div class="row">
		<div class="col-lg-12">
			<select class="form-select my-1 equipment" onChange="onChangeEqupment(event)">
				<option value="" selected>Оберіть обладнання</option>
				<?php foreach ($equipments as $item) : ?>
					<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="col-lg-12">
			<input class="form-control my-1 disp" onkeyup="getSpecificRenovationObjects(event)" disabled>
		</div>
		<div class="col-lg-12">
			<ul class="list"></ul>
		</div>
	</div>
</form>
