async function updateFieldAjax(event, name_controller = 'controller', name_public_method_controller = 'method', field_name = null, field_title = null, id = null) {
	let form = new FormData();
	form.set('field', field_name ? field_name : event.target.closest("td").dataset.field_name);
	form.set('field_title', field_title ? field_title : event.target.closest("td").dataset.field_title);
	form.set('id', id ? id : event.target.closest("tr").dataset.id);
	form.set('value', event.target.value);
	console.log(form.get('field'));
	try {
		const response = await fetch(`/${name_controller}/${name_public_method_controller}`, {
			method: 'POST',
			headers: {
				"X-Requested-With": "XMLHttpRequest"
			},
			body: form
		});

		const result = await response.json();

		if (result.status === 'SUCCESS') {
			toastr.success(result.message, "Успіх");
		}
		else {
			toastr.error(result.message, "Помилка");
		}
	} catch (error) {
		toastr.error(error);
	}
}

async function getAllDataAjax(event, name_controller = 'controller', name_public_method_controller = 'method') {

}

async function getRowDataAjax(event, name_controller = 'controller', name_public_method_controller = 'method', id = null) {

}
