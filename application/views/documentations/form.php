<form>
	<div class="row">
		<div class="col-md-9">
			<div class="input-group input-group-static mb-4">
				<label for="name">Назва документа</label>
				<input type="text" class="form-control" id="name" maxlength="255" autocomplete="off">
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-group input-group-static mb-4">
				<label for="number">Номер документа</label>
				<input type="text" class="form-control" id="number" maxlength="255" autocomplete="off">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-3">
			<div class="input-group input-group-static mb-4">
				<label for="approval_document">Документ про затвердження</label>
				<input type="text" class="form-control" id="approval_document" maxlength="255" autocomplete="off">
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-group input-group-static mb-4">
				<label for="document_date_start">Дата затвердження документа</label>
				<input type="date" class="form-control" id="document_date_start" autocomplete="off">
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-group input-group-static mb-4">
				<label for="document_revision_date">Дата перегляду документа</label>
				<input type="date" class="form-control" id="document_revision_date" autocomplete="off">
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-group input-group-static mb-4">
				<label for="document_date_finish">Дата закінчення документа</label>
				<input type="date" class="form-control" id="document_date_finish" autocomplete="off">
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-5">
			<div class="input-group input-group-static mb-4">
				<label for="periodicity">Періодичність перегляду документа, роки</label>
				<input type="number" class="form-control" id="periodicity" min="1" max="9" pattern="/^-?\d+\.?\d*$/" onKeyPress="if (this.value.length == 1) return false;" autocomplete="off">
			</div>
		</div>
		<div class="col-md-4">
			<div class="input-group input-group-static mb-4">
				<label for="term">Термін зберігання документа</label>
				<select class="form-control" id="term">
					<option value="" selected>Оберіть термін зберігання документа</option>
					<option value="0">Постійно</option>
					<option value="1">Один рік</option>
					<option value="2">Два роки</option>
					<option value="3">Три роки</option>
				</select>
			</div>
		</div>
		<div class="col-md-3">
			<div class="input-group input-group-static mb-4">
				<label for="document_type">Тип документа</label>
				<select class="form-control" id="document_type">
					<option value="" selected>Оберіть тип документа</option>
					<option value="1">ОП</option>
					<option value="2">ПБ</option>
					<option value="3">ТЕ</option>
					<option value="4">Інше</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="input-group input-group-static mb-4">
				<label for="documentation_category_id">Група документа</label>
				<select class="form-control" id="documentation_category_id">
					<option value="" selected>Оберіть групу документа</option>
					<?php foreach ($category_tree as $item): ?>
						<option value="<?php echo $item['id']; ?>"><?php echo $item['path']; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="required">
				<label class="custom-control-label" for="required">Обов'язковий документ для СП</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="required_150">
				<label class="custom-control-label" for="required_150">Обов'язковий документ для ПС-150 кВ</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="required_35">
				<label class="custom-control-label" for="required_35">Обов'язковий документ для дільниць 35 кВ</label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="checked">
				<label class="custom-control-label" for="checked">Перевірений документ</label>
			</div>
		</div>
		<div class="col-md-4">
			<div class="form-check form-switch">
				<input class="form-check-input" type="checkbox" id="is_trash">
				<label class="custom-control-label" for="is_trash">Знаходиться в смітті?</label>
			</div>
		</div>
	</div>
</form>