<div class="row">
	<div class="col-12">
		<div class="card my-4">
			<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				<div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
					<h6 class="text-white text-capitalize ps-3">Фільтр</h6>
				</div>
			</div>
			<div class="card-body px-3 pb-2">
				<div class="row">
					<div class="col-md-3">
						<div class="input-group input-group-static mb-4">
							<select class="form-control ps-2" id="filter_plot" onchange="document.location=this.options[this.selectedIndex].value">
								<option value="/documentations" selected>Оберіть об'єкт (дільницю)</option>
								<?php foreach ($plots as $item): ?>
									<option value="/documentations?plot_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('plot_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<div class="col-md-3">
						<button class="btn btn-icon btn-3 btn-info w-100" type="button" <?php echo $this->input->get('plot_id') ? 'onclick="getListOp(event)"' : 'disabled'; ?>>
							<span class="btn-inner--icon"><i class="material-symbols-rounded">menu_book</i></span>
							<span class="btn-inner--text">Перелік інструкцій з ОП</span>
						</button>
					</div>
					<div class="col-md-3">
						<button class="btn btn-icon btn-3 btn-danger w-100" type="button" <?php echo $this->input->get('plot_id') ? 'onclick="getListPb(event)"' : 'disabled'; ?>>
							<span class="btn-inner--icon"><i class="material-symbols-rounded">menu_book</i></span>
							<span class="btn-inner--text">Перелік інструкцій з ПБ</span>
						</button>
					</div>
					<div class="col-md-3">
						<button class="btn btn-icon btn-3 btn-warning w-100" type="button" <?php echo $this->input->get('plot_id') ? 'onclick="getListTe(event)"' : 'disabled'; ?>>
							<span class="btn-inner--icon"><i class="material-symbols-rounded">menu_book</i></span>
							<span class="btn-inner--text">Перелік експлуатаційних інструкцій</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-12">
		<div class="card my-4">
			<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
				<div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
					<h6 class="text-white text-capitalize ps-3"><?php echo $title_heading_card; ?> (В реєстрі всього: <?php echo count($documentations); ?> документів)</h6>
				</div>
			</div>
			<div class="card-body px-0 pb-2">
				<div class="table-responsive p-0">
					<table class="table table-bordered table-striped table-hover align-items-center mb-0">
						<thead>
							<tr>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 55%;">Назва документа</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 9%;">Затвердження</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 8%;">Дата закінчення</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 8%;">Вид документа</th>
								<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 14%;">Група (Підгрупа) документа</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 2%;">Мусор?!</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 1%;">
									<i class="material-symbols-rounded">radio_button_checked</i>
								</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 1%;">
									<i class="material-symbols-rounded">radio_button_checked</i>
								</th>
								<!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 1%;">
									<i class="material-symbols-rounded">radio_button_checked</i>
								</th> -->
								<!-- <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 1%;">
									<i class="material-symbols-rounded">radio_button_checked</i>
								</th> -->
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 1%;">
									<i class="material-symbols-rounded">edit</i>
								</th>
								<th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width: 1%;">
									<i class="material-symbols-rounded">delete</i>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($documentations as $item): ?>
								<tr data-id="<?php echo $item->id; ?>">
									<td class="align-middle" style="white-space: normal;">
										<p class="text-xs font-weight-bold mb-0"><?php echo $item->name; ?></p>
										<p class="text-xs text-secondary mb-0 text-uppercase">
											<?php if ($item->number): ?>
												<?php echo $item->number ? '(' . $item->number . ')' : null; ?>
											<?php else: ?>
												<span class="text-success">Номер не передбачано</span>
											<?php endif; ?>
										</p>
									</td>
									<td class="align-middle text-sm">
										<p class="text-xs font-weight-bold mb-0"><?php echo $item->approval_document; ?></p>
										<p class="text-xs text-secondary mb-0">від <?php echo $item->date_start_doc; ?> року</p>
									</td>
									<td class="align-middle text-center text-sm">
										<span class="text-secondary text-xs font-weight-bold"><?php echo $item->date_finish_doc; ?></span>
									</td>
									<td class="align-middle text-center text-uppercase">
										<span class="text-secondary text-xs font-weight-bold"><?php echo $item->document_type_text; ?></span>
									</td>
									<td class="align-middle" style="white-space: normal;">
										<span class="text-secondary text-xs font-weight-bold"><?php echo $item->category_tree; ?></span>
									</td>
									<td class="align-middle text-center text-sm">
										<?php if ($item->is_trash): ?>
											<span class="badge badge-sm bg-gradient-warning">#Trash</span>
										<?php endif; ?>
									</td>
									<td class="align-middle text-center text-sm">
										<div class="form-check px-0">
											<input class="form-check-input" type="checkbox" id="my_docs_<?php echo $item->id; ?>" <?php echo $item->my_docs ? 'checked' : null; ?> <?php echo $this->input->get('plot_id') ? 'onclick="addDelDocs(event);"' : NULL; ?> <?php echo $this->input->get('plot_id') ? NULL : 'disabled'; ?>>
										</div>
										<!-- <i class="material-symbols-rounded text-primary"><?php echo $item->required ? 'radio_button_checked' : 'radio_button_unchecked'; ?></i> -->
									</td>
									<td class="align-middle text-center pt-3">
										<i class="material-symbols-rounded text-danger"><?php echo $item->required ? 'radio_button_checked' : 'radio_button_unchecked'; ?></i>
									</td>
									<!-- <td class="align-middle text-center pt-3">
										<i class="material-symbols-rounded text-warning"><?php echo $item->required_150 ? 'radio_button_checked' : 'radio_button_unchecked'; ?></i>
									</td> -->
									<!-- <td class="align-middle text-center pt-3">
										<i class="material-symbols-rounded text-info"><?php echo $item->required_35 ? 'radio_button_checked' : 'radio_button_unchecked'; ?></i>
									</td> -->
									<td class="align-middle text-center pt-3">
										<i class="material-symbols-rounded text-success" style="cursor: pointer;" <?php echo ($this->session->master->master_group === 'admin') ? 'onclick="editDocument(event);"' : NULL; ?>>edit</i>
									</td>
									<td class="align-middle text-center pt-3">
										<?php if (!$item->is_trash): ?>
											<i class="material-symbols-rounded text-danger" title="Викинути у сміття" style="cursor: pointer;" <?php echo ($this->session->master->master_group === 'admin') ? 'onclick="trashDocument(event);"' : NULL; ?>>delete_sweep</i>
										<?php else: ?>
											<i class="material-symbols-rounded text-danger" title="Дістати зі сміття" style="cursor: pointer;" <?php echo ($this->session->master->master_group === 'admin') ? 'onclick="untrashDocument(event);"' : NULL; ?>>restore_from_trash</i>
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

