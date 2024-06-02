<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-lg-4 mb-1">
				<select name="subdivision_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/passports_r3">Оберіть підрозділ</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="/passports_r3/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-4 mb-1">
				<select name="stantion_id" class="form-select select2" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/passports_r3?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>" class="form-select">Оберіть підстанцію</option>
					<?php foreach ($complete_renovation_objects as $item) : ?>
						<option value="/passports_r3/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('stantion_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php if (isset($specific_renovation_objects)) : ?>
			<div class="row my-2">
				<div class="col-lg-12">
					<h2 class="text-center text-success"><?php echo $complete_renovation_object->name; ?> (інв. № об'єкту в R3 <span class="text-danger"><u><?php echo $complete_renovation_object->r3_id; ?></u></span>)</h2>
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<table class="datatable table table-bordered table-hover table-striped d-none align-middle" data-order='[[ 0, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0" data-row-id="id">
						<thead>
							<tr class="align-middle text-center">
								<!-- <th class="text-center" style="width:5%;">#</th> -->
								<th class="text-center" style="width:20%;" data-data="equipment">Обладнання</th>
								<th class="text-center" style="width:25%;" data-data="dno">Диспетчерська назва</th>
								<th class="text-center" style="width:10%;" data-data="sub_number_r3">Субномер</th>
								<th class="text-center" style="width:25%;" data-data="type">Тип обладнання</th>
								<th class="text-center" style="width:10%;" data-data="number">Зав. №</th>
								<th class="text-center" style="width:10%;" data-data="year_made">Рік випуску</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($specific_renovation_objects as $row) : ?>
								<tr>
									<!-- <td class="text-center"><?php echo $row->id; ?></td> -->
									<td><?php echo $row->equipment . ' ' . ($row->voltage_class / 1000) . ' кВ'; ?></td>
									<td><?php echo $row->name; ?></td>
									<td class="text-center">
										<?php foreach ($row->places as $place) : ?>
											<span class="d-none"><?php echo $place['sub_number_r3']; ?></span>
											<input type="text" class="form-control form-control-sm text-center my-1 bg-<?php echo $place['place_color']; ?>" value="<?php echo $place['sub_number_r3']; ?>" data-passport_id="<?php echo $place['passport_id']; ?>" onchange="editSubNumberR3(event);" oninput="deleteStringInput(event);">
										<?php endforeach;  ?>
									</td>
									<td class="text-center">
										<?php foreach ($row->places as $place) : ?>
											<span class="badge my-1 bg-<?php echo $place['place_color']; ?>"><?php echo $place['type']; ?></span></br>
										<?php endforeach;  ?>
									</td>
									<td class="text-center">
										<?php foreach ($row->places as $place) : ?>
											<span class="badge my-1 bg-<?php echo $place['place_color']; ?>"><?php echo $place['number']; ?></span></br>
										<?php endforeach;  ?>
									</td>
									<td class="text-center">
										<?php foreach ($row->places as $place) : ?>
											<span class="badge my-1 bg-<?php echo $place['place_color']; ?>"><?php echo $place['production_date']; ?></span></br>
										<?php endforeach;  ?>
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