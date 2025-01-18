function activeForm(event) {
	if ($(".datatable tbody").find("input, select").length > 0) {
		let table = $(".datatable").DataTable();
		$(table.cells().nodes()).find("input, select").prop("disabled", function (i, v) {
			if (v == true) {
				event.currentTarget.className = "btn btn-secondary";
				event.currentTarget.title = "Деактувати форму";
				event.currentTarget.innerHTML = "<span>Деактувати форму</span>";
			}
			else {
				event.currentTarget.className = "btn btn-warning";
				event.currentTarget.title = "Активувати форму";
				event.currentTarget.innerHTML = "<span>Активувати форму</span>";
			}
			return !v;
		});
	}
}

$(document).ready(function () {
	$(".datatable").on("requestChild.dt", function (e, row) {
		row.child(format(row.data())).show();
		$(row.child()).addClass("bg-info");
	});

	const table = $(".datatable")
		.on("processing.dt", function (e, settings, processing) {
			$(".loading").css("display", processing ? "block" : "none");
		})
		.DataTable({
			// DataTables - Options
			dom:
				"<'row'<'col-sm-12 col-md-2 my-1 d-none'l><'col-sm-12 col-md-8 my-1 text-center d-none'B><'col-sm-12 col-md-2 my-1 d-none'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1'p>>",
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
			buttons: {
				dom: {
					container: {
						tag: "div",
					},
				},
				buttons: [
					{
						extend: "excel",
						className: "btn-success d-none d-md-inline",
						text: '<i class="fas fa-file-excel"></i>',
						exportOptions: {
							format: {
								body: function (data, row, col, node) {
									let result;
									if ($(node).find('input').val() || $(node).find('input').val() === '') {
										result = $(node).find('input').val();
									}
									else if ($(node).find('select').val() || $(node).find('select').val() === '') {
										result = $(node).find('select :selected').text();
									}
									else {
										result = data;
									}
									return result;

									// return ($(node).find('input').val() || $(node).find('input').val() === '') ? $(node).find('input').val() : data;
								},
							},
							columns: ':not(".more, .delete, .word")',
						},
						attr: {
							id: "ButtonExcel",
							title: "Экспорт в Excel",
						},
						init: function (e, dt, node, config) {
							dt.removeClass("btn-secondary");
						},
					},
					{
						extend: "pdf",
						className: "btn-danger d-none d-md-inline",
						text: '<i class="fas fa-file-pdf"></i>',
						exportOptions: {
							format: {
								body: function (data, row, col, node) {
									let result;
									if ($(node).find('input').val() || $(node).find('input').val() === '') {
										result = $(node).find('input').val();
									}
									else if ($(node).find('select').val() || $(node).find('select').val() === '') {
										result = $(node).find('select :selected').text();
									}
									else {
										result = data;
									}
									return result;

									// return ($(node).find('input').val() || $(node).find('input').val() === '') ? $(node).find('input').val() : data;
								},
							},
							columns: ':not(".more, .delete, .word")',
						},
						attr: {
							id: "ButtonPDF",
							title: "Экспорт в PDF",
						},
						init: function (e, dt, node, config) {
							dt.removeClass("btn-secondary");
						},
						orientation: "landscape",
						pageSize: "A1",
						download: "open",
					},
					{
						className: "btn-warning d-none d-md-inline buttons-active-form",
						text: "Активувати форму",
						attr: {
							title: "Активувати форму",
							onClick: "activeForm(event);",
						},
						init: function (e, dt, node, config) {
							dt.removeClass("btn-secondary");
						},
					},
					{
						className: "btn-success d-none d-md-inline buttons-create",
						text: '<i class="fas fa-plus"></i> Створити',
						attr: {
							title: "Додати запис",
							onClick: "createRow(event);",
						},
						init: function (e, dt, node, config) {
							dt.removeClass("btn-secondary");
						},
					},
				],
			},
			// DataTables - Callbacks
			// drawCallback: function (settings) {
			// 	$("#datatables_length")
			// 		.removeClass("dataTables_length")
			// 		.addClass("d-grid gap-2 d-md-flex")
			// 		.find("select")
			// 		.removeClass("form-select-sm");
			// 	$("#datatables_length").parent().removeClass("d-none");
			// 	$("#datatables_filter")
			// 		.removeClass("dataTables_filter")
			// 		.addClass("d-grid gap-2 d-md-flex justify-content-md-end")
			// 		.find("input")
			// 		.removeClass("form-control-sm");
			// 	$("#datatables_filter").parent().removeClass("d-none");
			// },
			headerCallback: function (thead, data, start, end, display) {
				$(thead).find('th').each(function (k, v) {
					if ($(this).text()) {
						// $(this).text($(this).text().toUpperCase());
					};
				});
			},
			initComplete: function (settings, json) {
				$(".datatable").removeClass("d-none");

				$(".dataTables_length")
					.addClass("d-grid gap-2 d-md-flex")
					.find("select")
					.removeClass("custom-select-sm");
				$(".dataTables_length").parent().removeClass("d-none");

				$(".dt-buttons").removeClass("btn-group");
				$(".dt-buttons").parent().removeClass("d-none");

				$(".dataTables_filter")
					.addClass("d-grid gap-2 d-md-flex justify-content-md-end")
					.find("input")
					.removeClass("form-control-sm");
				$(".dataTables_filter").find("input").attr("name", "search");
				$(".dataTables_filter").parent().removeClass("d-none");
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

	// table.on('order.dt search.dt', function () {
	// 	let i = 1;

	// table
	// 	.cells(null, 0, { search: 'applied', order: 'applied' })
	// 	.every(function (cell) {
	// 		this.data(i++);
	// 	});
	// }).draw();

	// Add event listener for opening and closing details
	table.on("click", "td a.dt-control", function () {
		let tr = $(this).closest("tr");
		let row = table.row(tr);

		// $(this).find('i').toggleClass('fa-eye-slash text-primary fa-eye text-info');

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
		}
		else {
			// Open this row
			let html = format(row.data());
			row.child(html).show();
			$(row.child()).addClass("bg-info");
		}
	});

	// table_api(table);
});
