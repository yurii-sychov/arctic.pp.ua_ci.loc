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

async function format(d, tr) {
	let insulation_types = await getAllDataAjax('insulation_types', 'get_data_ajax');
	let passport = await getRowDataAjax('passports', 'get_row_data_ajax', d.id);

	let options = '';

	for (let i = 0; i < insulation_types.data.length; i++) {
		options += `<option ${passport.data.insulation_type_id === insulation_types.data[i].id ? 'selected' : null} value="${insulation_types.data[i].id}">${insulation_types.data[i].insulation_type}</option>`;
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
			<input class="form-control text-left" name="sub_number_r3[]" value="${passport.data.sub_number_r3}" maxlength="2" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled>
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
			<button class="btn btn-dark" type="button" title="Активувати форму" onclick="activeSubForm(event);">Активувати додаткову форму</button>
		</div>
	</div>
	`;

	return html;
}

async function openPassportProperties(event) {
	// $('#propertiesModal').modal({ keyboard: false, backdrop: 'static' });
	$('.modal .modal-footer').find('#buttonPropertiesFormModal').removeClass('btn btn-light btn-block').addClass('btn btn-dark btn-block');
	$('.modal .modal-footer').find('#buttonPropertiesFormModal').attr('title', 'Активувати форму');
	$('.modal .modal-footer').find('#buttonPropertiesFormModal').html('Активувати форму');

	let passport_id = event.target.closest("tr, dl").dataset.id;
	$('#propertiesModal').find('.modal-footer .create-pdf').attr({ href: '/passports/gen_passport_pdf/' + passport_id, target: '_blank' });


	let passport = await getRowDataAjax('passports', 'get_row_data_ajax', passport_id);
	let passport_properties = await getRowDataAjax('passports', 'get_data_passport_properties_ajax', passport_id);

	if (passport.data && passport_properties.data) {
		function htmlspecialchars(text) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};

			return text.replace(/[&<>"']/g, function (m) { return map[m]; });
		}

		let properties = '';
		passport_properties.data.forEach(function (item) {

			properties += `
			<tr class="table-secondary" data-id="${item.id}">
				<td class="align-middle"><strong>${item.property}</strong></td>
				<td class="align-middle" data-field_name="value" data-field_title="Значення"><input class="form-control" name="value[]" value="${htmlspecialchars(item.value)}" onChange="updateFieldAjax(event, 'passport_properties', 'update_field_ajax');" disabled></td>
			</tr>
			`;
		});

		switch (passport.data.place_id) {
			case "1":
				text_color = 'text-warning';
				break;
			case "2":
				text_color = 'text-success';
				break;
			case "3":
				text_color = 'text-danger';
				break;
			default:
				text_color = 'text-primary';
		}

		let html = `
			<h3 class="text-dark text-center"><strong>${passport.data.complete_renovation_object}</strong><h3>
			<h5 class="${text_color} text-center"><strong>ДНО:</strong> ${passport.data.specific_renovation_object} <strong> Місце встановлення:</strong> ${passport.data.place}</h5>
			<table class="table table-striped table-bordered table-hover table-sm">
				<thead class="thead-dark">
					<tr class="text-center">
						<th class="col-md-5 align-middle">Характеристика</th>
						<th class="col-md-7 align-middle">Значення</th>
					</tr>
				</thead>
				<tbody>${properties}</tbody>
			</table>
		`;

		$('#propertiesModal').find(".modal-body").empty().append(html);
		$('#propertiesModal').find('.overlay').hide();
	}
	if (!passport.data || !passport_properties.data) {
		toastr.error("Щось пішло не так. Будь ласка зверніться до адміністратора.", "Помилка");
		toastr.error(passport_properties.message, "Помилка");
		toastr.info('<a href="javascript:void(0);">Додати технічні характеристики!</a>', "Що потрібно зробити?");
	}
}

$('#propertiesModal').on('hidden.bs.modal', function (event) {
	$('#propertiesModal').find(".modal-body").empty();
	$('#propertiesModal').find('.overlay').show();
});
