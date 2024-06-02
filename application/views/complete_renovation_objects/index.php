<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<?php if ($count_subdivisions) : ?>
			<!-- <form action="/complete_renovation_objects/index" method="GET"> -->
			<div class="row justify-content-start mb-2">
				<div class="col-lg-3 mb-1">
					<select name="subdivision_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
						<option value="/complete_renovation_objects/index">Оберіть підрозділ</option>
						<?php foreach ($subdivisions as $item) : ?>
							<option value="/complete_renovation_objects/index/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : 'NULL'; ?>><?php echo $item->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="col-lg-3 mb-1">
					<select name="stantion_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
						<option value="/complete_renovation_objects/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>" class="form-select">Оберіть підстанцію</option>
						<?php foreach ($complete_renovation_objects as $item) : ?>
							<option value="/complete_renovation_objects/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('stantion_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<!-- </form> -->
		<?php endif; ?>

		<div class="row my-2">
			<div class="col-lg-6">
				<?php if ($this->session->user->group === 'admin') : ?>
					<a class="btn btn-primary my-1 disabled" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#addOperatingListObjectsModal">Додати енергетичний об`єкт</a>
				<?php endif; ?>
				<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer' || $this->session->user->group === 'master') : ?>
					<?php if ($this->input->get('subdivision_id')) : ?>
						<a class="btn btn-warning my-1" href="/schedules/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>">Річний план-графік на <strong><?php echo (date('Y') + 1) ?></strong> рік</a>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>

		<div class="table-responsive">
			<table class="table" id="collapseParent">
				<thead>
					<tr class="text-center">
						<th style="width:1%;"><i class="bi bi-pencil"></i></th>
						<th style="width:5%;">ID</th>
						<!-- <th style="width:30%;">Підрозділ</th> -->
						<th style="width:25%;">
							<a href="/complete_renovation_objects/index/?sort=name<?php echo $this->input->get('order') === 'asc' ? '&order=desc' : '&order=asc' ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : NULL; ?>">Об`єкт</a>
						</th>
						<th style="width:10%;">R3 номер</th>
						<th style="width:15%;">В ремонті <?php echo date('Y') + 1; ?> р.</th>
						<th style="width:10%;">Місяць</th>
						<th style="width:10%;">Кількість записів</th>
						<th style="width:20%;">
							<a href="/complete_renovation_objects/index/?sort=create_last_date<?php echo $this->input->get('order') === 'asc' ? '&order=desc' : '&order=asc' ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : NULL; ?>">Остання дата</a>
						</th>
						<th style="width:1%;"><i class="bi bi-journal-plus"></i></th>
						<th style="width:1%;"><i class="bi bi-file-pdf"></i></th>
						<th style="width:1%;"><i class="bi bi-file-excel"></i></th>
						<th style="width:1%;"><i class="bi bi-eye"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php $i = $per_page; ?>
					<?php foreach ($stantions as $item) : ?>
						<tr class="align-middle parent" data-subdivision_id="<?php echo $item->subdivision_id; ?>" data-complete_renovation_object_id="<?php echo $item->id; ?>">
							<td class="text-center">
								<a href="/complete_renovation_objects/edit/<?php echo $item->id; ?>" class="mx-1">
									<i class="bi bi-pencil text-success"></i>
								</a>
							</td>
							<td class="text-center"><?php echo $item->id; ?></td>
							<!-- <td><?php echo $item->subdivision; ?></td> -->
							<td class="stantion"><?php echo $item->name; ?></td>
							<td class="text-center r3_id">
								<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer') : ?>
									<input type="text" class="form-control text-center" value="<?php echo $item->r3_id; ?>" placeholder="0123456" maxlength="7" tabindex="5" onchange="editR3Number(event)" />
								<?php else : ?>
									<?php echo $item->r3_id; ?>
								<?php endif; ?>
							</td>
							<td class="text-center is_repair"><input type="checkbox" class="form-check-input" <?php echo $item->is_repair ? 'checked' : 'NULL'; ?> onclick="changeIsRepair(event);" /></td>
							<td class="text-center month">
								<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer') : ?>
									<input type="text" class="form-control text-center" value="<?php echo $item->month; ?>" placeholder="1,2,3,4,5,6,7,8,9,10,11,12" maxlength="26" tabindex="6" onchange="editMonth(event)" />
								<?php else : ?>
									<?php echo $item->month; ?>
								<?php endif; ?>
							</td>
							<td class="text-center"><?php echo $item->count_rows ? $item->count_rows : '-'; ?></td>
							<td class="text-center"><?php echo $item->create_last_date ? date('d.m.Y H:i:s', strtotime($item->create_last_date)) : '-'; ?></td>
							<td class="text-center">
								<a href="javascript:void(0);" class="mx-1" onclick="openAddOperatingListObjectModal(event)">
									<i class="bi bi-journal-plus text-success" title="Додати експлуатаційні дані по об`єкту" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a href="/complete_renovation_objects/gen_operating_list_object_pdf/<?php echo $item->id; ?>" class="mx-1" target="_blank">
									<i class="bi bi-file-pdf-fill text-danger" title="Згенерувати експлуатаційні дані по об`єкту PDF" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a href="/complete_renovation_objects/gen_operating_list_object_excel/<?php echo $item->id; ?>" class="mx-1" target="_blank">
									<i class="bi bi-file-excel-fill text-success" title="Згенерувати експлуатаційні дані по об`єкту EXCEL" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a class="mx-1" data-bs-toggle="collapse" href="#collapse_<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapse_<?php echo $i; ?>" onCLick="actionCollapse(event)">
									<i class="bi bi-eye text-info" title="Більше інформації" data-bs-toggle="tooltip"></i>
								</a>
							</td>
						</tr>
						<tr class="collapse collapse-horizontal <?php echo count($stantions) == 1 ? 'show' : NULL; ?>" id="collapse_<?php echo $i; ?>" data-bs-parent="#collapseParent">
							<td colspan="12">
								<?php if (count($item->operating_data)) : ?>
									<table class="table table-bordered table-info" id="Acts">
										<thead>
											<tr class="text-center">
												<th style="width:5%;">№ п/п</th>
												<th style="width:10%;">Дата</th>
												<th style="width:10%;">№ документу</th>
												<th style="width:15%;">Вид обслуговування</th>
												<th style="width:47%;">Перелік робіт</th>
												<th style="width:10%;">Виконавець</th>
												<th style="width:1%;"><i class="bi bi-pencil"></i></th>
												<th style="width:1%;"><i class="bi bi-image-fill"></i></th>
												<th style="width:1%;"><i class="bi bi-box-arrow-in-down"></i></th>
											</tr>
										</thead>
										<tbody>
											<?php $y = 1; ?>
											<?php foreach ($item->operating_data as $data) : ?>
												<tr class="form align-middle" data-id="<?php echo $data->id; ?>">
													<td class="text-center" onclick="editOperatingListObject(event);" title="Натисніть для активації форми" data-bs-toggle="tooltip" style="cursor:pointer;"><?php echo $y; ?></td>
													<td class="text-center">
														<input type="text" class="form-control service_date datemask datepicker" name="service_date" value="<?php echo date('d.m.Y', strtotime($data->service_date)); ?>" maxlength="10" disabled />
													</td>
													<td class="text-center">
														<input type="text" class="form-control document_number" name="document_number" value="<?php echo htmlspecialchars($data->document_number); ?>" maxlength="20" disabled />
													</td>
													<td class="text-center">
														<select name="type_service_id" class="form-select type_service_short_name" disabled>
															<option value="">Оберіть вид обслуговування</option>
															<?php foreach ($type_services as $type_service) : ?>
																<option value="<?php echo $type_service->id; ?>" <?php echo $type_service->id == $data->type_service_id ? 'selected' : NULL; ?>><?php echo $type_service->short_name; ?></option>
															<?php endforeach; ?>
														</select>
													</td>
													<td>
														<input type="text" class="form-control service_data" name="service_data" value="<?php echo htmlspecialchars($data->service_data); ?>" maxlength="255" disabled />
													</td>
													<td>
														<input type="text" class="form-control executor" name="executor" value="<?php echo htmlspecialchars($data->executor); ?>" maxlength="50" disabled />
													</td>
													<td class="text-center">
														<a href="javascript:void(0);" class="edit mx-1" onclick="editOperatingListObjectHandler(event);">
															<i class="bi bi-pencil text-success" title="Змінити дані" data-bs-toggle="tooltip"></i>
														</a>
													</td>
													<td class="text-center">
														<?php if (is_file('./uploads/acts/' . $data->document_scan) && $data->document_scan) : ?>
															<a href="<?php echo '/uploads/acts/' . $data->document_scan; ?>" class="view-act-scan mx-1" target="_blank">
																<i class="bi bi-image-fill text-danger" title="Подивитись скан акту" data-bs-toggle="tooltip"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="upload-file mx-1">
																<i class="bi bi-image-fill text-secondary"></i>
															</a>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<a href="javascript:void(0);" class="upload-file mx-1" onclick="reUploadFile(event)">
															<i class="bi bi-box-arrow-in-down text-warning" title="<?php echo $data->document_scan ? 'Замінити скан документу' : 'Завантажити скан документу'; ?>" data-bs-toggle="tooltip"></i>
														</a>

														<input type="text" name="is_edit" class="d-none" />
														<input type="file" name="document_scan" class="d-none" />
													</td>
												</tr>
												<?php $y++; ?>
											<?php endforeach; ?>
										</tbody>
									</table>
								<?php else : ?>
									<table class="table table-danger">
										<tr class="text-center">
											<td>Дані відсутні</td>
										</tr>
									</table>
								<?php endif; ?>
							</td>
						</tr>
						<?php $i++; ?>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">Показано з <?php echo $per_page; ?> по <?php echo $i - 1; ?> запис з <?php echo isset($total_filter_rows) ? $total_filter_rows : $total_rows; ?> записів</th>
						<!-- <th colspan="1" class="text-end">
							<select class="form-select" name="">
								<option value="">Кількість строк</option>
								<option value="5">5</option>
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
								<option value="100">100</option>
								<option value="250">250</option>
							</select>
						</th> -->
					</tr>
				</tfoot>
			</table>
		</div>
		<?php echo $this->pagination->create_links(); ?>
	</div>
</div>

<!-- Modal Form Add Operating List -->
<div class="modal fade" id="addOperatingListObjectModal" tabindex="-1" aria-labelledby="addOperatingListObjectModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addOperatingListObjectModalLabel">Додавання експлуатаційних даних</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('complete_renovation_objects/form_operating_list_object');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary action" onclick="addOperatingListObject(event);">Зберегти</button>
			</div>
		</div>
	</div>
</div>