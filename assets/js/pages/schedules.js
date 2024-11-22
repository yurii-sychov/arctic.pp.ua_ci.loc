function genarate_program_excel(event) {
	location = '/schedules/genarate_program_excel';
}

function genarate_multi_year_schedule_kr_excel(event) {
	location = '/schedules/genarate_multi_year_schedule_excel/1';
}

function genarate_multi_year_schedule_pr_excel(event) {
	location = '/schedules/genarate_multi_year_schedule_excel/2';
}

function genarate_multi_year_schedule_to_excel(event) {
	location = '/schedules/genarate_multi_year_schedule_excel/3';
}

function deleteSchedule(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href = "/schedules/delete/" + $(event.currentTarget).parents("tr").data("id");
	} else {
		return;
	}
}

function addRowMaterial(event) {
	fetch("/materials/get_materials_ajax")
		.then((response) => {
			return response.json();
		})
		.then((data) => {
			let options = "";
			data.materials.forEach((element) => {
				options += `<option value="${element.id}">${element.name + " (" + element.r3_id + ")"
					}</option>`;
			});
			$(".new-row select").append(options);

			$(".new-row select").on("change", function () {
				const select = this.value;
				data.materials.forEach((el) => {
					if (el.id == select) {
						$(this).closest("tr").find(".unit").html(el.unit);
						// $(this).closest('tr').find('.quantity input').val('0.00');
					}
				});
				if (select == "") {
					$(this).closest("tr").find(".unit").html("");
					$(this).closest("tr").find(".quantity input").val("0.00");
				}
			});
		});

	const schedule_id = $(event.currentTarget).data("schedule_id");
	$(event.target)
		.closest(".row-button")
		.next(".row-table")
		.find("table > tbody")
		.prepend(
			`<tr class="align-middle new-row bg-secondary">
			<td class="number"></td>
			<td class="material">
				<input type="hidden" name="schedule_id[]" value="${schedule_id}" />
				<select class="form-select form-select-sm" name="material_id[]">
					<option value="">Оберіть матеріал</option>
				<select/>
			</td>
			<td class="unit"></td>
			<td class="text-center price">???</td>
			<td class="quantity"><input type="text" class="form-control form-control-sm text-center" name="quantity[]" value="0.00" /></td>
			<td class="is_do"></td>
			<td class="text-center delete">
				<a href="javascript:void(0);" onclick="deleteExtraMaterial(event);">
					<i class="bi bi-trash text-info" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
				</a>
			</td>
		</tr>`
		);

	$(event.target).closest("button").prev().removeClass("d-none");
	$(event.target).closest("button").next().next().removeClass("disabled");
}

function removeRowMaterial(event) {
	$(event.target)
		.closest(".row-button")
		.next(".row-table")
		.find("table > tbody > tr:first")
		.remove();
	let new_rows = $(".new-row").length;
	if (new_rows == 0) {
		$(event.currentTarget).addClass("d-none");
		$(event.currentTarget).next().next().next().addClass("disabled");
	}
}

function activeFormMaterials(event) {
	$(event.currentTarget)
		.closest("div.row-button")
		.next(".row-table")
		.find("table .quantity input")
		.prop("disabled", (i, v) => !v);
}

async function saveMaterials(event) {
	let form = $(event.target).closest(".collapse, .collapse_main").find("form").serialize();

	try {
		const response = await fetch("/schedules/add_material_ajax", {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: form,
		});
		const data = await response.json();

		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
			location.href = location.href;
		} else {
			toastr.error(data.message, "Помилка");
		}
	} catch (error) {
		console.log(error);
	}
}

function deleteMaterial(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		const schedule_id = $(event.target).closest("tr").data("schedule_id");
		const material_id = $(event.target).closest("tr").data("material_id");
		const year_service = $(event.target).closest("tr").data("year_service");

		$.ajax({
			method: "POST",
			url: "/schedules/delete_material_ajax",
			data: { schedule_id, material_id, year_service },
		}).done(function (data) {
			if (data.status === "SUCCESS") {
				toastr.success(data.message, "Успіх");
				$(event.target).closest("tr").remove();
				// location.href = location.href;
			} else {
				toastr.error(data.message, "Помилка");
			}
		});
	} else {
		return;
	}
}

