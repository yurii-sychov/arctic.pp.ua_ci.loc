async function editSubNumberR3(event) {
	const passport_id = event.target.dataset.passport_id;
	const sub_number_r3 = event.target.value;

	try {
		const response = await fetch('/passports_r3/edit_sub_number_r3_ajax',
			{
				method: "POST",
				headers: {
					'Content-Type': 'application/json;charset=utf-8',
					"X-Requested-With": "XMLHttpRequest",
				},
				body: JSON.stringify({ passport_id, sub_number_r3 }),
			});
		const data = await response.json();
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	}
	catch (error) {
		console.log(error);
	}

}

function deleteStringInput(event) {
	event.target.value = event.target.value.replace(/\D/g, '');
}

$(document).ready(function () {
	// $('.select2').select2({
	// 	theme: 'bootstrap-5'
	// });

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
						dt.removeClass('btn-secondary')
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
						dt.removeClass('btn-secondary')
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
						dt.removeClass('btn-secondary')
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
						dt.removeClass('btn-secondary')
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


