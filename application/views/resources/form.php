<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5>
			<?php echo $title_heading_card; ?>
		</h5>
	</div>
	<div class="card-body">
		<?php if (validation_errors()) : ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?php echo validation_errors(); ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>
		<form action="/resources/create" method="POST">
			<div class="row mb-2">
				<div class="col-md-2">
					<label for="ResourceType" class="form-label"><strong>Тип ресурсу</strong></label>
					<select class="form-select" id="ResourceType" name="type_resource">
						<option value="" selected>Оберіть тип ресурсу</option>
						<option value="1" <?php echo set_select('type_resource', '1'); ?>>Матеріали</option>
						<option value="2" <?php echo set_select('type_resource', '2'); ?> disabled>Працівники</option>
						<option value="3" <?php echo set_select('type_resource', '3'); ?> disabled>Техніка</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ResourceName" class="form-label"><strong>Назва ресурсу</strong></label>
					<input type="text" class="form-control" id="ResourceName" placeholder="Введіть назву ресурсу" maxlength="255" name="name" value="<?php echo set_value('name'); ?>" />
				</div>
				<div class="col-md-2">
					<label for="ResourceUnit" class="form-label"><strong>Одиниця виміру</strong></label>
					<select value="" class="form-select" id="ResourceUnit" name="unit">
						<option value="" selected>Оберіть одиницю виміру</option>
						<option value="м³" <?php echo set_select('unit', 'м³'); ?>>м&#179;</option>
						<option value="м²" <?php echo set_select('unit', 'м²'); ?>>м&#178;</option>
						<option value="л" <?php echo set_select('unit', 'л'); ?>>л</option>
						<option value="шт" <?php echo set_select('unit', 'шт'); ?>>шт</option>
						<option value="кг" <?php echo set_select('unit', 'кг'); ?>>кг</option>
						<option value="люд.год" <?php echo set_select('unit', 'люд.год'); ?> disabled>люд.год</option>
						<option value="маш.год" <?php echo set_select('unit', 'маш.год'); ?> disabled>маш.год</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="ResourceR3Id" class="form-label"><strong>Номер R3</strong></label>
					<input type="text" class="form-control" id="ResourceR3Id" placeholder="Введіть назву ресурсу" maxlength="8" name="r3_id" value="<?php echo set_value('r3_id'); ?>" />
				</div>
				<div class="col-md-4">
					<label for="ResourcPhoto" class="form-label text-danger"><strong>Фото ресурсу *</strong></label>
					<input type="file" class="form-control text-danger" id="ResourceR3Id" name="photo" disabled />
				</div>
			</div>
			<div class="row mb-2">
				<div class="col-md-12 d-grid gap-2 d-md-block">
					<button type="submit" class="btn btn-primary">Додати ресурс</button>
					<a href="/resources" class="btn btn-success">Назад до ресурсів</a>
				</div>
			</div>
		</form>
	</div>
</div>