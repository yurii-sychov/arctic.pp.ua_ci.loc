function exportToExcel(event) {
	alert("exportToExcel(event)");
}

function exportToPDF(event) {
	alert("exportToPDF(event)");
}

function uploadPhoto(event) {
	let addressString = document.location.href.split("/");
	location.href =
		"/equipments/upload_photo/" + addressString[addressString.length - 1];
}

function backToHome(event) {
	location.href = document.referrer;
}
