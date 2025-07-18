async function format(d) {
	// `d` is the original data object for the row
	let html = "";
	let tr_passport_properties = "";
	d.passport_properties.forEach(function (v, k) {
		let icon_trash = d.DT_RowData.user_group === "admin" ? `<a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger" style="font-size: 24px"></i></a>` : "";
		let icon_is_block = d.DT_RowData.user_group === 'admin'
			? `<input class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Деблокувати/Блокувати" type="checkbox" onclick="changeIsBlockProperty(event);" ${v.is_block == 1 ? 'checked' : ''} value="${v.id}" style="font-size: 24px">`
			: `${v.is_block == 1 ? '<i class="bi bi-lock text-secondary" title="Заблоковано" style="font-size: 24px"></i>' : '<i class="bi bi-unlock text-primary" title="Розблоковано" style="font-size: 24px"></i>'}`;
		tr_passport_properties += `
			<tr class="align-middle" data-id="${v.id}">
				<th class="text-center">${k + 1}</th>
				<th>${v.property}</th>
				<td><input class="form-control" value="${htmlspecialchars(v.value)}" name="value" disabled tabindex="1"></td>
				<td class="text-center">
				<a href="javascript:void(0);" ${(d.DT_RowData.user_group === "admin" || v.is_block == 0) ? 'onclick="editProperty(event);"' : null} data-bs-toggle="tooltip" data-bs-placement="top" title="Активувати форму"><i class="bi bi-toggle-off" style="font-size: 24px"></i></a>
				${icon_is_block}
				${(icon_trash = "")}
				</td>
			</tr>
		`;
	});

	let tr_operating_list = "";
	d.operating_list.forEach(function (v, k) {
		let icon_trash =
			d.DT_RowData.user_group === "admin"
				? `<a href="javascript:void(0);" onclick="deleteOperatingList(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger" style="font-size: 24px"></i></a>`
				: `<a href="javascript:void(0);" onclick="deleteOperatingList(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Видалити"><i class="bi bi-trash text-danger" style="font-size: 24px"></i></a>`;
		tr_operating_list += `
			<tr class="align-middle" data-id="${v.id}" data-user_group="${d.DT_RowData.user_group}">
			<td><input class="form-control text-center datepicker" value="${v.service_date_format}" name="service_date" disabled></td>
			<td>
				<select class="form-select" name="type_service_id" disabled>
					<option value="">Оберіть тип обслуговування</option>
					<option value="1" ${v.type_service_id == 1 ? "selected" : ""}>КР</option>
					<option value="2" ${v.type_service_id == 2 ? "selected" : ""}>ПР</option>
					<option value="3" ${v.type_service_id == 3 ? "selected" : ""}>ТО</option>
					<option value="4" ${v.type_service_id == 4 ? "selected" : ""}>АВР</option>
				</select>
			</td>
			<td><input class="form-control" placeholder="Введіть дані з експлуатації" value="${htmlspecialchars(v.service_data)}" name="service_data" disabled></td>
			<td><input class="form-control" placeholder="Введіть виконавця" value="${htmlspecialchars(v.executor)}" name="executor" disabled></td>
			<td class="text-center">
			<a href="javascript:void(0);" data-bs-container="body" data-bs-toggle="popover" data-bs-placement="left" data-bs-html="true" data-bs-content="${htmlspecialchars(v.stantion) + '<br />' + htmlspecialchars(v.disp) + '<br />' + htmlspecialchars(v.place)}" title="Місце виконання робіт"><i class="bi bi-eye text-success" style="font-size: 24px"></i></a>
			<a href="javascript:void(0);" onclick="editOperatingList(event);" data-bs-toggle="tooltip" data-bs-placement="top" title="Активувати форму"><i class="bi bi-toggle-off" style="font-size: 24px"></i></a>
			${icon_trash}
			</td>
			</tr>
		`;
	});

	html = `
		<div class="card">
			<div class="card-header"><h5>Більше інформації</h5></div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-5">
						<h4 class="text-center">Технічні характеристики</h4>
						<table class="table table-bordered table-striped more-info">
							<thead>
								<tr>
								<th class="text-center" style="width:5%">#</th>
								<th class="text-center" style="width:50%">Характеристика</th>
								<th class="text-center" style="width:35%">Значення</th>
								<th class="text-center" style="width:10%">Дія</th>
								</tr>
							</thead>
							<tbody>
								${tr_passport_properties}
								<tr class="text-center align-middle">
								<td class="text-center">#</td>
								<td>
								<select class="form-select" disabled>
								<option value="" selected>Оберіть характеристику</option>
								</select>
								</td>
								<td>
								<input class="form-control" disabled>
								</td>
								<td>
								<a href="javascript:void(0);" onclick="alert('Функція в розробці')">
								<i class="bi bi-plus-square text-success" style="font-size: 24px"></i>
								</a>
								</td>
								</tr>
							</tbody>
						</table>
						<a href="/passports/gen_passport_pdf/${d.id}" class="btn btn-danger my-1" target="_blank"><i class="bi bi-file-earmark-pdf"></i> Друкувати</a>
		`;
	if (
		d.DT_RowData.user_group == "admin" || d.DT_RowData.user_group == "master" || d.DT_RowData.user_group == "engineer"
	) {
		html += `&nbsp;<a class="btn btn-info my-1 d-non" href="/passports/copy_passport_properties/${d.DT_RowData.equipment_id}/${d.DT_RowData.specific_renovation_object_id}/${d.DT_RowData.id}"><i class="bi bi-clipboard-plus"></i> Копіювати характеристики</a>`;
	}

	html += `</div>
		<div class="col-md-7">
			<h4 class="text-center">Експлуатаційні дані</h4>
			<table class="table table-bordered table-striped more-info">
				<thead>
					<tr>
						<th class="text-center" style="width:13%">Дата</th>
						<th class="text-center" style="width:10%">#</th>
						<th class="text-center" style="width:52%">Експлуатаційні дані</th>
						<th class="text-center" style="width:15%">Викованець</th>
						<th class="text-center" style="width:10%">Дія</th>
					</tr>
				</thead>
				<tbody>
					${tr_operating_list}
				</tbody>
			</table>
		</div>
			<hr class="mt-5" />
			</div>
			</div>
		</div>
	`;
	return html;
}