<?php if ($this->input->get('plot_id')): ?>
	<div class="row">
		<div class="col-12">
			<div class="card my-4">
				<div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
					<div class="bg-gradient-info shadow-dark border-radius-lg pt-4 pb-3">
						<h6 class="text-white text-capitalize ps-3"><?php echo $plot->name; ?></h6>
					</div>
				</div>
				<div class="card-body px-0 pb-2">
					<div class="row px-3 pb-2">
						<div class="col-12">
							<div class="nav-wrapper position-relative end-0">
								<ul class="nav nav-pills nav-fill p-1" role="tablist">
									<li class="nav-item" onclick="getDocType(1, 'op')">
										<a class="nav-link mb-0 px-0 py-1 btn btn-secondary active" data-bs-toggle="tab" href="#op" role="tab" aria-controls="op" aria-selected="true">
											ОП
										</a>
									</li>
									<li class="nav-item" onclick="getDocType(2, 'pb')">
										<a class="nav-link mb-0 px-0 py-1 btn btn-secondary" data-bs-toggle="tab" href="#pb" role="tab" aria-controls="pb" aria-selected="false">
											ПБ
										</a>
									</li>
									<li class="nav-item" onclick="getDocType(3, 'te')">
										<a class="nav-link mb-0 px-0 py-1 btn btn-secondary" data-bs-toggle="tab" href="#te" role="tab" aria-controls="te" aria-selected="false">
											ТЕ
										</a>
									</li>
									<li class="nav-item" onclick="getDocType(4, 'other')">
										<a class="nav-link mb-0 px-0 py-1 btn btn-secondary" data-bs-toggle="tab" href="#other" role="tab" aria-controls="other" aria-selected="true">
											Інше
										</a>
									</li>
								</ul>
							</div>
						</div>
					</div>
					<div class="row px-0 pb-2">
						<div class="col-12">
							<div class="tab-content">
								<?php $this->load->view('documentations/op'); ?>
								<?php $this->load->view('documentations/pb'); ?>
								<?php $this->load->view('documentations/te'); ?>
								<?php $this->load->view('documentations/other'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<table class="table d-none">
	<thead>
		<th class="text-uppercase">id</th>
		<th class="text-uppercase">name</th>
		<th class="text-uppercase">path</th>
		<th class="text-uppercase">pid</th>
		<th class="text-uppercase">level</th>
	</thead>
	<tbody>
		<?php foreach ($category_tree as $item): ?>
			<tr>
				<td><?php echo $item['id']; ?></td>
				<td><?php echo $item['name']; ?></td>
				<td><?php echo $item['path']; ?></td>
				<td><?php echo $item['parent_id']; ?></td>
				<td><?php echo $item['level']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

<!-- formModal -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title font-weight-normal" id="formModalLabel">Форма</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('documentations/form'); ?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal" id="close">Закрити</button>
				<button type="button" class="btn bg-gradient-primary" id="submit">Відправити дані</button>
			</div>
		</div>
	</div>
</div>