<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>

<body>
	<br><br><br><br>
	<table cellpadding="2" border="0">
		<tbody>
			<tr>
				<td><strong>ЗАТВЕРДЖУЮ</strong></td>
			</tr>
			<tr>
				<td><strong>Начальник СП</strong></td>
			</tr>
			<tr>
				<td><strong>______________ Юрій СИЧОВ</strong></td>
			</tr>
			<tr>
				<td><strong>" ___ " ___________ 20 ___ рік</strong></td>
			</tr>
		</tbody>
	</table>

	<table cellpadding="2">
		<thead>
			<tr align="center">
				<th><strong>ПЕРЕЛІК</strong></th>
			</tr>
			<tr align="center">
				<th><?php echo $doc_name; ?></th>
			</tr>
			<tr align="center">
				<th>
					<?php echo $plot->name; ?>
				</th>
			</tr>
		</thead>
	</table>

	<table cellpadding="2">
		<tbody>
			<tr>
				<td>Термін дії встановлений:</td>
			</tr>
			<tr>
				<td>з " ___ " ___________ 20 ___ р.</td>
			</tr>
			<tr>
				<td>по " ___ " ___________ 20 ___ р.</td>
			</tr>
		</tbody>
	</table>
	<br><br>

	<table border="1" align="center" cellpadding="2">
		<thead>
			<tr>
				<th style="width: 5%;"><strong>№<br>п/п</strong></th>
				<th style="width: 84%;line-height: 250%;"><strong><?php echo $doc_name; ?></strong></th>
				<th style="width: 11%;"><strong>Термін<br>зберігання</strong></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($documentations as $k => $item): ?>
				<tr>
					<td style="width: 5%;"><?php echo $k + 1; ?></td>
					<td style="width: 84%;" align="left">
						<?php echo $item->name; ?>
						<br>
						(<?php echo $item->number; ?>)
					</td>
					<td style="width: 11%;">постійно</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<br><br><br><br>

	<table border="0">
		<tbody>
			<tr>
				<td align="right" style="width: 30%;"><strong>Начальник СП</strong></td>
				<td align="center" style="width: 40%;"></td>
				<td align="left" style="width: 30%;"><strong>Юрій СИЧОВ</strong></td>
			</tr>
		</tbody>
	</table>
</body>

</html>