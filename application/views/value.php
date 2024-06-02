<?php

/**
 * Developer: Yurii Sychov
 * Site: http://sychov.pp.ua
 * Email: yurii@sychov.pp.ua
 */

defined('BASEPATH') or exit('No direct script access allowed');

?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Language" content="en">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Title</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
	<meta name="description" content="Tables are the backbone of almost all web applications.">
	<meta name="msapplication-tap-highlight" content="no">

	<link rel="icon" href="data:;base64,=">
</head>

<body>
	<ul>
		<?php foreach ($values as $item) : ?>
			<li>
				<a href="#" class="value" onClick="set_value(event);" data-field="<?php echo $field; ?>">
					<?php echo $item->$field; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<script>
		function set_value(event) {
			event.preventDefault();
			const field = event.target.dataset.field;
			console.log(event.target.text.trim());
			console.log(opener);
			opener.document.querySelector('form [name="' + field + '"]').value = event.target.text.trim();
			close();
		}
	</script>
</body>

</html>
