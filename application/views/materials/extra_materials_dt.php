<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-4 mb-1">
				<select name="subdivision_id" class="form-select" onChange="document.location=this.options[this.selectedIndex].value">
					<option value="/materials/extra_materials">Оберіть матеріал для якого потрібно додати додатковий</option>
					<?php foreach ($materials as $item) : ?>
						<option value="/materials/extra_materials/?material_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('material_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php if ($this->input->get('material_id')) : ?>
				<div class="col-lg-4 mb-1">
					<select name="material_id" class="form-select" onChange="fillMaterial_id(event);" id="Material">
						<option value="">Оберіть матеріал який потрібно додати</option>
						<?php foreach ($materials as $item) : ?>
							<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-lg-2 mb-1">
					<input type="text" class="form-control" placeholder="Введіть кількість матеріалу" onChange="fillQuantity(event);" id="Quantity">
				</div>
				<div class="col-lg-2 mb-1 d-grid gap-2">
					<button type="submit" class="btn btn-outline-primary" disabled id="SendData" form="FormSubmit" onClick="sendData(event);">Відправити дані</button>
				</div>
			<?php endif; ?>
		</div>

		<?php if ($this->input->get('material_id')) : ?>
			<div class="row my-2">
				<div class="col-lg-12">
					<h2 class="text-center text-success"><strong class="text-danger"><?php echo $material_name->name; ?></strong><br>(Матеріал запланований для наступних об'єктів)</h2>
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<table class="datatable table table-bordered table-hover table-striped d-none align-middle" data-order='[[ 1, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0" data-row-id="id">
						<thead>
							<tr class="align-middle text-center">
								<th class="text-center" style="width:5%;" data-data="schedule_id" data-orderable="false">S_ID</th>
								<th class="text-center" style="width:20%;" data-data="stantion">Підстанція</th>
								<th class="text-center" style="width:15%;" data-data="dno">Диспетчерська назва</th>
								<th class="text-center" style="width:13%;" data-data="plan_quantity">Запланована кількість</th>
								<th class="text-center" style="width:10%;" data-data="unit">Одиниця виміру</th>
								<th class="text-center" style="width:25%;" data-data="material">Додатковий матеріал</th>
								<th class="text-center" style="width:10%;" data-data="quantity">Кількість</th>
								<th class="text-center" style="width:1%;" data-data="use" data-orderable="false"><i class="bi bi-recycle" title="Не використовувати матеріал"></i></th>
								<th class="text-center" style="width:1%;" data-data="delete" data-orderable="false"><i class="bi bi-trash" title="Видалити матеріал назавжди"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($objects as $row) : ?>
								<tr>
									<td class="text-center">
										<input type="hidden" class="form-control text-center schedule" name="schedule_id[]" value="<?php echo $row->schedule_id; ?>" readonly>
										<?php echo $row->schedule_id; ?>
									</td>
									<td class="text-start">
										<?php echo $row->stantion; ?>
									</td>
									<td class="text-center">
										<?php echo $row->disp; ?>
									</td>
									<td class="text-center">
										<?php echo $row->plan_quantity; ?>
									</td>
									<td class="text-center">
										<?php echo $row->unit; ?>
									</td>
									<td class="text-center">
										<input type="hidden" class="form-control text-center material" name="material_id[]" readonly>
										<select class="form-select material" disabled>
											<option value="">Оберіть матеріал який потрібно додати</option>
											<?php foreach ($materials as $item) : ?>
												<option value="<?php echo $item->id; ?>"><?php echo $item->name; ?></option>
											<?php endforeach; ?>
										</select>
									</td>
									<td class="text-center">
										<input type="text" class="form-control text-center quantity" name="quantity[]" tabindex="6" autocomplete="off">
									</td>
									<td class="text-center">
										<input type="checkbox" class="form-check-input use" name="use[]" disabled>
									</td>
									<td class="text-center">
										<i class="bi bi-trash text-danger" title="Видалити матеріал назавжди для (<?php echo htmlspecialchars($row->stantion); ?> <?php echo $row->disp; ?>)" style="cursor:pointer" onclick="deleteMaterial(event)" data-schedule_id="<?php echo $row->schedule_id; ?>" data-material_id="<?php echo $this->input->get('material_id'); ?>" data-year_service="<?php echo $row->year_service; ?>"></i>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>