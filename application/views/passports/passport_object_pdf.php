<style>
	td {
		border: 1px solid #000000;
		line-height: 100%;
	}

	.group th {
		font-weight: bold;
		border-bottom: 1px solid #000000;
		letter-spacing: 1px;
	}

	.no-group th {
		font-weight: bold;
		border: 1px solid #000000;
	}
</style>
<h1 align="center">
	ПАСПОРТ ЕНЕРГЕТИЧНОГО ОБ`ЄКТА
	<!-- <span style="color: green">(Процес розробки)</span> -->
</h1>
<!-- <h5 align="center" style="color: green">Пропозиції та зауваження надавати О. ГОРДІЄНКО (тел. 15-45)</h5> -->
<h2 align="center"><?php echo $complete_renovation_object->name; ?></h2>

<?php foreach ($results as $group => $equipments) : ?>
	<table border="0" cellpadding="5" cellspacing="0" align="center">
		<thead>
			<tr class="group">
				<th colspan="5">
					<h3>
						<a href="/passports/gen_passport_object_pdf/<?php echo $this->uri->segment(4) ? $this->uri->segment(3) : $this->uri->segment(3) . '/' . $equipments[0]['equipment_id']; ?>" style="text-decoration:none; color:#000000;">
							<?php echo $equipments[0]['equipment']; ?><small><?php echo ' (' . count($equipments) . ' од.)'; ?></small>
						</a>
					</h3>
				</th>
			</tr>
			<tr class="no-group">
				<th width="<?php echo $equipments[0]['id_w']; ?>">№ з/п</th>
				<th width="<?php echo $equipments[0]['disp_w']; ?>">Дисп. назва<br />(місце)</th>
				<th width="<?php echo $equipments[0]['type_w']; ?>">Тип обладнання<br />(ізоляція)</th>
				<th width="<?php echo $equipments[0]['number_w']; ?>" valign="middle">Зав. №</th>
				<th width="<?php echo $equipments[0]['year_w']; ?>">Рік виготовлення<br />(рік вводу)
				</th>
				<th width="<?php echo $item['properties_w']; ?>">Основні технічні характеристики</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1;
			foreach ($equipments as $item) : ?>
				<tr style="color: <?php echo $item['color']; ?>;" nobr="true">
					<td width="<?php echo $item['id_w']; ?>">
						<?php echo $i; ?>
					</td>
					<td width="<?php echo $item['disp_w']; ?>" style="text-align: center;" bgcolor="<?php echo $item['disp_bc']; ?>">
						<?php echo $item['disp'] . '<br /><small>(' . $item['place'] . ')</small>'; ?>
					</td>
					<td width="<?php echo $item['type_w']; ?>" style="text-align: center;" bgcolor="<?php echo $item['type_bc']; ?>">
						<?php echo $item['type'] . '<br /><small>(' . $item['insulation_type'] . ')</small>'; ?>
					</td>
					<td width="<?php echo $item['number_w']; ?>" bgcolor="<?php echo $item['number_bc']; ?>">
						<?php echo $item['number']; ?>
					</td>
					<td width="<?php echo $item['year_w']; ?>" bgcolor="<?php echo $item['year_bc']; ?>">
						<?php echo $item['year']; ?><br /><small color="<?php echo (!$item['c_year'] or ($item['c_year'] < $item['year'])) ? 'red' : NULL; ?>"><?php echo $item['c_year'] ? "(" . $item['c_year'] . ")" : "(NULL)"; ?></small>
					</td>
					<td width="<?php echo $item['properties_w']; ?>" style="text-align: left;">
						<?php if (count($item['properties'])) : ?>
							<ul>

								<?php foreach ($item['properties'] as $row) : ?>
									<li><strong><?php echo $row->property; ?>: </strong><?php echo $row->value; ?></li>
								<?php endforeach; ?>

							</ul>
						<?php else : ?>
							Дані не введено.
						<?php endif; ?>
					</td>
				</tr>
			<?php $i++;
			endforeach; ?>
		</tbody>
	</table>
	<br />
	<br />
<?php endforeach; ?>