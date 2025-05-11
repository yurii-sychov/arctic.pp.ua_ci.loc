async function openMaterialsModal(event) {
	let modal = new bootstrap.Modal(document.getElementById('materialsModal'), {
		keyboard: false
	});
	modal.show();

	let schedule_id = event.target.closest('tr').dataset.schedule_id;

	try {
		const response = await fetch('/realization/get_materials_for_schedule_id_ajax/' + schedule_id, {
			method: 'GET',
			headers: {
				'Content-Type': 'application/json'
			}
		});

		if (!response.ok) {
			throw new Error(`Ошибка HTTP: ${response.status}`);
		}

		const data = await response.json();

		let html = '';
		for (let i = 0; i < data.data.length; i++) {
			let is_extra = (data.data[i].is_extra == 1) ? 'table-danger' : 'table-default';
			html += `
				<tr class="${is_extra}" >
					<td class="text-center">${i + 1}</td>
					<td class="text-center">${data.data[i].r3_id}</td>
					<td class="text-start">${data.data[i].name}</td>
					<td class="text-center">${data.data[i].unit}</td>
					<td class="text-center">${data.data[i].quantity}</td>
				</tr>
			`;
		}

		$('#materialsModal').find('table tbody').empty().append(html);

	} catch (error) {
		toastr.error('Ошибка запроса:', error);
	}
}

function generateScheduleExcel(event) {
	const url = new URL(location.href);
	if (url.searchParams.get('stantion_id')) {
		location.href = '/schedules/genarate_year_schedule_simple_excel/' + url.searchParams.get('stantion_id');
	}
}

function generateMaterialsExcel(event) {
	location.href = '/realization/genarate_year_materials_simple_excel/';
}

function activeFormRow(event) {
	event.target.closest('tr').querySelector('td.date-service-actual input').toggleAttribute("disabled");
	event.target.classList.toggle('bi-pencil');
	event.target.classList.toggle('text-success');
	event.target.classList.toggle('bi-check-lg');
	event.target.classList.toggle('text-danger');
}

