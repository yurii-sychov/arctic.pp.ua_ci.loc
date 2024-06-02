$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
});

function actionCollapse(event) {
	// $(event.currentTarget).toggleClass(
	// 	"bi-eye-slash text-primary bi-eye text-info"
	// );
	// 	const tr_current = $(event.currentTarget).closest("tr");
	// 	const tr_not_current_and_next = $("#collapseParent tbody tr.parent").not(
	// 		tr_current
	// 	);
	// 	tr_current.toggleClass("bg-custom");
	// 	tr_not_current_and_next.toggle();
}

function openAddOperatingListObjectModal(event) {
	const subdivision_id = $(event.currentTarget)
		.closest("tr")
		.data("subdivision_id");
	const complete_renovation_object_id = $(event.currentTarget)
		.closest("tr")
		.data("complete_renovation_object_id");
	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const title = `Додавання експлуатаційних даних по об\`єкту<br /><span class="text-success">${stantion}<span>`;

	$("#idSubdivisionIdAdd").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAdd").val(complete_renovation_object_id);

	const modal = $("#addOperatingListObjectModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function addOperatingListObject(event) {
	const form = $("#formAddOperatingListObject");
	formData = new FormData(form.get(0));
	$.ajax({
		method: "POST",
		url: "/complete_renovation_objects/add_operating_list_object",
		data: formData,
		processData: false,
		contentType: false,
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			form.find("input").removeClass("is-invalid").addClass("is-valid");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				$("#addOperatingListObjectModal").modal("hide");
				event.target.disabled = false;
				form.find(".row input, .row select").removeClass("is-invalid is-valid");
				form[0].reset();
				location.reload();
			}, 1000);
		} else {
			form
				.find(".row input, .row select")
				.removeClass("is-invalid")
				.addClass("is-valid");
			for (let key in data.errors) {
				console.log(data.errors[key]);
				if (key === form[0].elements[key].name) {
					form
						.find('[name="' + key + '"]')
						.addClass("is-invalid")
						.next()
						.text(data.errors[key]);
				}
			}
			// console.log(data.errors);
			// console.log(form[0].elements.service_date.name);
			toastr.error(data.message, "Помилка");
		}
	});
}

function editOperatingListObject(event) {
	if (
		$(event.currentTarget).closest("tr").find("input, select").attr("disabled")
	) {
		$(event.currentTarget)
			.closest("tr")
			.find("input, select")
			.removeAttr("disabled");
		$(event.currentTarget).closest("tr").find('[name="is_edit"]').val(1);
	} else {
		$(event.currentTarget)
			.closest("tr")
			.find("input, select")
			.attr("disabled", "deabled");
		$(event.currentTarget).closest("tr").find('[name="is_edit"]').val(0);
	}
}

function editOperatingListObjectHandler(event) {
	let form = $(event.currentTarget).closest("tr").find("input, select");

	let is_edit = $(event.currentTarget)
		.closest("tr")
		.find('[name="is_edit"]')
		.val();
	let id = $(event.currentTarget).closest("tr").data("id");
	let service_date = $(event.currentTarget)
		.closest("tr")
		.find('[name="service_date"]')
		.val();
	let document_number = $(event.currentTarget)
		.closest("tr")
		.find('[name="document_number"]')
		.val();
	let type_service_id = $(event.currentTarget)
		.closest("tr")
		.find('[name="type_service_id"]')
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
		url: "/complete_renovation_objects/edit_operating_list_object",
		data: {
			is_edit,
			id,
			service_date,
			document_number,
			type_service_id,
			service_data,
			executor,
		},
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			form
				.attr("disabled", "disabled")
				.removeClass("is-invalid is-valid")
				.addClass("is-valid");
			$(event.target).closest("tr").find('[name="is_edit"]').val(0);
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				form.removeClass("is-valid");
				// location.reload();
			}, 1000);
		} else {
			for (let i = 0; i < form.length; i++) {
				$(event.target)
					.closest("tr")
					.find('[name="' + form[i].name + '"]')
					.removeClass("is-valid is-invalid");
				for (let key in data.errors) {
					if (key === form[i].name) {
						$(event.target)
							.closest("tr")
							.find('[name="' + key + '"]')
							.removeClass("is-invalid is-valid")
							.addClass("is-invalid");
					} else {
						$(event.target)
							.closest("tr")
							.find('[name="' + form[i].name + '"]')
							.addClass("is-valid");
					}
				}
			}
			toastr.error(data.message, "Помилка");
		}
	});
}

function reUploadFile(event) {
	const id = $(event.currentTarget).closest("tr").data("id");
	const input = $(event.currentTarget)
		.closest("tr")
		.find('[name="document_scan"]');
	input.click();
	input.off();
	let agree = confirm("Ви впевнені?");
	if (agree == false) {
		return;
	}
	input.change((e) => {
		const file = e.target.files[0];
		const formData = new FormData();
		formData.append("id", id);
		formData.append("document_scan", file);
		$.ajax({
			method: "POST",
			url: "/complete_renovation_objects/upload_document_scan",
			data: formData,
			processData: false,
			contentType: false,
		}).done(function (data) {
			if (data.status === "SUCCESS") {
				toastr.success(data.message, "Успіх");
				setTimeout(() => {
					location.reload();
				}, 1000);
			} else {
				toastr.error(data.message, "Помилка");
			}
		});
		input.val("");
	});
}

function changeIsRepair(event) {
	const id = $(event.target).parents("tr").data("complete_renovation_object_id");
	let value;
	if ($(event.target).prop("checked")) {
		value = 1;
	} else {
		value = 0;
	}
	$.ajax({
		method: "POST",
		url: "/complete_renovation_objects/change_is_repair_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editMonth(event) {
	const id = $(event.target).closest("tr").data("complete_renovation_object_id");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/complete_renovation_objects/edit_month_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

function editR3Number(event) {
	const id = $(event.target).closest("tr").data("complete_renovation_object_id");
	const value = event.target.value;

	$.ajax({
		method: "POST",
		url: "/complete_renovation_objects/edit_r3_number_ajax",
		data: { id, value },
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			toastr.success(data.message, "Успіх");
		} else {
			toastr.error(data.message, "Помилка");
		}
	});
}

$("#addOperatingListObjectModal").on("hidden.bs.modal", function (event) {
	$(event.target).find(".row select, .row input").val("");
	$(event.target)
		.find(".row select, .row input")
		.removeClass("is-invalid is-valid")
		.next()
		.text("");
});

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

let exampleEl = $('[data-bs-toggle="tooltip"]');
if (exampleEl) {
	for (let i = 0; i < exampleEl.length; i++) {
		let tooltip = new bootstrap.Tooltip(exampleEl[i]);
	}
}

ClassicEditor
	.create(document.querySelector('#description'))
	.catch(error => {
		console.error(error);
	});
