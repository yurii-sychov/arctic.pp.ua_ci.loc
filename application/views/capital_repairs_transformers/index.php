<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row justify-content-start mb-2">
			<div class="col-lg-4 mb-1">
				<select name="stantion_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/capital_repairs_transformers/index" class="form-select">Оберіть підрозділ</option>
					<?php foreach ($subdivisions as $item) : ?>
						<option value="/capital_repairs_transformers/index/?subdivision_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('subdivision_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-4 mb-1">
				<select name="stantion_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/capital_repairs_transformers/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>" class="form-select">Оберіть підстанцію</option>
					<?php foreach ($stantions as $item) : ?>
						<option value="/capital_repairs_transformers/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('stantion_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="col-lg-4 mb-1">
				<select name="disp_id" class="form-select" onchange="document.location=this.options[this.selectedIndex].value">
					<option value="/capital_repairs_transformers/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?>" class="form-select">Оберіть диспечерську назву</option>
					<?php foreach ($disps as $item) : ?>
						<option value="/capital_repairs_transformers/index/?subdivision_id=<?php echo $this->input->get('subdivision_id'); ?>&stantion_id=<?php echo $this->input->get('stantion_id'); ?>&disp_id=<?php echo $item->id; ?>" <?php echo $item->id == $this->input->get('disp_id') ? 'selected' : NULL; ?>><?php echo $item->name; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<div class="table-responsive">
			<table class="table" id="collapseParent">
				<thead>
					<tr class="text-center">
						<th style="width: 5%;">№ п/п</th>
						<th style="width: 35%;">Підстанція</th>
						<th style="width: 10%;">Дисп. назва</th>
						<th style="width: 25%;">Тип</th>
						<th style="width: 10%;">Зав. №</th>
						<th style="width: 10%;">Рік виготовлення</th>
						<th style="width: 1%;"><i class="bi bi-list-columns"></i></th>
						<th style="width: 1%;"><i class="bi bi-journal-plus"></i></th>
						<th style="width: 1%;"><i class="bi bi-camera"></i></th>
						<th style="width: 1%;"><i class="bi bi-file-zip"></i></th>
						<th style="width: 1%;"><i class="bi bi-eye"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php $i = $per_page; ?>
					<?php foreach ($passports as $item) : ?>
						<tr class="text-center parent" data-subdivision_id="<?php echo $item->subdivision_id; ?>" data-complete_renovation_object_id="<?php echo $item->complete_renovation_object_id; ?>" data-specific_renovation_object_id="<?php echo $item->specific_renovation_object_id; ?>" data-place_id="<?php echo $item->place_id; ?>" data-passport_id="<?php echo $item->id; ?>">
							<td><?php echo $i; ?></td>
							<td class="text-start stantion"><?php echo $item->stantion; ?></td>
							<td class="disp"><?php echo $item->disp; ?></td>
							<td class="text-start"><?php echo $item->type; ?></td>
							<td><?php echo $item->number; ?></td>
							<td><?php echo date('Y', strtotime($item->production_date)); ?></td>
							<td class="text-center">
								<a href="javascript:void(0);" class="mx-1" onCLick="openOperatingListModal(event)">
									<i class="bi bi-list-columns text-warning" title="Подивитись експлуатаційну відомість СП" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a href="javascript:void(0);" class="mx-1" onCLick="openAddDocumentModal(event)">
									<i class="bi bi-journal-plus text-success" title="Додати документацію" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a href="javascript:void(0);" class="mx-1" onCLick="openAddPhotosModal(event)">
									<i class="bi bi-camera text-danger" title="Додати фотографії" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a href="/capital_repairs_transformers/get_zip_archive/<?php echo $item->id; ?>" class="mx-1">
									<i class="bi bi-file-zip text-primary" title="Завантажити Zip архів" data-bs-toggle="tooltip"></i>
								</a>
							</td>
							<td class="text-center">
								<a class="mx-1" data-bs-toggle="collapse" href="#collapse_<?php echo $i; ?>" role="button" aria-expanded="false" aria-controls="collapse_<?php echo $i; ?>" onCLick="typeof(actionCollapse) === 'function' ? actionCollapse(event) : '';">
									<i class="bi bi-eye text-info" title="Більше інформації" data-bs-toggle="tooltip"></i>
								</a>
							</td>
						</tr>
						<tr class="collapse collapse-horizontal <?php echo count($passports) == 1 ? 'show' : NULL; ?>" id="collapse_<?php echo $i; ?>" data-bs-parent="#collapseParent">
							<td colspan="3">
								<?php if (count($item->documents)) : ?>
									<table class="table table-bordered table-info" id="TableDocuments">
										<thead>
											<tr class="text-center align-middle">
												<th style="width: 10%;">№ п/п</th>
												<th style="width: 17%;">Дата</th>
												<th style="width: 69%;">Короткий опис документу</th>
												<th style="width: 1%;"><i class="bi bi-pencil"></i></th>
												<th style="width: 1%;"><i class="bi bi-file-pdf-fill"></i></th>
												<th style="width: 1%;"><i class="bi bi-trash"></i></th>
												<th style="width: 1%;"><i class="bi bi-upload"></i></th>
											</tr>
										</thead>
										<tbody>
											<?php $y = 1; ?>
											<?php foreach ($item->documents as $doc) : ?>
												<tr class="align-middle" data-id="<?php echo $doc->id; ?>">
													<?php if ($this->session->user->id == $doc->created_by || $this->session->user->group === 'admin') : ?>
														<td class="text-center" onclick="editRow(event);" title="Натисніть для активації форми" data-bs-toggle="tooltip" style="cursor:pointer;"><?php echo $y; ?></td>
													<?php else : ?>
														<td class="text-center"><?php echo $y; ?></td>
													<?php endif; ?>
													<td class="text-center">
														<input type="text" class="form-control document-date datemask datepicker text-center" name="document_date" value="<?php echo date('d.m.Y', strtotime($doc->document_date)); ?>" maxlength="10" disabled />
													</td>
													<td>
														<input type="text" class="form-control document-description" name="document_description" value="<?php echo htmlspecialchars($doc->document_description); ?>" maxlength="255" disabled />
													</td>
													<td class="text-center">
														<?php if ($this->session->user->id == $doc->created_by || $this->session->user->group === 'admin') : ?>
															<a href="javascript:void(0);" class="edit mx-1" onclick="editDocumentHandler(event);">
																<i class="bi bi-pencil text-success" title="Змінити дані" data-bs-toggle="tooltip"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="edit mx-1">
																<i class="bi bi-pencil text-secondary" title="Ви не можете змінити дані" data-bs-toggle="tooltip"></i>
															</a>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if (is_file('./uploads/documents/' . $doc->document_scan) && $doc->document_scan) : ?>
															<a href="/uploads/documents/<?php echo $doc->document_scan; ?>" class="mx-1" target="_blank">
																<i class="bi bi-file-pdf-fill text-warning" title="Подивитись документ" data-bs-toggle="tooltip"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="mx-1">
																<i class="bi bi-file-pdf-fill text-secondary" title="Подивитись документ" data-bs-toggle="tooltip"></i>
															</a>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if ($this->session->user->id == $doc->created_by || $this->session->user->group === 'admin') : ?>
															<a href="javascript:void(0);" class="mx-1" onClick="deleteDocument(event);">
																<i class="bi bi-trash text-danger" title="Видалити запис" data-bs-toggle="tooltip"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="mx-1">
																<i class="bi bi-trash text-secondary" title="Видалити запис" data-bs-toggle="tooltip"></i>
															</a>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php if ($this->session->user->id == $doc->created_by || $this->session->user->group === 'admin') : ?>
															<a href="javascript:void(0);" class="upload-file mx-1" onclick="reUploadFile(event);">
																<i class="bi bi-upload text-warning" data-bs-toggle="tooltip" title="<?php echo $doc->document_scan ? 'Замінити скан документу' : 'Завантажити скан документу'; ?>"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="upload-file mx-1">
																<i class="bi bi-upload text-secondary" title="Ви не можете завантажити або замінити скан документу" data-bs-toggle="tooltip"></i>
															</a>
														<?php endif; ?>

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
							<td colspan="8">
								<?php if (count($item->photo_albums)) : ?>
									<table class="table table-bordered table-info" id="TablePhotoAlbums">
										<thead>
											<tr class="text-center align-middle">
												<th style="width: 10%;">№ п/п</th>
												<th style="width: 17%;">Дата</th>
												<th style="width: 70%;">Назва фотоальбому</th>
												<th style="width: 1%;"><i class="bi bi-pencil"></i></th>
												<th style="width: 1%;"><i class="bi bi-file-pdf-fill"></i></th>
												<th style="width: 1%;"><i class="bi bi-trash"></i></th>
											</tr>
										</thead>
										<tbody>
											<?php $y = 1; ?>
											<?php foreach ($item->photo_albums as $album_name => $album) : ?>
												<tr class="align-middle" data-id="<?php echo $album['id']; ?>">
													<?php if ($this->session->user->id == $doc->created_by || $this->session->user->group === 'admin') : ?>
														<td class="text-center" onclick="editRow(event);" title="Натисніть для активації форми" data-bs-toggle="tooltip" style="cursor:pointer;"><?php echo $y; ?></td>
													<?php else : ?>
														<td class="text-center"><?php echo $y; ?></td>
													<?php endif; ?>
													<td class="text-center">
														<input type="text" class="form-control photo_album-date datemask datepicker text-center" name="photo_album_date" value="<?php echo date('d.m.Y', strtotime($album['photo_album_date'])); ?>" maxlength="10" disabled />
													</td>
													<td>
														<input type="text" class="form-control photo-album-name" name="photo_album_name" value="<?php echo htmlspecialchars($album['photo_album_name']); ?>" maxlength="255" disabled />
													</td>
													<td class="text-center">
														<?php if ($this->session->user->id == $doc->created_by || $this->session->user->group === 'admin') : ?>
															<a href="javascript:void(0);" class="edit mx-1" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Змінити дані" onclick="editPhotoAlbumHandler(event);">
																<i class="bi bi-pencil text-success"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="edit mx-1" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Ви не можете змінити дані">
																<i class="bi bi-pencil text-secondary"></i>
															</a>
														<?php endif; ?>
													</td>
													<td class="text-center">
														<?php foreach ($item->photos[$album_name] as $k => $photo) : ?>
															<a href="/uploads/photos/<?php echo $photo['photo']; ?>" data-lightbox="image_<?php echo $album_name; ?>" title="Подивитись фотоальбом" data-bs-toggle="tooltip">
																<i class="bi bi-card-image text-warning <?php echo $k > 0 ? 'd-none' : NULL; ?>"></i>
															</a>
														<?php endforeach; ?>
													</td>
													<td class="text-center">
														<?php if ($this->session->user->id == $album['created_by'] || $this->session->user->group === 'admin') : ?>
															<a href="javascript:void(0);" class="mx-1" onClick="deletePhotoAlbum(event);" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Видалити фотоальбом">
																<i class="bi bi-trash text-danger"></i>
															</a>
														<?php else : ?>
															<a href="javascript:void(0);" class="mx-1" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Ви не можете видалити цей фотоальбом">
																<i class="bi bi-trash text-secondary"></i>
															</a>
														<?php endif; ?>

														<input type="text" name="is_edit" class="d-none" />
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
						<th colspan="7">Показано з <?php echo $per_page; ?> по <?php echo $i - 1; ?> запис з <?php echo isset($total_filter_rows) ? $total_filter_rows : $total_rows; ?> записів</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<?php echo $this->pagination->create_links(); ?>
	</div>
</div>

<!-- Modal Form Add Docs -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addDocumentModalLabel">Додавання документу</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('capital_repairs_transformers/form_add_document');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addDocument(event)">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal Form Add Photos -->
<div class="modal fade" id="addPhotosModal" tabindex="-1" aria-labelledby="addPhotosModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addPhotosModalLabel">Додавання фотографій</h5>
			</div>
			<div class="modal-body">
				<?php $this->load->view('capital_repairs_transformers/form_add_photos');
				?>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
				<button type="button" class="btn btn-primary" onClick="addPhotos(event)">Зберегти</button>
			</div>
		</div>
	</div>
</div>

<!-- Modal OperatingList -->
<div class="modal fade" id="operatingListModal" tabindex="-1" aria-labelledby="operatingListModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="operatingListModalLabel">Експлуатаційна відомість</h5>
			</div>
			<div class="modal-body">OperatingList (розробка)
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
			</div>
		</div>
	</div>
</div>