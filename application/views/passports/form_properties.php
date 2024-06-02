<form id="formAddProperties">
	<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
	<table class="table table-hover table-bordered">
		<thead>
			<tr class="text-center align-middle">
				<th>Характеристика</th>
				<th>Значення</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
</form>
