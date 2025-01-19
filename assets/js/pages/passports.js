function createRow(event) {
	location.href = '/passports/create';
}

function deleteRow(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href = '/passports/delete_row/' + $(event.currentTarget).parents('tr').data('id');
	} else {
		return;
	}
}

async function format(d) {
	let insulation_types = await getAllDataAjax('insulation_types', 'get_data_ajax');

	let options = '';

	for (let i = 0; i < insulation_types.data.length; i++) {
		options += `<option ${d.insulation_type === insulation_types.data[i].insulation_type ? 'selected' : null} value="${insulation_types.data[i].id}">${insulation_types.data[i].insulation_type}</option>`;
	}

	let html = `
	<h2 class="text-left">Додаткова інформація</h2>
	<dl class="row" data-id="${d.id}">
		<dt class="col-sm-3">Вид ізоляції</dt>
		<dd class="col-sm-9" data-field_name="insulation_type_id" data-field_title="Вид ізоляції">
		<select class="custom-select" name"insulation_type_id[]" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled>
			<option value="">Оберіть вид ізоляції</option>
			${options}
		</select>
		</dd>
		<dt class="col-sm-3">Під_номер R3</dt>
		<dd class="col-sm-9" data-field_name="sub_number_r3" data-field_title="Під_номер R3">
			<input class="form-control text-left" name="sub_number_r3[]" value="${d.sub_number_r3}" maxlength="2" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled>
		</dd>
		<dt class="col-sm-3">Запис створив</dt>
		<dd class="col-sm-9">${d.created_by}</dd>
		<dt class="col-sm-3">Запис змінив</dt>
		<dd class="col-sm-9">${d.updated_by}</dd>
		<dt class="col-sm-3">Дата створення запису</dt>
		<dd class="col-sm-9">${d.created_at}</dd>
		<dt class="col-sm-3">Дата зміни запису</dt>
		<dd class="col-sm-9">${d.updated_at}</dd>
	</dl>
	<hr>
	<div class="row">
		<div class="col-12 text-right">
			<button class="btn btn-dark" type="button" title="Активувати форму" onclick="activeSubRow(event);"><span>Активувати додаткову форму</span></button>
		</div>
	</div>
	`;

	return html;
}
