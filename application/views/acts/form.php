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
					<form name="submit-to-google-sheet" id="createForm">
						<div class="form-row row-1">
							<div class="col-md-3">
								<div class="form-group">
									<label for="quantity">Кількість дат</label>
									<div class="select2-warning">
										<select class="custom-select select2" id="quantity" name="quantity" data-dropdown-css-class="select2-warning" style="width: 100%;" onchange="document.location=this.options[this.selectedIndex].value">
											<?php for ($i = 1; $i <= 5; $i++) : ?>
												<option value="/acts/create/<?php echo $i; ?>" <?php echo ($i == $quantity) ? 'selected' : NULL; ?>><?php echo $i; ?></option>
											<?php endfor; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="disp">Наказ про призначення комісії</label>
									<input class="form-control" id="disp" name="order" type="text" value="<?php echo $array_data[2][4]; ?>" readonly style="font-weight: 900;color: #0000ff;">
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="commission_head">Голова комісії</label>
									<input class="form-control" id="commission_head" name="commission_head" type="text" value="<?php echo $array_data[2][2]; ?>" readonly style="font-weight: 900;color: #0000ff;">
								</div>
							</div>
						</div>
						<div class="form-row row-2">
							<div class="col-md-3">
								<div class="form-group">
									<label for="acceptance_date">Дата приймання об'єкту</label>
									<div class="input-group date" id="acceptancedate" data-target-input="nearest">
										<input class="form-control datetimepicker-input" id="acceptance_date" name="acceptance_date" type="text" data-target="#acceptancedate" placeholder="dd.mm.yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask required />
										<div class="input-group-append" data-target="#acceptancedate" data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fa fa-calendar"></i></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="station">Енергетичний об'єкт</label>
									<div class="select2-warning">
										<select class="custom-select select2" id="station" name="station" data-dropdown-css-class="select2-warning" style="width: 100%;" required>
											<option value="">Оберіть енергетичний об'єкт</option>
											<?php foreach ($stations as $item) : ?>
												<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="dno">Диспечерські найменування</label>
									<input class="form-control" id="dno" name="dno" type="text" placeholder="Введіть диспечерські найменування" required>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="work_head">Керівник робіт</label>
									<div class="select2-warning">
										<select class="custom-select select2" id="work_head" name="work_head" data-dropdown-css-class="select2-warning" style="width: 100%;" required>
											<option value="">Оберіть керівника робіт</option>
											<?php foreach ($work_heads as $item) : ?>
												<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row row-3">
							<div class="col-md-3">
								<div class="form-group">
									<label for="plan_start_1">Планова дата початку</label>
									<?php for ($i = 1; $i <= $quantity; $i++) : ?>
										<div class="input-group date mb-2" id="planstart_<?php echo $i; ?>" data-target-input="nearest">
											<input class="form-control datetimepicker-input" id="plan_start_<?php echo $i; ?>" name="plan_start_<?php echo $i; ?>" type="text" data-target="#planstart_<?php echo $i; ?>" placeholder="dd.mm.yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask required />
											<div class="input-group-append" data-target="#planstart_<?php echo $i; ?>" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									<?php endfor; ?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="plan_end_1">Планова дата закінчення</label>
									<?php for ($i = 1; $i <= $quantity; $i++) : ?>
										<div class="input-group date mb-2" id="planend_<?php echo $i; ?>" data-target-input="nearest">
											<input class="form-control datetimepicker-input" id="plan_end_<?php echo $i; ?>" name="plan_end_<?php echo $i; ?>" type="text" class="form-control datetimepicker-input" data-target="#planend_<?php echo $i; ?>" placeholder="dd.mm.yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask required />
											<div class="input-group-append" data-target="#planend_<?php echo $i; ?>" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									<?php endfor; ?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fact_start_1">Фактична дата початку</label>
									<?php for ($i = 1; $i <= $quantity; $i++) : ?>
										<div class="input-group date mb-2" id="factstart_<?php echo $i; ?>" data-target-input="nearest">
											<input class="form-control datetimepicker-input" id="fact_start_<?php echo $i; ?>" name="fact_start_<?php echo $i; ?>" type="text" class="form-control datetimepicker-input" data-target="#factstart_<?php echo $i; ?>" placeholder="dd.mm.yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask required />
											<div class="input-group-append" data-target="#factstart_<?php echo $i; ?>" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									<?php endfor; ?>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fact_end_1">Фактична дата закінчення</label>
									<?php for ($i = 1; $i <= $quantity; $i++) : ?>
										<div class="input-group date mb-2" id="factend_<?php echo $i; ?>" data-target-input="nearest">
											<input class="form-control datetimepicker-input" id="fact_end_<?php echo $i; ?>" name="fact_end_<?php echo $i; ?>" type="text" class="form-control datetimepicker-input" data-target="#factend_<?php echo $i; ?>" placeholder="dd.mm.yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask required />
											<div class="input-group-append" data-target="#factend_<?php echo $i; ?>" data-toggle="datetimepicker">
												<div class="input-group-text"><i class="fa fa-calendar"></i></div>
											</div>
										</div>
									<?php endfor; ?>
								</div>
							</div>
						</div>
						<div class="form-row row-4">
							<div class="col-md-3">
								<div class="form-group">
									<label for="contract_date">Договір від дати</label>
									<div class="input-group date" id="contractdate" data-target-input="nearest">
										<input class="form-control datetimepicker-input" id="contract_date" name="contract_date" type="text" data-target="#contractdate" placeholder="dd.mm.yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd.mm.yyyy" data-mask />
										<div class="input-group-append" data-target="#contractdate" data-toggle="datetimepicker">
											<div class="input-group-text"><i class="fa fa-calendar"></i></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="contract_number">Номер договору</label>
									<input class="form-control" id="contract_number" name="contract_number" type="text" placeholder="Наприклад Д-15">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="plan_sum">Планова сума, грн</label>
									<input class="form-control" id="plan_sum" name="plan_sum" type="text" placeholder="Наприклад 1236,05">
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="fact_sum">Фактична сума, грн</label>
									<input class="form-control" id="fact_sum" name="fact_sum" type="text" placeholder="Наприклад 562,04">
								</div>
							</div>
						</div>
						<div class="form-row row-5">
							<div class="col-md-3">
								<div class="form-group">
									<label for="deviation">Роботи виконані згідно або з відхиленням</label>
									<div class="select2-warning">
										<select class="custom-select select2" id="deviation" name="deviation" data-dropdown-css-class="select2-warning" style="width: 100%;" required>
											<option value="">Оберіть необхідну позицію</option>
											<option value="без відхилень">без відхилень</option>
											<option value="з відхиленнями">з відхиленнями</option>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="work_complete">Роботи при виконанні ремонту</label>
									<input class="form-control" id="work_complete" name="work_complete" type="text" placeholder="Введіть дані" required>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="documents">Документи</label>
									<input class="form-control" id="documents" name="documents" type="text" placeholder="Введіть дані" required>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label for="defects">Недоробки</label>
									<input class="form-control" id="defects" name="defects" type="text" placeholder="Введіть дані" required>
								</div>
							</div>
						</div>
						<div class="form-row row-6">
							<div class="col-md-6">
								<div class="form-group">
									<label for="result_repair">Роботи призвели до</label>
									<input class="form-control" id="result_repair" name="result_repair" type="text" placeholder="Введіть дані" value="поліпшення експлуатаційних характеристик" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="result">Оцінка</label>
									<input class="form-control" id="result" name="result" type="text" placeholder="Введіть оцінку" value="задовільно" required>
								</div>
							</div>
						</div>
						<div class="form-row row-7">
							<div class="col-md-12">
								<div class="form-group">
									<label for="commission_members">Члени комісії</label>
									<div class="select2-warning">
										<select class="custom-select select2" id="commission_members" name="commission_members" multiple data-placeholder="Оберіть членів комісії" data-dropdown-css-class="select2-warning" style="width: 100%;" required>
											<?php foreach ($commission_members as $item) : ?>
												<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row row-8">
							<div class="col-md-12">
								<div class="form-group">
									<label for="work_members">Члени бригади</label>
									<div class="select2-warning">
										<select class="custom-select select2" id="work_members" name="work_members" multiple data-placeholder="Оберіть членів бригади" data-dropdown-css-class="select2-warning" style="width: 100%;" required>
											<?php foreach ($work_members as $item) : ?>
												<option value="<?php echo $item; ?>"><?php echo $item; ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="form-row row-9">
							<div class="col-md-12">
								<a class="btn btn-success" href="/acts"><i class="fas fa-arrow-left"></i> Назад до списку</a>
								<button class="btn btn-primary" type="submit"><i class="fas fa-cloud"></i> Відправити дані</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