function editDateServiceActual(event) {
	const schedule_id = $(event.target).closest("tr").data("schedule_id");
	const year_service = $(event.target).closest("tr").data("year_service");
	const is_contract_method = $(event.target).closest("tr").data("is_contract_method");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/realization/edit_date_service_actual_ajax",
		data: { schedule_id, year_service, is_contract_method, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

$(".datemask").mask("99.99.9999");

$(document).ready(function () {
	$('.datatable').on('requestChild.dt', async function (e, row) {
		let data = {};
		data.schedule_id = row.id();
		data.user_group = row.node().dataset.user_group;
		data.data = row.data();
		let html = await format(data);
		row.child(html).show();
	});

	let table = $('.datatable')
		.on('processing.dt', function (e, settings, processing) {
			$('.loading').css('display', processing ? 'block' : 'none');
		})
		.DataTable({
			// DataTables - Options
			dom: "<'row'<'col-sm-12 col-md-4 my-1 d-none'l><'col-sm-12 col-md-4 my-1 text-center'B><'col-sm-12 col-md-4 my-1 d-none'f>>" +
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
			buttons: [
				// {
				// 	extend: 'colvisRestore',
				// 	text: 'Восстановить колонки'
				// },
				{
					extend: 'excel',
					text: '<i class="bi bi-file-earmark-excel"></i> Экспорт в Excel',
					className: 'btn-success',
					exportOptions: {
						columns: ':not(".edit, .view, .delete")' + ':visible'
					},
					attr: {
						id: 'ButtonExcel',
						title: 'Экспорт в Excel',
						"data-bs-toggle": "tooltip"
					},
					init: function (e, dt, node, config) {
						dt.removeClass('btn-secondary');
					}
				},
				{
					extend: 'csv',
					text: '<i class="bi bi-file-earmark-excel"></i>',
					className: 'btn-info',
					exportOptions: {
						columns: ':not(".edit, .view, .delete")' + ':visible'
					},
					attr: {
						id: 'ButtonCsv',
						title: 'Экспорт в CSV',
						"data-bs-toggle": "tooltip"
					},
					init: function (e, dt, node, config) {
						dt.removeClass('btn-secondary');
					}
				},
				// {
				// 	extend: 'pdf',
				// 	text: '<i class="bi bi-file-earmark-excel"></i>',
				// 	className: 'mb-2 btn-danger',
				// 	orientation: 'landscape',
				// 	pageSize: 'LEGAL',
				// 	download: 'open',
				// 	exportOptions: {
				// 		columns: ':not(".edit, .view, .delete")' + ':visible'
				// 	},
				// 	attr: {
				// 		id: 'ButtonPdf',
				// 		title: 'Экспорт в PDF'
				// 	},
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	}
				// },
				// {
				// 	extend: 'colvis',
				// 	text: '<i class="fas fa-list"></i>',
				// 	className: 'mb-2 btn-primary',
				// 	attr: {
				// 		id: 'ButtonColvis',
				// 		title: 'Управление колонками',
				// 	},
				// 	collectionLayout: 'fixed four-column',
				// 	columns: ':not(".noVis")',
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	},
				// 	columnText: function (dt, idx, title) {
				// 		if (dt.settings()[0].aoColumns[idx].name) {
				// 			return dt.settings()[0].aoColumns[idx].name;
				// 		}
				// 		else {
				// 			return dt.settings()[0].aoColumns[idx].title;
				// 		}
				// 	}
				// },
				{
					extend: 'copy',
					text: '<i class="bi bi-clipboard"></i>',
					className: 'btn-warning',
					exportOptions: {
						columns: ':not(".edit, .view, .delete")'
					},
					attr: {
						id: 'ButtonCopy',
						title: 'Копировать',
						"data-bs-toggle": "tooltip"
					},
					init: function (e, dt, node, config) {
						dt.removeClass('btn-secondary');
					}
				},
				{
					extend: 'print',
					text: '<i class="bi bi-printer"></i>',
					className: 'btn btn-danger',
					attr: {
						id: 'ButtonPrint',
						title: 'Печатать',
						"data-bs-toggle": "tooltip"
					},
					init: function (e, dt, node, config) {
						dt.removeClass('btn-secondary');
					},
					// autoPrint: false,
					exportOptions: {
						columns: ':visible'
						// format: {
						// 	header: function ( data, columnIdx, th) {
						// 		return columnIdx +': '+ data;
						// 	}
						// }
					}
				},
				// {
				// 	text: '<i class="fas fa-sync-alt"></i>',
				// 	className: 'mb-2 btn-info',
				// 	attr: {
				// 		id: 'ButtonRefresh',
				// 		title: 'Обновить'
				// 	},
				// 	action: function (e, dt, node, config) {
				// 		dt.ajax.reload();
				// 	},
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	}
				// },
				// {
				// 	text: 'Изменить всё',
				// 	className: 'mb-2',
				// 	attr:  {
				// 		'id': 'updateAllRows',
				// 		'type': 'submit',
				// 		'disabled': 'disabled',
				// 	}
				// },
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

				let tooltipEl = $('[data-bs-toggle="tooltip"]');
				if (tooltipEl) {
					for (let i = 0; i < tooltipEl.length; i++) {
						let tooltip = new bootstrap.Tooltip(tooltipEl[i]);
					}
				}
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
				info: "Показано з _START_ до _END_ запису з _TOTAL_ записів",
				search: "_INPUT_",
				searchPlaceholder: "Пошук ...",
				lengthMenu: "_MENU_ ",
				zeroRecords: "Немає записів для відображення",
				emptyTable: "Дані відсутні в таблиці",
				processing: "Чекайте!",
			},
		});

	table.on('order.dt search.dt', function () {
		let i = 1;

		table
			.cells(null, 0, { search: 'applied', order: 'applied' })
			.every(function (cell) {
				this.data(i++);
			});
	}).draw();

	// Add event listener for opening and closing details
	table.on("click", "td a.dt-control", async function () {
		let tr = $(this).closest("tr");
		let row = table.row(tr);

		// $(this).find("i").toggleClass("bi-eye-slash text-primary bi-eye text-info");

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
		} else {
			// Open this row
			let data = {};
			data.schedule_id = $(tr).data('id');
			data.user_group = $(tr).data('user_group');
			data.data = row.data();
			let html = await format(data);
			row.child(html).show();
		}
	});
});
