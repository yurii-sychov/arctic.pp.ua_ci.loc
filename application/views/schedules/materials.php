<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="text-center loading">
			<div class="spinner-border text-primary" role="status"></div>
		</div>
		<table class="datatable table table-striped table-hover table-bordered d-none" data-order='[[ 0, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
			<thead class="table-primary">
				<tr class="align-middle">
					<!-- <th>№ п/п</th> -->
					<th class="text-center" style="width: 25px;">Найменування матеріалу</th>
					<th class="text-center" style="width: 15px;">Номер R3</th>
					<th class="text-center" style="width: 15px;">Одиниця виміру</th>
					<!-- <th class="text-center">Кількість</th> -->
					<th class="text-center" style="width: 15px;">Вид ремонту</th>
					<!-- <th class="text-center">ПС</th> -->
					<th class="text-center" style="width: 15px;">ПС 35-150 кВ (Баланс СП)</th>
					<th class="text-center" style="width: 15px;">ПС 35 кВ (Баланс СРМ)</th>
				</tr>
			</thead>
			<tbody>
				<?php $i = 1; ?>
				<?php foreach ($materials as $item) : ?>
					<tr class="text-start">
						<!-- <td><?php echo $i; ?></td> -->
						<td class="text-start"><?php echo $item->name; ?></td>
						<td class="text-center"><?php echo $item->r3; ?></td>
						<td class="text-center"><?php echo $item->unit; ?></td>
						<!-- <td><?php echo $item->quantity; ?></td> -->
						<td class="text-center"><?php echo $item->type_service; ?></td>
						<!-- <td><?php echo $item->stantion; ?></td> -->
						<td class="text-center"><?php echo $item->stantion_150; ?></td>
						<td class="text-center"><?php echo $item->stantion_35; ?></td>
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>