async function addDocument(event) {
	document.getElementById('submit').setAttribute('onclick', 'addData(event)');

	const formModal = await new bootstrap.Modal(document.getElementById('formModal'), {
		keyboard: false
	});
	await formModal.show();

	const myModalEl = document.getElementById('formModal');
	myModalEl.querySelector('.modal-title').innerHTML = 'Форма додавання документа';
	myModalEl.addEventListener('hidden.bs.modal', event => {
		restoreModal(myModalEl);
	}, { once: true });
}

async function addData(event) {
	let formData = new FormData();

	let formModal = document.getElementById('formModal');

	let fields = get_fields();

	fields.forEach((el) => {
		if (formModal.querySelector('#' + el)) {
			formData.set(el, formModal.querySelector('#' + el).value);
		}
	});

	formData.set('required', formModal.querySelector('#required').checked ? 1 : 0);
	formData.set('required_150', formModal.querySelector('#required_150').checked ? 1 : 0);
	formData.set('required_35', formModal.querySelector('#required_35').checked ? 1 : 0);
	formData.set('is_trash', formModal.querySelector('#is_trash').checked ? 1 : 0);

	let result = await fetchPostData('documentations/add_data_row_ajax/', formData);

	if (result.status === 'ERROR') {
		toastr.error(result.message, 'Помилка', { 'positionClass': 'toast-top-full-width' });
		return;
	}
	toastr.success(result.message, 'Успіх');
	location.href = '/documentations';
}

async function fetchPostData(url, data) {
	try {
		const response = await fetch(url, {
			method: 'POST',
			headers: {
				'X-Requested-With': 'XMLHttpRequest'
			},
			body: data
		});

		if (!response.ok) {
			throw new Error('Сервер вернул ошибку');
		}

		const result = await response.json();
		return result;
	} catch (error) {
		return { status: 'ERROR', message: error };
	}
}

async function editDocument(event) {
	const id = await event.target.closest('tr').dataset.id;

	let result = await fetchGetData('documentations/get_data_row_ajax/' + id);

	if (result.status === 'ERROR') {
		toastr.error(result.message, 'Помилка');
		return;
	}

	await fillForm(result.document);

	document.getElementById('submit').setAttribute('onclick', 'editData(event)');
	document.getElementById('submit').setAttribute('data-id', id);

	const formModal = await new bootstrap.Modal(document.getElementById('formModal'), {
		keyboard: false
	});

	await formModal.show();

	const myModalEl = document.getElementById('formModal');
	myModalEl.querySelector('.modal-title').innerHTML = 'Форма зміни даних про документ';
	myModalEl.addEventListener('hidden.bs.modal', event => {
		restoreModal(myModalEl);
	}, { once: true });
}

async function editData(event) {
	let formData = new FormData();

	let formModal = document.getElementById('formModal');

	let fields = get_fields();

	fields.forEach((el) => {
		if (formModal.querySelector('#' + el)) {
			formData.set(el, formModal.querySelector('#' + el).value);
		}
	});

	formData.set('required', formModal.querySelector('#required').checked ? 1 : 0);
	formData.set('required_150', formModal.querySelector('#required_150').checked ? 1 : 0);
	formData.set('required_35', formModal.querySelector('#required_35').checked ? 1 : 0);
	formData.set('is_trash', formModal.querySelector('#is_trash').checked ? 1 : 0);

	let result = await fetchPostData('documentations/edit_data_row_ajax/' + event.target.dataset.id, formData);

	if (result.status === 'ERROR') {
		toastr.error(result.message, 'Помилка', { 'positionClass': 'toast-top-full-width' });
		return;
	}
	toastr.success(result.message, 'Успіх');
	setTimeout(() => {
		location.reload();
	}, 2000);
}

