function activeForm(event) {
	const activeTab = localStorage.getItem('resources_activeTab');
	const inputs = $('#tabContent div[aria-labelledby="' + activeTab + '"]').find('.table-responsive').find('table input');
	if ($(inputs).prop('disabled') == 1) {
		$(inputs).prop('disabled', 0);
		$('#activeDeactiveForm').removeClass('btn-success').addClass('btn-warning').text('Деактивувати форму').blur();
	}
	else {
		$(inputs).prop('disabled', 1);
		$('#activeDeactiveForm').removeClass('btn-warning').addClass('btn-success').text('Активувати форму').blur();
	}
}

function changePrice(event) {
	event.target.value = event.target.value.replace(/,/, '.');
}

function exportToExcel(event) {
	alert('Планується експорт в Excel файл даних по всім ресурсам, починаючи з 2024 року.');
}

function exportToWord(event) {
	alert('Планується створення СЛ на отримання цін на наступний рік.');
}

function exportToPDF(event) {
	alert('Планується експорт в PDF файл даних по всім ресурсам з даними та фотографіями ресурсів.');
}

$('#tableMaterials input').keydown(function (e) {
	let td;
	switch (e.keyCode) {
		case 39: // right
			td = $(this).parent('td').next();
			break;

		case 37: // left
			td = $(this).parent('td').prev();
			break;

		case 40: // down
			var i = $(this).parent().index() + 1;
			td = $(this).closest('tr').next().find('td:nth-child(' + i + ')');
			break;

		case 38: // up
			var i = $(this).parent().index() + 1;
			td = $(this).closest('tr').prev().find('td:nth-child(' + i + ')');
			break;
	}
	if (td) td.find('input').select();
});


async function editValue(event) {
	const id = $(event.target).parents("td").data("id");
	const field_name = $(event.target).parents("td").data("field_name");
	const field = $(event.target).parents("td").data("field");
	const table = $(event.target).parents("td").data("table");
	const value = event.target.value;

	// const data = { id, field, table, value };

	const handler = (table === 'materials' || table === 'workers' || table === 'technics') ? 'edit_resource_ajax' : 'edit_price_ajax';

	let form = new FormData();
	form.set('id', id);
	form.set('field_name', field_name);
	form.set('field', field);
	form.set('table', table);
	form.set('value', value);

	try {
		const response = await fetch('/resources/' + handler, {
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

async function addDeleteCustomMaterial(event) {
	const material_id = $(event.target).data("material_id");
	const id = $(event.target).data("id");
	// event.target.removeAttribute("data-id");

	// if (!event.target.checked) {
	// 	return;
	// }

	const handler = event.target.checked ? 'add_custom_material_ajax' : 'delete_custom_material_ajax';

	let form = new FormData();
	form.set('material_id', material_id);
	form.set('id', id);

	try {
		const response = await fetch('/resources/' + handler, {
			method: 'POST',
			headers: {
				"X-Requested-With": "XMLHttpRequest"
			},
			body: form
		});
		const result = await response.json();
		if (event.target.checked) {
			event.target.disabled = true;
			event.target.setAttribute("data-id", result.id);
		}
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

function deleteMaterial(event) {
	event.preventDefault();
	// let result = confirm('Ви впевнені?');
	// if (result) {
	// 	location.href = '/resources/delete_material/' + $(event.currentTarget).parents('tr').data('id');
	// } else {
	// 	return;
	// }
	let result = prompt('Введіть пин-код для видалення даних');
	if (result == '0910') {
		location.href = '/resources/delete_material/' + $(event.currentTarget).parents('tr').data('id');
	} else {
		return;
	}
}

function deleteWorker(event) {
	event.preventDefault();
	// let result = confirm('Ви впевнені?');
	// if (result) {
	// 	location.href = '/resources/delete_worker/' + $(event.currentTarget).parents('tr').data('id');
	// } else {
	// 	return;
	// }
	let result = prompt('Введіть пин-код для видалення даних');
	if (result == '0910') {
		location.href = '/resources/delete_worker/' + $(event.currentTarget).parents('tr').data('id');
	} else {
		return;
	}
}

function deleteTechnic(event) {
	event.preventDefault();
	// let result = confirm('Ви впевнені?');
	// if (result) {
	// 	location.href = '/resources/delete_technic/' + $(event.currentTarget).parents('tr').data('id');
	// } else {
	// 	return;
	// }
	let result = prompt('Введіть пин-код для видалення даних');
	if (result == '0910') {
		location.href = '/resources/delete_technic/' + $(event.currentTarget).parents('tr').data('id');
	} else {
		return;
	}
}

$(document).ready(function () {
	const table = $('.datatable')
		.on('processing.dt', function (e, settings, processing) {
			$('.loading').css('display', processing ? 'block' : 'none');
		})
		.DataTable({
			// DataTables - Options
			dom: "<'row'<'col-sm-12 col-md-4 my-1 d-none'l><'col-sm-12 col-md-4 my-1'B><'col-sm-12 col-md-4 my-1 d-none'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1'p>>",
			lengthMenu: [
				[5, 10, 20, 50, 100, 200, -1],
				[
					"Показати 5 записів",
					"Показати 10 записів",
					"Показати 20 записів",
					"Показати 50 записів",
					"Показати 100 записів",
					"Показати 200 записів",
					"Показати всі записи",
				],
			],

			// DataTables - Callbacks
			drawCallback: function (settings) {
				$('#tabContent div.tab-pane').find('.table-responsive').find('table input').prop('disabled', 1);
				$('#activeDeactiveForm').removeClass('btn-warning').addClass('btn-success').text('Активувати форму');

				$('.datatable').removeClass('d-none');

				$(".dataTables_length").addClass("d-grid gap-2 d-md-flex").find("select").removeClass("form-select-sm");
				$(".dataTables_length").parent().removeClass("d-none");
				$(".dataTables_length").removeClass("dataTables_length");

				$(".dataTables_filter").addClass("d-grid gap-2 d-md-flex justify-content-md-end").find("input").removeClass("form-control-sm");
				$(".dataTables_filter").parent().removeClass("d-none");
				$(".dataTables_filter").removeClass("dataTables_filter");
			},

			// DataTables - Internationalisation
			language: {
				infoFiltered: "(відфільтровано з _MAX_ рядків)",
				paginate: {
					first: "«",
					previous: "‹",
					next: "›",
					last: "»",
				},
				info: "Показано з _START_ до _END_ запису з _TOTAL_",
				search: "_INPUT_",
				searchPlaceholder: "Пошук ...",
				lengthMenu: "_MENU_ ",
				zeroRecords: "Немає записів для відображення",
				emptyTable: "Дані відсутні в таблиці",
				processing: "Чекайте!",
			},
		});
});

const tabElements = $('button[data-bs-toggle="tab"]');

tabElements.each(function (index, el) {
	el.addEventListener('shown.bs.tab', function (event) {
		$('#tabContent div.tab-pane').find('.table-responsive').find('table input').prop('disabled', 1);
		$('#activeDeactiveForm').removeClass('btn-warning').addClass('btn-success').text('Активувати форму');
		localStorage.setItem('resources_activeTab', event.target.id);
	});
});

let activeTabEl = document.querySelector(localStorage.getItem('resources_activeTab') ? '#' + localStorage.getItem('resources_activeTab') : '#materials-tab');
let activeTab = new bootstrap.Tab(activeTabEl);

if (Object.getOwnPropertyNames(activeTab).length) activeTab.show();

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
	return new bootstrap.Tooltip(tooltipTriggerEl);
});
