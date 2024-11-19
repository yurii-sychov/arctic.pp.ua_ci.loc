function format(d) {
	// `d` is the original data object for the row
	// console.log(d);
	const html = `
		<div class="row mb-2">
			<div class="col-md-4">
				<input type="checkbox" class="form-check-input" id="addNextYear_${d.id}" value="${d.id}" ${d.will_add == 1 ? 'checked' : null} onclick="addNextYear(event);" />
				<label class="form-check-label" for="addNextYear_${d.id}"><strong>Додати в графік наступного року</strong></label>
			</div>
			<div class="col-md-4">
				<input type="checkbox" class="form-check-input" id="deleteNextYear_${d.id}" value="${d.id}" ${d.is_repair == 0 ? 'checked' : null} onclick="deleteNextYear(event);" />
				<label class="form-check-label" for="deleteNextYear_${d.id}"><strong class="text-primary">Видалити з графіку наступного року</strong></label>
			</div>
			<div class="col-md-4">
				<input type="checkbox" class="form-check-input" id="updateMethodService_${d.id}" value="${d.id}" ${d.is_contract_method == 1 ? 'checked' : null} onclick="updateMethodService(event);" />
				<label class="form-check-label" for="updateMethodService_${d.id}"><strong>${d.is_contract_method == 1 ? 'Підрядний спосіб' : 'Господарський спосіб'}</strong></label>
			</div>
		</div>
		<hr>
		<div class="row mb-2">
			<div class="col-md-12">
				<label class="form-check-label" for="addNote_${d.id}"><strong>Примітка</strong></label>
				<input disabled type="text" class="form-control" id="addNote_${d.id}" value="${d.note ? d.note : ''}" placeholder="Додайте примітку та нажміть клавішу Enter" onclick="addNote(event);" />
			</div>
		</div>
	`;

	return html;
}

