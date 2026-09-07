<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="text-center loading">
			<div class="spinner-border text-primary" role="status"></div>
		</div>
		<div class="table-responsive">
			<table class="datatable table table-striped table-hover table-bordered d-none" data-order='[[ 0, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
				<thead>
					<tr class="align-middle">
						<th class="text-center" style="width: 5%;">№ п/п</th>
						<th class="text-center" style="width: 15%;">ПС</th>
						<th class="text-center" style="width: 7%;">ДНО</th>
						<th class="text-center" style="width: 10%;">Вид ремонту</th>
						<th class="text-center" style="width: 63%;">Найменування матеріалу (кількість, одиниця виміру, номер R3)</th>
						<!-- <th class="text-center" style="width: 10%;">Номер R3</th> -->
						<!-- <th class="text-center" style="width: 10%;">Одиниця виміру</th> -->
					</tr>
				</thead>
				<tbody>
					<?php $i = 1; ?>
					<?php foreach ($materials as $item) : ?>
						<tr class="text-start align-middle">
							<td class="text-center align-middle"><?php echo $i; ?></td>
							<td class="text-start align-middle"><?php echo $item['stantion']; ?></td>
							<td class="text-center align-middle"><?php echo $item['disp']; ?></td>
							<td class="text-center align-middle"><?php echo $item['type_service']; ?></td>
							<td class="text-start align-middle">
								<table class="table table-hover table-primary m-0">
									<?php foreach ($item['materials'] as $key => $material) : ?>
										<tr class="text-start align-middle">
											<td class="text-start" style="width: 60%;">
												<?php echo $material['name']; ?>
											</td>
											<td class="text-center" style="width: 10%;">
												<?php echo $material['quantity']; ?>
											</td>
											<td class="text-center" style="width: 10%;">
												<?php echo $material['unit']; ?>
											</td>
											<td class="text-center" style="width: 10%;">
												<?php echo $material['r3'] ? $material['r3'] : '00000000'; ?>
											</td>
											<td class="text-center" style="width: 5%;">
												<a href="javascript:void(0);" class="text-success"><i class="bi bi-pencil"></i></a>
											</td>
											<td class="text-center" style="width: 5%;">
												<a href="javascript:void(0);" class="text-danger"><i class="bi bi-trash"></i></a>
											</td>
										</tr>
									<?php endforeach; ?>
								</table>
							</td>
							<!-- <td class="text-center">
							<?php foreach ($item['materials']['r3'] as $key => $r3) : ?>
								<?php echo $r3; ?>
								<?php if (isset($item['materials']['r3'][$key + 1])) : ?>
									<br>
								<?php endif; ?>
							<?php endforeach; ?>
						</td> -->
							<!-- <td class="text-center">
							<?php foreach ($item['materials']['unit'] as $key => $unit) : ?>
								<?php echo $unit; ?>
								<?php if (isset($item['materials']['unit'][$key + 1])) : ?>
									<br>
								<?php endif; ?>
							<?php endforeach; ?>
						</td> -->
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>