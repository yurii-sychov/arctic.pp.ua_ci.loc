<h1 align="center">
	<a href="/passports/gen_passport_pdf/<?php echo $passport->id; ?>" style="text-decoration:none; color:#000000;">
		ПАСПОРТ
	</a>
</h1>
<h2 align="center">
	<a href="/passports/gen_passport_pdf/<?php echo $passport->id; ?>/1" style="text-decoration:none; color:#000000;">
		<?php echo $passport->stantion; ?>
	</a>
</h2>
<h3 align="center"><?php echo $passport->equipment . ' (' . $passport->disp . ' ' . $passport->place . ')'; ?></h3>
<table border="1" align="center" cellpadding="5">
	<thead>
		<tr>
			<th width="33%"><strong>Тип</strong></th>
			<th width="33%"><strong>Номер</strong></th>
			<th width="17%"><strong>Рік випуску</strong></th>
			<th width="17%"><strong>Рік вводу</strong></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<th width="33%"><?php echo $passport->type; ?></th>
			<th width="33%"><?php echo $passport->number; ?></th>
			<th width="17%"><?php echo date("Y", strtotime($passport->production_date)); ?></th>
			<th width="17%"><?php echo $passport->commissioning_year; ?></th>
		</tr>
	</tbody>
</table>

<?php if (count($passport->properties)) : ?>
	<h3 align="center">Технічні характеристики</h3>
	<table border="1" align="center" cellpadding="5">
		<thead>
			<tr>
				<th width="66%"><strong>Характеристика</strong></th>
				<th width="34%"><strong>Значення</strong></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($passport->properties as $item) : ?>
				<?php if ($item->value) : ?>
					<tr>
						<td width="66%" align="left"><strong><?php echo $item->property; ?></strong></td>
						<td width="34%"><?php echo $item->value; ?></td>
					</tr>
				<?php endif; ?>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
<?php if (count($passport->properties) < 1) : ?>
	<h3 align="center" style="color:red;">Технічні характеристики відсутні в реєстрі.</h3>
<?php endif; ?>

<?php if ($is_hide === NULL) : ?>
	<?php if (count($operating_list)) : ?>
		<h3 align="center">Експлуатаційна відомість</h3>
		<table border="1" align="center" cellpadding="5">
			<thead>
				<tr>
					<th width="15%"><strong>Дата</strong></th>
					<th width="65%"><strong>Дані про пошкодження, ремонти, випробування, чищення, результати огляду, відбори проб масла</strong></th>
					<th width="20%"><strong>Виконавець</strong></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($operating_list as $item) : ?>
					<tr>
						<td width="15%"><strong><?php echo $item->service_date_format; ?></strong></td>
						<td width="65%" align="left"><?php echo $item->service_data; ?></td>
						<td width="20%" align="left"><?php echo $item->executor; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	<?php endif; ?>
	<?php if (count($operating_list) < 1) : ?>
		<h3 align="center" style="color:red;">Експлуатаційна відомість відсутня в реєстрі.</h3>
	<?php endif; ?>

	<br />
	<br />
	<br />
	<br />
	<br />

	<table>
		<tr>
			<td width="40%" align="right"><strong><?php echo $this->session->user->position; ?></strong></td>
			<td width="30%"></td>
			<td width="30%">
				<strong><?php echo $this->session->user->name . ' ' . mb_strtoupper($this->session->user->surname); ?></strong>
			</td>
		</tr>
	</table>

<?php endif; ?>