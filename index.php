<?php require_once('./include/DB_itit.php');


//Select all services
$selectServices = $db->query("SELECT * FROM ClimateData_administration");
$servicesVisibility = ($selectServices->fetchAll(PDO::FETCH_ASSOC));


function isVisibleBlock($servicesVisibility, $start, $end){
    $is_visible = false;
	for($i = $start; $i < $end; $i++ ) {
		if( ! $servicesVisibility[$i][visible] == 0){
			$is_visible = true;
			return $is_visible;
		}
	}
	return $is_visible;
}

$help_block_visible         = isVisibleBlock($servicesVisibility, 0, 4);
$temperature_block_visible  = isVisibleBlock($servicesVisibility, 4, 6);
$precipitations_visible     = isVisibleBlock($servicesVisibility, 6, 8);
$operative_info_visible     = isVisibleBlock($servicesVisibility, 8, 12);
$conditions_block_visible   = isVisibleBlock($servicesVisibility, 12, 16);
$same_years_block_visible   = isVisibleBlock($servicesVisibility, 16, 18);


$title = 'Главное меню информационно-аналитической системы';
$heading = 'Главное меню';
$sub_heading = 'В системе хранятся данные за период с 1961г. по настоящее время';

//Вложение вспомогательных файлов
include_once ('./include/climate_auth.php');
include_once ('./include/header.php');
?>

	<section class="warning-copyright">
		<div class="container">

		</div>
	</section>

	<section class="content">
		<div class="content-container">
			<div class="container">

				<!--Warning message-->
				<div class="row">
					<div class="col-md-12">
						<div class="warning-message">
							<p class="footer-info--heading">
								<b>Модуль "Агроклиматические ресурсы". При использовании обязательно цитирование.</b>
							</p>
							<p class="footer-info--description">
								<blockquote>
								Антонов С.А. Тенденции изменения климата и их влияние на земледелие Ставропольского края  /С.А. Антонов // Известия Оренбургского государственного аграрного университета. 2017. №4 (66). С. 43-46.
                                <!---
                                Информационно-аналитическая система «Агроклиматический потенциал Ставропольского края»/С.А.Антонов, Л.И.Желнакова, О.В. Петин. – Электронный ресурс. – Михайловск: ГНУ Ставропольский НИИСХ Россельхозакадемии, 2010. Режим доступа: http://climate.sniish.ru/mshsk/index.php Загл. с экрана. Дата доступа: <?echo date("d.m.Y");?>
                               
                                Данный модуль разработан в рамках Государственного контракта № 0121200000817000022 от 14.06.2017г. в части разработки и внедрения модуля
                                "Агроклиматические ресурсы" геоинформационной системы «Распределение земель сельскохозяйственного назначения в Ставропольском крае», установленной в министерстве сельского хозяйства Ставропольского края,
                                для обеспечения государственных нужд Ставропольского края.--->
                            </blockquote>
							</p>
						</div>
					</div>
				</div> <!--Warning message-->

				<div class="row">
					<div class="col-md-12">

						<div class="block-container clearfix  <?php echo(( ! $help_block_visible) ? 'hidden' : ''); ?>">
							<div class="container-heading content-header-color">
								<h4>Справочная информация</h4>
							</div>
							<div class="container-elements clearfix">


								<div class="col-md-3 <?php echo(( ! $servicesVisibility[0][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="economy.php">

											<div class="service-icon economics-icon-hover">
												<img src="img/icons/svg/economics.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Информация о состоянии экономики АПК в Ставропольском крае
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Экономика АПК
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo((!$servicesVisibility[1][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="soil.php">

											<div class="service-icon ground-icon-hover-color">
												<img src="img/icons/svg/ground.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Информация о почвенном покрове Ставропольского края
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Почвы
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo(( ! $servicesVisibility[2][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="climate_info.php">

											<div class="service-icon climate-icon-hover">
												<img src="img/icons/svg/climate.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Информация о многолетних климатических условиях в Ставропольском крае
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Климат
										</h5>
									</div>
								</div> <!--end service element-->



								<div class="col-md-3 <?php echo((! $servicesVisibility[3][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="clear_steam.php">

											<div class="service-icon decade-data-icon-hover">
												<img src="img/icons/svg/clear_steam.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Оперативная информация о климатически обусловленном количестве чистых паров (показатель И.В. Свисюка)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Чистые пары
										</h5>
									</div>
								</div> <!--end service element-->


							</div>

						</div>

					</div>
				</div>
			</div>

		</div>


		<div class="content-container half-size">
			<div class="container">
				<div class="row">
					<div class="col-md-6">

						<div class="block-container clearfix <?php echo(( ! $temperature_block_visible) ? 'hidden' : '')  ; ?>">
							<div class="container-heading content-header-color">
								<h4>Температура </h4>
							</div>
							<div class="container-elements clearfix">


								<div class="col-md-6 <?php echo(( ! $servicesVisibility[4][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="analyze_t.php">

											<div class="service-icon temperature--hover-icon">
												<img src="img/icons/svg/deviations.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Анализ отклонений температуры воздуха по месяцам за различные временные периоды
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Отклонения
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-6 <?php echo(( ! $servicesVisibility[5][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="temperature_dynamic.php">

											<div class="service-icon temperature--hover-icon">
												<img src="img/icons/svg/dynamic.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Анализ динамики температуры воздуха по месяцам
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Динамика
										</h5>
									</div>
								</div> <!--end service element-->

							</div>

						</div>

					</div>


					<div class="col-md-6">

						<div class="block-container clearfix <?php echo(( ! $precipitations_visible ) ? 'hidden' : '')  ; ?>">
							<div class="container-heading content-header-color">
								<h4>Осадки</h4>
							</div>
							<div class="container-elements clearfix">


								<div class="col-md-6 <?php echo(( ! $servicesVisibility[6][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="analyze_p.php">

											<div class="service-icon deviation-hover-icon">
												<img src="img/icons/svg/deviations.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Анализ отклонений количества осадков по месяцам за различные временные периоды
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Отклонения
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-6 <?php echo((! $servicesVisibility[7][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="precip_dynamic.php">

											<div class="service-icon deviation-hover-icon">
												<img src="img/icons/svg/dynamic.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Анализ динамики количества осадков по месяцам
                                                </p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Динамика
										</h5>
									</div>
								</div> <!--end service element-->


							</div>

						</div>

					</div>
				</div>
			</div>

		</div>  <!--End half-size container-->

		<div class="content-container">
			<div class="container">
				<div class="row">
					<div class="col-md-12">

						<div class="block-container clearfix <?php echo(( ! $operative_info_visible ) ? 'hidden' : '')  ; ?>">
							<div class="container-heading content-header-color">
								<h4>Оперативная информация</h4>
							</div>
							<div class="container-elements clearfix">


								<div class="col-md-3 <?php echo((!$servicesVisibility[8][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="gtk.php">

											<div class="service-icon economics-icon-hover">
												<img src="img/icons/svg/gidro_coefficient.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Анализ гидротермического коэффициента (ГТК показатель Г.Т. Селянинова)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Гидротермический коэффициент
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo((!$servicesVisibility[9][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="walter.php">

											<div class="service-icon ground-icon-hover-color">
												<img src="img/icons/svg/valter.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Анализ засушливости с помощью климадиаграммы Вальтера
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Климадиаграмма Вальтера
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo((!$servicesVisibility[10][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="bcp.php">

											<div class="service-icon climate-potential-hover-icon">
												<img src="img/icons/svg/climate_potential.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Анализ динамики биоклиматического потенциала (БКП показатель Д.И. Шашко)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Биоклиматический потенциал
										</h5>
									</div>
								</div> <!--end service element-->



								<div class="col-md-3 <?php echo((!$servicesVisibility[11][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="continent.php">

											<div class="service-icon decade-data-icon-hover">
												<img src="img/icons/svg/mainland.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Анализ динамики континентальности климата (показатель Н.Н. Иванова)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Континентальность
										</h5>
									</div>
								</div> <!--end service element-->
							</div>
						</div>
					</div>
				</div>
			</div>

		</div> <!--end operative information-->

		<div class="content-container">
			<div class="container">
				<div class="row">
					<div class="col-md-12">

						<div class="block-container clearfix <?php echo(( ! $conditions_block_visible) ? 'hidden' : '')  ; ?>">
							<div class="container-heading content-header-color">
								<h4>Агроклиматические условия возделывания сельскохозяйственных культур</h4>
							</div>
							<div class="container-elements clearfix">


								<div class="col-md-3 <?php echo((!$servicesVisibility[12][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="ylanova.php">

											<div class="service-icon economics-icon-hover">
												<img src="img/icons/svg/ozimaya_wheat.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Динамика агроклиматических условий озимой пшеницы (показатель Е.С. Улановой)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Озимая пшеница
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo((!$servicesVisibility[13][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="sapojnikova.php">

											<div class="service-icon ground-icon-hover-color">
												<img src="img/icons/svg/yaroviye.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Динамика агроклиматических условий ранних яровых колосовых (показатель С.А. Сапожниковой)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Яровые колосовые
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo((!$servicesVisibility[14][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="melnik.php">

											<div class="service-icon sunflower-hover-icon">
												<img src="img/icons/svg/sunflower.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Динамика агроклиматических условий подсолнечника (показатель Ю.С. Мельника)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Подсолнечник
										</h5>
									</div>
								</div> <!--end service element-->

								<div class="col-md-3 <?php echo((!$servicesVisibility[15][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="chirkov.php">

											<div class="service-icon corn-hover-icon">
												<img src="img/icons/svg/corn.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Динамика агроклиматических условий кукурузы (показатель Ю.И. Чиркова)
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											Кукуруза
										</h5>
									</div>
								</div> <!--end service element-->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div> <!--end agroclimate conditions-->


		<div class="content-container ">
			<div class="container">
				<div class="row">
					<div class="col-md-12">

						<div class="block-container clearfix last-container <?php echo(( ! $same_years_block_visible ) ? 'hidden' : '')  ; ?>">
							<div class="container-heading content-header-color">
								<h4>Годы аналоги</h4>
							</div>
							<div class="container-elements clearfix">


								<div class="col-md-3 <?php echo((!$servicesVisibility[16][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="analog_tp.php">

											<div class="service-icon same-years-hover-icon">
												<img src="img/icons/svg/temperature_compearison.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
													Поиск годов-аналогов по температуре и осадкам по месячным данным
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											По температуре и осадкам
										</h5>
									</div>
								</div> <!--end service element-->


								<div class="col-md-3 <?php echo((!$servicesVisibility[17][visible]) ? 'hidden' : ''); ?>">
									<div class="container-elements-element clearfix">
										<a href="analog_demarton.php">

											<div class="service-icon same-years-hover-icon">
												<img src="img/icons/svg/index_comparison.svg" class="svg">
											</div>
											<div class="service-description">
												<p>
                                                    Поиск годов-аналогов по индексу аридности по различным сочетаниям месячных данных
												</p>
											</div>
										</a>
									</div>
									<div class="service-name">
										<h5>
											По индексу аридности
										</h5>
									</div>
								</div> <!--end service element-->
							</div>

						</div>

					</div>
				</div>
			</div>

		</div> <!--end similar years-->

    </section>
	<?php include_once ('./include/footer.php'); ?>
