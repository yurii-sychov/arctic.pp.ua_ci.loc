<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>
		<form method="POST" class="needs-validation <?php echo $this->input->post() ? 'has-validated' : NULL; ?>">
			<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
			<div class="row">
				<div class="col-lg-12 mb-3">
					<label for="Name" class="form-label"><strong>Ваше ім`я</strong></label>
					<input id="Name" type="text" name="name" class="form-control <?php echo form_error('name') ? 'is-invalid' : NULL; ?>" value="<?php echo $this->input->post() ? set_value('name') : NULL; ?>" />
					<div class="<?php echo form_error('name') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('name'); ?></div>
				</div>
				<div class="col-lg-12 mb-3">
					<label for="Email" class="form-label"><strong>Ваше Email</strong></label>
					<input id="Email" type="text" name="email" class="form-control <?php echo form_error('email') ? 'is-invalid' : NULL; ?>" value="<?php echo $this->input->post() ? set_value('email') : NULL; ?>" />
					<div class="<?php echo form_error('email') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('email'); ?></div>
				</div>
				<div class="col-lg-12 mb-3">
					<label for="Subject" class="form-label"><strong>Тема повідомлення</strong></label>
					<input id="Subject" type="text" name="subject" class="form-control <?php echo form_error('subject') ? 'is-invalid' : NULL; ?>" value="<?php echo $this->input->post() ? set_value('subject') : NULL; ?>" />
					<div class="<?php echo form_error('subject') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('subject'); ?></div>
				</div>
				<div class="col-lg-12 mb-3">
					<label for="Message" class="form-label"><strong>Повідомлення</strong></label>
					<textarea name="message" id="Message" rows="5" class="form-control <?php echo form_error('message') ? 'is-invalid' : NULL; ?>"><?php echo $this->input->post() ? set_value('message') : NULL; ?></textarea>
					<div class="<?php echo form_error('message') ? 'invalid-feedback' : 'valid-feedback' ?>"><?php echo form_error('message'); ?></div>
				</div>
				<div class="col-lg-12 mb-3 d-grid gap-2 d-md-block">
					<button type="submit" class="btn btn-primary mb-1"><strong>Відправити повідомлення</strong></button>
					<a href="/profile" class="btn btn-success mb-1">Повернутись до профілю</a>
				</div>
			</div>
		</form>
	</div>
</div>
