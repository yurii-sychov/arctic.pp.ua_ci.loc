<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card card-indigo card-outline">
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
				<!-- /.card-header -->
				<div class="card-body">
					<?php foreach ($calendar_day as $month => $day): ?>
						<!-- <div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Початок та кінець першого дня:</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="far fa-clock"></i></span>
										</div>
										<input type="text" class="form-control float-right reservationtime">
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Початок та кінець другого дня:</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="far fa-clock"></i></span>
										</div>
										<input type="text" class="form-control float-right reservationtime">
									</div>
								</div>
							</div>
						</div> -->
						<div class="table-responsive">
							<table class="table table-bordered table-striped table-hover table-sm mb-3">
								<!-- <caption>List of users</caption> -->
								<thead>
									<tr>
										<th class="text-center" colspan="32">
											<?php echo mb_strtoupper($months[$month]); ?>
										</th>
									</tr>
									<tr>
										<th class="text-center" style="width: 7%;">Працівник</th>
										<?php for ($i = 0; $i < 31; $i++): ?>
											<th class="text-center <?php echo (isset($calendar_day_off[$month][$i]) && $calendar_day_off[$month][$i]) ? 'text-danger' : NULL; ?> <?php echo (isset($calendar_second_thursday[$month][$i]) && $calendar_second_thursday[$month][$i]) ? 'text-success' : NULL; ?>" style="width: 3%;"><?php echo isset($day[$i]) ? $day[$i] : NULL; ?></th>
										<?php endfor; ?>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($shift_workers as $worker): ?>
										<tr>
											<td class="align-middle">
												<?php echo $worker->surname; ?>
												<?php echo $worker->name ? ' ' . mb_substr($worker->name, 0, 1) . '.' : NULL; ?>
												<?php echo $worker->patronymic ? ' ' . mb_substr($worker->patronymic, 0, 1) . '.' : NULL; ?>
											</td>
											<?php for ($i = 0; $i < 31; $i++): ?>
												<th class="text-center" style="width: 3%;">
													<?php if (isset($day[$i])): ?>
														<input type="text" class="form-control form-control-sm mb-1 text-center" data-date="<?php echo $calendar_day_full[$month][$i]; ?>">
														<!-- <input type="text" class="form-control form-control-sm mt-1 float-right reservationtime"> -->
													<?php endif; ?>
												</th>
											<?php endfor; ?>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</div>