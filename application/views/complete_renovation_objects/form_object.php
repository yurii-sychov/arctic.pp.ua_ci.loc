<form id="formObject" method="POST">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="name" class="form-label">Назва об'єкту</label>
			<input type="text" class="form-control" id="name" placeholder="Введіть назву об'єкту" name="name" autocomplete="off" value="<?php echo htmlspecialchars($stantion->name); ?>">
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<label for="description" class="form-label">Опис об'єкту</label>
			<textarea class="form-control" id="description" name="description" rows="10" cols="50"><?php echo htmlspecialchars($stantion->description ?? ''); ?></textarea>
			<div class="invalid-feedback"></div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12 mb-3">
			<a href="/complete_renovation_objects" class="btn btn-success">Назад</a>
			<button type="submit" class="btn btn-primary action">Зберегти</button>
		</div>
	</div>
</form>