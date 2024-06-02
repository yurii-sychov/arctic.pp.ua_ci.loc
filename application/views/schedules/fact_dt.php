<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-2 mb-1">
				<select name="subdivision_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/schedules/index">Оберіть підрозділ</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="/schedules/index/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-2 mb-1">
				<select name="stantion_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/schedules/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>" class="form-select">Оберіть підстанцію</option>
					<?php foreach ($complete_renovation_objects as $item) : ?>
						<option value="/schedules/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('stantion_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-2 mb-1">
				<input type="text" value="<?php echo isset($avr_price->price) ? 'Сума для АВР: ' . number_format($avr_price->price, 2, ',', ' ') : NULL; ?>, грн" class="form-control text-danger" placeholder="Сума для АВР на <?php echo (date('Y') + 1); ?> рік, грн." <?php echo $this->session->user->group !== 'admin' ? 'disabled' : NULL; ?> onchange="editAvrPrice(event);" style="font-weight: 900;">
			</div>
			<?php if ($this->input->get('subdivision_id') && $this->input->get('stantion_id')) : ?>
				<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
					<div class="col-md-12 col-lg-3 d-grid gap-2 mb-1">
						<a class="btn <?php echo count($equipments) > 0 ? 'btn-primary' : 'btn-outline-info'; ?>" href="/schedules/genarate_schedule/<?php echo $this->input->get('subdivision_id'); ?>/<?php echo $this->input->get('stantion_id'); ?>"><?php echo count($equipments) > 0 ? '<i class="bi bi-arrow-clockwise"></i> Оновити' : '<i class="bi bi-database-add"></i> Генерувати' ?> графік по об'єкту на <strong><?php echo (date('Y') + 1); ?></strong> рік</a>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<?php if ($this->session->user->group === 'admin' && count($equipments) > 0) : ?>
				<div class="col-md-12 col-lg-3 d-grid gap-2 mb-1">
					<a class="btn btn-danger" href="/schedules/delete_schedule/<?php echo $this->input->get('subdivision_id'); ?>/<?php echo $this->input->get('stantion_id'); ?>"><i class="bi bi-trash"></i> Видалити графік для об'єкту на <strong><?php echo (date('Y') + 1); ?></strong> рік</a>
				</div>
			<?php endif; ?>
		</div>
		<hr>
		<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
			<div class="row">
				<?php if ($this->input->get('subdivision_id') && $this->input->get('stantion_id') && count($equipments) > 0) : ?>
					<div class="col-sm-12 col-md-6 col-lg-3 d-grid gap-2 mb-1">
						<a class="btn btn-outline-secondary" href="/schedules/genarate_defect_list_excel/<?php echo $this->input->get('stantion_id'); ?>"><i class="bi bi-file-earmark-excel"></i> Відомість дефектів та витрат на <strong><?php echo (date('Y') + 1); ?></strong> рік</a>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-3 d-grid gap-2 mb-1">
						<a class="btn btn-outline-warning" href="/schedules/genarate_year_schedule_complex_excel/<?php echo $this->input->get('stantion_id'); ?>"><i class="bi bi-file-earmark-excel"></i> Річний план-графік на <strong><?php echo (date('Y') + 1); ?></strong> рік</a>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-3 d-grid gap-2 mb-1">
						<a class="btn btn-outline-success" href="/schedules/genarate_year_schedule_simple_excel/<?php echo $this->input->get('stantion_id'); ?>"><i class="bi bi-file-earmark-excel"></i> Річний план-графік для майстрів на <strong><?php echo (date('Y') + 1); ?></strong> рік</a>
					</div>
					<div class="col-sm-12 col-md-6 col-lg-3 d-grid gap-2 mb-1">
						<a class="btn btn-outline-dark" href="javascript:void(0)" disabled><i class="bi bi-file-earmark-excel"></i> FREE BUTTON</a>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if (count($equipments)) : ?>
			<div class="row my-2">
				<div class="col-lg-12">
					<h2 class="text-center text-success"><?php echo $complete_renovation_object->name; ?> <span class="text-danger">(дата оновлення: <?php echo ''; ?>)</span></h2>
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<table class="datatable table table-bordere table-dark d-none" data-order='[[ 1, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0" data-row-id="id">
						<caption>
							<ul>
								<li class="text-danger">Рожевим кольором виділено обладнання, що планується ремонтувати у <?php echo date('Y'); ?> році. При зміні року останнього обслуговування не повинно потрапляти в графік.</li>
								<li class="text-info">Блакитним кольором виділено обладнання, що планується ремонтувати у <?php echo (date('Y') + 1); ?> році поза планом.</li>
							</ul>
						</caption>
						<thead>
							<tr class="align-middle text-center">
								<th class="text-center" style="width:5%;">№ п/п</th>
								<th class="text-center" style="width:10%;" data-data="dno">Дисп. назва</th>
								<th class="text-center" style="width:12%;" colspan="3">Ресурси</th>
								<th class="text-center" style="width:17%;" data-data="equipment">Вид обладнання</th>
								<th class="text-center" style="width:15%;">Тип обладнання</th>
								<th class="text-center" style="width:10%;">Спосіб обслуговування</th>
								<th class="text-center" style="width:10%;">Тип обслуговування</th>
								<th class="text-center" style="width:7%;">План, м.</th>
								<th class="text-center" style="width:10%;">Факт, ч. м. р.</th>
								<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-database-dash"></i></th>
								<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-card-text"></i></th>
								<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-basket"></i></th>
								<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-trash"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; ?>
							<?php foreach ($equipments as $row) : ?>
								<tr id="<?php echo $row->id; ?>" class="align-middle <?php if ($row->will_delete) : ?>table-danger<?php endif; ?> <?php if ($row->will_add) : ?>table-info<?php endif; ?>" data-id="<?php echo $row->id; ?>" data-user_group="<?php echo $this->session->user->group; ?>">
									<td class="text-center"><?php echo $i; ?></td>
									<td class="text-center"><?php echo $row->dno; ?></td>
									<td class="text-center materials-info" style="width:4%;">
										<span style="cursor:pointer;" class="position-relative badge rounded-pill <?php echo count($row->materials) > 0 ? 'bg-primary' : 'bg-danger'; ?>" data-bs-toggle="tooltip" title="Кількість матеріалів"><?php echo count($row->materials) ? '<i class="bi bi-boxes"></i> ' . (count($row->materials) - count($row->materials_is_extra)) : '<i class="bi bi-boxes"></i> 0'; ?>
											<span style="cursor:pointer;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?php echo count($row->materials_is_extra) > 0 ? 'bg-danger' : 'bg-secondary'; ?>">
												<?php echo count($row->materials_is_extra) > 0 ? count($row->materials_is_extra) . ' +' : 0; ?>
											</span>
										</span>
									</td>
									<td class="text-center workers-info" style="width:4%;">
										<span style="cursor:pointer;" class="position-relative badge rounded-pill <?php echo count($row->workers) > 0 ? 'bg-info text-dark' : 'bg-danger'; ?>" data-bs-toggle="tooltip" title="Кількість працівників"><?php echo count($row->workers) ? '<i class="bi bi-people"></i> ' . (count($row->workers) - count($row->workers_is_extra)) : '<i class="bi bi-people"></i> 0'; ?>
											<span style="cursor:pointer;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?php echo count($row->workers_is_extra) > 0 ? 'bg-danger' : 'bg-secondary'; ?>">
												<?php echo count($row->workers_is_extra) > 0 ? count($row->workers_is_extra) . ' +' : 0; ?>
											</span>
										</span>
									</td>
									<td class="text-center technics-info" style="width:4%;">
										<span style="cursor:pointer;" class="position-relative badge rounded-pill <?php echo count($row->technics) > 0 ? 'bg-warning text-dark' : 'bg-danger'; ?>" data-bs-toggle="tooltip" title="Кількість техніки"><?php echo count($row->technics) ? '<i class="bi bi-bus-front-fill"></i> ' . (count($row->technics) - count($row->technics_is_extra)) : '<i class="bi bi-bus-front-fill"></i> 0'; ?>
											<span style="cursor:pointer;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?php echo count($row->technics_is_extra) > 0 ? 'bg-danger' : 'bg-secondary'; ?>">
												<?php echo count($row->technics_is_extra) > 0 ? count($row->technics_is_extra) . ' +' : 0; ?>
											</span>
										</span>
									</td>
									<td class="text-start"><?php echo $row->equipment; ?> <?php echo $row->voltage > 1000 ? number_format(($row->voltage / 1000), 0) : $row->voltage / 1000; ?> кВ</td>
									<td class="text-center">
										<?php foreach ($row->passports as $passport) : ?>
											<span class="badge <?php echo $passport->color; ?> my-1"><?php echo $passport->short_type . ' (Зав. №' . $passport->number . ')'; ?></span><br>
										<?php endforeach; ?>
									</td>
									<td class="text-center"><?php echo $row->type_service; ?></td>
									<td class="text-center"><?php echo $row->is_contract_method ? '<span class="text-danger">підр.</span>' : '<span>госп.<span>'; ?></td>
									<td class="text-center" data-search="<?php echo $row->month; ?>" data-order="<?php echo $row->month; ?>">
										<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer' or $this->session->user->group === 'master') : ?>
											<input type="text" name="month" class="form-control form-control-sm text-center" value="<?php echo $row->month; ?>" maxlength="2" tabindex="5" onchange="editMonth(event);">
										<?php else : ?>
											<?php echo $row->month; ?>
										<?php endif; ?>
									</td>
									<td>
										<input type="text" class="form-control form-control-sm text-center" disabled>
									</td>
									<td class="text-center">
										<a href="javascript:void(0);">
											<i class="bi bi-database-dash <?php echo $row->is_repair ? 'text-success' : 'text-danger'; ?>" title="Враховується при генерації звітів. Буде видалено при оновлені графіку" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
										</a>
									</td>
									<td class="text-center">
										<a href="javascript:void(0);">
											<i class="bi bi-card-text text-warning" title="Згенерувати акт технічного стану" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
										</a>
									</td>
									<td class="text-center">
										<a class="dt-control mx-1" href="javascript:void(0);">
											<i class="bi bi-eye text-info" title="Інформація про ресурси" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
										</a>
									</td>
									<td class="text-center">
										<a href="javascript:void(0);" onclick="deleteSchedule(event);">
											<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
										</a>
									</td>
								</tr>
								<?php $i++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="modal" tabindex="-1" id="addTechnicModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-light bg-info">
				<h5 class="modal-title">Додавання техніки</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="technicFormModal">
					<input type="hidden" name="schedule_id" value="">
					<div class="mb-3">
						<label for="idTechnicModal" class="form-label"><strong>Техніка</strong></label>
						<select class="form-select form-select-sm" name="technic_id" id="idTechnicModal">
							<option value="">Оберіть техніку</option>
							<?php foreach ($technics as $item) : ?>
								<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-3">
						<label for="quantityModal" class="form-label"><strong>Кількість, маш.год</strong></label>
						<input type="text" class="form-control form-control-sm text-center" name="quantity" placeholder="1.20" id="quantityModal">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addTechnic(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" id="addWorkerModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header text-dark bg-warning">
				<h5 class="modal-title">Додавання працівника</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="workerFormModal">
					<input type="hidden" name="schedule_id" value="">
					<div class="mb-3">
						<label for="idWorkerModal" class="form-label"><strong>Працівник</strong></label>
						<select class="form-select form-select-sm" name="worker_id" id="idWorkerModal">
							<option value="">Оберіть працівника</option>
							<?php foreach ($workers as $item) : ?>
								<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="mb-3">
						<label for="quantityModal" class="form-label"><strong>Кількість, люд.год</strong></label>
						<input type="text" class="form-control form-control-sm text-center" name="quantity" placeholder="1.20" id="quantityModal">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addWorker(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<div class="modal" tabindex="-1" id="addWorkersModal">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-dark bg-success">
				<h5 class="modal-title">Додавання працівників</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form id="workersFormModal">
					<input type="hidden" name="schedule_id" value="">
					<table class="table table-striped table-hover table-bordered text-center">
						<thead>
							<tr>
								<!-- <th><i class="bi bi-check-lg"></i></th> -->
								<th>Працівник</th>
								<th>Кількість, люд.год</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($workers as $item) : ?>
								<tr>
									<!-- <td><input type="checkbox" class="form-check-input" tabindex="1" style="cursor: pointer;"></td> -->
									<td>
										<input type="hidden" name="worker_id[]" value="<?php echo $item->id; ?>">
										<input type="text" class="form-control text-center" value="<?php echo $item->name; ?>" disabled>
									</td>
									<th>
										<input type="text" name="quantity[]" class="form-control text-center" placeholder="1.20" tabindex="3">
									</th>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addWorkers(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>