function deleteWorker(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		const schedule_id = $(event.target).closest("tr").data("schedule_id");
		const worker_id = $(event.target).closest("tr").data("worker_id");
		const year_service = $(event.target).closest("tr").data("year_service");
		const count = $(event.target).closest("tr").data("count");

		$.ajax({
			method: "POST",
			url: "/schedules/delete_worker_ajax",
			data: { schedule_id, worker_id, year_service, count },
		}).done(function (data) {
			if (data.status === "SUCCESS") {
				toastr.success(data.message, "Успіх");
				$(event.target).closest("tr").remove();
				// location.href = location.href;
			} else {
				toastr.error(data.message, "Помилка");
			}
		});
	} else {
		return;
	}
}

function deleteTechnic(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		const schedule_id = $(event.target).closest("tr").data("schedule_id");
		const technic_id = $(event.target).closest("tr").data("technic_id");
		const year_service = $(event.target).closest("tr").data("year_service");

		$.ajax({
			method: "POST",
			url: "/schedules/delete_technic_ajax",
			data: { schedule_id, technic_id, year_service },
		}).done(function (data) {
			if (data.status === "SUCCESS") {
				toastr.success(data.message, "Успіх");
				$(event.target).closest("tr").remove();
				// location.href = location.href;
			} else {
				toastr.error(data.message, "Помилка");
			}
		});
	} else {
		return;
	}
}

function deleteExtraMaterial(event) {
	let new_rows = $(".new-row").length;
	if (new_rows == 1) {
		$(event.currentTarget)
			.closest(".row-table")
			.prev(".row-button")
			.find(".button-delete")
			.addClass("d-none");
		$(event.currentTarget)
			.closest(".row-table")
			.prev(".row-button")
			.find(".botton-save-materials")
			.addClass("disabled");
	}
	$(event.target).closest("tr").remove();
}

