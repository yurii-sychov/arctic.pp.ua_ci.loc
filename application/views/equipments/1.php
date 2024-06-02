<div class="card text-dark bg-light">
	<div class="card-header">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="row">
			<?php foreach ($passports as $item) : ?>
				<div class="col-lg-4 col-md-6 col-sm-12 mb-3">
					<div class="card border-secondary">
						<img src="<?php echo (isset($item->photo) && file_exists('./uploads/passports/photos/' . $item->photo->photo)) ? '/uploads/passports/photos/' . $item->photo->photo : 'https://akkusys.shop/media/image/fb/29/2c/9882706-1.jpg' ?>" class="card-img-top" alt="...">
						<div class="card-body">
							<h5 class="card-title"><?php echo $item->stantion . ' (' . $item->disp . ')'; ?></h5>
							<!-- <p class="card-text">Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...</p> -->
						</div>
						<ul class="list-group list-group-flush">
							<li class="list-group-item text-primary"><strong>Тип:</strong> <?php echo $item->type;  ?></li>
							<?php foreach ($properties as $key => $value) : ?>
								<li class="list-group-item"><strong><?php echo $value->name; ?>:</strong> <?php echo isset($item->passports_properties[$key]) ? $item->passports_properties[$key]->value : 'NULL'  ?></li>
							<?php endforeach; ?>
						</ul>
						<div class="card-body">
							<a href="#" class="btn btn-success disabled">Правити</a>
							<a href="#" class="btn btn-danger disabled">Видалити</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>