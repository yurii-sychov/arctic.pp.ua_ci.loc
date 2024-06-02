<h1 class="text-center">Експлуатаційна відомість</h1>
<h2 class="text-center"><?php echo $complete_renovation_object->name; ?></h2>

<table class="table">
	<thead>
		<tr>
			<th width="10%">№ з/п</th>
			<th width="15%">Дата</th>
			<th width="10%">Дисп. назва (місце)</th>
			<th width="45%">Дані про пошкодження, ремонти, випробування, чищення, результати огляду, відбори проб масла</th>
			<th width="20%">Виконавець</th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 1;
		foreach ($results as $item) : ?>
			<tr>
				<td width="10%"><?php echo $i; ?></td>
				<td width="15%"><?php echo $item->service_date_format; ?></td>
				<td width="10%"><?php echo $item->disp . " (" . $item->place . ")"; ?></td>
				<td width="45%"><?php echo $item->service_data; ?></td>
				<td width="20%"><?php echo $item->executor; ?></td>
			</tr>
		<?php $i++;
		endforeach; ?>
	</tbody>
</table>
<br />
<br />
