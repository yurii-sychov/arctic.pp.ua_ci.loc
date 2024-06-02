function activeForm(event) {
	$(event.currentTarget).closest('div.tab-pane').find('.table-responsive').find('table .quantity input').prop('disabled', (i, v) => !v);
}

function changeQuantity(event) {
	event.target.value = event.target.value.replace(/,/, '.');
	const quantity = $(event.target).val();
	const price = $(event.target).closest('tr').find('.price').text();
	const price_total = (Number(quantity) * Number(price)).toFixed(2);
	$(event.target).closest('tr').find('.price_total').text(price_total);

	const price_totals = $(event.target).closest('table').find('.price_total');
	// console.log(price_totals)
	let summa_money = 0;
	price_totals.each(function (index, el) {
		summa_money += Number(el.innerText);
	})

	$(event.target).closest('table').find('tfoot .summa_money').text(new Intl.NumberFormat("ua-UA", { style: "currency", currency: "UAH" }).format(summa_money));
}

function exportToExcel(event) {
	location.href = '/estimates/generate_excel';
}

function exportToWord(event) {
	location.href = '/estimates/generate_word';
}

function exportToPDF(event) {
	location.href = '/estimates/generate_pdf';
}

function deleteMaterial(event) {
	event.preventDefault();
	let result = confirm('Ви впевнені?');
	if (result) {
		location.href = '/estimates/delete_material/' + $(event.currentTarget).parents('tr').data('cipher_id') + "/" + $(event.currentTarget).parents('tr').data('material_id');
	} else {
		return;
	}
}

function copyWorker(event) {
	event.preventDefault();
	let result = confirm('Ви дійсно хочете додати працівника з таким розрядом?');
	if (result) {
		location.href = '/estimates/copy_worker/' + $(event.currentTarget).parents('tr').data('cipher_id') + "/" + $(event.currentTarget).parents('tr').data('worker_id');
	} else {
		return;
	}
}

function deleteWorker(event) {
	event.preventDefault();
	let result = confirm('Ви впевнені?');
	if (result) {
		location.href = '/estimates/delete_worker/' + $(event.currentTarget).parents('tr').data('cipher_id') + "/" + $(event.currentTarget).parents('tr').data('worker_id');
	} else {
		return;
	}
}

function deleteTechnic(event) {
	event.preventDefault();
	let result = confirm('Ви впевнені?');
	if (result) {
		location.href = '/estimates/delete_technic/' + $(event.currentTarget).parents('tr').data('cipher_id') + "/" + $(event.currentTarget).parents('tr').data('technic_id');
	} else {
		return;
	}
}

function editQuantityMaterial(event) {
	const cipher_id = $(event.target).parents("tr").data("cipher_id");
	const material_id = $(event.target).parents("tr").data("material_id");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/estimates/edit_quantity_material_ajax",
		data: { cipher_id, material_id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editQuantityWorker(event) {
	const cipher_id = $(event.target).parents("tr").data("cipher_id");
	const worker_id = $(event.target).parents("tr").data("worker_id");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/estimates/edit_quantity_worker_ajax",
		data: { cipher_id, worker_id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editQuantityTechnic(event) {
	const cipher_id = $(event.target).parents("tr").data("cipher_id");
	const technic_id = $(event.target).parents("tr").data("technic_id");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/estimates/edit_quantity_technic_ajax",
		data: { cipher_id, technic_id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

const tabElements = $('button[data-bs-toggle="tab"]');

tabElements.each(function (index, el) {
	el.addEventListener('shown.bs.tab', function (event) {
		localStorage.setItem('estimates_activeTab', event.target.id);
	});
})

let activeTabEl = document.querySelector(localStorage.getItem('estimates_activeTab') ? '#' + localStorage.getItem('estimates_activeTab') : '#materials-tab');
let activeTab = new bootstrap.Tab(activeTabEl);

if (Object.getOwnPropertyNames(activeTab).length) activeTab.show();

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl)
});