async function fillForm(data) {
	let formModal = document.getElementById('formModal');

	let fields = get_fields();

	fields.forEach((el) => {
		if (formModal.querySelector('#' + el)) {
			formModal.querySelector('#' + el).value = data[el];
		}
	});

	formModal.querySelector('#required').checked = (data.required == 1) ? true : false;
	formModal.querySelector('#required_150').checked = (data.required_150 == 1) ? true : false;
	formModal.querySelector('#required_35').checked = (data.required_35 == 1) ? true : false;
	formModal.querySelector('#is_trash').checked = (data.is_trash == 1) ? true : false;
}

async function fetchGetData(url) {
	try {
		const response = await fetch(url, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json',
				'X-Requested-With': 'XMLHttpRequest'
			}
		});

		if (!response.ok) {
			throw new Error(`Ошибка HTTP: ${response.status}`);
		}

		const data = await response.json();
		return data;
	} catch (error) {
		return { status: 'ERROR', message: error };
	}
}

async function trashDocument(event) {
	const id = await event.target.closest('tr').dataset.id;
	const result = confirm('Ви впевнені?');
	if (result) {
		location.href = '/documentations/trash_row/' + id;
	} else {
		return;
	}
}

async function untrashDocument(event) {
	const id = await event.target.closest('tr').dataset.id;
	const result = confirm('Ви впевнені?');
	if (result) {
		location.href = '/documentations/untrash_row/' + id;
	} else {
		return;
	}
}

async function addDelDocs(event) {
	const id = event.target.closest('tr').dataset.id;
	const urlParams = new URLSearchParams(window.location.search);

	let formData = new FormData();

	formData.set('documentation_id', id);
	formData.set('plot_id', urlParams.get('plot_id'));
	formData.set('checked', event.target.checked);

	let result = await fetchPostData('documentations/add_doc_ajax/', formData);

	if (result.status === 'ERROR') {
		toastr.error(result.message, 'Помилка');
		return;
	}
	toastr.success(result.message, 'Успіх', { 'positionClass': 'toast-top-center' });

}

function restoreModal(modal) {
	document.getElementById('submit').removeAttribute('onclick');
	document.getElementById('submit').removeAttribute('data-id');

	const form = document.getElementById('formModal').querySelector('form');
	form.reset();

	modal.querySelector('.modal-title').innerHTML = 'Форма';
}

function get_fields() {
	return ['name', 'number', 'approval_document', 'approval_document', 'document_date_start', 'document_date_finish', 'periodicity', 'document_type', 'documentation_category_id'];
}

async function getDocType(doc_type, tab) {
	let result = await fetchGetData('documentations/get_data_doc_type_ajax/' + doc_type);

	if (result.status === 'ERROR') {
		toastr.error(result.message, 'Помилка');
		return;
	}

	if (document.getElementById(tab)) {
		let tr = '';
		await result.document.forEach((el) => {
			tr += `
			<tr>
				<td style="white-space: normal;">${el.name}</td>
				<td>${el.document_date_start}</td>
				<td>${el.document_date_finish}</td>
				<td>${el.document_type}</td>
				<td>${el.documentation_category_id}</td>
			</tr>
		`;
		});

		document.getElementById(tab).innerHTML = getTable();
		let table = document.getElementById(tab);
		table.querySelector('table tbody').innerHTML = tr;
	}

}

function getTable() {
	let table = `
		<div class="table-responsive p-0">
			<table class="table table-bordered table-striped table-hover align-items-center mb-0">
				<thead>
					<tr>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 60%;">Назва документа</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 9%;">Затвердження</th>
						<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 8%;">Дата закінчення</th>
						<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 8%;">Вид документа</th>
						<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 15%;">Група (Підгрупа) документа</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	`;

	return table;
}

getDocType(1, 'op');

function getListOp(event) {
	const urlParams = new URLSearchParams(window.location.search);
	window.open('/documentations/list_pdf/1/' + urlParams.get('plot_id'), '_blank');
}

function getListPb(event) {
	const urlParams = new URLSearchParams(window.location.search);
	window.open('/documentations/list_pdf/2/' + urlParams.get('plot_id'), '_blank');
}

function getListTe(event) {
	const urlParams = new URLSearchParams(window.location.search);
	window.open('/documentations/list_pdf/3/' + urlParams.get('plot_id'), '_blank');
}


