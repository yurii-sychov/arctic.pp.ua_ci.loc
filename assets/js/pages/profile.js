$(document).ready(function () {
	setTimeout(function () {
		if ($(".scrollbar-container")[0]) {
			$('.scrollbar-container').each(function () {
				const ps = new PerfectScrollbar($(this)[0], {
					// wheelSpeed: 20,
					// wheelPropagation: false,
					// minScrollbarLength: 20
				});
				$($(this)[0]).fadeIn(1000);
				ps.update();
			});
		}

	}, 1000);
});
