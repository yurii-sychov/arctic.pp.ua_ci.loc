$(document).ready(function () {
	let table = $('#table').dataTable({
		// DataTables - Options
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
