<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-1">
				<select class="form-select my-1 bg-info" id="FilterYear" onchange="document.location=this.options[this.selectedIndex].value">
					<?php for ($i = 2024; $i < 2041; $i++) : ?>
						<?php
						if (!$this->uri->segment(3) && $i == (date('Y') + 1)) {
							$selected = 'selected';
						} elseif ($i == $this->uri->segment(3)) {
							$selected = 'selected';
						} else {
							$selected = NULL;
						}
						?>
						<option value="/resources/index/<?php echo $i;  ?>" <?php echo $selected; ?>><?php echo $i; ?> рік</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<div class="row my-2">
			<div class="col-lg-12">
				<nav>
					<div class="nav nav-tabs" id="nav-tab" role="tablist">
						<button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab" aria-controls="materials" aria-selected="true">Матеріали</button>
						<button class="nav-link" id="workers-tab" data-bs-toggle="tab" data-bs-target="#workers" type="button" role="tab" aria-controls="workers" aria-selected="false">Працівники</button>
						<button class="nav-link" id="technics-tab" data-bs-toggle="tab" data-bs-target="#technics" type="button" role="tab" aria-controls="technics" aria-selected="false">Техніка</button>
					</div>
				</nav>
				<div class="row my-2">
					<div class="col-md-12 text-end d-grid gap-2 d-lg-block">
						<button class="btn btn-success my-1" id="activeDeactiveForm" onClick="activeForm(event)">Активувати форму</button>
						<a href="/resources/create/" class="btn btn-primary my-1 disabled" id="addResource">Додати ресурс</a>
					</div>
				</div>
				<div class="tab-content" id="tabContent">

					<div class="tab-pane fade" id="materials" role="tabpanel" aria-labelledby="materials-tab">
						<div class="row my-2">
							<div class="col-lg-12">
								<div class="text-center loading">
									<div class="spinner-border text-primary" role="status"></div>
								</div>
								<table class="datatable table table-hover table-bordered align-middle d-none" id="tableMaterials" data-order='[[ 1, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
									<thead class="table-primary">
										<tr class="text-center">
											<th class="text-center" style="width:5%;" data-orderable="false">#</th>
											<th class="text-center" style="width:48%;">Матеріал</th>
											<th class="text-center" style="width:15%;">Одиниця виміру</th>
											<th class="text-center" style="width:15%;">Номер R3</th>
											<th class="text-center" style="width:15%;">Ціна для <?php echo ($this->uri->segment(3)) ? $this->uri->segment(3) : (date('Y') + 1); ?> року, грн</th>
											<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-image"></i></th>
											<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-trash"></i></th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($materials as $item) : ?>
											<tr class="text-center <?php echo isset($item->price) and $item->price == 0 ? 'bg-danger text-white' : NULL; ?>" data-id="<?php echo $item->id; ?>">
												<td class="id text-center"><?php echo $item->id; ?></td>
												<td class="name input text-start" data-search="<?php echo $item->name; ?>" data-order="<?php echo $item->name; ?>" data-field_name="Матеріал" data-field="name" data-table="materials" data-id="<?php echo $item->id; ?>">
													<input type="text" class="form-control" value="<?php echo $item->name; ?>" maxlength="255" onChange="editValue(event);" />
												</td>
												<td class="unit input text-center" data-search="<?php echo $item->unit; ?>" data-order="<?php echo $item->unit; ?>" data-field_name="Одиниця виміру" data-field="unit" data-table="materials" data-id="<?php echo $item->id; ?>">
													<input type="text" class="form-control text-center" value="<?php echo $item->unit; ?>" maxlength="10" onChange="editValue(event);" />
												</td>
												<td class="r3_id input text-center" data-search="<?php echo $item->r3_id; ?>" data-order="<?php echo $item->r3_id; ?>" data-field_name="Номер R3" data-field="r3_id" data-table="materials" data-id="<?php echo $item->id; ?>">
													<input type="text" class="form-control text-center" value="<?php echo $item->r3_id; ?>" maxlength="8" onChange="editValue(event);" />
												</td>
												<td class="price input text-center" data-search="<?php echo isset($item->price) && $item->price; ?>" data-order="<?php echo isset($item->price) && $item->price; ?>" data-field_name="Ціна" data-field="price" data-table="materials_prices" data-id="<?php echo isset($item->materials_prices_id) && $item->materials_prices_id; ?>">
													<input type="text" class="form-control text-center" value="<?php echo isset($item->price) && $item->price; ?>" maxlength="11" onChange="editValue(event);" onkeyup="changePrice(event);" />
												</td>
												<td class="photo text-center">
													<a href="javascript:void(0);" title="Подивитися фото<br />(<?php echo $item->name; ?>)<br />Функція в розробці" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="left" data-bs-custom-class="custom-tooltip"><i class="bi bi-image text-warning"></i></a>
												</td>
												<td class="delete text-center">
													<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
														<a href="/resources/delete_material/<?php echo $item->id; ?>" title="Видалити<br />(<?php echo $item->name; ?>)" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="left" onClick="deleteMaterial(event);"><i class="bi bi-trash text-danger"></i></a>
													<?php else : ?>
														<a href="javascript:void(0);"><i class="bi bi-trash text-secondary"></i></a>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="tab-pane fade" id="workers" role="tabpanel" aria-labelledby="workers-tab">
						<div class="row my-2">
							<div class="col-lg-12">
								<div class="text-center loading">
									<div class="spinner-border text-primary" role="status"></div>
								</div>
							</div>
							<table class="datatable table table-hover table-bordered table-light align-middle d-none" id="tableWorkers" data-order='[[ 1, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
								<thead>
									<tr class="text-center">
										<th class="text-center" style="width:5%;" data-orderable="false">#</th>
										<th class="text-center" style="width:48%;">Працівник</th>
										<th class="text-center" style="width:15%;">Одиниця виміру</th>
										<th class="text-center" style="width:15%;">Номер R3</th>
										<th class="text-center" style="width:15%;">Ціна для <?php echo ($this->uri->segment(3)) ? $this->uri->segment(3) : (date('Y') + 1); ?> року, грн</th>
										<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-image"></i></th>
										<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-trash"></i></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($workers as $item) : ?>
										<tr class="text-center" data-id="<?php echo $item->id; ?>">
											<td class="id text-center"><?php echo $item->id; ?></td>
											<td class="name input text-start" data-search="<?php echo $item->name; ?>" data-order="<?php echo $item->name; ?>" data-field_name="Працівник" data-field="name" data-table="workers" data-id="<?php echo $item->id; ?>">
												<input type="text" class="form-control" value="<?php echo $item->name; ?>" maxlength="255" onChange="editValue(event);" />
											</td>
											<td class="unit input text-center" data-search="<?php echo $item->unit; ?>" data-order="<?php echo $item->unit; ?>" data-field_name="Одиниця виміру" data-field="unit" data-table="workers" data-id="<?php echo $item->id; ?>">
												<input type="text" class="form-control text-center" value="<?php echo $item->unit; ?>" maxlength="10" onChange="editValue(event);" />
											</td>
											<td class="r3_id input text-center" data-search="<?php echo isset($item->r3_id) ? $item->r3_id : NULL; ?>" data-order="<?php echo isset($item->r3_id) ? $item->r3_id : NULL; ?>" data-field_name="Номер R3" data-field="r3_id" data-table="workers" data-id="<?php echo $item->id; ?>">
												<!-- <input type="text" class="form-control text-center" value="<?php echo isset($item->r3_id) ? $item->r3_id : 'NONE'; ?>" maxlength="8" onChange="editValue(event);" /> -->
												<?php echo isset($item->r3_id) ? $item->r3_id : 'NONE'; ?>
											</td>
											<td class="price input text-center" data-search="<?php echo $item->price; ?>" data-order="<?php echo $item->price; ?>" data-field_name="Ціна" data-field="price" data-table="workers_prices" data-id="<?php echo $item->workers_prices_id; ?>">
												<input type="text" class="form-control text-center" value="<?php echo $item->price; ?>" maxlength="11" onChange="editValue(event);" onkeyup="changePrice(event);" />
											</td>
											<td class="photo text-center">
												<a href="javascript:void(0);" title="Подивитися фото<br />(<?php echo $item->name; ?>)<br />Функція в розробці" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="left" data-bs-custom-class="custom-tooltip"><i class="bi bi-image text-warning"></i></a>
											</td>
											<td class="delete text-center">
												<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
													<a href="/resources/delete_worker/<?php echo $item->id; ?>" title="Видалити<br />(<?php echo $item->name; ?>)" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="left" onClick="deleteWorker(event);"><i class="bi bi-trash text-danger"></i></a>
												<?php else : ?>
													<a href="javascript:void(0);"><i class="bi bi-trash text-secondary"></i></a>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>

					<div class="tab-pane fade" id="technics" role="tabpanel" aria-labelledby="technics-tab">
						<div class="row my-2">
							<div class="col-lg-12">
								<div class="text-center loading">
									<div class="spinner-border text-primary" role="status"></div>
								</div>
							</div>
							<table class="datatable table table-hover table-bordered table-light align-middle d-none" id="tableTechnics" data-order='[[ 1, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
								<thead>
									<tr class="text-center">
										<th class="text-center" style="width:5%;" data-orderable="false">#</th>
										<th class="text-center" style="width:48%;">Техніка</th>
										<th class="text-center" style="width:15%;">Одиниця виміру</th>
										<th class="text-center" style="width:15%;">Номер R3</th>
										<th class="text-center" style="width:15%;">Ціна для <?php echo ($this->uri->segment(3)) ? $this->uri->segment(3) : (date('Y') + 1); ?> року, грн</th>
										<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-image"></i></th>
										<th class="text-center" style="width:1%;" data-orderable="false"><i class="bi bi-trash"></i></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($technics as $item) : ?>
										<tr class="text-center" data-id="<?php echo $item->id; ?>">
											<td class="id text-center"><?php echo $item->id; ?></td>
											<td class="name input text-start" data-search="<?php echo $item->name; ?>" data-order="<?php echo $item->name; ?>" data-field_name="Техніка" data-field="name" data-table="technics" data-id="<?php echo $item->id; ?>">
												<input type="text" class="form-control" value="<?php echo $item->name; ?>" maxlength="255" onChange="editValue(event);" />
											</td>
											<td class="unit input text-center" data-search="<?php echo $item->unit; ?>" data-order="<?php echo $item->unit; ?>" data-field_name="Одиниця виміру" data-field="unit" data-table="technics" data-id="<?php echo $item->id; ?>">
												<input type="text" class="form-control text-center" value="<?php echo $item->unit; ?>" maxlength="10" onChange="editValue(event);" />
											</td>
											<td class="r3_id input text-center" data-search="<?php echo isset($item->r3_id) ? $item->r3_id : NULL; ?>" data-order="<?php echo isset($item->r3_id) ? $item->r3_id : NULL; ?>" data-field_name="Номер R3" data-field="r3_id" data-table="technics" data-id="<?php echo $item->id; ?>">
												<!-- <input type="text" class="form-control text-center" value="<?php echo isset($item->r3_id) ? $item->r3_id : 'NONE'; ?>" maxlength="8" onChange="editValue(event);" /> -->
												<?php echo isset($item->r3_id) ? $item->r3_id : 'NONE'; ?>
											</td>
											<td class="price input text-center" data-search="<?php echo $item->price; ?>" data-order="<?php echo $item->price; ?>" data-field_name="Ціна" data-field="price" data-table="technics_prices" data-id="<?php echo $item->technics_prices_id; ?>">
												<input type="text" class="form-control text-center" value="<?php echo $item->price; ?>" maxlength="11" onChange="editValue(event);" onkeyup="changePrice(event);" />
											</td>
											<td class="photo text-center">
												<a href="javascript:void(0);" title="Подивитися фото<br />(<?php echo $item->name; ?>)<br />Функція в розробці" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="left" data-bs-custom-class="custom-tooltip"><i class="bi bi-image text-warning"></i></a>
											</td>
											<td class="delete text-center">
												<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
													<a href="/resources/delete_technic/<?php echo $item->id; ?>" title="Видалити<br />(<?php echo $item->name; ?>)" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-html="true" data-bs-placement="left" onClick="deleteTechnic(event);"><i class="bi bi-trash text-danger"></i></a>
												<?php else : ?>
													<a href="javascript:void(0);"><i class="bi bi-trash text-secondary"></i></a>
												<?php endif; ?>
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
	</div>
</div>