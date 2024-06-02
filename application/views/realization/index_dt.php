<div class="card mb-3">
	<div class="card-header text-white bg-secondary mb-2">
		<h5>Форма додавання позапланових ремонтів</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-4">
				<label for="search" class="form-label"><strong>Пошук обладнання для додавання</strong></label>
				<input type="text" class="form-control" id="search" placeholder="Почніть вводити диспечерьске найменування обладнання">
				<button type="submit" class="btn btn-secondary mt-3">Додати</button>
			</div>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-3 mb-1">
				<select name="subdivision_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/realization/index">Оберіть підрозділ</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="/realization/index/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-3 mb-1">
				<select name="stantion_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/realization/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>" class="form-select">Оберіть підстанцію</option>
					<?php foreach ($complete_renovation_objects as $item) : ?>
						<option value="/realization/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('stantion_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<?php if (count($equipments)) : ?>
			<div class="row my-2">
				<div class="col-lg-12">
					<h2 class="text-center text-success"><?php echo $complete_renovation_object->name; ?></h2>
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<table class="datatable table table-bordered table-striped table-hover d-none" data-order='[[ 1, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0" data-row-id="id">
						<caption>
							<ul>
								<li class="text-danger">Рожевим кольором виділено обладнання, що ремонтувалося позапланово.</li>
							</ul>
						</caption>
						<thead class="table-dark">
							<tr class="align-middle text-center">
								<th class="text-center" style="width:5%;" data-orderable="false">№ п/п</th>
								<th class="text-center" style="width:10%;" data-data="dno">Дисп. назва</th>
								<th class="text-center" style="width:22%;" data-data="equipment">Вид обладнання</th>
								<th class="text-center" style="width:22%;">Тип обладнання</th>
								<th class="text-center" style="width:10%;">Спосіб обслуговування</th>
								<th class="text-center" style="width:10%;">Тип обслуговування</th>
								<th class="text-center" style="width:10%;">План, м.</th>
								<th class="text-center" style="width:10%;">Факт, д.м.р</th>
								<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-pencil"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 1; ?>
							<?php foreach ($equipments as $row) : ?>
								<tr data-schedule_id="<?php echo $row->schedule_id; ?>" data-year_service="<?php echo $row->year_service; ?>" data-is_contract_method="<?php echo $row->is_contract_method; ?>" class="align-middle <?php echo $row->is_unscheduled ? 'bg-danger' : NULL; ?>">
									<td class="text-center"><?php echo $i; ?></td>
									<td class="text-center"><?php echo $row->disp; ?></td>
									<td><?php echo $row->equipment; ?></td>
									<td>-</td>
									<td class="text-center"><?php echo $row->is_contract_method ? 'підр.' : 'госп.'; ?></td>
									<td class="text-center"><?php echo $row->type_service; ?></td>
									<td class="text-center"><?php echo $row->month_service; ?></td>
									<td class="text-center date-service-actual" data-search="<?php echo $row->date_service_actual; ?>" data-order="<?php echo $row->date_service_actual; ?>">
										<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer' or $this->session->user->group === 'master') : ?>
											<input type="date" class="form-control form-control-sm text-center" onchange="editDateServiceActual(event);" value="<?php echo $row->date_service_actual == '0000-00-00' ? '' : $row->date_service_actual; ?>" name="date_service_actual[]" disabled>
										<?php else : ?>
											<?php echo $row->date_service_actual; ?>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<a href="javascript:void(0);" onclick="activeFormRow(event);"><i class="bi bi-pencil text-success"></i>
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