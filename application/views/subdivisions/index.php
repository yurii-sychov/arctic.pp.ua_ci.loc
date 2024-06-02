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
				<div class="card-body">
					<div class="text-center loading">
						<div class="spinner-border text-primary" role="status"></div>
					</div>
					<table id="SubdivisionsTable" class="datatable table table-bordered table-hover table-striped d-none" data-order='[[ 2, "asc" ]]' data-page-length="5" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
						<thead class="thead-light">
							<tr>
								<th class="align-middle text-center" style="width:5%;"><?php echo "ID"; ?></th>
								<th class="align-middle text-center" style="width:75%;"><?php echo "Назва підрозділу" ?></th>
								<th class="align-middle text-center" style="width:10%;"><?php echo "Сортування"; ?></th>
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
									<td class="align-middle" data-field_name="name" data-field_title="Назва підрозділу" data-search="<?php echo $item->name; ?>" data-order="<?php echo $item->name; ?>">
										<input type="text" name="name[]" class="form-control text-left" value="<?php echo $item->name; ?>" maxlength="255" tabindex="1" onChange="updateFieldAjax(event, 'subdivisions', 'update_field_ajax');" disabled />
									</td>
									<td class="align-middle text-center" data-field_name="sort" data-field_title="Сортування" data-search="<?php echo $item->sort; ?>" data-order="<?php echo $item->sort; ?>">
										<input type="text" name="sort[]" class="form-control text-center" value="<?php echo $item->sort; ?>" maxlength="2" tabindex="2" onChange="updateFieldAjax(event, 'subdivisions', 'update_field_ajax');" disabled />
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
			</div>
		</div>
	</div>
</div>