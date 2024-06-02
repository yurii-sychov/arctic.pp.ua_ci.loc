<div class="card">
	<div class="card-body">
		<?php echo form_error('name'); ?>
		<form method="POST" class="needs-validation <?php echo $this->input->post() ? 'has-validated' : NULL; ?>">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<div class="card-header mb-2">
				<h5><?php echo $title_heading_card; ?></h5>
			</div>
			<div class="row my-2">
				<div class="col-lg-4 text-center">
					<img src="<?php echo $user->gender == 1 ? '/assets/images/avatar_male.webp' :  '/assets/images/avatar_female.webp'; ?>" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
					<h5 class="my-3"><?php echo $user->name . " " . $user->surname; ?></h5>
					<p class="text-muted mb-1"><?php echo $user->position; ?></p>
					<p class="text-muted mb-4">Україна</p>
					<div class="mb-2 d-grid gap-2 d-md-block">
						<button type="submit" class="btn btn-primary mb-1">Відправити</button>
						<a href="/profile" class="btn btn-success mb-1">Повернутись до профілю</a>
					</div>
				</div>
				<div class="col-lg-8">
					<div class="row">
						<div class="col-sm-4">
							<label for="Surname">Прізвище</label>
						</div>
						<div class="col-sm-8">
							<input id="Surname" type="text" class="form-control <?php echo form_error('surname') ? 'is-invalid' : NULL; ?>" name="surname" value="<?php echo $this->input->post() ? set_value('surname') : $user->surname; ?>" />
							<div class="<?php echo form_error('surname') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('surname'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="Name">Ім`я</label>
						</div>
						<div class="col-sm-8">
							<input id="Name" type="text" class="form-control <?php echo form_error('name') ? 'is-invalid' : NULL; ?>" name="name" value="<?php echo $this->input->post() ? set_value('name') : $user->name; ?>" />
							<div class="<?php echo form_error('name') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('name'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="Patronymic">По батькові</label>
						</div>
						<div class="col-sm-8">
							<input id="Patronymic" type="text" class="form-control <?php echo form_error('patronymic') ? 'is-invalid' : NULL; ?>" name="patronymic" value="<?php echo $this->input->post() ? set_value('patronymic') : $user->patronymic; ?>" />
							<div class="<?php echo form_error('patronymic') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('patronymic'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="SubdivisionId">Підрозділ</label>
						</div>
						<div class="col-sm-8">
							<select id="SubdivisionId" name="subdivision_id" class="form-select">
								<option value="">Оберіть підрозділ</option>
								<?php foreach ($subdivisions as $item) : ?>
									<option value="<?php echo $item->id; ?>" <?php echo (($this->input->post() && $this->input->post('subdivision_id') == $item->id) || $user->subdivision_id == $item->id) ? set_select('subdivision_id', $item->id, TRUE) : set_select('subdivision_id', $item->id, FALSE); ?>><?php echo $item->name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="Gender">Стать</label>
						</div>
						<div class="col-sm-8">
							<select id="Gender" name="gender" class="form-select">
								<option value="1" <?php echo (($this->input->post() && $user->gender == 1) || $user->gender == 1) ? set_select('gender', 1, TRUE) : set_select('gender', 1, FALSE); ?>>Чоловік</option>
								<option value="0" <?php echo (($this->input->post() && $user->gender == 0) || $user->gender == 0) ? set_select('gender', 0, TRUE) : set_select('gender', 0, FALSE); ?>>Жінка</option>
							</select>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="Email">Email</label>
						</div>
						<div class="col-sm-8">
							<input id="Email" type="text" class="form-control <?php echo form_error('email') ? 'is-invalid' : NULL; ?>" name="email" value="<?php echo $this->input->post() ? set_value('email') : $user->email; ?>" />
							<div class="<?php echo form_error('email') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('email'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="Phone">Телефон</label>
						</div>
						<div class="col-sm-8">
							<input id="Phone" type="text" class="form-control <?php echo form_error('phone') ? 'is-invalid' : NULL; ?>" name="phone" value="<?php echo $this->input->post() ? set_value('phone') : $user->phone; ?>" />
							<div class="<?php echo form_error('phone') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('phone'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="PhoneMobile">Мобільний</label>
						</div>
						<div class="col-sm-8">
							<input id="PhoneMobile" type="text" class="form-control <?php echo form_error('phone_mobile') ? 'is-invalid' : NULL; ?>" name="phone_mobile" value="<?php echo $this->input->post() ? set_value('phone_mobile') : $user->phone_mobile; ?>" />
							<div class="<?php echo form_error('phone_mobile') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('phone_mobile'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<label for="Rows">Скількі рядків показувати на сторінці?</label>
						</div>
						<div class="col-sm-8">
							<input id="Rows" type="text" class="form-control <?php echo form_error('rows') ? 'is-invalid' : NULL; ?>" name="rows" value="<?php echo $this->input->post() ? set_value('rows') : $user->rows; ?>" />
							<div class="<?php echo form_error('rows') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('rows'); ?></div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<p class="mb-0">Адреса</p>
						</div>
						<div class="col-sm-8">
							<p class="text-muted mb-0">Україна</p>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-sm-4">
							<p class="mb-0">Енергетичні о'єкти</p>
						</div>
						<div class="col-sm-8">
							<p class="text-muted mb-0">
								<span class="badge bg-primary my-1">Україна</span>
							</p>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>