function fillMaterial_id(event) {
	const material_id = event.target.value;
	$('.datatable').DataTable().$('.material').val(material_id);
	$('.datatable').DataTable().$('.material').addClass('bg-info');
	let ma = $('#Material').val();
	let qu = $('#Quantity').val();
	if (ma && qu) {
		$('#SendData').removeAttr('disabled');
	}
	else {
		$('#SendData').attr('disabled', 'disabled');

	}
}

function fillQuantity(event) {
	const quantity = event.target.value;
	$('.datatable').DataTable().$('.quantity').val(quantity);
	$('.datatable').DataTable().$('.quantity').addClass('bg-info');
	let ma = $('#Material').val();
	let qu = $('#Quantity').val();
	if (ma && qu) {
		$('#SendData').removeAttr('disabled');
	}
	else {
		$('#SendData').attr('disabled', 'disabled');
	}
}

async function sendData(event) {
	event.preventDefault();
	let form = $('.datatable').DataTable().$('input, select').serialize();

	try {
		const response = await fetch('/materials/add_extra_materials/', {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: form
		});
		const data = await response.json();
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
			location.href = location.href;
		} else {
			toastr.error('Щось пішло не так', "Помилка");
		}
	} catch (error) {
		console.log(error);
	}
}

$(document).ready(function () {
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
				// {
				// 	extend: 'excel',
				// 	text: '<i class="bi bi-file-earmark-excel"></i> Экспорт в Excel',
				// 	className: 'btn-success',
				// 	exportOptions: {
				// 		columns: ':not(".edit, .view, .delete")' + ':visible'
				// 	},
				// 	attr: {
				// 		id: 'ButtonExcel',
				// 		title: 'Экспорт в Excel',
				// 		"data-bs-toggle": "tooltip"
				// 	},
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	}
				// },
				// {
				// 	extend: 'csv',
				// 	text: '<i class="bi bi-file-earmark-excel"></i>',
				// 	className: 'btn-info',
				// 	exportOptions: {
				// 		columns: ':not(".edit, .view, .delete")' + ':visible'
				// 	},
				// 	attr: {
				// 		id: 'ButtonCsv',
				// 		title: 'Экспорт в CSV',
				// 		"data-bs-toggle": "tooltip"
				// 	},
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	}
				// },
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
				// {
				// 	extend: 'copy',
				// 	text: '<i class="bi bi-clipboard"></i>',
				// 	className: 'btn-warning',
				// 	exportOptions: {
				// 		columns: ':not(".edit, .view, .delete")'
				// 	},
				// 	attr: {
				// 		id: 'ButtonCopy',
				// 		title: 'Копировать',
				// 		"data-bs-toggle": "tooltip"
				// 	},
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	}
				// },
				// {
				// 	extend: 'print',
				// 	text: '<i class="bi bi-printer"></i>',
				// 	className: 'btn btn-danger',
				// 	attr: {
				// 		id: 'ButtonPrint',
				// 		title: 'Печатать',
				// 		"data-bs-toggle": "tooltip"
				// 	},
				// 	init: function (e, dt, node, config) {
				// 		dt.removeClass('btn-secondary')
				// 	},
				// 	// autoPrint: false,
				// 	exportOptions: {
				// 		columns: ':visible'
				// 		// format: {
				// 		// 	header: function ( data, columnIdx, th) {
				// 		// 		return columnIdx +': '+ data;
				// 		// 	}
				// 		// }
				// 	}
				// },
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
});