$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
	const serverSide = true;
	const table = $("#datatables")
		.on("processing.dt", function (e, settings, processing) {
			$(".loading").css("display", processing ? "block" : "none");
		})
		.DataTable({
			// DataTables - Features
			// processing: true,
			autoWidth: false,
			stateSave: true,
			stateDuration: 60 * 60 * 24 * 365,
			deferRender: true,
			pagingType: "full_numbers",
			serverSide: serverSide,

			// DataTables - Data
			ajax: {
				url: serverSide
					? "/passports/get_data_server_side"
					: "/passports/get_data",
				type: "POST",
				// data: ''
				// data: serverSide ? null : { post: 1 },
			},

			// DataTables - Callbacks
			createdRow: function (row, data, dataIndex, cells) {
				// if (data.place_id == 1) {
				// 	$(row).addClass('text-warning');
				// }
				// else if (data.place_id == 2) {
				// 	$(row).addClass('text-success');
				// }
				// else if (data.place_id == 3) {
				// 	$(row).addClass('text-danger');
				// }
				// else {
				// 	$(row).addClass('text-primary');
				// }
				if ((!data.commissioning_year || data.commissioning_year == '0000') || !data.type || !data.number) {
					$(row).addClass('text-danger');
				}
				if (!data.short_type) {
					$(row).addClass('text-primary');
				}

				$(row).attr("data-id", data.id);
				$(row).attr("data-subdivision_id", data.subdivision_id);
				$(row).attr("data-complete_renovation_object_id", data.complete_renovation_object_id);
				$(row).attr("data-specific_renovation_object_id", data.specific_renovation_object_id);
				$(row).attr("data-place_id", data.place_id);
				$(row).attr("data-equipment_id", data.equipment_id);

				$(row).css("cursor", "pointer");
				$(row).addClass("align-middle");

				$(row).find('.type').attr({ "data-bs-toggle": "popover", "data-bs-placement": "left", "data-bs-trigger": "hover focus", "data-bs-content": data.short_type ? data.short_type : null });
			},

			preDrawCallback: function (settings) {
				$("#datatables_filter").find('input[type="search"]').attr("name", "search");
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
				// $("#datatables_filter").find('input[type="search"]').attr("name", "search");

				if (
					typeof settings.json !== "undefined" &&
					settings.json.user_group !== "admin"
				) {
					$("td.actions").find("i.bi-trash").closest("a").remove();
				}

				// $(settings.aanFeatures.l).find("select").removeClass("form-select-sm");
				// $(settings.aanFeatures.f).find("input").removeClass("form-control-sm");

				$.extend($.fn.dataTableExt.oStdClasses, {
					sFilterInput: "form-control yourClass",
					sLengthSelect: "form-control yourClass",
				});

				let popoverEl = $('[data-bs-toggle="popover"]');

				if (popoverEl) {
					for (let i = 0; i < popoverEl.length; i++) {
						let popover = new bootstrap.Popover(popoverEl[i]);
					}
				}

				let tooltipEl = $('[data-bs-toggle="tooltip"]');
				if (tooltipEl) {
					for (let i = 0; i < tooltipEl.length; i++) {
						let tooltip = new bootstrap.Tooltip(tooltipEl[i]);
					}
				}
			},

			headerCallback(thead, data, start, end, display) {
				$(thead).find('th').addClass("text-center align-middle");
			},

			stateLoadParams: function (settings, data) {
			},

			initComplete: function (settings, json) {
				for (i = 0; i < settings.aoColumns.length; i++) {
					if (settings.aoColumns[i].name === "complete_renovation_object_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterStantion").val(value.substring(1, value.length - 1));

						if (value !== "") {
							$("#FilterStantion").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].name === "equipment_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterEquipment").val(value.substring(1, value.length - 1));

						if (value !== "") {
							$("#FilterEquipment").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].name === "insulation_type_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterInsulationType").val(value.substring(1, value.length - 1));

						if (value !== "") {
							$("#FilterInsulationType").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].name === "voltage_class_id") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterVoltageClass").val(value.substring(1, value.length - 1));

						if (value !== "") {
							$("#FilterVoltageClass").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].name === "is_photo") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterIsPhoto").val(value.substring(1, value.length - 1));

						if (value !== "") {
							$("#FilterIsPhoto").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].name === "is_astor") {
						const value = settings.aoPreSearchCols[i].sSearch;
						$("#FilterIsAstor").val(value.substring(1, value.length - 1));

						if (value !== "") {
							$("#FilterIsAstor").addClass("text-success");
						}
					}
					if (settings.aoColumns[i].name === "updated_at") {
						if (settings.aaSorting[0][0] == settings.aoColumns[i].idx) {
							const value = settings.aLastSort[0].dir;
							$("#OrderUpdateAt").val(value);
						}
					}
				}
			},

			// DataTables - Options
			dom: serverSide
				? "<'row'<'col-sm-12 col-md-4 my-1 d-none'l><'col-sm-12 col-md-4 my-1'B><'col-sm-12 col-md-4 my-1 d-none'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1'p>>"
				: "<'row'<'col-sm-12 col-md-4 my-1'l><'col-sm-12 col-md-4 my-1'B><'col-sm-12 col-md-4 my-1'f>>" +
				"<'row'<'table-responsive'<'col-sm-12'tr>>>" +
				"<'row'<'col-sm-12 col-md-5 my-1'i><'col-sm-12 col-md-7 my-1'p>>",
			lengthMenu: [
				[2, 3, 6, 12, 27, 51, 102],
				[
					"Показати 2 записи",
					"Показати 3 записи",
					"Показати 6 записів",
					"Показати 12 записів",
					"Показати 27 записів",
					"Показати 51 записів",
					"Показати 102 записів",
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
					name: "stantion",
					orderable: true,
					searchable: true,
					visible: true,
					width: "23%",
					className: "stantion",
				},
				{
					data: "equipment",
					title: "Обладнання",
					name: "equipment",
					orderable: true,
					searchable: true,
					visible: true,
					width: "19%",
					className: "equipment",
				},
				{
					data: "disp",
					title: "Дисп.",
					name: "disp",
					orderable: true,
					searchable: true,
					visible: true,
					width: "8%",
					className: "disp",
					// render: function (data, type, row, meta) {
					// 	return row.DT_RowData.user_group === 'admin' ? `<input type="text" class="form-control form-control-sm text-center" readonle value="${data ? data : ""}" tabindex="${meta.col}" maxlength="255" onChange="changeDisp(event);" onDblClick="this.removeAttribute('readonly');">` : data;
					// },
				},
				{
					data: "place",
					title: "Місце",
					name: "place",
					orderable: true,
					searchable: true,
					visible: true,
					width: "8%",
					className: "place",
				},
				{
					data: "type",
					title: "Тип",
					name: "type",
					orderable: true,
					searchable: true,
					visible: true,
					width: "17%",
					className: "type",
				},
				{
					data: "short_type",
					title: "Краткий тип",
					name: "short_type",
					orderable: true,
					searchable: true,
					visible: false,
					width: "16%",
					className: "short-type",
				},
				{
					data: "number",
					title: "Номер",
					name: "number",
					orderable: true,
					searchable: true,
					visible: true,
					width: "9%",
					className: "number text-center",
				},
				{
					data: "production_date",
					title: '<i class="bi bi-calendar"></i>',
					name: "production_date",
					orderable: true,
					searchable: true,
					visible: true,
					width: "6%",
					className: "production-date text-center",
					render: function (data, type, row, meta) {
						let options = { year: "numeric", month: "numeric", day: "numeric" };
						return data == "0000-00-00" || data == null
							? "NO DATA"
							: new Date(data).toLocaleString("ru", options);
					},
				},
				{
					data: null,
					title: '<i class="bi bi-plus-square text-secondary"></i>',
					name: "add_properties",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "add-properties text-center",
					render: function (data, type, row, meta) {
						return `<a href="javascript:void(0);" tabindex="${meta.col}" onclick="fillAddPropertiesModal(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Додати властивості"><i class="bi bi-plus-square text-primary"></i></a>`;
					},
				},
				{
					data: null,
					title: '<i class="bi bi-pencil-square text-secondary"></i>',
					name: "edit_passport",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "edit-passport text-center",
					render: function (data, type, row, meta) {
						return `<a href="javascript:void(0);" tabindex="${meta.col}" onclick="${data.is_block == 0 || data.DT_RowData.user_group == "admin" ? "getDataPassportForEdit(event);" : ''}" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Редагувати паспорт"><i class="bi bi-pencil-square ${(data.is_block == 0 || data.DT_RowData.user_group == "admin") ? 'text-success' : 'text-secondary'}"></i></a>`;
					},
				},
				{
					data: null,
					title: '<i class="bi bi-arrow-left-right text-secondary"></i>',
					name: "move_passport",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "move-passport text-center",
					render: function (data, type, row, meta) {
						return `<a href="javascript:void(0);" tabindex="${meta.col}" onclick="getDataPassportForMove(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Перемістити паспорт"><i class="bi bi-arrow-left-right text-warning"></i></a>`;
					},
				},
				{
					data: null,
					title: '<i class="bi bi-file-earmark-pdf text-secondary"></i>',
					name: "passport_pdf",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "passport-pdf text-center",
					render: function (data, type, row, meta) {
						return `<a href="javascript:void(0);" tabindex="${meta.col}" onclick="printPassport(event);" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Сгенерувати паспорт"><i class="bi bi-file-earmark-pdf text-danger"></i></a>`;
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
					className: "more-info text-center",
					render: function (data, type, row, meta) {
						return `<a href="javascript:void(0);" tabindex="${meta.col}" class="mx-1 dt-control" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Більше інформації"><i class="bi bi-eye text-info"></i></a>`;
					},
				},
				{
					data: null,
					title: '<i class="bi bi-journal-plus text-secondary"></i>',
					name: "add_operation",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "add-operation text-center",
					render: function (data, type, row, meta) {
						return `<a href="javascript:void(0);" tabindex="${meta.col}" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Додати експлуатаційні дані" onclick="openAddOperatingListModal(event)"><i class="bi bi-journal-plus text-success"></i></a>`;
					},
				},
				{
					data: null,
					title: '<i class="bi bi-key text-secondary"></i>',
					name: "is_block",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "is-block text-center",
					render: function (data, type, row, meta) {
						if (data.DT_RowData.user_group == "admin") {
							return `<input class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Деблокувати/Блокувати" tabindex="${meta.col}" type="checkbox" name="is_block[]" ${data.DT_RowData.user_group == "admin" ? 'onclick="changeIsBlock(event);' : ''}" ${data.is_block == 1 ? 'checked' : ''} ${data.DT_RowData.user_group != "admin" ? 'disabled' : ''} value="${data.id}">`;
						}
						else {
							return `${data.is_block == 1 ? '<i class="bi bi-lock text-secondary" title="Заблоковано"></i>' : '<i class="bi bi-unlock text-primary" title="Розблоковано"></i>'}`;
						}
					},
				},
				{
					data: null,
					title: '<i class="bi bi-file-image text-secondary"></i>',
					name: "is_photo",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "is-photo text-center",
					render: function (data, type, row, meta) {
						if (data.DT_RowData.user_group == "admin") {
							return `<input class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Нема фото/Є фото" tabindex="${meta.col}" type="checkbox" name="is_photo[]" ${data.DT_RowData.user_group == "admin" ? 'onclick="changeIsPhoto(event);' : ''}" ${data.is_photo == 1 ? 'checked' : ''} ${data.DT_RowData.user_group != "admin" ? 'disabled' : ''} value="${data.id}">`;
						}
						else {
							return `${data.is_photo == 1 ? '<i class="bi bi-file-image text-primary" title="Є фото таблички"></i>' : '<i class="bi bi-file-earmark-image text-secondary" title="Нема фото таблички"></i>'}`;
						}
					},
				},
				{
					data: null,
					title: '<i class="bi bi-database-fill text-secondary"></i>',
					name: "is_astor",
					orderable: false,
					searchable: false,
					visible: true,
					width: "0.5%",
					className: "is-astor text-center",
					render: function (data, type, row, meta) {
						if (data.DT_RowData.user_group == "admin" || data.DT_RowData.user_group == "engineer") {
							return `<input class="form-check-input" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Не в Асторі/В Асторі" tabindex="${meta.col}" type="checkbox" name="is_astor[]" ${(data.DT_RowData.user_group == "admin" || data.DT_RowData.user_group == "engineer") ? 'onclick="changeIsAstor(event);' : ''}" ${data.is_astor == 1 ? 'checked' : ''} ${(data.DT_RowData.user_group == "admin" || data.DT_RowData.user_group == "engineer") ? '' : 'disabled'} value="${data.id}">`;
						}
						else {
							return `${data.is_astor == 1 ? '<i class="bi bi-database-fill text-success" title="В Асторі"></i>' : '<i class="bi bi-database text-secondary" title="Не в Асторі"></i>'}`;
						}
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
						return `<a href="javascript:void(0);" tabindex="${meta.col}" ${data.DT_RowData.user_group == "admin" ? 'onclick="deletePassport(event);' : null}" class="mx-1" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual" title="Видалити паспорт"><i class="bi bi-trash ${data.DT_RowData.user_group == "admin" ? 'text-danger' : 'text-secondary'}"></i></a>`;
					},
				},
				{
					data: "complete_renovation_object_id",
					title: "complete_renovation_object_id",
					name: "complete_renovation_object_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "complete-renovation-object-id",
				},
				{
					data: "specific_renovation_object_id",
					title: "specific_renovation_object_id",
					name: "specific_renovation_object_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "specific-renovation-object-id",
				},
				{
					data: "place_id",
					title: "place_id",
					name: "place_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "place-id",
				},
				{
					data: "equipment_id",
					title: "equipment_id",
					name: "equipment_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "equipment-id",
				},
				{
					data: "insulation_type_id",
					title: "insulation_type_id",
					name: "insulation_type_id",
					orderable: true,
					searchable: true,
					visible: false,
					width: "",
					className: "insulation-type-id",
				},
				{
					data: "voltage_class_id",
					title: "voltage_class_id",
					name: "voltage_class_id",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "voltage-class-id",
				},
				{
					data: "updated_at",
					title: "updated_at",
					name: "updated_at",
					orderable: false,
					searchable: true,
					visible: false,
					width: "",
					className: "updated-at",
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

	table.on('requestChild.dt', async function (e, row) {
		row.child(await format(row.data())).show();
	});

	// Add event listener for opening and closing details
	table.on("click", "td a.dt-control", async function () {
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
			let html = await format(row.data());
			row.child(html).show();
			$(".datepicker").datepicker({
				format: "dd.mm.yyyy",
				autoclose: true,
			});

			let popoverEl = $('[data-bs-toggle="popover"]');
			if (popoverEl) {
				for (let i = 0; i < popoverEl.length; i++) {
					let popover = new bootstrap.Popover(popoverEl[i]);
				}
			}

		}
	});

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	async function get_complete_renovation_objects() {
		if (localStorage.getItem('passports_subdivision_id')) {
			$("#FilterSubdivision").val(localStorage.getItem('passports_subdivision_id'));
		}
		try {
			response = await fetch('/complete_renovation_objects/get_complete_renovation_objects_ajax?subdivision_id=' + localStorage.getItem('passports_subdivision_id'), {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					"X-Requested-With": "XMLHttpRequest"
				},
			});
			const data = await response.json();

			// if (data.status === 'ERROR') {
			// 	throw new Error(data.message);
			// }

			let options = `<option value="">Всі підстанції</option>`;
			if (data.complete_renovation_objects) {
				data.complete_renovation_objects.forEach(el => {
					options += `<option value="${el.id}" ${el.id == localStorage.getItem('passports_stantion_id') ? 'selected' : ''}>${el.name}</option>`;
				});
			}
			$("#FilterStantion option").remove();
			$("#FilterStantion").append(options);
		} catch (error) {
			console.error(error);
		}
	}

	get_complete_renovation_objects();

	$("#FilterSubdivision").on("change", async function (event) {
		localStorage.setItem('passports_subdivision_id', event.target.value);

		try {
			response = await fetch('/complete_renovation_objects/get_complete_renovation_objects_ajax?subdivision_id=' + event.target.value, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					"X-Requested-With": "XMLHttpRequest"
				},
			});
			const data = await response.json();

			// if (data.status === 'ERROR') {
			// 	throw new Error(data.message);
			// }

			let options = `<option value="">Всі підстанції</option>`;
			if (data.complete_renovation_objects) {
				data.complete_renovation_objects.forEach(el => {
					options += `<option value="${el.id}">${el.name}</option>`;
				});
			}
			$("#FilterStantion option").remove();
			$("#FilterStantion").append(options);
		} catch (error) {
			console.error(error);
		}
	});

	$("#FilterStantion").on("change", function (event) {
		localStorage.setItem('passports_stantion_id', event.target.value);

		table
			.columns(".complete-renovation-object-id")
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
			.columns(".equipment-id")
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
			.columns(".insulation-type-id")
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterVoltageClass").on("change", function () {
		table
			.columns(".voltage-class-id")
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterIsPhoto").on("change", function () {
		table
			.columns(".is-photo")
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#FilterIsAstor").on("change", function () {
		table
			.columns(".is-astor")
			.search(this.value ? "^" + this.value + "$" : "", true, false)
			.draw();

		if (this.value === "") {
			$(this).removeClass("text-success");
		} else {
			$(this).addClass("text-success");
		}
	});

	$("#OrderUpdateAt").on("change", function () {
		table
			.columns(".updated-at")
			.order(this.value)
			.draw();
	});

	$("#clearLocalStorage").on("click", function () {
		// localStorage.removeItem('passports_subdivision_id');
		localStorage.removeItem('passports_stantion_id');
		table.state.clear();
		window.location.reload();
	});
});