$(document).ready(function () {
	const serverSide = true;
	const table = $("#datatables")
		.on("processing.dt", function (e, settings, processing) {
			$(".loading").css("display", processing ? "block" : "none");
		})
		.DataTable({
			// DataTables - Features
			// processing: true,
			rowId: "id",
			autoWidth: false,
			stateSave: true,
			deferRender: true,
			pagingType: "full_numbers",
			serverSide: serverSide,

			// DataTables - Data
			ajax: {
				url: serverSide
					? "/multi_year_schedule/get_data_server_side"
					: "/multi_year_schedule/get_data",
				type: "POST",
				// data: {
				// 	icon_edit: "icon_edit",
				// 	icon_view: "icon_view,",
				// 	icon_delete: "icon_delete",
				// 	select_checkbox: "select_checkbox",
				// },
			},

			// DataTables - Callbacks
			createdRow: function (row, data, dataIndex, cells) {
				$(row).attr("data-id", data.id);
				$(row).attr("data-specific_renovation_object_id", data.specific_renovation_object_id);
				$(row).css("cursor", "pointer");
				$(row).addClass("align-middle");
				if (data.is_repair == 0) {
					$(row).addClass("table-danger");
				}
				// typeof createdRow === "function" ? createdRow(row, data, dataIndex, cells) : null;
			},
			drawCallback: function (settings) {
				$("#datatables_length")
					.removeClass("dataTables_length")
					.addClass("d-grid gap-2 d-md-flex")
					.find("select")
					.removeClass("form-select-sm");
				$("#datatables_length").parent().removeClass("d-none");
				$("#datatables_filter")
					.removeClass("dataTables_filter")
					.addClass("d-grid gap-2 d-md-flex justify-content-md-end")
					.find("input")
					.removeClass("form-control-sm");
				$("#datatables_filter").parent().removeClass("d-none");
			},
			headerCallback(thead, data, start, end, display) {
				// $(thead).addClass("text-center align-middle");
			},
			preDrawCallback: function (settings) {
				$("#datatables thead").addClass("text-center align-middle");
				for (i = 0; i < settings.aoColumns.length; i++) {
					if (settings.aoColumns[i].data === "type_service_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						if (value.substring(1, value.length - 1) == 1) {
							$("#datatables").find("th.year_service").text("Рік останього КР");
						} else if (value.substring(1, value.length - 1) == 2) {
							$("#datatables").find("th.year_service").text("Рік останього ПР");
						} else if (value.substring(1, value.length - 1) == 3) {
							$("#datatables").find("th.year_service").text("Рік останього ТО");
						} else if (value.substring(1, value.length - 1) == 4) {
							$("#datatables")
								.find("th.year_service")
								.text("Рік останього АВР");
						} else {
							$("#datatables")
								.find("th.year_service")
								.text("Рік останього обслуговування");
						}
					}
				}
			},
			initComplete: function (settings, json) {
				// this.api()
				// 	.column(".stantion")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterStantion").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".equipment")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterEquipment").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".type-service")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterTypeService").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".voltage")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterVoltageClass").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				// this.api()
				// 	.column(".status")
				// 	.data()
				// 	.unique()
				// 	.sort()
				// 	.each(function (v, k) {
				// 		$("#FilterStatus").append(
				// 			`<option value="${htmlspecialchars(v)}">${v}</option>`
				// 		);
				// 	});

				for (i = 0; i < settings.aoColumns.length; i++) {
					if (settings.aoColumns[i].data === "complete_renovation_object_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterStantion").val(value.substring(1, value.length - 1));
						// $("#FilterStantion").val(value);

						if (value !== "") {
							$("#FilterStantion").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].data === "equipment_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterEquipment").val(value.substring(1, value.length - 1));
						// $("#FilterEquipment").val(value);

						if (value !== "") {
							$("#FilterEquipment").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].data === "insulation_type_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterInsulationType").val(
							value.substring(1, value.length - 1)
						);
						// $("#FilterInsulationType").val(value);

						if (value !== "") {
							$("#FilterInsulationType").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].data === "type_service_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						// if (value.substring(1, value.length - 1) == 1) {
						// 	$("#datatables").find("th.year_service").text("Рік останього КР");
						// } else if (value.substring(1, value.length - 1) == 2) {
						// 	$("#datatables").find("th.year_service").text("Рік останього ПР");
						// } else if (value.substring(1, value.length - 1) == 3) {
						// 	$("#datatables").find("th.year_service").text("Рік останього ТО");
						// } else if (value.substring(1, value.length - 1) == 4) {
						// 	$("#datatables")
						// 		.find("th.year_service")
						// 		.text("Рік останього АВР");
						// } else {
						// 	$("#datatables")
						// 		.find("th.year_service")
						// 		.text("Рік останього обслуговування");
						// }
						$("#FilterTypeService").val(value.substring(1, value.length - 1));
						// $("#FilterTypeService").val(value);

						if (value !== "") {
							$("#FilterTypeService").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].data === "voltage_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterVoltageClass").val(value.substring(1, value.length - 1));
						// $("#FilterVoltageClass").val(value);

						if (value !== "") {
							$("#FilterVoltageClass").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].data === "status") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterStatus").val(value.substring(1, value.length - 1));
						// $("#FilterStatus").val(value);

						if (value !== "") {
							$("#FilterStatus").addClass("text-success");
						}
					}
				}
			},
			stateLoadParam: function (settings, data) { },

			// DataTables - Options
			dom:
				"<'row'<'col-sm-12 col-md-2 my-1 text-left d-none'l><'col-sm-12 col-md-8 my-1 text-center'B><'col-sm-12 col-md-2 my-1 text-right d-none'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1 text-center'p>>",
			lengthMenu: [
				[3, 6, 12, 24, 51, 105],
				[
					"Показати 3 записа",
					"Показати 6 записів",
					"Показати 12 записів",
					"Показати 24 записа",
					"Показати 51 запис",
					"Показати 105 записів",
					// "Показати всі записи",
				],
			],

			// DataTables - Columns
			columns: [
				{
					data: "id",
					title: "ID",
					name: "ID",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "id text-center",
				},
				{
					data: "stantion",
					title: "Підстанція",
					name: "Підстанція",
					orderable: true,
					searchable: true,
					visible: true,
					width: "21%",
					className: "stantion",
				},
				{
					data: "complete_renovation_object_id",
					title: "Підстанція",
					name: "Підстанція",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "complete_renovation_object_id",
				},
				{
					data: "equipment",
					title: "Обладнання",
					name: "Обладнання",
					orderable: true,
					searchable: true,
					visible: true,
					width: "17%",
					className: "equipment",
				},
				// {
				// 	data: "equipment_with_voltage",
				// 	title: "Обладнання",
				// 	name: "Обладнання",
				// 	orderable: true,
				// 	searchable: true,
				// 	visible: true,
				// 	width: "20%",
				// 	className: "equipment-with-voltage",
				// },
				{
					data: "equipment_id",
					title: "Обладнання",
					name: "Обладнання",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "equipment_id",
				},
				{
					data: "insulation_type_id",
					title: "Вид ізоляції",
					name: "Вид ізоляції",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "insulation_type_id",
				},
				{
					data: "voltage_id",
					title: "voltage",
					name: "voltage",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "voltage_id",
				},
				{
					data: "disp",
					title: "Дисп.",
					name: "Дисп.",
					orderable: true,
					searchable: true,
					visible: true,
					width: "8%",
					className: "disp text-center",
					render: function (data, type, row, meta) {
						const is_disabled = row.type_service_id == 1 ? null : "disabled";
						const back = (row.DT_RowData.user_group === 'master')
							? data
							: `<input type="text" class="form-control form-control-sm text-center" value="${data ? data : ""
							}" tabindex="${meta.col}" maxlength="255" onchange="changeDisp(event);"  ${is_disabled} />`;
						return back;
					},
				},
				{
					data: "places",
					title: "Інфо",
					name: "Інфо",
					orderable: false,
					searchable: false,
					visible: true,
					width: "13.5%",
					className: "text-center places",
					render: function (data, type, row, meta) {
						let html = "";
						data.forEach(function (value, key, array) {
							html += `<span class="badge bg-${value.place_color}">${value.place_name} ${value.type} (№ ${value.number})</span><br/>`;
						});
						return html;
					},
				},
				// {
				// 	data: "type",
				// 	title: "Тип",
				// 	name: "Тип",
				// 	orderable: true,
				// 	searchable: true,
				// 	visible: true,
				// 	width: "10%",
				// 	className: "type",
				// },
				{
					data: "short_type_service",
					title: "Вид ремонту",
					name: "Вид ремонту",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "text-center short_type_service bg-info",
				},
				{
					data: "type_service_id",
					title: "Вид ремонту",
					name: "Вид ремонту",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "type_service_id",
				},
				{
					data: "cipher_id",
					title: "Шифр ремонту",
					name: "Шифр ремонту",
					orderable: true,
					searchable: true,
					visible: true,
					width: "8%",
					className: "cipher_id",
					render: function (data, type, row, meta) {
						if (row.DT_RowData.user_group !== 'admin' && row.DT_RowData.user_group !== 'engineer') {
							table.column('.cipher_id').visible(0);
						}

						let options = `<option value="">Оберіть шифр</option>`;
						row.DT_RowData.ciphers.forEach(el => {
							options += `<option value="${el.id}" ${data == el.id ? 'selected' : ''}>${'ID_' + el.id + ' (' + el.cipher + '_' + el.name + ')'}</option>`;
						});

						const back = (row.DT_RowData.user_group === 'master')
							? data
							: `<select class="form-select select2" onchange="changeCipher(event);">${options}</select>`;
						// : `<input type="text" class="form-control form-control-sm text-left" value="${data ? data : ""}" tabindex="${meta.col}" maxlength="10" onchange="changeCipher(event);" />`;
						return back;
					},
				},
				{
					data: "cipher_id",
					title: "ID шифру ремонту",
					name: "Шифр ремонту",
					orderable: true,
					searchable: true,
					visible: false,
					width: "8%",
					className: "cipher_id",
					render: function (data, type, row, meta) {
						if (row.DT_RowData.user_group !== 'admin' && row.DT_RowData.user_group !== 'engineer') {
							table.column('.cipher_id').visible(0);
						}

						let options = `<option value="">Оберіть шифр</option>`;
						row.DT_RowData.ciphers.forEach(el => {
							options += `<option value="${el.id}" ${data == el.id ? 'selected' : ''}>${'ID_' + el.id + ' (' + el.cipher + '_' + el.name + ')'}</option>`;
						});

						const back = (row.DT_RowData.user_group === 'master')
							? data
							// : `<select class="form-select select2" onchange="changeCipher(event);">${options}</select>`
							: `<input type="text" class="form-control form-control-sm text-left" value="${data ? data : ""}" tabindex="${meta.col}" maxlength="10" onchange="changeCipher(event);" />`;
						return back;
					},
				},
				{
					data: "periodicity",
					title: "Періодичність",
					name: "Періодичність",
					orderable: false,
					searchable: true,
					visible: true,
					width: "7%",
					className: "periodicity text-center",
					render: function (data, type, row, meta) {
						const back = (row.DT_RowData.user_group === 'master')
							? data
							: `<input type="text" class="form-control form-control-sm text-center" value="${data ? data : ""}" tabindex="${meta.col}" maxlength="2" onchange="changePeriodicity(event);" />`;
						return back;
					},
				},
				{
					data: "year_service",
					title: "Рік останього обслуговування",
					name: "Рік останього обслуговування",
					orderable: true,
					searchable: true,
					visible: true,
					width: "7%",
					className: "year_service",
					render: function (data, type, row, meta) {
						return `
						<input type="text" class="form-control form-control-sm text-center" value="${data ? data : ""
							}" tabindex="${meta.col}" maxlength="4" onchange="changeYearService(event);"/>
					`;
					},
				},
				{
					data: "year_commissioning",
					title: "Рік вводу",
					name: "Рік вводу",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "year_commissioning text-center",
					render: function (data, type, row, meta) {
						const is_disabled = row.type_service_id == 1 ? null : "disabled";
						const back = (row.DT_RowData.user_group === 'master')
							? data
							: `<input type="text" class="form-control form-control-sm text-center" value="${data ? data : ""}" tabindex="${meta.col}" maxlength="4" onchange="changeYearCommissioning(event);" ${is_disabled} />`;
						return back;
					},
				},
				{
					data: "year_repair_invest",
					title: "Факт ІП",
					name: "Факт ІП",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "year_repair_invest text-center",
					render: function (data, type, row, meta) {
						const is_disabled = row.type_service_id == 1 ? null : "disabled";
						const back = (row.DT_RowData.user_group === 'master')
							? data
							: `<input type="text" class="form-control form-control-sm text-center" value="${data ? data : ""}" tabindex="${meta.col}" maxlength="4" onchange="changeYearRepairInvest(event);" ${is_disabled} />`;
						return back;
					},
				},
				{
					data: "year_plan_repair_invest",
					title: "План ІП",
					name: "План ІП",
					orderable: true,
					searchable: true,
					visible: true,
					width: "5%",
					className: "year-plan-repair-invest text-center",
					render: function (data, type, row, meta) {
						const is_disabled = row.type_service_id == 1 ? null : "disabled";
						const back = (row.DT_RowData.user_group === 'master')
							? data
							: `<input type="text" class="form-control form-control-sm text-center" value="${data ? data : ""}" tabindex="${meta.col}" maxlength="4" onchange="changeYearPlanRepairInvest(event);"  ${is_disabled} />`;
						return back;
					},
				},
				{
					data: 'status',
					title: '<i class="bi bi-check-square text-secondary"></i>',
					name: "status",
					orderable: false,
					searchable: true,
					visible: true,
					width: "0.5%",
					className: "status text-center",
					render: function (data, type, row, meta) {
						if (data == 0) {
							return `<input class="form-check-input" type="checkbox" tabindex="${meta.col}" onclick="changeStatus(event);" />`;
						} else {
							return `<input class="form-check-input" type="checkbox" tabindex="${meta.col}" checked onclick="changeStatus(event);" />`;
						}
					},
				},
				{
					data: null,
					title: '<i class="bi bi-eye text-secondary"></i>',
					name: "more_info",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "more_info text-center",
					render: function (data, type, row, meta) {
						return `
						<a href="javascript:void(0);" tabindex="${meta.col}" class="mx-1 dt-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Більше інформації"><i class="bi bi-eye text-info"></i></a>`;
					},
				},
				{
					data: null,
					title: '<i class="bi bi-trash text-secondary"></i>',
					name: "delete_passport",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "delete-passport text-center",
					render: function (data, type, row, meta) {
						return `
						<a href="javascript:void(0);" tabindex="${meta.col}" ${data.DT_RowData.user_group == "admin" ? 'onclick="deleteSpecificRenovationObject(event);' : null}" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Видалити паспорт"><i class="bi bi-trash ${data.DT_RowData.user_group == "admin" ? 'text-danger' : 'text-secondary'}"></i></a>`;
					},
				},
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
				searchPlaceholder: "Пошук по дисп. ...",
				lengthMenu: "_MENU_ ",
				zeroRecords: "Немає записів для відображення",
				emptyTable: "Дані відсутні в таблиці",
				processing: "Чекайте!",
			},
		});

	table.on('requestChild.dt', function (e, row) {
		console.log('row', row);
		row.child(format(row.data())).show();
	});

	// Add event listener for opening and closing details
	$("#datatables tbody").on("click", "td a.dt-control", function () {
		var tr = $(this).closest("tr");
		var row = table.row(tr);

		$(this).find("i").toggleClass("bi-eye-slash text-primary bi-eye text-info");
		tr.toggleClass("bg-custom");

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
			table.ajax.reload(null, false);
		} else {
			// Open this row
			let html = format(row.data());
			row.child(html).show();
			$(".datepicker").datepicker({
				format: "dd.mm.yyyy",
				autoclose: true,
			});
		}
	});

	table.on("init", function () {
		$(".datepicker").datepicker({
			format: "yyyy",
			autoclose: true,
		});
	});

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	async function get_complete_renovation_objects() {
		if (localStorage.getItem('multi_year_schedule_subdivision_id')) {
			$("#FilterSubdivision").val(localStorage.getItem('multi_year_schedule_subdivision_id'));
		}
		try {
			response = await fetch('/complete_renovation_objects/get_complete_renovation_objects_ajax?subdivision_id=' + localStorage.getItem('multi_year_schedule_subdivision_id'), {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					"X-Requested-With": "XMLHttpRequest"
				},
			});
			const json = await response.json();

			let options = `<option value="">Всі підстанції</option>`;
			if (json.complete_renovation_objects) {
				json.complete_renovation_objects.forEach(el => {
					options += `<option value="${el.id}" ${el.id == localStorage.getItem('multi_year_schedule_stantion_id') ? 'selected' : null}>${el.name}</option>`;
				});
			}
			$("#FilterStantion option").remove();
			$("#FilterStantion").append(options);
		} catch (error) {
			console.error('Ошибка:', error);
		}
	}

	get_complete_renovation_objects();

	$("#FilterSubdivision").on("change", async function (event) {
		localStorage.setItem('multi_year_schedule_subdivision_id', event.target.value);

		try {
			response = await fetch('/complete_renovation_objects/get_complete_renovation_objects_ajax?subdivision_id=' + event.target.value, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					"X-Requested-With": "XMLHttpRequest"
				},
			});
			const json = await response.json();

			let options = `<option value="">Всі підстанції</option>`;
			if (json.complete_renovation_objects) {
				json.complete_renovation_objects.forEach(el => {
					options += `<option value="${el.id}">${el.name}</option>`;
				});
			}
			$("#FilterStantion option").remove();
			$("#FilterStantion").append(options);
		} catch (error) {
			console.error('Ошибка:', error);
		}
	});

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$("#FilterStantion").on("change", function (event) {
		localStorage.setItem('multi_year_schedule_stantion_id', event.target.value);
		table
			.columns(".complete_renovation_object_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, true)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterEquipment").on("change", function () {
		table
			.columns(".equipment_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterInsulationType").on("change", function () {
		table
			.columns(".insulation_type_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterTypeService").on("change", function () {
		if (this.value == 1) {
			$("#datatables").find("th.year_service").text("Рік останього КР");
		} else if (this.value == 2) {
			$("#datatables").find("th.year_service").text("Рік останього ПР");
		} else if (this.value == 3) {
			$("#datatables").find("th.year_service").text("Рік останього ТО");
		} else if (this.value == 4) {
			$("#datatables").find("th.year_service").text("Рік останього АВР");
		} else {
			$("#datatables")
				.find("th.year_service")
				.text("Рік останього обслуговування");
		}
		table
			.columns(".type_service_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	// table.columns(".type_service_id").search("^1$", true, false).draw();

	$("#FilterVoltageClass").on("change", function () {
		table
			.columns(".voltage_id")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterStatus").on("change", function () {
		table
			.columns(".status")
			// .search(this.value)
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#ResetFilters").on("click", function () {
		$("#FilterStantion").val("");
		table.columns(".complete_renovation_object_id").search("").draw();
		$("#FilterEquipment").val("");
		table.columns(".equipment_id").search("").draw();
		$("#FilterTypeService").val("");
		table.columns(".type_service_id").search("").draw();
		$("#FilterVoltageClass").val("");
		table.columns(".voltage_id").search("").draw();
		$("#FilterStatus").val("");
		table.columns(".status").search("").draw();
	});

	$("#clearLocalStorage").on("click", function () {
		localStorage.removeItem('multi_year_schedule_subdivision_id');
		localStorage.removeItem('multi_year_schedule_stantion_id');
		table.state.clear();
		window.location.reload();
	});
});

function deleteSpecificRenovationObject(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href =
			"/multi_year_schedule/delete_specific_renovation_object/" +
			$(event.currentTarget).parents("tr").data("specific_renovation_object_id");
	} else {
		return;
	}
}

function changeCipher(event) {
	const id = $(event.target).parents("tr").data("id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_cipher_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeDisp(event) {
	const id = $(event.target).parents("tr").data("specific_renovation_object_id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/specific_renovation_objects/change_disp_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			// $(event.target).attr('readonly', 'readyonly');
			// const table = $("#datatables").DataTable();
			// table.ajax.reload(null, false);
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changePeriodicity(event) {
	const id = $(event.target).parents("tr").data("id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_periodicity_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeYearService(event) {
	const id = $(event.target).parents("tr").data("id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_year_service_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeYearCommissioning(event) {
	const id = $(event.target)
		.parents("tr")
		.data("specific_renovation_object_id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/specific_renovation_objects/change_year_commissioning_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeYearRepairInvest(event) {
	const id = $(event.target)
		.parents("tr")
		.data("specific_renovation_object_id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/specific_renovation_objects/change_year_repair_invest_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeYearPlanRepairInvest(event) {
	const id = $(event.target)
		.parents("tr")
		.data("specific_renovation_object_id");
	const value = event.target.value;
	$.ajax({
		method: "POST",
		url: "/specific_renovation_objects/change_year_plan_repair_invest_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeStatus(event) {
	const id = $(event.target).parents("tr").data("id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/change_status_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function addNextYear(event) {
	const id = $(event.target).val();
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/add_next_year_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function deleteNextYear(event) {
	const id = $(event.target).val();
	let value;
	if ($(event.target).prop("checked")) {
		value = 0;
	} else {
		value = 1;
	}
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/delete_next_year_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function updateMethodService(event) {
	const id = $(event.target).val();
	let value;
	if ($(event.target).prop("checked")) {
		$(event.target).next().find('strong').text('Підрядний спосіб');
		value = 1;
	} else {
		$(event.target).next().find('strong').text('Господарський спосіб');
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/multi_year_schedule/update_method_service_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function htmlspecialchars(str) {
	if (typeof str == "string") {
		str = str.replace(/&/g, "&amp;");
		str = str.replace(/"/g, "&quot;");
		str = str.replace(/'/g, "&#039;");
		str = str.replace(/</g, "&lt;");
		str = str.replace(/>/g, "&gt;");
	}
	return str;
}

let exampleEl = $('[data-bs-toggle="tooltip"]');
if (exampleEl) {
	for (let i = 0; i < exampleEl.length; i++) {
		let tooltip = new bootstrap.Tooltip(exampleEl[i]);
	}
}