function editMaterialQuantity(event) {
	const schedule_id = $(event.target).closest("tr").data("schedule_id");
	const material_id = $(event.target).closest("tr").data("material_id");
	const year_service = $(event.target).closest("tr").data("year_service");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/schedules/edit_material_quantity_ajax",
		data: { schedule_id, material_id, year_service, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editWorkerQuantity(event) {
	const schedule_id = $(event.target).closest("tr").data("schedule_id");
	const worker_id = $(event.target).closest("tr").data("worker_id");
	const year_service = $(event.target).closest("tr").data("year_service");
	const count = $(event.target).closest("tr").data("count");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/schedules/edit_worker_quantity_ajax",
		data: { schedule_id, worker_id, year_service, count, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editTechnicQuantity(event) {
	const schedule_id = $(event.target).closest("tr").data("schedule_id");
	const technic_id = $(event.target).closest("tr").data("technic_id");
	const year_service = $(event.target).closest("tr").data("year_service");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/schedules/edit_technic_quantity_ajax",
		data: { schedule_id, technic_id, year_service, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editMonth(event) {
	const id = $(event.target).closest("tr").data("id");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/schedules/edit_month_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeQuantity(event) {
	event.target.value = event.target.value.replace(/,/, ".");
	const quantity = $(event.target).val();
}

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

let myCollapsibles = $("table .collapse");

myCollapsibles.each(function (index, el) {
	el.addEventListener("show.bs.collapse", function (event) {
		myCollapsibles.removeClass("show");
	});
	el.addEventListener("shown.bs.collapse", function (event) {
		localStorage.setItem("showCollapse", event.target.id);
	});
	el.addEventListener("hidden.bs.collapse", function (event) {
		localStorage.removeItem("showCollapse");
	});
});

let myCollapse = document.getElementById(localStorage.getItem("showCollapse"));

if (myCollapse) {
	let bsCollapse = new bootstrap.Collapse(myCollapse, {
		toggle: true,
	});
}

let tooltipEl = $('[data-bs-toggle="tooltip"]');
if (tooltipEl) {
	for (let i = 0; i < tooltipEl.length; i++) {
		let tooltip = new bootstrap.Tooltip(tooltipEl[i]);
	}
}

$(window).on("load", function () {
	$("table").removeClass("d-none");
	$(".loading").css("display", "none");
});

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

async function get_materials_for_schedule(schedule_id) {
	try {
		const response = await fetch('/schedules/get_materials_for_schedule/' + schedule_id, {
			method: "GET",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
		});
		const data = await response.json();
		return data;
	} catch (error) {
		console.log(error);
	}
}

async function get_workers_for_schedule(schedule_id) {
	try {
		const response = await fetch('/schedules/get_workers_for_schedule/' + schedule_id, {
			method: "GET",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
		});
		const data = await response.json();
		return data;
	} catch (error) {
		console.log(error);
	}
}

async function get_technics_for_schedule(schedule_id) {
	try {
		const response = await fetch('/schedules/get_technics_for_schedule/' + schedule_id, {
			method: "GET",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
		});
		const data = await response.json();
		return data;
	} catch (error) {
		console.log(error);
	}
}

async function get_prices_materials_for_schedule(id) {
	try {
		const response = await fetch('/schedules/get_prices_materials_for_schedule/' + id, {
			method: "GET",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
		});
		const data = await response.json();
		return data;
	} catch (error) {
		console.log(error);
	}
}

async function get_note_for_schedule(id) {
	try {
		const response = await fetch('/schedules/get_note_for_schedule/' + id, {
			method: "GET",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
		});
		const data = await response.json();
		return data;
	} catch (error) {
		console.log(error);
	}
}

async function addNote(event) {
	// console.log(event.target.dataset.schedule_id);
	let schedule_id = event.target.dataset.schedule_id;
	let note = event.target.value;
	try {
		const response = await fetch('/schedules/add_note_for_schedule/', {
			method: "POST",
			headers: {
				'Content-Type': 'application/json;charset=utf-8',
				"X-Requested-With": "XMLHttpRequest",
			},
			body: JSON.stringify({ 'schedule_id': schedule_id, 'note': note }),
		});
		const data = await response.json();
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	} catch (error) {
		console.log(error);
	}
}

async function addWorker(event) {
	let form = $('#workerFormModal').serialize();

	try {
		const response = await fetch('/schedules/add_worker_for_schedule/', {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: form,
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

async function addWorkers(event) {
	let form = $('#workersFormModal').serialize();

	try {
		const response = await fetch('/schedules/add_workers_for_schedule/', {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: form,
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

function fillFormWorkerModal(event) {
	const schedule_id = event.target.dataset.schedule_id;
	$('#workerFormModal').find('[name="schedule_id"]').val(schedule_id);
}

function fillFormWorkersModal(event) {
	const schedule_id = event.target.dataset.schedule_id;
	$('#workersFormModal').find('[name="schedule_id"]').val(schedule_id);
}

async function addTechnic(event) {
	let form = $('#technicFormModal').serialize();

	try {
		const response = await fetch('/schedules/add_technic_for_schedule/', {
			method: "POST",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
			body: form,
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

function fillFormTechnicModal(event) {
	const schedule_id = event.target.dataset.schedule_id;
	$('#technicFormModal').find('[name="schedule_id"]').val(schedule_id);
}

async function format(d) {
	// console.log('d', d);
	let data = d.data;

	let materials = await get_materials_for_schedule(d.schedule_id);
	let workers = await get_workers_for_schedule(d.schedule_id);
	let technics = await get_technics_for_schedule(d.schedule_id);
	let prices_materials = await get_prices_materials_for_schedule(d.schedule_id);
	let note = await get_note_for_schedule(d.schedule_id);

	let materials_html = '';
	let workers_html = '';
	let technics_html = '';

	var n = 1;
	if (materials.data != undefined) {
		materials.data.forEach(function (v, k) {
			var quantity = (d.user_group === 'admin' || d.user_group === 'engineer')
				? `<input type="text" name="quantity" class="form-control form-control-sm text-center" value="${v.quantity}" tabindex="4" disabled onchange="editMaterialQuantity(event);" onkeyup="changeQuantity(event);">`
				: `${v.quantity}`;
			var del = (d.user_group === 'admin' || d.user_group === 'engineer')
				? `<a href="javascript:void(0);" onClick="deleteMaterial(event);">
				<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
			</a>`
				: `<a href="javascript:void(0);">
				<i class="bi bi-trash text-secondary" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
			</a>`;
			materials_html +=
				`<tr style="background:#adb5bd;" class="align-middle text-center" data-schedule_id="${v.schedule_id}" data-material_id="${v.material_id}" data-year_service="${v.year_service}">
				<td class="text-center number">${n}</td>
				<td class="text-start material ${(v.is_extra == 1) ? 'text-danger' : ''}">${v.name}${(v.is_extra == 1) ? '*' : ''}</td>
				<td class="text-start unit">${v.unit}</td>
				<td class="text-center price">???</td>
				<td class="text-center quantity">${quantity}</td>
				<td class="text-center is_do"><input type="checkbox" class="form-check-input" title="${v.is_repair == 1 ? 'Планувати в ремонт' : 'Не планувати в ремонт'}" ${v.is_repair == 0 ? 'checked' : ''} style="cursor: pointer;" onClick="changeIsRepair(event, 'material');" disable></td>
				<td class="text-center delete">${del}</td>
			</tr>`;
			n++;
		});
	}

	var n = 1;
	if (workers.data != undefined) {
		workers.data.forEach(function (v, k) {
			var quantity = (d.user_group === 'admin' || d.user_group === 'engineer')
				? `<input type="text" name="quantity" class="form-control form-control-sm text-center" value="${v.quantity}" tabindex="4" disabled onchange="editWorkerQuantity(event);" onkeyup="changeQuantity(event);">`
				: `${v.quantity}`;
			var del = (d.user_group === 'admin' || d.user_group === 'engineer')
				? `<a href="javascript:void(0);" onClick="deleteWorker(event);">
				<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
			</a>`
				: `<a href="javascript:void(0);">
				<i class="bi bi-trash text-secondary" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
			</a>`;
			workers_html +=
				`<tr style="background:#20c997;" class="align-middle text-center" data-schedule_id="${v.schedule_id}" data-worker_id="${v.worker_id}" data-year_service="${v.year_service}" data-count=${v.count}>
				<td class="text-center number">${n}</td>
				<td class="text-start worker ${(v.is_extra == 1) ? 'text-danger' : ''}">${v.name}${(v.is_extra == 1) ? '*' : ''}</td>
				<td class="text-start unit">${v.unit}</td>
				<td class="text-center price">???</td>
				<td class="text-center quantity">${quantity}</td>
				<td class="text-center is_do"><input type="checkbox" class="form-check-input" title="${v.is_repair == 1 ? 'Планувати в ремонт' : 'Не планувати в ремонт'}" ${v.is_repair == 0 ? 'checked' : ''} style="cursor: pointer;" onClick="changeIsRepair(event, 'worker');" disable></td>
				<td class="text-center delete">${del}</td>
			</tr>`;
			n++;
		});
	}

	var n = 1;
	if (technics.data != undefined) {
		technics.data.forEach(function (v, k) {
			var quantity = (d.user_group === 'admin' || d.user_group === 'engineer')
				? `<input type="text" name="quantity" class="form-control form-control-sm text-center" value="${v.quantity}" tabindex="4" disabled onchange="editTechnicQuantity(event);" onkeyup="changeQuantity(event);">`
				: `${v.quantity}`;
			var del = (d.user_group === 'admin' || d.user_group === 'engineer')
				? `<a href="javascript:void(0);" onClick="deleteTechnic(event);">
				<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
			</a>`
				: `<a href="javascript:void(0);">
				<i class="bi bi-trash text-secondary" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
			</a>`;
			technics_html +=
				`<tr tr style = "background:#fd7e14;" class="align-middle text-center" data-schedule_id="${v.schedule_id}" data-technic_id="${v.technic_id}" data-year_service="${v.year_service}">
				<td class="text-center number">${n}</td>
				<td class="text-start technic ${(v.is_extra == 1) ? 'text-danger' : ''}">${v.name}${(v.is_extra == 1) ? '*' : ''}</td>
				<td class="text-start unit">${v.unit}</td>
				<td class="text-center price">???</td>
				<td class="text-center quantity">${quantity}</td>
				<td class="text-center is_do"><input type="checkbox" class="form-check-input" title="${v.is_repair == 1 ? 'Планувати в ремонт' : 'Не планувати в ремонт'}" ${v.is_repair == 0 ? 'checked' : ''} style="cursor: pointer;" onClick="changeIsRepair(event, 'technic');" disable></td>
				<td class="text-center delete">${del}</td>
			</tr>`;
			n++;
		});
	}

	let button = (d.user_group === 'admin' || d.user_group === 'engineer')
		? `<button class="button-add-work btn btn-outline-info btn-sm" title="Додати техніку" onClick="fillFormTechnicModal(event);" data-schedule_id="${d.schedule_id}" data-bs-toggle="modal" data-bs-target="#addTechnicModal">Додати техніку</button>
		<button class="button-add-work btn btn-outline-warning btn-sm" title="Додати працівника" onClick="fillFormWorkerModal(event);" data-schedule_id="${d.schedule_id}" data-bs-toggle="modal" data-bs-target="#addWorkerModal">Додати працівника</button>
		<button class="button-add-works btn btn-outline-success btn-sm" title="Додати працівників" onClick="fillFormWorkersModal(event);" data-schedule_id="${d.schedule_id}" data-bs-toggle="modal" data-bs-target="#addWorkersModal">Додати працівників</button>
		<button class="button-delete btn btn-danger btn-sm d-none" title="Видалити рядок" onClick="removeRowMaterial(event);"><i class="bi bi-dash-lg"></i></button>
		<button class="button-add btn btn-primary btn-sm" title="Додати рядок" onClick="addRowMaterial(event);" data-schedule_id="${d.schedule_id}"><i class="bi bi-plus-lg"></i></button>
		<button class="button-active-form btn btn-success btn-sm" title="Внести зміни" onClick="activeFormMaterials(event);"><i class="bi bi-pencil"></i></button>
		<button class="botton-save-materials btn btn-info btn-sm disabled" title="Зберегти зміни" onClick="saveMaterials(event);">Зберегти</button>`
		: '';
	let html =
		`<div class="row collapse_main">
			<div class="col-md-12">
				<div class="row my-1 row-button">
					<div class="col-md-12 text-end">
						${button}
					</div>
				</div>
				<div class="row my-1 row-table">
					<div class="col-md-12">
						<form id="Form_${d.schedule_id}">
							<h2 class="text-center text-danger" data-bs-toggle="tooltip" title="Далі буде...">${data.equipment} ${data.dno} (на ремонт заплановано <span class="text-success">${new Intl.NumberFormat('ua-UK', { style: 'currency', currency: 'UAH' }).format(prices_materials.data)}</span>)</h2>
							<table class="table table-striped table-hover table-bordered table-sm bg-light ">
								<caption><strong>* Червоним кольором виділені додаткові ресурси, або ті в яких змінена кількість</strong></caption>
								<thead class="table-dark">
									<tr class="align-middle text-center">
										<th class="text-center" style="width:5%;"style="cursor: pointer;">№ п/п</th>
										<th class="text-center" style="width:35%;">Ресурс</th>
										<th class="text-center" style="width:35%;">Одиниця виміру</th>
										<th class="text-center" style="width:10%;">Ціна</th>
										<th class="text-center" style="width:9%;">Кількість</th>
										<th class="text-center" style="width:3%;"><input type="checkbox" class="form-check-input" disabled></th>
										<th class="text-center" style="width:3%;"><i class="bi bi-trash text-secondary"></i></th>
									</tr>
								</thead>
								<tbody>
									${materials_html}
									${workers_html}
									${technics_html}
								</tbody>
							</table>
						</form>
					<div>
				</div>
				<div class="row my-1">
					<div class="col-md-11">
						<input type="text" class="form-control placeholder="Додайте примітку" onchange="addNote(event);" data-schedule_id="${d.schedule_id}" value="${note.data.note ? note.data.note : ''}">
					</div>
				</div>
			</div>
		</div>`;
	return html;
}

async function set_rows(event) {
	let rows = event.target.value;
	const response = await fetch('/schedules/index?rows=' + rows, {
		method: "GET",
		headers: {
			"Content-Type": "application/x-www-form-urlencoded",
			"X-Requested-With": "XMLHttpRequest",
		},
	});
	location.href = location.href;
}

async function editAvrPrice(event) {
	let price = event.target.value;
	try {
		const response = await fetch('/schedules/edit_avr_price_ajax', {
			method: "POST",
			headers: {
				'Content-Type': 'application/json;charset=utf-8',
				"X-Requested-With": "XMLHttpRequest",
			},
			body: JSON.stringify({ 'price': price }),
		});
		const data = await response.json();
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
		console.log(data);
	} catch (error) {
		console.log(error);
	}
}

function changeIsRepair(event, resource) {
	const schedule_id = $(event.target).parents("tr").data("schedule_id");

	let resource_id = 0;

	if (resource === 'material') {
		resource_id = $(event.target).parents("tr").data("material_id");
	}
	if (resource === 'worker') {
		resource_id = $(event.target).parents("tr").data("worker_id");
	}
	if (resource === 'technic') {
		resource_id = $(event.target).parents("tr").data("technic_id");
	}

	let value;
	if ($(event.target).prop("checked")) {
		value = 0;
	} else {
		value = 1;
	}
	console.log('resource', resource);
	console.log('schedule_id', schedule_id);
	console.log('resource_id', resource_id);
	console.log('value', value);
	$.ajax({
		method: "POST",
		url: "/schedules/change_is_repair_ajax",
		data: { resource, schedule_id, resource_id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}


