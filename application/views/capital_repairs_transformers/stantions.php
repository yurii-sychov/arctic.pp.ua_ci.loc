<div class="row">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-body">
				<div class="card-header mb-2 text-center">
					<h5>
						<?php echo $title_heading_card_sp; ?>
						<span class="badge rounded-pill bg-info"><?php echo count($stantions_sp); ?></span>
					</h5>
				</div>
				<div class="col-lg-12">
					<div class="row row-cols-1 row-cols-md-2 g-1" id="containerScrollbar_1" style="display:none;<?php echo count($stantions_sp) <= 8 ? 'height:170px' : 'height:420px'; ?>">
						<?php foreach ($stantions_sp as $stantion) : ?>
							<div class="col">
								<div class="card">
									<div class="card-body">
										<p class="card-title text-center">
											<a href="/capital_repairs_transformers/sdzp/<?php echo $stantion->subdivision_id . '/' . $stantion->id; ?>" class="btn shadow-none"><?php echo $stantion->name; ?></a>
										</p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-sm-6">
		<div class="card">
			<div class="card-body">
				<div class="card-header mb-2 text-center">
					<h5>
						<?php echo $title_heading_card_srm; ?>
						<span class="badge rounded-pill bg-info"><?php echo count($stantions_srm); ?></span>
					</h5>
				</div>
				<div class="col-lg-12">
					<div class="row row-cols-1 row-cols-md-2 g-1" id="containerScrollbar_2" style="display:none;<?php echo count($stantions_srm) <= 8 ? 'height:170px' : 'height:420px'; ?>">
						<?php foreach ($stantions_srm as $stantion) : ?>
							<div class="col">
								<div class="card">
									<div class="card-body">
										<p class="card-title text-center">
											<a href="/capital_repairs_transformers/sdzp/<?php echo $stantion->subdivision_id . '/' . $stantion->id; ?>" class="btn shadow-none"><?php echo $stantion->name; ?></a>
										</p>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
