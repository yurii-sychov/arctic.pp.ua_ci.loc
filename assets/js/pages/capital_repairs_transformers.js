$(document).ready(function () {
	$(".datemask").mask("99.99.9999");
});

function addDocument(event) {
	const form = $("#formAddDocument");
	const modal = $("#addDocumentModal");
	formData = new FormData(form.get(0));
	$.ajax({
		method: "POST",
		url: "/capital_repairs_transformers/add_document",
		data: formData,
		processData: false,
		contentType: false,
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				modal.modal("hide");
				event.target.disabled = false;
				form.find(".row input").removeClass("is-invalid is-valid");
				form[0].reset();
				location.reload();
			}, 1000);
		}
		if (data.status === "ERROR" && data.errors) {
			if (data.errors.document_date) {
				$("#documentDate")
					.addClass("is-invalid")
					.next()
					.text(data.errors.document_date);
			} else {
				$("#documentDate")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.document_description) {
				$("#documentDescription")
					.addClass("is-invalid")
					.next()
					.text(data.errors.document_description);
			} else {
				$("#documentDescription")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
		if (data.status === "ERROR" && data.error) {
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			$("#documentScan").addClass("is-invalid").next().text(data.message);
			toastr.error(data.message, "Помилка");
		}
	});
}

function openAddDocumentModal(event) {
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
	const passport_id = $(event.currentTarget).closest("tr").data("passport_id");

	$("#idSubdivisionIdAddDocument").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAddDocument").val(
		complete_renovation_object_id
	);
	$("#idSpecificRenovationObjectIdAddDocument").val(
		specific_renovation_object_id
	);
	$("#idPlaceIdAddDocument").val(place_id);
	$("#idPassportIdAddDocument").val(passport_id);

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const title = `Завантаження документу<br /><span class="text-success">${stantion} (${disp})<span>`;

	const modal = $("#addDocumentModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function addPhotos(event) {
	const form = $("#formAddPhotos");
	const modal = $("#addPhotosModal");
	formData = new FormData(form.get(0));
	$.ajax({
		method: "POST",
		url: "/capital_repairs_transformers/add_photos",
		data: formData,
		processData: false,
		contentType: false,
	}).done(function (data) {
		if (data.status === "SUCCESS") {
			event.target.disabled = true;
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			toastr.success(data.message, "Успіх");
			setTimeout(() => {
				modal.modal("hide");
				event.target.disabled = false;
				form.find(".row input").removeClass("is-invalid is-valid");
				form[0].reset();
				location.reload();
			}, 1000);
		}
		if (data.status === "ERROR" && data.errors) {
			if (data.errors.photo_album_date) {
				$("#photoAlbumDate")
					.addClass("is-invalid")
					.next()
					.text(data.errors.photo_album_date);
			} else {
				$("#photoAlbumDate")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			if (data.errors.photo_album_name) {
				$("#photoAlbumName")
					.addClass("is-invalid")
					.next()
					.text(data.errors.photo_album_name);
			} else {
				$("#photoAlbumName")
					.removeClass("is-invalid")
					.addClass("is-valid")
					.next()
					.text("");
			}
			toastr.error(data.message, "Помилка");
		}
		if (data.status === "ERROR" && data.error) {
			form
				.find(".row input")
				.removeClass("is-invalid")
				.addClass("is-valid")
				.next()
				.text("");
			$("#photos").addClass("is-invalid").next().text(data.message);
			toastr.error(data.message, "Помилка");
		}
	});
}

function openAddPhotosModal(event) {
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
	const passport_id = $(event.currentTarget).closest("tr").data("passport_id");

	$("#idSubdivisionIdAddPhoto").val(subdivision_id);
	$("#idCompleteRenovationObjectIdAddPhoto").val(complete_renovation_object_id);
	$("#idSpecificRenovationObjectIdAddPhoto").val(specific_renovation_object_id);
	$("#idPlaceIdAddPhoto").val(place_id);
	$("#idPassportIdAddPhoto").val(passport_id);

	const stantion = $(event.currentTarget)
		.closest("tr")
		.find(".stantion")
		.text();
	const disp = $(event.currentTarget).closest("tr").find(".disp").text();
	const title = `Завантаження фото<br /><span class="text-success">${stantion} (${disp})<span>`;

	const modal = $("#addPhotosModal");
	modal.find(".modal-title").html(title);
	modal.modal("show");
}

function openOperatingListModal(event) {
	const modal = $("#operatingListModal");
	modal.modal("show");
}

$("#addDocumentModal, #addPhotosModal").on("hidden.bs.modal", function (event) {
	$(event.target).find(".row select, .row input").val("");
	$(event.target)
		.find(".row select, .row input")
		.removeClass("is-invalid is-valid")
		.next()
		.text("");
	$(event.target).find("form input").val("");
});

function deleteDocument(event) {
	let result = confirm("Ви впевнені? Буде видалено запис з документом.");
	if (result) {
		location.href =
			"/capital_repairs_transformers/delete_document/" +
			$(event.currentTarget).parents("tr").data("id");
	} else {
		return;
	}
}

function deletePhotoAlbum(event) {
	let result = confirm("Ви впевнені?");
	if (result) {
		location.href =
			"/capital_repairs_transformers/delete_photo_album/" +
			$(event.currentTarget).parents("tr").data("id");
	} else {
		return;
	}
}

function editRow(event) {
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

function editDocumentHandler(event) {
	let form = $(event.currentTarget).closest("tr").find("input, select");

	let is_edit = $(event.currentTarget)
		.closest("tr")
		.find('[name="is_edit"]')
		.val();
	let id = $(event.currentTarget).closest("tr").data("id");
	let document_date = $(event.currentTarget)
		.closest("tr")
		.find('[name="document_date"]')
		.val();
	let document_description = $(event.currentTarget)
		.closest("tr")
		.find('[name="document_description"]')
		.val();

	$.ajax({
		method: "POST",
		url: "/capital_repairs_transformers/edit_document",
		data: {
			is_edit,
			id,
			document_date,
			document_description,
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

function editPhotoAlbumHandler(event) {
	let form = $(event.currentTarget).closest("tr").find("input, select");

	let is_edit = $(event.currentTarget)
		.closest("tr")
		.find('[name="is_edit"]')
		.val();
	let id = $(event.currentTarget).closest("tr").data("id");
	let photo_album_date = $(event.currentTarget)
		.closest("tr")
		.find('[name="photo_album_date"]')
		.val();
	let photo_album_name = $(event.currentTarget)
		.closest("tr")
		.find('[name="photo_album_name"]')
		.val();

	$.ajax({
		method: "POST",
		url: "/capital_repairs_transformers/edit_photo_album",
		data: {
			is_edit,
			id,
			photo_album_date,
			photo_album_name,
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
			url: "/capital_repairs_transformers/upload_document_scan",
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

function actionCollapse(event) {
	// $(event.currentTarget).toggleClass(
	// 	"bi-eye-slash text-primary bi-eye text-info"
	// );
	// const tr_current = $(event.currentTarget).closest("tr");
	// const tr_not_current_and_next = $("#collapseParent tbody tr.parent").not(
	// 	tr_current
	// );
	// tr_current.toggleClass("bg-custom");
	// tr_not_current_and_next.toggle(400);
}

$(".datepicker").datepicker({
	format: "dd.mm.yyyy",
	autoclose: true,
});

var exampleEl = $('[data-bs-toggle="tooltip"]');
if (exampleEl) {
	for (let i = 0; i < exampleEl.length; i++) {
		var tooltip = new bootstrap.Tooltip(exampleEl[i]);
	}
}

lightbox.option({
	wrapAround: true,
	albumLabel: "Зображення %1 з %2",
});

const containerScrollbar_1 = new PerfectScrollbar("#containerScrollbar_1");

if (containerScrollbar_1) $("#containerScrollbar_1").show();

const containerScrollbar_2 = new PerfectScrollbar("#containerScrollbar_2");

if (containerScrollbar_2) $("#containerScrollbar_2").show();
