<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card card-warning card-outline">
				<div class="card-header">
					<h3 class="card-title"><?php echo $title_heading_card; ?></h3>
					<div class="card-tools">
						<button type="button" class="btn btn-tool" data-card-widget="maximize">
							<i class="fas fa-expand"></i>
						</button>
						<button type="button" class="btn btn-tool" data-card-widget="collapse">
							<i class="fas fa-minus"></i>
						</button>
						<button type="button" class="btn btn-tool" data-card-widget="remove">
							<i class="fas fa-times"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<table id="ActsTable" class="datatable table table-bordered table-hover table-striped d-none" data-order='[[ 0, "asc" ]]' data-page-length="5" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
						<thead class="thead-light">
							<tr>
								<th class="align-middle text-center" style="width:15%;"><?php echo mb_strtoupper('Об\'єкт'); ?></th>
								<th class="align-middle text-center" style="width:15%;"><?php echo mb_strtoupper('ДНО'); ?></th>
								<th class="align-middle text-center" style="width:10%;"><?php echo mb_strtoupper('Початок'); ?></th>
								<th class="align-middle text-center" style="width:10%;"><?php echo mb_strtoupper('Кінець'); ?></th>
								<th class="align-middle text-center" style="width:15%;"><?php echo mb_strtoupper('Керівник'); ?></th>
								<th class="align-middle text-center" style="width:30%;"><?php echo mb_strtoupper('Виконавці'); ?></th>
								<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="word"><i class="fas fa-file-word text-secondary"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($results as $key => $item) : ?>
								<tr data-id="<?php echo $item['id']; ?>">
									<td class="align-middle"><?php echo $item['station']; ?></td>
									<td class="align-middle"><?php echo $item['dno']; ?></td>
									<td class="align-middle text-center"><?php echo $item['fact_start']; ?></td>
									<td class="align-middle text-center"><?php echo $item['fact_end']; ?></td>
									<td class="align-middle"><?php echo $item['work_head']; ?></td>
									<td class="align-middle"><?php echo $item['work_members']; ?></td>
									<td class="align-middle text-center">
										<a href="/acts/act_generation/<?php echo $item['id']; ?>" title="Генерувати акт в Word">
											<i class="fas fa-file-word text-primary"></i>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
