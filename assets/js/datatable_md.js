$(document).ready(function () {
	const table = $(".datatable")
		.on("processing.dt", function (e, settings, processing) {
			$(".loading").css("display", processing ? "block" : "none");
		}).dataTable({
			stateDuration: 60 * 60 * 24 * 365,
			// DataTables - Options
			dom:
				"<'row'<'col-sm-12 col-md-2 my-2 d-non'l><'col-sm-12 col-md-8 my-2 text-center d-non'B><'col-sm-12 col-md-2 my-1 d-non'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-6 my-2 d-flex justify-content-start'i><'col-sm-12 col-md-6 my-2 d-flex justify-content-end'p>>",
			lengthMenu: [
				[5, 10, 20, 50, 100, 200, 300, -1],
				[
					"Показати 5 записів",
					"Показати 10 записів",
					"Показати 20 записів",
					"Показати 50 записів",
					"Показати 100 записів",
					"Показати 200 записів",
					"Показати 300 записів",
					"Показати всі записи",
				],
			],
			// DataTables - Callbacks
			preDrawCallback: function (settings) {
			},
			drawCallback: function (settings) {
			},
			headerCallback: function (thead, data, start, end, display) {
			},
			initComplete: function (settings, json) {
				$(".datatable").removeClass("d-none");
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

	// $('.filter').on("change", function () {
	// 	localStorage.setItem(this.name, this.value);

	// 	console.log(this.name, this.value);

	// 	table.api()
	// 		.columns('.' + this.name)
	// 		.search(this.value ? "^" + this.value + "$" : "", true, true)
	// 		.draw();
	// });
});