// function getDataPassportForEdit_OLD(event) {
// 	let id = $(event.currentTarget).closest("tr").data("id");
// 	$.ajax({
// 		method: "POST",
// 		url: "/passports/get_data_passport",
// 		data: { id },
// 	}).done(function (data) {
// 		if (data.status === "SUCCESS") {
// 			let modal = $("#editPassportModal");
// 			modal.modal("show");
// 			console.log(data);
// 			toastr.success(data.message, "OK");
// 			fillFormEditPassport(data.passport, data.disp);
// 		} else {
// 			toastr.error(data.message, "Помилка");
// 		}
// 	});
// }

async function getDataPassportForEdit(event) {
	try {
		const id = $(event.currentTarget).closest("tr").data("id");
		const modal = $("#editPassportModal");
		const response = await fetch("/passports/get_data_passport_ajax/" + id, {
			method: "GET",
			headers: {
				"Content-Type": "application/x-www-form-urlencoded",
				"X-Requested-With": "XMLHttpRequest",
			},
		});
		const data = await response.json();
		console.table(data.passport);
		if (data.status === 'ERROR') {
			throw new Error(data.message);
		}
		await fillFormEditPassport(data.passport, data.disp);
		await modal.modal("show");
	} catch (error) {
		toastr.error(error, 'Помилка запроса');
	}
}

