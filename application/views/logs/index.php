<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover table-bordered">
				<thead>
					<tr class="text-center">
						<th style="width:10%">ПІБ</th>
						<th style="width:20%">Дія</th>
						<th style="width:30%">До</th>
						<th style="width:30%">Після</th>
						<th style="width:10%">Час</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($logs as $item) : ?>
						<tr class="align-middle">
							<td><?php echo $item->surname . "<br />" . $item->name . "<br />" . $item->patronymic; ?></td>
							<td><?php echo $item->action; ?></td>
							<td>
								<pre><?php print_r($item->data_before); ?></pre>
							</td>
							<td>
								<pre><?php print_r($item->data_after); ?></pre>
							</td>
							<td class="text-center"><?php echo $item->created_at; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>