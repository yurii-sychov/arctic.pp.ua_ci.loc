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

function format(d) {
	let html = `
	<h2 class="text-left">Додаткова інформація</h2>
	<dl class="row">
		<dt class="col-sm-3">Вид ізоляції</dt>
		<dd class="col-sm-9">${d.insulation_type}</dd>
		<dt class="col-sm-3">Запис створив</dt>
		<dd class="col-sm-9">${d.created_by}</dd>
		<dt class="col-sm-3">Запис змінив</dt>
		<dd class="col-sm-9">${d.updated_by}</dd>
		<dt class="col-sm-3">Дата створення запису</dt>
		<dd class="col-sm-9">${d.created_at}</dd>
		<dt class="col-sm-3">Дата зміни запису</dt>
		<dd class="col-sm-9">${d.updated_at}</dd>
	</dl>
	`;

	return html;
}
