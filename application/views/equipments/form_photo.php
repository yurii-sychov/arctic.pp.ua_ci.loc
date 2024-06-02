<div class="card">
	<div class="card-header">
		<?php echo $title_heading_card; ?>
	</div>
	<div class="card-body">
		<?php if ($this->session->flashdata('message')) : ?>
			<div class="alert <?php echo $this->session->flashdata('action') === 'error' ? 'alert-danger' : 'alert-success'; ?> alert-dismissible fade show" role="alert">
				<?php echo $this->session->flashdata('message');  ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>
		<form method="POST" id="formAddPhotos" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-3">
					<div class="mb-3">
						<label for="inputFile" class="form-label">Завантажити фото обладнання</label>
						<input class="form-control" type="file" id="inputFile" name="photo">
					</div>
					<button type="submit" class="btn btn-primary">Відправити</button>
				</div>
				<div class="col-md-9">
					<div class="row">
					<?php foreach($passports as $item): ?>
						<div class="col-lg-3 col-md-6 col-sm-12">
						<div class="form-check">
						<img src="<?php echo (isset($item->photo) && file_exists('./uploads/passports/photos/'.$item->photo)) ? '/uploads/passports/photos/'.$item->photo : 'https://akkusys.shop/media/image/fb/29/2c/9882706-1.jpg' ?>" alt="photo" width="50">
							<label class="form-check-label" for="passportID_<?php echo $item->passport_id; ?>"><?php echo $item->type; ?></label>
							<input type="checkbox" class="form-check-input" id="passportID_<?php echo $item->passport_id; ?>" name="passports[]" value="<?php echo $item->passport_id; ?>" <?php echo set_checkbox('passports', $item->passport_id); ?> <?php echo (!isset($item->photo) || !file_exists('./uploads/passports/photos/'.$item->photo)) ? 'checked' : NULL; ?>>
						</div>
						</div>
					<?php endforeach; ?>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
