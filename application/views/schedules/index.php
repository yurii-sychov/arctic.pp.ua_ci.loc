<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-3 mb-1">
				<select name="subdivision_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/schedules/index">Оберіть підрозділ</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="/schedules/index/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-3 mb-1">
				<select name="stantion_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/schedules/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>" class="form-select">Оберіть підстанцію</option>
					<?php foreach ($complete_renovation_objects as $item) : ?>
						<option value="/schedules/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('stantion_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
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
						<a class="btn btn-outline-dark" href="javascript:void(0);"><i class="bi bi-file-earmark-excel"></i> План ремонтів на <strong><?php echo (date('Y') + 1); ?></strong> рік</a>
					</div>
				<?php endif; ?>

			</div>
		<?php endif; ?>

		<?php if (count($equipments)) : ?>
			<div class="row my-2">
				<div class="col-lg-12">
					<h2 class="text-center text-success"><?php echo $complete_renovation_object->name; ?> <span class="text-danger">(оновлення: <?php echo ''; ?>)</span></h2>
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<div class="table-responsive">
						<table class="table table-bordere table-dark d-none">
							<caption>
								<ul>
									<li class="text-danger">Рожевим кольором виділено обладнання, що планується ремонтувати у <?php echo date('Y'); ?> році. При зміні року останнього обслуговування не повинно потрапляти в графік.</li>
									<li class="text-info">Блакитним кольором виділено обладнання, що планується ремонтувати у <?php echo (date('Y') + 1); ?> році поза планом.</li>
								</ul>
							</caption>
							<thead>
								<tr class="align-middle text-center">
									<th style="width:5%;">№ п/п</th>
									<th style="width:10%;"><a href="/<?php echo uri_string(); ?>/?subdivision_id=<?php echo $this->input->get('subdivision_id') ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : ''; ?>&field=dno&sort=<?php echo $this->input->get('sort') === 'asc' ? 'desc' : 'asc'; ?>">Дисп. назва</a></th>
									<th style="width:10%;" colspan="3">Ресурси</th>
									<th style="width:24%;"><a href="/<?php echo uri_string(); ?>/?subdivision_id=<?php echo $this->input->get('subdivision_id') ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : ''; ?>&field=equipment&sort=<?php echo $this->input->get('sort') === 'asc' ? 'desc' : 'asc'; ?>">Вид обладнання</a></th>
									<th style="width:15%;">Тип обладнання</th>
									<th style="width:15%;"><a href="/<?php echo uri_string(); ?>/?subdivision_id=<?php echo $this->input->get('subdivision_id') ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : ''; ?>&field=type_service&sort=<?php echo $this->input->get('sort') === 'asc' ? 'desc' : 'asc'; ?>">Спосіб обладнання</a></th>
									<th style="width:12%;"><a href="/<?php echo uri_string(); ?>/?subdivision_id=<?php echo $this->input->get('subdivision_id') ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : ''; ?>&field=is_contract_method&sort=<?php echo $this->input->get('sort') === 'asc' ? 'desc' : 'asc'; ?>">Тип обслуговування</a></th>
									<th style="width:5%;"><a href="/<?php echo uri_string(); ?>/?subdivision_id=<?php echo $this->input->get('subdivision_id') ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?><?php echo $this->input->get('page') ? '&page=' . $this->input->get('page') : ''; ?>&field=month&sort=<?php echo $this->input->get('sort') === 'asc' ? 'desc' : 'asc'; ?>">Місяць</a></th>
									<th style="width:1%;"><i class="bi bi-database-dash"></i></th>
									<th style="width:1%;"><i class="bi bi-card-text"></i></th>
									<th style="width:1%;"><i class="bi bi-basket"></i></th>
									<th style="width:1%;"><i class="bi bi-trash"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1; ?>
								<?php $p = $per_page; ?>
								<?php foreach ($equipments as $row) : ?>
									<tr class="align-middle <?php if ($row->will_delete) : ?>table-danger<?php endif; ?> <?php if ($row->will_add) : ?>table-info<?php endif; ?>" data-id="<?php echo $row->id; ?>">
										<td class="text-center"><?php echo $i; ?></td>
										<td class="text-center">
											<?php echo $row->dno; ?>
										</td>
										<td class="text-center materials-info" style="width:2%;">
											<span style="cursor:pointer;" class="position-relative badge rounded-pill <?php echo count($row->materials) > 0 ? 'bg-primary' : 'bg-danger'; ?>" data-bs-toggle="tooltip" title="Кількість матеріалів"><?php echo count($row->materials) ? '<i class="bi bi-boxes"></i> ' . (count($row->materials) - count($row->materials_is_extra)) : '<i class="bi bi-boxes"></i> 0'; ?>
												<span style="cursor:pointer;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?php echo count($row->materials_is_extra) > 0 ? 'bg-danger' : 'bg-secondary'; ?>">
													<?php echo count($row->materials_is_extra) > 0 ? count($row->materials_is_extra) . ' +' : 0; ?>
												</span>
											</span>
										</td>
										<td class="text-center workers-info" style="width:2%;">
											<span style="cursor:pointer;" class="position-relative badge rounded-pill <?php echo count($row->workers) > 0 ? 'bg-info text-dark' : 'bg-danger'; ?>" data-bs-toggle="tooltip" title="Кількість працівників"><?php echo count($row->workers) ? '<i class="bi bi-people"></i> ' . (count($row->workers) - count($row->workers_is_extra)) : '<i class="bi bi-people"></i> 0'; ?>
												<span style="cursor:pointer;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill <?php echo count($row->workers_is_extra) > 0 ? 'bg-danger' : 'bg-secondary'; ?>">
													<?php echo count($row->workers_is_extra) > 0 ? count($row->workers_is_extra) . ' +' : 0; ?>
												</span>
											</span>
										</td>
										<td class="text-center technics-info" style="width:2%;">
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
										<td class="text-center">
											<?php if ($this->session->user->group === 'admin' or $this->session->user->group === 'engineer' or $this->session->user->group === 'master') : ?>
												<input type="text" name="month" class="form-control form-control-sm text-center" value="<?php echo $row->month; ?>" maxlength="2" tabindex="5" onchange="editMonth(event);">
											<?php else : ?>
												<?php echo $row->month; ?>
											<?php endif; ?>
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
											<a class="mx-1" data-bs-toggle="collapse" href="#collapse_<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapse_<?php echo $i; ?>" onCLick="typeof(actionCollapse) === 'function' ? actionCollapse(event) : '';">
												<i class="bi bi-basket text-info" title="Інформація про ресурси" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
											</a>
										</td>
										<td class="text-center">
											<a href="javascript:void(0);" onclick="deleteSchedule(event);">
												<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
											</a>
										</td>
									</tr>
									<tr class="collapse collapse-horizontal" id="collapse_<?php echo $i; ?>">
										<td colspan="14">
											<div class="row">
												<div class="col-md-12">
													<div class="row my-1 row-button">
														<div class="col-md-12 text-end">
															<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																<button class="button-delete btn btn-danger btn-sm d-none" title="Видалити рядок" onClick="removeRowMaterial(event);"><i class="bi bi-dash-lg"></i></button>
																<button class="button-add btn btn-primary btn-sm" title="Додати рядок" onClick="addRowMaterial(event);" data-schedule_id="<?php echo $row->id; ?>"><i class="bi bi-plus-lg"></i></button>
																<button class="button-active-form btn btn-success btn-sm" title="Внести зміни" onClick="activeFormMaterials(event);"><i class="bi bi-pencil"></i></button>
																<button class="botton-save-materials btn btn-info btn-sm disabled" title="Зберегти зміни" onClick="saveMaterials(event);">Зберегти</button>
															<?php endif; ?>
														</div>
													</div>
													<div class="row my-1 row-table">
														<div class="col-md-12">
															<form id="Form_<?php echo $row->id; ?>">
																<h2 class="text-center text-danger" data-bs-toggle="tooltip" title="Далі буде..."><?php echo $row->equipment . ' ' . $row->dno; ?> (на ремонт заплановано <s class="text-success">1000 ₴</s>)</h2>
																<table class="table table-striped table-hover table-bordered table-sm bg-light ">
																	<?php if (count($row->materials) > 0) : ?>
																		<caption><strong>* Червоним кольором виділені додаткові ресурси, або ті в яких змінена кількість</strong></caption>
																	<?php endif; ?>
																	<thead class="table-dark">
																		<tr class="align-middle text-center">
																			<th style="width:5%;">№ п/п</th>
																			<th style="width:42%;">Ресурс</th>
																			<th style="width:42%;">Одиниця виміру</th>
																			<th style="width:10%;">Кількість</th>
																			<th style="width:1%;"><i class="bi bi-trash text-secondary"></i></th>
																		</tr>
																	</thead>
																	<tbody>
																		<?php $n = 1; ?>
																		<?php foreach ($row->materials as $material) : ?>
																			<tr style="background:#adb5bd;" class="align-middle" data-schedule_id="<?php echo $material->schedule_id; ?>" data-material_id="<?php echo $material->material_id; ?>" data-year_service="<?php echo $material->year_service; ?>">
																				<td class="text-center number"><?php echo $n; ?></td>
																				<td class="text-start material <?php echo $material->is_extra ? 'text-danger' : NULL; ?>"><?php echo $material->name; ?><?php echo $material->is_extra ? ' *' : NULL; ?></td>
																				<td class="text-start unit"><?php echo $material->unit; ?></td>
																				<td class="text-center quantity">
																					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																						<input type="text" name="quantity" class="form-control form-control-sm text-center" value="<?php echo $material->quantity; ?>" tabindex="4" disabled onchange="editQuantity(event);" onkeyup="changeQuantity(event);">
																					<?php else : ?>
																						<?php echo $material->quantity; ?>
																					<?php endif; ?>
																				</td>
																				<td class="text-center delete">
																					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																						<a href="javascript:void(0);" onClick="deleteMaterial(event);">
																							<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
																						</a>
																					<?php else : ?>
																						<a href="javascript:void(0);">
																							<i class="bi bi-trash text-secondary" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
																						</a>
																					<?php endif; ?>
																				</td>
																			</tr>
																			<?php $n++; ?>
																		<?php endforeach; ?>

																		<?php $n = 1; ?>
																		<?php foreach ($row->workers as $worker) : ?>
																			<tr style="background:#20c997;" class="align-middle" data-schedule_id=" <?php echo $worker->schedule_id; ?>" data-worker_id="<?php echo $worker->worker_id; ?>" data-year_service="<?php echo $worker->year_service; ?>">
																				<td class="text-center number"><?php echo $n; ?></td>
																				<td class="text-start worker <?php echo $worker->is_extra ? 'text-danger' : NULL; ?>"><?php echo $worker->name; ?><?php echo $worker->is_extra ? ' *' : NULL; ?></td>
																				<td class="text-start unit"><?php echo $worker->unit; ?></td>
																				<td class="text-center quantity">
																					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																						<!-- <input type="text" name="quantity" class="form-control form-control-sm text-center" value="<?php echo $worker->quantity; ?>" tabindex="4" disabled onchange="editQuantity(event);" onkeyup="changeQuantity(event);"> -->
																						<?php echo $worker->quantity; ?>
																					<?php else : ?>
																						<?php echo $worker->quantity; ?>
																					<?php endif; ?>
																				</td>
																				<td class="text-center delete">
																					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																						<a href="javascript:void(0);" onClick="deleteWorker(event);">
																							<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
																						</a>
																					<?php else : ?>
																						<a href="javascript:void(0);">
																							<i class="bi bi-trash text-secondary" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
																						</a>
																					<?php endif; ?>
																				</td>
																			</tr>
																			<?php $n++; ?>
																		<?php endforeach; ?>

																		<?php $n = 1; ?>
																		<?php foreach ($row->technics as $technic) : ?>
																			<tr style="background:#fd7e14;" class="align-middle" data-schedule_id=" <?php echo $technic->schedule_id; ?>" data-technic_id="<?php echo $technic->technic_id; ?>" data-year_service="<?php echo $technic->year_service; ?>">
																				<td class="text-center number"><?php echo $n; ?></td>
																				<td class="text-start technic <?php echo $technic->is_extra ? 'text-danger' : NULL; ?>"><?php echo $technic->name; ?><?php echo $technic->is_extra ? ' *' : NULL; ?></td>
																				<td class="text-start unit"><?php echo $technic->unit; ?></td>
																				<td class="text-center quantity">
																					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																						<!-- <input type="text" name="quantity" class="form-control form-control-sm text-center" value="<?php echo $technic->quantity; ?>" tabindex="4" disabled onchange="editQuantity(event);" onkeyup="changeQuantity(event);"> -->
																						<?php echo $technic->quantity; ?>
																					<?php else : ?>
																						<?php echo $technic->quantity; ?>
																					<?php endif; ?>
																				</td>
																				<td class="text-center delete">
																					<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																						<a href="javascript:void(0);" onClick="deleteTechnic(event);">
																							<i class="bi bi-trash text-danger" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
																						</a>
																					<?php else : ?>
																						<a href="javascript:void(0);">
																							<i class="bi bi-trash text-secondary" title="Видалити" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-trigger="hover manual"></i>
																						</a>
																					<?php endif; ?>
																				</td>
																			</tr>
																			<?php $n++; ?>
																		<?php endforeach; ?>
																	</tbody>
																</table>
															</form>
															<hr>
														</div>
													</div>
												</div>
											</div>
										</td>
									</tr>
									<?php $i++; ?>
									<?php $p++; ?>
								<?php endforeach; ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="9">Показано з <?php echo $per_page; ?> по <?php echo $p - 1; ?> запис з <?php echo isset($total_filter_rows) ? $total_filter_rows : $total_rows; ?> записів</th>
									<th colspan="5" class="text-end">
										<select class="form-select" name="rows" onchange="set_rows(event);">
											<option value="">Кількість рядків</option>
											<option value="5" <?php echo $this->session->userdata('rows') == 5 ? 'selected' : NULL; ?>>5 рядків</option>
											<option value="10" <?php echo $this->session->userdata('rows') == 10 ? 'selected' : NULL; ?>>10 рядків</option>
											<option value="20" <?php echo $this->session->userdata('rows') == 20 ? 'selected' : NULL; ?>>20 рядків</option>
											<option value="50" <?php echo $this->session->userdata('rows') == 50 ? 'selected' : NULL; ?>>50 рядків</option>
											<option value="100" <?php echo $this->session->userdata('rows') == 100 ? 'selected' : NULL; ?>>100 рядків</option>
										</select>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
					<?php echo $this->pagination->create_links(); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>