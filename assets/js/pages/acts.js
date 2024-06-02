$(document).ready(function () {
	if ($(".dt-buttons").length) {
		// $(".dt-buttons").find(".buttons-excel, .buttons-pdf, .buttons-active-form").remove();
	}

	if ($(".date").length) {
		$(".date").datetimepicker({
			format: "DD.MM.YYYY",
		});
	}

	if ($("[data-mask]").length) {
		$("[data-mask]").inputmask();
	}

	if ($(".select2").length) {
		$(".select2").select2();
	}
});

if ($("#createForm").length) {
	$("#createForm").validate({
		// rules: {
		// 	email: {
		// 		required: true,
		// 		email: true,
		// 	},
		// 	password: {
		// 		required: true,
		// 		minlength: 5
		// 	},
		// 	terms: {
		// 		required: true
		// 	},
		// },
		// messages: {
		// 	email: {
		// 		required: "Please enter a email address",
		// 		email: "Please enter a valid email address"
		// 	},
		// 	password: {
		// 		required: "Please provide a password",
		// 		minlength: "Your password must be at least 5 characters long"
		// 	},
		// 	terms: "Please accept our terms"
		// },
		errorElement: "div",
		errorPlacement: function (error, element) {
			error.addClass("invalid-feedback");
			element.closest(".form-group").append(error);
		},
		highlight: function (element, errorClass, validClass) {
			$(element).addClass("is-invalid");
		},
		unhighlight: function (element, errorClass, validClass) {
			$(element).removeClass("is-invalid").addClass("is-valid");
		},
	});

	const scriptURL = "https://script.google.com/macros/s/AKfycbxU8QUjkzfemNIh_MxmovduwT8AWQg9vMlZPG3zotVa9AQyg61703zbMKdprqN_lLCnvg/exec";
	const form = document.forms["submit-to-google-sheet"];

	form.addEventListener("submit", (e) => {
		e.preventDefault();
		if ($("#createForm").valid()) {
			let form_data = new FormData(form);
			form_data.set("id", (+new Date()).toString(16));
			let count = location.href.split("/").pop();

			let plan_start = [];
			for (let i = 1; i <= count; i++) {
				if (form_data.get("plan_start_" + i) !== "") {
					plan_start.push(form_data.get("plan_start_" + i));
				}
			}

			let plan_end = [];
			for (let i = 1; i <= count; i++) {
				if (form_data.get("plan_end_" + i) !== "") {
					plan_end.push(form_data.get("plan_end_" + i));
				}
			}

			let fact_start = [];
			for (let i = 1; i <= count; i++) {
				if (form_data.get("fact_start_" + i) !== "") {
					fact_start.push(form_data.get("fact_start_" + i));
				}
			}

			let fact_end = [];
			for (let i = 1; i <= count; i++) {
				if (form_data.get("fact_end_" + i) !== "") {
					fact_end.push(form_data.get("fact_end_" + i));
				}
			}

			form_data.set("plan_start", plan_start.join(", "));
			form_data.set("fact_start", fact_start.join(", "));
			form_data.set("plan_end", plan_end.join(", "));
			form_data.set("fact_end", fact_end.join(", "));
			form_data.set("commission_members", form_data.getAll("commission_members").join(", "));
			form_data.set("work_members", form_data.getAll("work_members").join(", "));

			fetch(scriptURL, {
				method: "POST",
				body: form_data,
			})
				.then((response) => {
					console.log("Success!", response);
					alert("Дані додано!!!");
					// location.href = "/acts";
				})
				.catch((error) => console.error("Error!", error.message));
		}
	});
}

function createRow(event) {
	location.href = "/acts/create/1";
}
