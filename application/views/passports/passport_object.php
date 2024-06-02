<h1 class="text-center">ПАСПОРТ (процес розробки)</h1>
<h2 class="text-center"><?php echo $complete_renovation_object->name; ?></h2>

<?php foreach ($results as $group => $equipments) : ?>
	<table class="table">
		<thead>
			<tr>
				<th colspan="5">
					<h3 class="text-center"><?php echo $equipments[0]['equipment']; ?><small><?php echo ' (' . count($equipments) . ' од.)'; ?></small></h3>
				</th>
			</tr>
			<tr>
				<th width="<?php echo $equipments[0]['id_w']; ?>">№ з/п</th>
				<th width="<?php echo $equipments[0]['disp_w']; ?>">Дисп. назва (місце)</th>
				<th width="<?php echo $equipments[0]['type_w']; ?>">Тип обладнання (ізоляція)</th>
				<th width="<?php echo $equipments[0]['number_w']; ?>">Зав. №</th>
				<th width="<?php echo $equipments[0]['year_w']; ?>">Рік виг.</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;
			foreach ($equipments as $item) : ?>
				<tr style="color: <?php echo $item['color']; ?>;">
					<td width="<?php echo $item['id_w']; ?>"><?php echo $i; ?></td>
					<td width="<?php echo $item['disp_w']; ?>" style="text-align: left;"><?php echo $item['disp'] . ' (' . $item['place'] . ')'; ?></td>
					<td width="<?php echo $item['type_w']; ?>" style="text-align: left;"><?php echo $item['type'] . ' <small>(' . $item['insulation_type'] . ')</small>'; ?></td>
					<td width="<?php echo $item['number_w']; ?>"><?php echo $item['number']; ?></td>
					<td width="<?php echo $item['year_w']; ?>"><?php echo $item['year']; ?></td>
				</tr>
			<?php $i++;
			endforeach; ?>
		</tbody>
	</table>
	<br />
	<br />
<?php endforeach; ?>
