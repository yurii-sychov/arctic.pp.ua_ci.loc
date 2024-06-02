<div class="card text-dark bg-light">
	<div class="card-header mb-2">
		<h5><?php echo $title_heading_card; ?></h5>
	</div>
	<div class="card-body">
		<div class="text-center loading">
			<div class="spinner-border text-primary" role="status"></div>
		</div>
		<table class="datatable table table-hover table-bordered table-light align-middle d-none" data-order='[[ 0, "asc" ]]' data-page-length="10" data-state-save="1" data-paging-type="full_numbers" data-auto-width="0">
			<thead>
				<tr class="text-center">
					<th style="width:5%">#</th>
					<th style="width:14%">Прізвище</th>
					<th style="width:14%">Ім`я</th>
					<th style="width:15%">Підрозділ</th>
					<th style="width:15%">Посада</th>
					<th style="width:35%" data-orderable="false">Підстанції</th>
					<th style="width:1%" data-orderable="false"><i class="bi bi-pencil"></th>
					<th style="width:1%" data-orderable="false"><i class="bi bi-door-open"></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($users as $item) : ?>
					<tr>
						<td class="text-center"><?php echo $item->id; ?></td>
						<td><?php echo $item->surname; ?></td>
						<td><?php echo $item->name; ?></td>
						<td><?php echo $item->subdivision; ?></td>
						<td><?php echo $item->position; ?></td>
						<td>
							<?php foreach ($item->stantions as $stantion) : ?>
								<span class="badge bg-primary my-1">
									<?php echo $stantion->stantion; ?>
								</span>
							<?php endforeach; ?>
						</td>
						<td class="text-center">
							<?php if ($this->session->user->id == 1) : ?>
								<a class="mx-1" href="/profile/update/<?php echo $item->id; ?>" title="Редагувати"><i class="bi bi-pencil text-success"></i></a>
							<?php endif; ?>
						</td>
						<td class="text-center">
							<?php if ($this->session->user->id == 1) : ?>
								<a class="mx-1" href="/authentication/jump/<?php echo $item->id; ?>" title="Увійти"><i class="bi bi-door-open text-primary"></i></a>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>