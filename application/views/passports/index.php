<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card card-indigo card-outline">
				<div class="card-header">
					<h3 class="card-title">Фільтр</h3>
					<div class="card-tools">
						<?php if ($this->uri->segment(3) && $this->uri->segment(4)) : ?>
							<a href="/passports/get_data_excel/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>" title="Експортувати в Excel енергетичний об`єкт" class="btn btn-outline-success btn-sm" id="ButtonExportExcel">
								<i class="fas fa-file-excel"></i>
							</a>
						<?php endif; ?>
						<?php if ($this->uri->segment(3)) : ?>
							<a href="/passports/get_data_excel/<?php echo $this->uri->segment(3); ?>" title="<?php echo $this->uri->segment(3) == 1 ? 'Експортувати в Excel весь підрозділ' : 'Експортувати в Excel всю ЕМ'; ?>" class="btn btn-outline-primary btn-sm" id="ButtonExportExcelAll">
								<i class="fas fa-file-excel"></i>
							</a>
						<?php endif; ?>
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
								<select class="custom-select" name="subdivision_id" id="Subdivision" onchange="document.location=this.options[this.selectedIndex].value">
									<option value="/passports">Оберіть підрозділ</option>
									<?php foreach ($subdivisions as $item) : ?>
										<option value="/passports/index/<?php echo $item->id; ?>" <?php echo $item->id == $this->uri->segment(3) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="SpecificRenovationObject">Енергетичний об'єкт</label>
								<select class="custom-select" name="specific_renovation_object_id" id="SpecificRenovationObject" onchange="document.location=this.options[this.selectedIndex].value">
									<option value="/passports/index/<?php echo $this->uri->segment(3); ?>">Оберіть енергетичний об'єкт</option>
									<?php foreach ($complete_renovation_objects as $item) : ?>
										<option value="/passports/index/<?php echo $this->uri->segment(3); ?>/<?php echo $item->id; ?>" <?php echo $item->id == $this->uri->segment(4) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<label for="Equipment">Обладнання</label>
								<select class="custom-select" name="equipment_id" id="Equipment" onchange="document.location=this.options[this.selectedIndex].value" disabled>
									<option value="/passports/index/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>">Оберіть обладнання</option>
									<?php foreach ($equipments as $item) : ?>
										<option value="/passports/index/<?php echo $this->uri->segment(3); ?>/<?php echo $this->uri->segment(4); ?>/<?php echo $item->id; ?>" <?php echo $item->id == $this->uri->segment(5) ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
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
						<table id="PassportsTable" class="datatable table table-bordered table-hover table-striped d-none" data-order='[[ 0, "asc" ]]' data-page-length="5" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
							<thead class="thead-light">
								<tr>
									<th class="align-middle text-center" style="width:5%;" data-data="id">ID</th>
									<th class="align-middle text-center" style="width:8%;">ДНО</th>
									<th class="align-middle text-center" style="width:9%;">Місце</th>
									<th class="align-middle text-center" style="width:13%;">Тип обладнання</th>
									<th class="align-middle text-center" style="width:10%;">Короткий тип</th>
									<th class="align-middle text-center" style="width:11%;">Зав. №</th>
									<th class="align-middle text-center" style="width:10%;">Дата виготовлення</th>
									<th class="align-middle text-center" style="width:9%;">Рік вводу</th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="isBlock">IsBlock?</th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="isPhoto">IsPhoto?</th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="more"><i class="fas fa-eye text-secondary"></i></th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="properties"><i class="fas fa-search-plus text-secondary"></i></th>
									<th class="align-middle text-center" style="width:5%;" data-orderable="false" data-class-name="delete"><i class="fas fa-trash text-secondary"></i></th>
									<th data-visible="false" data-data="sub_number_r3">Під_номер R3</th>
									<th data-visible="false" data-data="equipment">Вид обладнання</th>
									<th data-visible="false" data-data="insulation_type">Вид ізоляції</th>
									<th data-visible="false" data-data="insulation_type_id">ID виду ізоляції</th>
									<th data-visible="false" data-data="created_by">Запис створив</th>
									<th data-visible="false" data-data="updated_by">Запис змінив</th>
									<th data-visible="false" data-data="created_at">Дата створення запису</th>
									<th data-visible="false" data-data="updated_at">Дата зміни запису</th>
									<th data-visible="false" data-data="page_size_pdf" data-class-name="page-size-pdf">Розмір листа PDF</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($results as $item) : ?>
									<tr id="<?php echo $item->id; ?>" data-id="<?php echo $item->id; ?>">
										<td class="align-middle text-center"><?php echo $item->id; ?></td>
										<td class="align-middle text-center"><?php echo $item->specific_renovation_object; ?></td>
										<td class="align-middle text-center" data-field_name="place_id" data-field_title="Місце">
											<select class="custom-select" name="place_id[]" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled>
												<option value="">Оберіть місце</option>
												<?php foreach ($places as $place): ?>
													<option value="<?php echo $place->id; ?>" <?php echo $place->id === $item->place_id ? 'selected' : NULL; ?>><?php echo $place->name; ?></option>
												<?php endforeach; ?>
											</select>
										</td>
										<td class="align-middle" data-field_name="type" data-field_title="Тип обладнання" data-search="<?php echo $item->type; ?>" data-order="<?php echo $item->type; ?>">
											<input type="text" name="type[]" class="form-control text-left" value="<?php echo $item->type; ?>" maxlength="255" tabindex="1" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle" data-field_name="short_type" data-field_title="Короткий тип" data-search="<?php echo $item->short_type; ?>" data-order="<?php echo $item->short_type; ?>">
											<input type="text" name="short_type[]" class="form-control text-left" value="<?php echo $item->short_type; ?>" maxlength="15" tabindex="2" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle" data-field_name="number" data-field_title="Зав. №" data-search="<?php echo $item->number; ?>" data-order="<?php echo $item->number; ?>">
											<input type="text" name="number[]" class="form-control text-left" value="<?php echo $item->number; ?>" maxlength="30" tabindex="3" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle text-center" data-field_name="production_date" data-field_title="Рік виготовлення" data-search="<?php echo $item->production_date; ?>" data-order="<?php echo $item->production_date; ?>">
											<input type="text" name="production_date[]" class="form-control text-center" value="<?php echo $item->production_date_format; ?>" maxlength="10" tabindex="4" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask />
											<!-- <div class="input-group date" id="reservationdate_<?php echo $item->id; ?>" data-target-input="nearest" datetimepicker>
												<input type="text" name="production_date[]" class="form-control text-center datetimepicker-input" value="<?php echo $item->production_date_format; ?>" maxlength="10" tabindex="4" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" isabled data-target="#reservationdate_<?php echo $item->id; ?>" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask />
												<div class="input-group-append" data-target="#reservationdate_<?php echo $item->id; ?>" data-toggle="datetimepicker">
													<div class="input-group-text"><i class="fa fa-calendar"></i></div>
												</div>
											</div> -->
										</td>
										<td class="align-middle text-center" data-field_name="commissioning_year" data-field_title="Рік вводу" data-search="<?php echo $item->commissioning_year; ?>" data-order="<?php echo $item->commissioning_year; ?>">
											<input type="text" name="commissioning_year[]" class="form-control text-center" value="<?php echo $item->commissioning_year; ?>" maxlength="4" tabindex="5" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled />
										</td>
										<td class="align-middle text-center" data-field_name="is_block" data-field_title="Блокування">
											<div class="custom-control custom-switch">
												<input type="checkbox" class="custom-control-input" data-field_name="is_block" data-field_title="Деблокувати/Блокувати" id="switch_is_block_<?php echo $item->id; ?>" <?php echo $item->is_block ? 'checked' : NULL; ?> value="<?php echo $item->is_block; ?>" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled />
												<label class="custom-control-label" for="switch_is_block_<?php echo $item->id; ?>" title="Деблокувати/Блокувати" style="cursor: pointer;"></label>
											</div>
										</td>
										<td class="align-middle text-center" data-field_name="is_photo" data-field_title="Фото">
											<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
												<input type="checkbox" class="custom-control-input" data-field_name="is_photo" data-field_title="Нема фото/Є фото" id="switch_is_photo_<?php echo $item->id; ?>" <?php echo $item->is_photo ? 'checked' : NULL; ?> value="<?php echo $item->is_photo; ?>" onChange="updateFieldAjax(event, 'passports', 'update_field_ajax');" disabled />
												<label class="custom-control-label" for="switch_is_photo_<?php echo $item->id; ?>" title="Нема фото/Є фото" style="cursor: pointer;"></label>
											</div>
										</td>
										<td class="align-middle text-center">
											<a class="dt-control" href="javascript:void(0);" title="Більше інформації" tabindex="-1">
												<i class="fas fa-eye text-info"></i>
											</a>
										</td>
										<td class="align-middle text-center">
											<a href="javascript:void(0);" title="Технічні характеристики" tabindex="-1" data-toggle="modal" data-target="#propertiesModal" onClick="openPassportProperties(event);">
												<i class="fas fa-search-plus text-warning"></i>
											</a>
										</td>
										<td class="align-middle text-center">
											<a href="javascript:void(0);" onClick="deleteRow(event);" title="Видалити" tabindex="-1">
												<i class="fas fa-trash text-danger"></i>
											</a>
										</td>
										<td><?php echo $item->sub_number_r3; ?></td>
										<td><?php echo $item->equipment . ' ' . $item->voltage . ' кВ'; ?></td>
										<td><?php echo $item->insulation_type; ?></td>
										<td><?php echo $item->insulation_type_id; ?></td>
										<td><?php echo $item->created_by; ?></td>
										<td><?php echo $item->updated_by; ?></td>
										<td><?php echo $item->created_at; ?></td>
										<td><?php echo $item->updated_at; ?></td>
										<td>A2</td>
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

<!-- Modal -->
<div class="modal fade" id="propertiesModal" tabindex="-1" aria-labelledby="propertiesModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="overlay">
				<i class="fas fa-2x fa-sync fa-spin"></i>
			</div>
			<div class="modal-header">
				<h5 class="modal-title" id="propertiesModalLabel"><strong>Характеристики обладнання</strong></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer flex-column flex-lg-row flex-nowrap">
				<a href="javascript:void(0);" class="btn btn-danger btn-block create-pdf" id="createPassportModal">Генерувати паспорт в форматі PDF</a>
				<button class="btn btn-dark btn-block" id="buttonPropertiesFormModal" type="button" title="Активувати форму" onclick="activeModalForm(event);">Активувати форму</button>
				<button type="button" class="btn btn-warning btn-block" data-dismiss="modal" id="closeModal">Закрити вікно</button>
			</div>
		</div>
	</div>
</div>