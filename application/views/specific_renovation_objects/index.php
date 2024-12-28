<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card card-indigo card-outline">
				<div class="card-header">
					<h3 class="card-title">Фільтр</h3>
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
					<div class="row">
						<div class="col-sm-4">
							<div class="form-group">
								<label for="Subdivision">Підрозділ</label>
								<select class="custom-select" name="subdivision" id="Subdivision" onchange="document.location=this.options[this.selectedIndex].value">
									<option value="/specific_renovation_objects">Оберіть підрозділ</option>
									<?php foreach ($subdivisions as $item) : ?>
										<option value="/specific_renovation_objects/index/<?php echo $item->id; ?>" <?php echo $item->id == $this->uri->segment(3) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="SpecificRenovationObject">Енергетичний об'єкт</label>
								<select class="custom-select" name="specific_renovation_object_id" id="SpecificRenovationObject" onchange="document.location=this.options[this.selectedIndex].value">
									<option value="/specific_renovation_objects/index/<?php echo $this->uri->segment(3); ?>">Оберіть енергетичний об'єкт</option>
									<?php foreach ($complete_renovation_objects as $item) : ?>
										<option value="/specific_renovation_objects/index/<?php echo $this->uri->segment(3); ?>/<?php echo $item->id; ?>" <?php echo $item->id == $this->uri->segment(4) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="Equipment">Обладнання</label>
								<select class="custom-select" name="equipment_id" id="Equipment" onchange="document.location=this.options[this.selectedIndex].value" disabled>
									<option value="/specific_renovation_objects/index/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>">Оберіть обладнання</option>
									<?php foreach ($equipments as $item) : ?>
										<option value="/specific_renovation_objects/index/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $item->id; ?>" <?php echo $item->id == $this->uri->segment(5) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>
				</div>
				<!-- /.card-body -->
			</div>
			<!-- /.card -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div>
<!-- /.container-fluid -->

<?php if (isset($results) && count($results) > 0) : ?>
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
					<!-- /.card-header -->
					<div class="card-body">
						<div class="text-center loading">
							<div class="spinner-border text-primary" role="status"></div>
						</div>
						<table id="SpecificRenovationObjectsTable" class="datatable table table-bordered table-hover table-striped d-none" data-order='[[ 0, "asc" ]]' data-page-length="5" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
							<thead class="thead-light">
								<tr>
									<th class="align-middle text-center" style="width:5%;"><?php echo "ID"; ?></th>
									<th class="align-middle text-center" style="width:20%;"><?php echo "ДНО"; ?></th>
									<th class="align-middle text-center" style="width:35%;" data-class-name="equipment-id"><?php echo "Вид обладнання"; ?></th>
									<th class="align-middle text-center" style="width:10%;"><?php echo "Рік вводу"; ?></th>
									<th class="align-middle text-center" style="width:10%;"><?php echo "Рік ІП (факт)"; ?></th>
									<th class="align-middle text-center" style="width:10%;"><?php echo "Рік ІП (план)"; ?></th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="more"><i class="fas fa-eye text-secondary"></i></th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="delete"><i class="fas fa-trash text-secondary"></i></th>
									<th data-visible="false" data-data="created_by">Запис створив</th>
									<th data-visible="false" data-data="updated_by">Запис змінив</th>
									<th data-visible="false" data-data="created_at">Дата створення запису</th>
									<th data-visible="false" data-data="updated_at">Дата зміни запису</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($results as $item) : ?>
									<tr id="<?php echo $item->id; ?>" data-id="<?php echo $item->id; ?>">
										<td class="align-middle text-center"><?php echo $item->id; ?></td>
										<td class="align-middle" data-field_name="name" data-field_title="ДНО" data-search="<?php echo $item->name; ?>" data-order="<?php echo $item->name; ?>">
											<input type="text" name="name[]" class="form-control text-left" value="<?php echo $item->name; ?>" maxlength="255" tabindex="1" onChange="updateFieldAjax(event, 'specific_renovation_objects', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle text-left"><?php echo $item->equipment . ' ' . $item->voltage . ' кВ'; ?></td>
										<td class="align-middle text-center" data-field_name="year_commissioning" data-field_title="Рік вводу" data-search="<?php echo $item->year_commissioning; ?>" data-order="<?php echo $item->year_commissioning; ?>">
											<input type="text" name="year_commissioning[]" class="form-control text-center" value="<?php echo $item->year_commissioning; ?>" maxlength="4" tabindex="2" onChange="updateFieldAjax(event, 'specific_renovation_objects', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle text-center" data-field_name="year_repair_invest" data-field_title="Рік ІП (факт)" data-search="<?php echo $item->year_repair_invest; ?>" data-order="<?php echo $item->year_repair_invest; ?>">
											<input type="text" name="year_repair_invest[]" class="form-control text-center" value="<?php echo $item->year_repair_invest; ?>" maxlength="4" tabindex="3" onChange="updateFieldAjax(event, 'specific_renovation_objects', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle text-center" data-field_name="year_plan_repair_invest" data-field_title="Рік ІП (план)" data-search="<?php echo $item->year_plan_repair_invest; ?>" data-order="<?php echo $item->year_plan_repair_invest; ?>">
											<input type="text" name="year_plan_repair_invest[]" class="form-control text-center" value="<?php echo $item->year_plan_repair_invest; ?>" maxlength="4" tabindex="4" onChange="updateFieldAjax(event, 'specific_renovation_objects', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle text-center">
											<a class="dt-control" href="javascript:void(0);" tabindex="-1">
												<i class="fas fa-eye text-info"></i>
											</a>
										</td>
										<td class="align-middle text-center">
											<a href="javascript:void(0);" onClick="deleteRow(event);" tabindex="-1">
												<i class="fas fa-trash text-danger"></i>
											</a>
										</td>
										<td><?php echo $item->created_by; ?></td>
										<td><?php echo $item->updated_by; ?></td>
										<td><?php echo $item->created_at; ?></td>
										<td><?php echo $item->updated_at; ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<!-- /.card-body -->
				</div>
				<!-- /.card -->
			</div>
			<!-- /.col -->
		</div>
		<!-- /.row -->
	</div>
	<!-- /.container-fluid -->
<?php endif; ?>