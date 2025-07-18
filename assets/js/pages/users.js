$(document).ready(function () {
	const table = $('.datatable')
		.on('processing.dt', function (e, settings, processing) {
			$('.loading').css('display', processing ? 'block' : 'none');
		})
		.DataTable({
			stateDuration: 60 * 60 * 24 * 365,
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