async function fillFormEditPassport(passport, disp) {
	$("#idEdit").val(passport.id);
	$("#idCompleteRenovationObjectEdit").val(passport.complete_renovation_object_id);
	$("#idEquipmentEdit").val(disp.equipment_id);
	$("#idInsulationTypeEdit").val(passport.insulation_type_id);
	$("#idPlaceEdit").val(passport.place_id);
	$("#idVoltageClassEdit").val(disp.voltage_class_id);
	$("#idSpecificRenovationObjectEdit").val(disp.name);
	$("#idTypeEdit").val(passport.type);
	$("#idShortTypeEdit").val(passport.short_type);
	$("#idProductionDateEdit").val(passport.production_date).datepicker("update");
	$("#idСommissioningYearEdit").val(passport.commissioning_year);
	$("#idNumberEdit").val(passport.number);
	$("#idRefinementMethodEdit").val(passport.refinement_method);
}

function addPassport(event) {
	const form = $("#formAddPassport");
	$.ajax({
		method: "POST",
		url: "/passports/add_passport",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formAddPassport")
				.find(".row select, .row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#addPassportModal").modal("hide");
				event.target.disabled = false;
				$("#formAddPassport")
					.find(".row select, .row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
				// table.order([0, "desc"]).draw();
			}, 1000);
		} else {
			if (data.errors.complete_renovation_object_id) {
				$("#idCompleteRenovationObjectAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.complete_renovation_object_id);
			} else {
				$("#idCompleteRenovationObjectAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.equipment_id) {
				$("#idEquipmentAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.equipment_id);
			} else {
				$("#idEquipmentAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.insulation_type_id) {
				$("#idInsulationTypeAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.insulation_type_id);
			} else {
				$("#idInsulationTypeAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.place_id) {
				$("#idPlaceAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.place_id);
			} else {
				$("#idPlaceAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.voltage_class_id) {
				$("#idVoltageClassAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.voltage_class_id);
			} else {
				$("#idVoltageClassAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.specific_renovation_object) {
				$("#idSpecificRenovationObjectAdd").addClass("is-invalid").next().text(data.errors.specific_renovation_object);
			} else {
				$("#idSpecificRenovationObjectAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.type) {
				$("#idTypeAdd").addClass("is-invalid").next().text(data.errors.type);
			} else {
				$("#idTypeAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.production_date) {
				$("#idProductionDateAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.production_date);
			} else {
				$("#idProductionDateAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.number) {
				$("#idNumberAdd")
					.addClass("is-invalid")
					.next()
					.text(data.errors.number);
			} else {
				$("#idNumberAdd")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

function editPassport(event) {
	const form = $("#formEditPassport");
	$.ajax({
		method: "POST",
		url: "/passports/edit_passport",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formEditPassport")
				.find(".row select, .row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#editPassportModal").modal("hide");
				event.target.disabled = false;
				$("#formEditPassport")
					.find(".row select, .row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			// if (data.errors.complete_renovation_object_id) {
			// 	$("#idCompleteRenovationObjectEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.complete_renovation_object_id);
			// } else {
			// 	$("#idCompleteRenovationObjectEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			// if (data.errors.equipment_id) {
			// 	$("#idEquipmentEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.equipment_id);
			// } else {
			// 	$("#idEquipmentEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			if (data.errors.insulation_type_id) {
				$("#idInsulationTypeEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.insulation_type_id);
			} else {
				$("#idInsulationTypeEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			// if (data.errors.place_id) {
			// 	$("#idPlaceEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.place_id);
			// } else {
			// 	$("#idPlaceEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			// if (data.errors.voltage_class_id) {
			// 	$("#idVoltageClassEdit")
			// 		.addClass("is-invalid")
			// 		.next()
			// 		.text(data.errors.voltage_class_id);
			// } else {
			// 	$("#idVoltageClassEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			// if (data.errors.disp) {
			// 	$("#ididSpecificRenovationObjectEdit").addClass("is-invalid").next().text(data.errors.disp);
			// } else {
			// 	$("#ididSpecificRenovationObjectEdit")
			// 		.removeClass("is-invalid")
			// 		.addClass("is-valid")
			// 		.next()
			// 		.text("");
			// }
			if (data.errors.type) {
				$("#idTypeEdit").addClass("is-invalid").next().text(data.errors.type);
			} else {
				$("#idTypeEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.production_date) {
				$("#idProductionDateEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.production_date);
			} else {
				$("#idProductionDateEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.commissioning_year) {
				$("#idСommissioningYearEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.commissioning_year);
			} else {
				$("#idСommissioningYearEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.number) {
				$("#idNumberEdit")
					.addClass("is-invalid")
					.next()
					.text(data.errors.number);
			} else {
				$("#idNumberEdit")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

function movePassport(event) {
	const form = $("#formMovePassport");
	$.ajax({
		method: "POST",
		url: "/passports/move_passport",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formMovePassport")
				.find(".row select, .row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#movePassportModal").modal("hide");
				event.target.disabled = false;
				$("#formMovePassport")
					.find(".row select, .row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			console.log(data.errors);
			if (data.errors.subdivision_id) {
				$("#idSubdivisionMove")
					.addClass("is-invalid")
					.next()
					.text(data.errors.subdivision_id);
			} else {
				$("#idSubdivisionMove")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.complete_renovation_object_id) {
				$("#idCompleteRenovationObjectMove")
					.addClass("is-invalid")
					.next()
					.text(data.errors.complete_renovation_object_id);
			} else {
				$("#idCompleteRenovationObjectMove")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.specific_renovation_object_id) {
				$("#idSpecificRenovationObjectMove")
					.addClass("is-invalid")
					.next()
					.text(data.errors.specific_renovation_object_id);
			} else {
				$("#idSpecificRenovationObjectMove")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.place_id) {
				$("#idPlaceMove")
					.addClass("is-invalid")
					.next()
					.text(data.errors.place_id);
			} else {
				$("#idPlaceMove")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

$("#addPassportModal, #editPassportModal, #movePassportModal, #addOperatingListModal").on(
	"hidden.bs.modal",
	function (event) {
		$(event.target).find(".row select, .row input").val("");
		$(event.target)
			.find(".row select, .row input")
			.removeClass("is-invalid is-valid")
			.next()
			.text("");
	}
);

function getDataPassportForMove(event) {
	let id = $(event.currentTarget).closest("tr").data("id");
	let request = $.ajax({
		method: "POST",
		url: "/passports/get_data_passport_for_move",
		data: { id },
	});
	request.done(function (data, textStatus, jqXHR) {
		if (data.status === "SUCCESS") {
			let modal = $("#movePassportModal");
			modal.modal("show");
			fillFormMovePassport(data.passport, data.disp);
			toastr.success(textStatus, "OK");

			// Отримуємо з сервера список підрозділів
			getSubdivisions().then((subdivisions) => {
				fillSelect(subdivisions, 'idSubdivisionMove');
			});
		}
		else {
			toastr.error(data.message, "Помилка");
		}
	});
	request.fail(function (jqXHR, textStatus, errorThrown) {
		toastr.error(errorThrown, "Помилка");
	});
}

function fillFormMovePassport(passport, disp) {
	$("#idMoveOld").val(passport.id);
	$("#equipmentIdMoveOld").val(disp.equipment_id);
	$("#voltageClassIdMoveOld").val(disp.voltage_class_id);
	$("#idSubdivisionMoveOld").val(passport.subdivision);
	$("#idCompleteRenovationObjectMoveOld").val(passport.complete_renovation_object);
	$("#idSpecificRenovationObjectMoveOld").val(passport.specific_renovation_object + ' (№ ' + passport.number + ')');
	$("#idPlaceMoveOld").val(passport.place);
}

function fillSelect(data, id) {
	$.each(data, function (k, v) {
		$('#' + id).append(`<option value="${v.id}">${v.name}</option`);
	});
}

function getSubdivisions() {
	return new Promise((resolve, reject) => {
		let request = $.get("/subdivisions/get_subdivisions_ajax", { val: 0 });
		request.done(function (data, textStatus, jqXHR) {
			console.log(jqXHR);
			resolve(data.subdivisions);
			$('#idSubdivisionMove').find('option:not(:first)').remove();
		});
		request.fail(function (jqXHR, textStatus, errorThrown) {
			reject(textStatus);
			$('#idSubdivisionMove').addClass('bg-danger');
			console.log(errorThrown);
		});
	});
}

function getCompleteRenovationObjects(event) {
	return new Promise((resolve, reject) => {
		let subdivision_id = $(event.currentTarget).val();
		if (subdivision_id) {
			let request = $.get("/complete_renovation_objects/get_complete_renovation_objects_ajax", { subdivision_id });
			request.done(function (data, textStatus, jqXHR) {
				resolve(data.complete_renovation_objects);
				$('#idCompleteRenovationObjectMove').find('option:not(:first)').remove();
				$('#idSpecificRenovationObjectMove').find('option:not(:first)').remove();
				fillSelect(data.complete_renovation_objects, 'idCompleteRenovationObjectMove');
			});
			request.fail(function (jqXHR, textStatus, errorThrown) {
				reject(textStatus);
				$('#idCompleteRenovationObjectMove').addClass('bg-danger');
				console.log(errorThrown);
			});
		}
		else {
			$('#idCompleteRenovationObjectMove').find('option:not(:first)').remove();
			$('#idSpecificRenovationObjectMove').find('option:not(:first)').remove();
		}
	});
}

function getSpecificRenovationObjects(event) {
	return new Promise((resolve, reject) => {
		let complete_renovation_object_id = $(event.currentTarget).val();
		let equipment_id = $('#equipmentIdMoveOld').val();
		let voltage_class_id = $('#voltageClassIdMoveOld').val();
		if (complete_renovation_object_id) {
			let request = $.get("/specific_renovation_objects/get_specific_renovation_objects_ajax", { complete_renovation_object_id, equipment_id, voltage_class_id });
			request.done(function (data, textStatus, jqXHR) {
				resolve(data.specific_renovation_objects);
				console.log(data.specific_renovation_objects);
				$('#idSpecificRenovationObjectMove').find('option:not(:first)').remove();
				fillSelect(data.specific_renovation_objects, 'idSpecificRenovationObjectMove');
			});
			request.fail(function (jqXHR, textStatus, errorThrown) {
				reject(textStatus);
				$('#idSpecificRenovationObjectMove').addClass('bg-danger');
				console.log(errorThrown);
			});
		}
		else {
			$('#idSpecificRenovationObjectMove').find('option:not(:first)').remove();
		}
	});
}

function printPassport(event) {
	const id = $(event.currentTarget).closest("tr").data("id");
	window.open("/passports/gen_passport_pdf/" + id, "_blank");
}

function addProperties(event) {
	const form = $("#formAddProperties");
	$.ajax({
		method: "POST",
		url: "/passports/add_properties",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			setTimeout(() => {
				$("#addPropertiesModal").modal("hide");
				event.target.disabled = false;
				$("#formAddProperties").find("tbody").html("");
				toastr.success(data.message, "Успіх");
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editProperty(event) {
	$(event.currentTarget).find("i").toggleClass("bi-toggle-on");
	$(event.currentTarget)
		.closest("tr")
		.find("input")
		.attr("disabled", function (index, attr) {
			if (typeof attr === "undefined" && index == 0) {
				let id = $(event.currentTarget).closest("tr").data("id");
				let value = $(event.currentTarget)
					.closest("tr")
					.find('[name="value"]')
					.val();
				$.ajax({
					method: "POST",
					url: "/passports/edit_property",
					data: { id, value },
				}).done(function (data) {
					if (data.status === "SUCCESS") {
						toastr.success(data.message, "Успіх");
					} else {
						toastr.error(data.message, "Помилка");
					}
				});
			}
			return attr == "disabled" ? null : "disabled";
		});
}

$("#addPropertiesModal").on("hidden.bs.modal", function (event) {
	$("#formAddProperties").find("tbody").html("");
});

function fillAddPropertiesModal(event) {
	const equipment_id = $(event.currentTarget)
		.closest("tr")
		.data("equipment_id");
	const passport_id = $(event.currentTarget).closest("tr").data("id");

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const place = $(event.currentTarget).closest("tr").find(".place").text();
	const title = `Додавання характеристик обладнання<br /><span class="text-primary">${stantion} (${disp} ${place})<span>`;

	$.ajax({
		method: "POST",
		url: "/passports/get_properties",
		data: { passport_id, equipment_id },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			let html = "";
			data.properties.forEach(function (val, key, arr) {
				html += `
<tr class="align-middle">
<th>
${val.name}
<input type="hidden" name="passport_id[]" value="${passport_id}">
<input type="hidden" name="property_id[]" value="${val.id}">
</th>
<td><input class="form-control" type="text" name="value[]"></td>
</tr>
`;
			});
			$("#formAddProperties").find("tbody").append(html);
			const modal = $("#addPropertiesModal");
			modal.find(".modal-title").html(title);
			modal.modal("show");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function openAddOperatingListModal(event) {
	const user_group = $(event.currentTarget).closest("tr").data("user_group");
	const subdivision_id = $(event.currentTarget)
		.closest("tr")
		.data("subdivision_id");
	const complete_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("complete_renovation_object_id");
	const specific_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("specific_renovation_object_id");
	const place_id = $(event.currentTarget).closest("tr").data("place_id");
	const passport_id = $(event.currentTarget).closest("tr").data("id");
	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const place = $(event.currentTarget).closest("tr").find(".place").text();
	const title = `Додавання експлуатаційних даних<br /><span class="text-success">${stantion} (${disp} ${place})<span>`;

	$("#idSubdivisionIdAdd").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAdd").val(complete_renovation_object_id);
	$("#idSpecificRenovationObjectIdAdd").val(specific_renovation_object_id);
	$("#idPlaceIdAdd").val(place_id);
	$("#idPassportIdAdd").val(passport_id);
	// $("#idServiceDateAdd").val("").datepicker("update");

	$(".places").html("");
	$.ajax({
		method: "POST",
		url: "/passports/get_places",
		data: { specific_renovation_object_id, place_id },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			let html = "";
			data.places.forEach(function (v, k) {
				html += `<div class="form-check">
<input class="form-check-input" data-passport_id="${v.id}" type="checkbox" value="${v.place_id}" id="check_${v.place_id}" name="places[]">
<label class="form-check-label" for="check_${v.place_id}">
Копіювати на ${v.place}
</label>
<input type="hidden" name="passports[${v.place_id}]" value="${v.id}">
</div>`;
			});
			$("#formAddOperatingList .places").append(html);
		} else {
		}
	});

	const modal = $("#addOperatingListModal");
	if (user_group == "user" || user_group == "head") {
		toastr.error("Вам не дозволена ця операція!", "Помилка");
	} else {
		modal.find(".modal-title").html(title);
		modal.modal("show");
	}
}

function addOperatingList(event) {
	const form = $("#formAddOperatingList");
	const places = $('[name="places[]"]');
	form[0].places = places;

	$.ajax({
		method: "POST",
		url: "/passports/add_operating_list",
		data: form.serialize(),
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			$("#formAddOperatingList")
				.find("input")
				.removeClass("is-invalid")
				.addClass("is-valid");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#addOperatingListModal").modal("hide");
				event.target.disabled = false;
				$("#formAddOperatingList")
					.find(".row input")
					.removeClass("is-invalid is-valid");
				form[0].reset();
				const table = $("#datatables").DataTable();
				table.ajax.reload(null, false);
			}, 1000);
		} else {
			$("#formAddOperatingList input, #formAddOperatingList select")
				.removeClass("is-invalid")
				.addClass("is-valid");
			for (let key in data.errors) {
				console.log(key);
				if (key === form[0].elements[key].name) {
					$('[name="' + key + '"]')
						.addClass("is-invalid")
						.next()
						.text(data.errors[key]);
				}
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

function editOperatingList(event) {
	$(event.currentTarget).find("i").toggleClass("bi-toggle-on");
	$(event.currentTarget)
		.closest("tr")
		.find("input, select")
		.attr("disabled", function (index, attr) {
			console.log(attr);
			if (typeof attr === "undefined" && index == 0) {
				let id = $(event.currentTarget).closest("tr").data("id");
				let type_service_id = $(event.currentTarget)
					.closest("tr")
					.find('[name="type_service_id"]')
					.val();
				let service_date = $(event.currentTarget)
					.closest("tr")
					.find('[name="service_date"]')
					.val();
				let service_data = $(event.currentTarget)
					.closest("tr")
					.find('[name="service_data"]')
					.val();
				let executor = $(event.currentTarget)
					.closest("tr")
					.find('[name="executor"]')
					.val();
				$.ajax({
					method: "POST",
					url: "/passports/edit_operating_list",
					data: { id, type_service_id, service_date, service_data, executor },
				}).done(function (data) {
					if (data.status === "SUCCESS") {
						toastr.success(data.message, "Успіх");
					} else {
						toastr.error(data.message, "Помилка");
					}
				});
			}
			return attr == "disabled" ? null : "disabled";
		});
}

function deletePassport(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href =
			"/passports/delete_passport/" +
			$(event.currentTarget).parents("tr").data("id");
	} else {
		return;
	}
}

function deleteOperatingList(event) {
	let result = confirm("Ви впевнені?");

	if (result) {
		if (
			$(event.currentTarget).closest("tr").data("user_group") !== "admin" &&
			$(event.currentTarget).closest("tr").data("user_group") !== "master"
		) {
			toastr.error("Ви не меєте прав видаляти ці дані!", "Помилка");
		} else {
			let id = $(event.currentTarget).closest("tr").data("id");
			let tr = $(event.currentTarget).closest("tr");
			$.ajax({
				method: "POST",
				url: "/passports/delete_operating_list",
				data: { id },
			}).done(function (data) {
				if (data.status === "SUCCESS") {
					$(tr).remove();
					toastr.success(data.message, "Успіх");
				} else {
					toastr.error(data.message, "Помилка");
				}
			});
		}
	}
}

function changeIsBlock(event) {
	const id = $(event.target).parents("tr").data("id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/passports/change_is_block_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeIsPhoto(event) {
	const id = $(event.target).parents("tr").data("id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/passports/change_is_photo_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeIsAstor(event) {
	const id = $(event.target).parents("tr").data("id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/passports/change_is_astor_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function changeIsBlockProperty(event) {
	const id = $(event.target).parents("tr").data("id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/passports/change_is_block_property_ajax",
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
			const table = $("#datatables").DataTable();
			table.ajax.reload(null, false);
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function exportToExcel() {
	toastr.info("exportToExcel()", "Info");
}

function getDataForCopyProperties() { }

function copyProperties(event) {
	alert("Error");
}

// function getSpecificRenovationObjects(event) {
// 	let disp = event.target.value;
// 	let equipment_id = $("#formCopyProperties").find(".equipment").val();

// 	$.ajax({
// 		method: "POST",
// 		url: "/passports/get_specific_renovation_objects",
// 		data: { disp, equipment_id },
// 	}).done(function (data) {
// 		if (data.status === "SUCCESS" && data.results.length) {
// 			let html = "";
// 			data.results.forEach(function (value) {
// 				let li = `<li>${value.equipment} ${value.name}</li>`;
// 				html += li;
// 			});
// 			$("#formCopyProperties").find(".list").append(html);
// 		} else {
// 			$("#formCopyProperties").find(".list").html("");
// 		}
// 	});
// }

// function onChangeEqupment(event) {
// 	$("#formCopyProperties").find(".disp").val("");
// 	$("#formCopyProperties").find(".list").html("");
// 	if (event.target.value !== "") {
// 		$("#formCopyProperties").find(".disp").removeAttr("disabled");
// 	} else {
// 		$("#formCopyProperties").find(".disp").attr("disabled", "disabled");
// 	}
// }

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

function createRow(event) {
	location.href = '/passports/create';
}

function deleteRow(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href = '/passports/delete_row/' + $(event.currentTarget).parents('tr').data('id');
	} else {
		return;
	}
}

//**************************************************** */

let tooltipEl = $('[data-bs-toggle="tooltip"]');
if (tooltipEl) {
	for (let i = 0; i < tooltipEl.length; i++) {
		let tooltip = new bootstrap.Tooltip(tooltipEl[i]);
	}
}

let popoverEl = $('[data-bs-toggle="popover"]');
if (popoverEl) {
	for (let i = 0; i < popoverEl.length; i++) {
		let popover = new bootstrap.Popover(popoverEl[i]);
	}
}

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});
