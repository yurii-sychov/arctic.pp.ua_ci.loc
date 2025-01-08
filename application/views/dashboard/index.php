<div class="container-fluid">
	<!-- Small boxes (Stat box) -->
	<div class="row">
		<div class="col-lg-3 col-6">
			<!-- small box -->
			<div class="small-box bg-info">
				<div class="inner">
					<h3>MySQL <?php echo $db_version; ?></h3>
					<p>Версія БД</p>
				</div>
				<div class="icon">
					<i class="ion ion-bag"></i>
				</div>
				<a href="https://www.mysql.com" class="small-box-footer">Більше інформації <i class="fas fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-6">
			<!-- small box -->
			<div class="small-box bg-danger">
				<div class="inner">
					<h3>PHP & JavaScript</h3>
					<p>Мова прграмування</p>
				</div>
				<div class="icon">
					<i class="ion ion-pie-graph"></i>
				</div>
				<a href="https://php.net" class="small-box-footer">Більше інформації <i class="fas fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<!-- ./col -->
		<!-- ./col -->
		<div class="col-lg-3 col-6">
			<!-- small box -->
			<div class="small-box bg-success">
				<div class="inner">
					<h3><?php echo $tables; ?><sup style="font-size: 20px"></sup></h3>
					<p>Кількість таблиць в БД</p>
				</div>
				<div class="icon">
					<i class="ion ion-stats-bars"></i>
				</div>
				<a href="#" class="small-box-footer">Більше інформації <i class="fas fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<!-- ./col -->
		<div class="col-lg-3 col-6">
			<!-- small box -->
			<div class="small-box bg-warning">
				<div class="inner">
					<h3><?php echo $users_count; ?></h3>
					<p>Зареєстрованих користувачів</p>
				</div>
				<div class="icon">
					<i class="ion ion-person-add"></i>
				</div>
				<a href="/users" class="small-box-footer">Більше інформації <i class="fas fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<!-- ./col -->
	</div>
	<!-- /.row -->
</div>