<div class="card">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<?php if (validation_errors()) : ?>
			<div class="alert alert-danger" role="alert">
				<?php echo validation_errors(); ?>
			</div>
		<?php endif; ?>
		<form method="POST">
			<h5 class="text-primary">(Шифр робіт - <?php echo $cipher->cipher; ?>) <?php echo $cipher->name; ?></h5>
			<input type="hidden" name="cipher_id" value="<?php echo $this->uri->segment(3); ?>">
			<div class="row my-2">
				<?php foreach ($workers as $k => $item) : ?>
					<div class="col-lg-4">
						<input type="checkbox" class="form-check-input" id="Id_<?php echo $item->id; ?>" name="worker_id[]" value="<?php echo $item->id; ?>" <?php echo $item->checked ? 'checked disabled' : NULL; ?> />
						<label class="form-check-label" for="Id_<?php echo $item->id; ?>"><strong><?php echo $item->name; ?></strong></label>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="row my-2">
				<div class="col-lg-12">
					<button type="submit" class="btn btn-primary">Відправити</button>
					<a href="<?php echo $this->session->estimates_referrer; ?>" class="btn btn-success">Назад</a>
				</div>
			</div>
		</form>
	</div>
</div>