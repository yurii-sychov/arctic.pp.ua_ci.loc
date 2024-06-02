<div class="card text-dark bg-light">
	<div class="card-header">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-4">
				<select class="form-select my-1" id="FilterTypeService" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/estimates" selected>Оберіть вид обслуговування</option>
					<?php foreach ($type_services as $item) :  ?>
						<option value="/estimates/index/<?php echo $item->id;  ?>" <?php echo $item->id == $this->uri->segment(3) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach;
					?>
				</select>
			</div>
			<div class="col-lg-7">
				<select class="form-select my-1" id="FilterCipher" onchange="document.location=this.options[this.selectedIndex].value" <?php echo ($this->uri->segment(3)) ? NULL : 'disabled'; ?>>
					<option value="/estimates/index/<?php echo $this->uri->segment(3); ?>" selected>Оберіть кошторис</option>
					<?php foreach ($estimates as $item) : ?>
						<option value="/estimates/index/<?php echo $this->uri->segment(3); ?>/<?php echo $item->id;  ?>" <?php echo $item->id == $this->uri->segment(4) ? 'selected' : NULL; ?>><?php echo 'ID:' . $item->id . ' (Шифр ' . $item->cipher . ') ' . $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-1">
				<select class="form-select my-1" id="FilterYear" onchange="document.location=this.options[this.selectedIndex].value" <?php echo ($this->uri->segment(3) && $this->uri->segment(4)) ? NULL : 'disabled'; ?>>
					<?php for ($i = 2024; $i < 2036; $i++) : ?>
						<option value="/estimates/index/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $i;  ?>" <?php echo ($i == $this->uri->segment(5) || $i == (date('Y') + 1)) ? 'selected' : NULL; ?>><?php echo $i; ?> рік</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<?php if (isset($ciphers)) : ?>
			<div class="row my-2">
				<div class="col-lg-12">
					<nav>
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							<button class="nav-link" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="button" role="tab" aria-controls="materials" aria-selected="true">Матеріали</button>
							<button class="nav-link" id="workers-tab" data-bs-toggle="tab" data-bs-target="#workers" type="button" role="tab" aria-controls="workers" aria-selected="false">Працівники</button>
							<button class="nav-link" id="technics-tab" data-bs-toggle="tab" data-bs-target="#technics" type="button" role="tab" aria-controls="technics" aria-selected="false">Техніка</button>
						</div>
					</nav>
					<div class="tab-content" id="tabContent">

						<div class="tab-pane fade" id="materials" role="tabpanel" aria-labelledby="materials-tab">
							<?php foreach ($ciphers as $cipher) : ?>
								<div class="row my-2">
									<div class="col-lg-12">
										<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
											<div class="row my-2">
												<div class="col-lg-9 text-start">
													<h5 class="text-secondary"><small><?php echo '(Шифр робіт - ' . $cipher->cipher . ')' ?></small> <?php echo $cipher->name; ?></h5>
												</div>
												<div class="col-lg-3 text-end d-grid gap-2 d-md-block">
													<button class="btn btn-success" title="Активувати форму" data-bs-toggle="tooltip" onClick="activeForm(event)"><i class="bi bi-pencil"></i></button>
													<a href="/estimates/add_materials/<?php echo $cipher->id; ?>" title="Додати матеріали до кошторису" data-bs-toggle="tooltip" class="btn btn-primary" data-cipher_id="<?php echo $cipher->id; ?>">Додати матеріали до кошторису</a>
												</div>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="row my-2">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table class="table table-hover table-bordered table-light align-middle" id="tableMaterials">
												<thead>
													<tr class="text-center">
														<th style="width:5%;">№ п/п</th>
														<th style="width:30%;">Матеріал</th>
														<th style="width:15%;">R3</th>
														<th style="width:15%;">Одиниця виміру</th>
														<th style="width:14%;">Кількість</th>
														<th style="width:10%;">Ціна, грн</th>
														<th style="width:10%;">Сума, грн</th>
														<th style="width:1%;"><i class="bi bi-trash text-secondary"></i></th>
													</tr>
												</thead>
												<tbody>
													<?php $summa = 0; ?>
													<?php foreach ($cipher->materials as $key => $row) : ?>
														<tr class="text-center" data-cipher_id="<?php echo $cipher->id; ?>" data-material_id="<?php echo $row->material_id; ?>">
															<td class="number"><?php echo $key + 1; ?></td>
															<td class="material text-start"><?php echo $row->material; ?></td>
															<td class="number_r3"><?php echo $row->number_r3; ?></td>
															<td class="unit"><?php echo $row->unit; ?></td>
															<td class="quantity">
																<input type="text" class="form-control" value="<?php echo $row->quantity; ?>" maxlength="5" tabindex="4" disabled onchange="editQuantityMaterial(event);" onkeyup="changeQuantity(event);" />
															</td>
															<td class="price"><?php echo $row->price; ?></td>
															<td class="price_total"><?php echo $row->price_total; ?></td>
															<td class="delete">
																<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																	<a href="/estimates/delete_material/<?php echo $cipher->id; ?>/<?php echo $row->material_id; ?>" title="Видалити матеріал з кошторису" data-bs-toggle="tooltip" onclick="deleteMaterial(event);"><i class="bi bi-trash text-danger"></i></a>
																<?php else : ?>
																	<a href="javascript:void(0);"><i class="bi bi-trash text-secondary"></i></a>
																<?php endif; ?>
															</td>
														</tr>
														<?php $summa = $row->price_total + $summa; ?>
													<?php endforeach; ?>
												</tbody>
												<tfoot>
													<tr>
														<th class="text-primary" colspan="6"><strong>Всього по матеріалам</strong></th>
														<th class="summa text-center text-primary" colspan="2"><?php echo number_format($summa, 2, ',', ' ') . ' грн.'; ?></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="tab-pane fade" id="workers" role="tabpanel" aria-labelledby="workers-tab">
							<?php foreach ($ciphers as $cipher) : ?>
								<div class="row my-2">
									<div class="col-lg-12">
										<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
											<div class="row my-2">
												<div class="col-lg-9 text-start">
													<h5 class="text-secondary"><small><?php echo '(Шифр робіт - ' . $cipher->cipher . ')' ?></small> <?php echo $cipher->name; ?></h5>
												</div>
												<div class="col-lg-3 text-end d-grid gap-2 d-md-block">
													<button class="btn btn-success" title="Активувати форму" data-bs-toggle="tooltip" onClick="activeForm(event)"><i class="bi bi-pencil"></i></button>
													<a href="/estimates/add_workers/<?php echo $cipher->id; ?>" title="Додати працівників до кошторису" data-bs-toggle="tooltip" class="btn btn-primary" data-cipher_id="<?php echo $cipher->id; ?>">Додати працівників до кошторису</a>
												</div>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="row my-2">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table class="table table-hover table-bordered table-light align-middle" id="tableWorkers">
												<thead>
													<tr class="text-center">
														<th style="width:5%;">№ п/п</th>
														<th style="width:45%;">Працівник</th>
														<th style="width:15%;">Одиниця виміру</th>
														<th style="width:13%;">Кількість</th>
														<th style="width:10%;">Ціна, грн</th>
														<th style="width:10%;">Сума, грн</th>
														<th style="width:1%;"><i class="bi bi-clipboard-plus text-secondary"></i></th>
														<th style="width:1%;"><i class="bi bi-trash text-secondary"></i></th>
													</tr>
												</thead>
												<tbody>
													<?php $summa_person_hours = 0; ?>
													<?php $summa_money = 0; ?>
													<?php foreach ($cipher->workers as $key => $row) : ?>
														<tr class="text-center" data-cipher_id="<?php echo $cipher->id; ?>" data-worker_id="<?php echo $row->worker_id; ?>">
															<td class="number"><?php echo $key + 1; ?></td>
															<td class="worker text-start"><?php echo $row->worker; ?></td>
															<td class="unit"><?php echo $row->unit; ?></td>
															<td class="quantity">
																<input type="text" class="form-control" value="<?php echo $row->quantity; ?>" maxlength="5" tabindex="4" disabled onchange="editQuantityWorker(event);" onkeyup="changeQuantity(event);" />
															</td>
															<td class="price"><?php echo $row->price; ?></td>
															<td class="price_total"><?php echo $row->price_total; ?></td>
															<td class="copy">
																<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																	<a href="/estimates/copy_worker/<?php echo $cipher->id; ?>/<?php echo $row->worker_id; ?>" title="Копіювати працівника для кошторису" data-bs-toggle="tooltip" onclick="copyWorker(event);"><i class="bi bi-clipboard-plus text-success"></i></a>
																<?php else : ?>
																	<a href="javascript:void(0);"><i class="bi bi-clipboard-plus text-secondary"></i></a>
																<?php endif; ?>
															</td>
															<td class="delete">
																<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																	<a href="/estimates/delete_worker/<?php echo $cipher->id; ?>/<?php echo $row->worker_id; ?>" title="Видалити працівника з кошторису" data-bs-toggle="tooltip" onclick="deleteWorker(event);"><i class="bi bi-trash text-danger"></i></a>
																<?php else : ?>
																	<a href="javascript:void(0);"><i class="bi bi-trash text-secondary"></i></a>
																<?php endif; ?>
															</td>
														</tr>
														<?php $summa_person_hours = $row->quantity + $summa_person_hours; ?>
														<?php $summa_money = $row->price_total + $summa_money; ?>
													<?php endforeach; ?>
												</tbody>
												<tfoot>
													<tr>
														<th class="text-primary" colspan="3"><strong>Всього по працівникам</strong></th>
														<th class="summa_person_hours text-left text-primary"><?php echo number_format($summa_person_hours, 2, ',', ' ') . ' люд.год'; ?></th>
														<th>&nbsp;</th>
														<th class="summa_money text-center text-primary" colspan="2"><?php echo number_format($summa_money, 2, ',', ' ') . ' грн.'; ?></th>
														<!-- <th>&nbsp;dd</th> -->
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

						<div class="tab-pane fade" id="technics" role="tabpanel" aria-labelledby="technics-tab">
							<?php foreach ($ciphers as $cipher) : ?>
								<div class="row my-2">
									<div class="col-lg-12">
										<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
											<div class="row my-2">
												<div class="col-lg-9 text-start">
													<h5 class="text-secondary"><small><?php echo '(Шифр робіт - ' . $cipher->cipher . ')' ?></small> <?php echo $cipher->name; ?></h5>
												</div>
												<div class="col-lg-3 text-end d-grid gap-2 d-md-block">
													<button class="btn btn-success" title="Активувати форму" data-bs-toggle="tooltip" onClick="activeForm(event)"><i class="bi bi-pencil"></i></button>
													<a href="/estimates/add_technics/<?php echo $cipher->id; ?>" title="Додати транспорт до кошторису" data-bs-toggle="tooltip" class="btn btn-primary" data-cipher_id="<?php echo $cipher->id; ?>">Додати транспорт до кошторису</a>
												</div>
											</div>
										<?php endif; ?>
									</div>
								</div>
								<div class="row my-2">
									<div class="col-lg-12">
										<div class="table-responsive">
											<table class="table table-hover table-bordered table-light align-middle" id="tableTechnics">
												<thead>
													<tr class="text-center">
														<th style="width:5%;">№ п/п</th>
														<th style="width:45%;">Транспорт</th>
														<th style="width:15%;">Одиниця виміру</th>
														<th style="width:14%;">Кількість</th>
														<th style="width:10%;">Ціна, грн</th>
														<th style="width:10%;">Сума, грн</th>
														<th style="width:1%;"><i class="bi bi-trash text-secondary"></i></th>
													</tr>
												</thead>
												<tbody>
													<?php $summa = 0; ?>
													<?php foreach ($cipher->technics as $key => $row) : ?>
														<tr class="text-center" data-cipher_id="<?php echo $cipher->id; ?>" data-technic_id="<?php echo $row->technic_id; ?>">
															<td class="number"><?php echo $key + 1; ?></td>
															<td class="technic text-start"><?php echo $row->technic; ?></td>
															<td class="unit"><?php echo $row->unit; ?></td>
															<td class="quantity">
																<input type="text" class="form-control" value="<?php echo $row->quantity; ?>" maxlength="5" tabindex="4" disabled onchange="editQuantityTechnic(event);" onkeyup="changeQuantity(event);" />
															</td>
															<td class="price"><?php echo $row->price; ?></td>
															<td class="price_total"><?php echo $row->price_total; ?></td>
															<td class="delete">
																<?php if ($this->session->user->group === 'admin' || $this->session->user->group === 'engineer') : ?>
																	<a href="/estimates/delete_technic/<?php echo $cipher->id; ?>/<?php echo $row->technic_id; ?>" title="Видалити техніку з кошторису" data-bs-toggle="tooltip" onclick="deleteTechnic(event);"><i class="bi bi-trash text-danger"></i></a>
																<?php else : ?>
																	<a href="javascript:void(0);"><i class="bi bi-trash text-secondary"></i></a>
																<?php endif; ?>
															</td>
														</tr>
														<?php $summa = $row->price_total + $summa; ?>
													<?php endforeach; ?>
												</tbody>
												<tfoot>
													<tr>
														<th class="text-primary" colspan="5"><strong>Всього по працівникам</strong></th>
														<th class="summa text-center text-primary" colspan="2"><?php echo number_format($summa, 2, ',', ' ') . ' грн.'; ?></th>
													</tr>
												</tfoot>
											</table>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>

					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>