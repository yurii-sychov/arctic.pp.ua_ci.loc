<?php if ($this->session->flashdata('message')) : ?>
	<div class="alert alert-warning alert-dismissible fade show" role="alert">
		<strong>Увага!</strong> <?php echo $this->session->flashdata('message'); ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>

<div class="card">
	<div class="card-body">
		<div class="card-header mb-2">
			<h5><?php echo $title_heading_card; ?></h5>
		</div>
		<div class="row my-2">
			<div class="col-lg-12">
				<div class="alert alert-info" role="alert">
					<h4 class="alert-heading">Увага!</h4>
					Ця сторінка створена для копіювання характеристик обладнання. Вона має <strong>невеликі обмеження</strong> в частині функціоналу.
					<br>
					За допомогою цієї сторінки, можна для прискорення роботи з технічними характеристиками обладнання <strong>копіювати однотипні</strong> дані в БД.
					<br>
					Бажаючі використовувати даний функціонал митєво клацаємо на кнопку нижче.
					<br>
					Хто бажає трохи зачекати, необхідно потренуватися та додати технічні характеристики мінімум для <strong>5 одиниць</strong> конкретного обладнання.
				</div>
				<button class="btn btn-info" disabled>Підписатися на функціонал</button>
			</div>
			<?php if (isset($donor)) : ?>
				<div class="col-lg-6">
					<h6 class="text-center my-2">Вибране лише обладнання по <?php echo $donor->stantion; ?>, та з тим самим типом, що і донор.</h6>
					<form id="formCopyProperties" method="POST" action="/passports/copy_passport_properties_insert">
						<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
						<div class="col-lg-12 my-2">
							<input type="hidden" value="<?php echo $donor->equipment_id; ?>" name="donor_equipment_id">
							<input type="hidden" value="<?php echo $donor->donor_passport_id; ?>" name="donor_passport_id">
							<input type="hidden" value="<?php echo $donor->specific_renovation_object_id; ?>" name="donor_specific_renovation_object_id">
							<?php foreach ($patients as $item) : ?>
								<div class="form-check">
									<input class="form-check-input" type="checkbox" value="<?php echo $item->id; ?>" id="flexCheckChecked_<?php echo $item->id; ?>" name="passport_id[]">
									<label class="form-check-label" for="flexCheckChecked_<?php echo $item->id; ?>">
										<?php echo $item->stantion . ' (' . $donor->equipment . ' ' . $item->disp . ' ' . $item->place . ' типу ' . $item->type . ')'; ?>
									</label>
								</div>
							<?php endforeach; ?>
						</div>
						<button type="submit" class="btn btn-primary my-2">Копіювати характеристики</button>
					</form>
				</div>
				<div class="col-lg-6">
					<h6 class="text-center my-2"><?php echo 'Донор для копіювання - <span class="text-danger">' . $donor->stantion . ' (' . $donor->equipment . ' '  . $donor->disp . ' ' . $donor->place . ' типу ' . $donor->type . ')</span>'; ?></h6>
					<table class="table table-striped table-hover">
						<thead>
							<tr class="text-center">
								<th>Характеристика</th>
								<th>Значення</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($passport_properties as $item) : ?>
								<tr>
									<th><?php echo $item->property; ?></th>
									<td><?php echo $item->value; ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>
