<div class="row signup justify-content-center animate__animated animate__zoomIn">
	<div class="col-md-7 col-lg-5">
		<div class="login-wrap p-4 p-md-5 shadow-lg">
			<div class="icon d-flex align-items-center justify-content-center">
				<span class="fa fa-user-o"></span>
			</div>
			<h3 class="text-center mb-4">Не маєш облікового запису?</h3>
			<form method="POST" class="login-form" id="formSignup">
				<input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>" />
				<div class="form-group">
					<input name="email" type="email" class="form-control rounded-left" placeholder="Введіть email" required>
				</div>
				<div class="form-group">
					<input name="login" type="text" class="form-control rounded-left" placeholder="Введіть логін" required>
				</div>
				<div class="form-group d-flex">
					<input name="password" type="password" class="form-control rounded-left" placeholder="Введіть пароль" required>
				</div>
				<!-- <div class="form-group d-flex">
					<input name="re_password" type="password" class="form-control rounded-left" placeholder="Повторіть пароль" required>
				</div> -->
				<div class="form-group">
					<input name="surname" type="text" class="form-control rounded-left" placeholder="Введіть прізвище" required>
				</div>
				<div class="form-group">
					<input name="name" type="text" class="form-control rounded-left" placeholder="Введіть ім`я" required>
				</div>
				<div class="form-group d-md-flex">
					<div class="w-50 text-md-left">
						<a href="/authentication/signin">Увійти</a>
					</div>
					<!-- <div class="w-50">
						<label class="checkbox-wrap checkbox-primary">Запам`ятати мене
							<input type="checkbox" disabled>
							<span class="checkmark"></span>
						</label>
					</div> -->
					<div class="w-50 text-md-right">
						<a href="/authentication/forgot">Забули пароль</a>
					</div>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary rounded submit p-3 px-5" id="buttonSignin">Реєстрація</button>
				</div>
			</form>
		</div>
	</div>
</div>