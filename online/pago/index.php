<?php

error_reporting(E_ALL);
ini_set('display_errors', False);

// Si no se define el idioma en la URL, se intenta usar el idioma del navegador
if (!$_GET['l']) {
  $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
} else {
  $lang = $_GET['l'];
}

switch ($lang) {
  case 'en':
    $idioma = 'en_US.UTF-8';
    break;
  case 'pt':
    $idioma = 'pt_PT.UTF-8';
    break;
  default:
    $lang = 'es';
    $idioma = 'es_AR.UTF-8'; // Español por defecto
    break;
}

// Define el idioma
putenv("LANG=$idioma");
setlocale(LC_ALL, $idioma);

// Define la ubicación de los ficheros de traducción
bindtextdomain("messages", "locale");
textdomain("messages");
bind_textdomain_codeset("messages", 'UTF-8');



// define la información del campo Source, que indica cómo llego el donante al formulario
if ($_GET['ref']) { $referer_get = $_GET['ref']; }
$referer_url = $_SERVER['HTTP_REFERER'];

if ($referer_get || $referer_url) {
  if ($referer_get) {
    $source_sep = ' | ';
  }
  $source = $referer_get . $source_sep . 'Referer: ' . $referer_url;
} else {
  $source = 'Acceso directo';
}

?>

<!doctype html>
<html lang="<?php echo $lang; ?>" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo sprintf(_('Reevo: Tejiendo alternativas en educación'));?></title>

  <link rel="icon" href="http://assets.reevo.org/logo/favicon/favicon.ico">

	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300italic,300,700,700italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/bootstrap-theme-flatly-min.css">
	<link rel="stylesheet" href="css/bootstrap-formhelpers-min.css">
	<link rel="stylesheet" href="css/bootstrapValidator-min.css"/>
	<link rel="stylesheet" href="css/bootstrap-datepicker.css"/>
	<link rel="stylesheet" href="css/bootstrap-select.css"/>
	<link rel="stylesheet" href="css/bootstrap-side-notes.css" />
	<link rel="stylesheet" href="css/style.css">


	<!-- Usado para permitir la carga de provincias/estados del CRM -->
	<!-- <script type="text/javascript" src="js/crm-assets/jquery-1.11.1.min.js"></script> -->


	<meta property="og:url"                content="https://donar.reevo.org/?l=<?php echo $lang; ?>" />
	<meta property="og:type"               content="article" />
	<meta property="og:title"              content="<?php echo sprintf(_('Campaña de financiamiento colectivo 2017 de Reevo: Tejiendo alternativas en educación')) ?>" />
	<meta property="og:description"        content="<?php echo sprintf(_('Hazte miembro de Reevo y con tu aporte mensual nos permitirás seguir tejiendo redes entornos a la Educación Alternativa...'))?>" />
	<meta property="og:image"              content="https://donar.reevo.org/img/banner-fb-<?php echo $lang; ?>.png" />


	<script src="js/jquery-2.1.4.js"></script>
	<!-- <script type="text/javascript" src="js/crm-assets/jquery-1.11.1.min.js"></script> -->


	<script src="js/bootstrap-min.js"></script>
	<script src="js/bootstrap-formhelpers-min.js"></script>
	<script type="text/javascript" src="js/bootstrapValidator-min.js"></script>
  <!-- <script src="js/bootstrap-datepicker.js"></script> -->
  <script src="js/bootstrap-select.js"></script>

	<script src="js/modernizr.js"></script> <!-- Modernizr -->
	<script src="js/velocity.min.js"></script>

	<script src="js/velocity.ui.min.js"></script>
	<script src="js/main.js"></script> <!-- Resource jQuery -->
  <script src="js/prefixfree.min.js"></script>

	<script type="text/javascript">
	// Code goes here
		$(function(){

			$('#jumbotron-form')
				.bootstrapValidator({
		        message: '<?php echo sprintf(_('Este valor no es válido'));?>',
		        feedbackIcons: {
		            valid: 'glyphicon glyphicon-ok',
		            invalid: 'glyphicon glyphicon-remove',
		            validating: 'glyphicon glyphicon-refresh'
		        },
		        fields: {
		            donation_box: {
		                validators: {
		                    notEmpty: {
		                        message: '<?php echo sprintf(_('No puede estar vacío'));?>'
		                    },
												digits: {
														message: '<?php echo sprintf(_('Sólo se aceptan dígitos'));?>'
												},
												between: {
														min: 51,
														max: 50000,
														message: '<?php echo sprintf(_('El valor debe ser mayor a 50.'));?>'
												}
		                }
		            },
								donation_box2: {
										validators: {
												notEmpty: {
														message: '<?php echo sprintf(_('No puede estar vacío'));?>'
												},
												digits: {
														message: '<?php echo sprintf(_('Sólo se aceptan dígitos'));?>'
												},
												between: {
														min: 201,
														max: 50000,
														message: '<?php echo sprintf(_('El valor debe ser mayor a 200.'));?>'
												}
										}
								},
		        }
						// .on('valid.bs.validator invalid.bs.validator', function (e) {
				    //   alert('xxx')})
		    });

		  $('#hacemos-extra-txt').on('hide.bs.collapse', function () {
		    $('#hacemos-extra-btn').html('<span class="glyphicon glyphicon-chevron-down"></span> Mostrar más');
		  })
		  $('#hacemos-extra-txt').on('show.bs.collapse', function () {
		    $('#hacemos-extra-btn').html('<span class="glyphicon glyphicon-chevron-up"></span> Ocultar');
		  });

			// activa los tooltips
			$(".tooltip-on").popover({ trigger: "hover", container: "body" });

			$('.panel-heading h4').on('click',function(e){
		    if($(this).parents('.panel').children('.panel-collapse').hasClass('in')){
		        e.stopPropagation();
		    }
			    // You can also add preventDefault to remove the anchor behavior that makes
			    // the page jump
			    // e.preventDefault();
			});

			$("#donation-radio input:radio").change(function () {
	      if ($("#donation-radio-extra-radio").is(":checked")) {
	              // $( "#donation-radio-extra" ).show(400,"swing");
	              $( "#donation-radio-extra" ).slideDown();
	          }
	          else {
	              // $( "#donation-radio-extra" ).hide(400,"swing");
	              $( "#donation-radio-extra" ).slideUp();
	          }
	    });
	    $("#donation-radio-extra-input-1").val( '10' );
	    $("#donation-radio .btn-group").change(function () {
	      x = $("#donation-radio input[type='radio']:checked").val();
	      $("#donation-radio-extra-input-1").val( x );
	    });

			$("#donation-radio-2 input:radio").change(function () {
	      if ($("#donation-radio-extra-radio-2").is(":checked")) {
	              // $( "#donation-radio-extra" ).show(400,"swing");
	              $( "#donation-radio-extra-2" ).slideDown();
	          }
	          else {
	              // $( "#donation-radio-extra" ).hide(400,"swing");
	              $( "#donation-radio-extra-2" ).slideUp();
	          }
	    });
	    $("#donation-radio-extra-input-2").val( '10' );
	    $("#donation-radio-2 .btn-group").change(function () {
	      x = $("#donation-radio-2 input[type='radio']:checked").val();
	      $("#donation-radio-extra-input-2").val( x );
	    });


		})

	</script>



</head>
<body data-hijacking="off" data-animation="fade">

	<section id="section1" class="cd-section  visible">
			<div class="row" style="">
				<h3 id="titulo" class="shake"><span><?php echo sprintf(_('Campaña de financiamiento colectivo 2017 de Reevo'))?>:</span><?php echo sprintf(_('Tejiendo alternativas en educación'))?></h3>
				<div class="vertical-center">
					<div class="vertical-center2 shake">
					<div class="col-md-4 col-xs-12 pilares">
						<img src="img/icon-1.png" class="img-responsive col-xs-3 col-sm-3 col-md-12" />
						<h3><?php echo sprintf(_('Facilitamos el encuentro'))?></h3>
						<span><?php echo sprintf(_('Propiciamos encuentros para intercambiar y producir conocimientos de forma colectiva.'))?></span>
					</div>
				  <div class="col-md-4 col-xs-12 pilares">
						<img src="img/icon-2.png" class="img-responsive col-xs-3 col-sm-3 col-md-12" />
						<h3><?php echo sprintf(_('Hablamos al mundo'))?></h3>
						<span><?php echo sprintf(_('Producimos contenidos originales, divulgamos noticias y estudiamos el desarrollo de las alternativas en la educación.'))?>'</span>
					</div>
				  <div class="col-md-4 col-xs-12 pilares">
						<img src="img/icon-3.png" class="img-responsive col-xs-3 col-sm-3 col-md-12" />
						<h3><?php echo sprintf(_('Conectamos los puntos'))?></h3>
						<span><?php echo sprintf(_('Creamos redes de apoyo, asesoramiento, investigación y acción entre proyectos educativos, individuos y colectivos.'))?></span>
					</div>
						<div class="col-md-12 col-xs-12 hacemos">
							<!-- <h3>¿Cómo lo hacemos?</h3> -->
							<span>
								<h3><?php echo sprintf(_('Desde el 2009, con la realización del film documental <b>"La Educación Prohibida"</b> y luego, a partir de 2012, iniciamos <b>Reevo</b> y nos dedicamos a conocer, investigar y promover experiencias educativas alternativas en Ibeoramérica y el mundo.'))?>
								<button id="hacemos-extra-btn" type="button" class="btn btn-primary btn-xs" data-toggle="collapse" data-target="#hacemos-extra-txt">
									<span class="glyphicon glyphicon-chevron-down"></span> <?php echo sprintf(_('Mostrar más'))?>
								</button></h3>
								<div id="hacemos-extra-txt" class="collapse">
									<p><?php echo sprintf(_('Nos proponemos tejer vínculos de intercambio a través de encuentros territoriales y virtuales, así como espacios de formación y aprendizaje que sean abiertos y gratuitos; producimos contenidos libres desde una mirada crítica y proactiva; fomentamos la acción social y colectiva para promover transformaciones en educación a diversas escalas.</p><p>Buscamos construir una red de personas que se aleje de los modelos convencionales, conformando equipos y espacios de trabajo autónomos no coercitivos o jerárquicos. Hacemos Reevo porque consideramos necesaria su existencia, pero también porque lo disfrutamos.</p>'))?>
								</div>
							</span>
						</div>
					</div>
			</div>
		</div>
	</section>

	<section id="section2" class="cd-section">
		<div>
			<div class="container">
				<div class="row" id="row-frase">
					<div class="col-md-1"></div>
					<div class="col-md-10">
						<h3 id="titulo-frase" class="shake"><?php echo sprintf(_('La educacion no cambia el mundo, <br/>cambia a las personas que van a cambiar el mundo.<span>Paulo Freire</span>'))?></h3>
					</div>
					<div class="col-md-1"></div>
				</div>

			  <div class="row" id="row-jumbotron">
			    <div class="col-md-1"></div>
			    <div class="col-md-10 vertical-center">
			      <div id="jumbotron-form" class="jumbotron shake col-md-12">
			        <!-- Inicio de acordeon  -->
			        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
			          <!-- Caja de aporte individual  -->
			          <div class="panel panel-default">
			            <div class="panel-heading" role="tab" id="headingOne">
			              <h4 class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
			                  <?php echo sprintf(_('Membresía'))?> <b><?php echo sprintf(_('Individual'))?></b>
			              </h4>
			            </div>
			            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
			              <div class="panel-body">
                      <div class="row">
                        <div id="" class="form-group">
				                  <label class="col-sm-12" for="textinput"><?php echo sprintf(_('Para amigos, educadores y activistas.'));?></label><br/>
                        </div>
                      </div>

											<div class="row">
				                <div id="donation-radio" class="form-group">
				                  <label class="col-md-2 col-sm-12 control-label" for="textinput"><?php echo sprintf(_('Monto'));?></label>
				                  <div class="btn-group col-md-8 col-sm-10" data-toggle="buttons">
				              			<label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Estarás apoyando el sostenimiento diario de las herramientas de Reevo (la red social, el mapa, la difusión en redes)'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Con u$d 5 mensuales'))?>" data-trigger="hover">
				              				<input type="radio" name="inlineRadioOptions" class="radio-inline-element" id="inlineRadio1" value="5"> u$d 5
				              			</label>
				              			<label class="btn btn-default active tooltip-on" data-content="<?php echo sprintf(_('Estarás contribuyendo con el desarrollo de contenidos en la Revista de Reevo y nuevas funcionalidades en nuestras herramientas.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Con u$d 10 mensuales'))?>" data-trigger="hover">
				              				<input type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="10"> u$d 10
				              			</label>
                            <label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Estarás apoyando la organización de eventos abiertos y gratuitos, y el abordaje de nuevas lineas de investigación.'))?>" rel="popover" data-placement="bottom" data-original-title="Con u$d 20 mensuales" data-trigger="hover">
				                      <input  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="20"> u$d 20
				              			</label>
				              			<label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Estarás contribuyendo significativamente con el crecimiento de nuestro equipo dedicado de trabajo y el desarrollo de proyectos/acciones de incidencia en la realidad diaria de proyectos educativos alternativos.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Con u$d 50 mensuales'))?>" data-trigger="hover">
				              				<input  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio4" value="50"> u$d 50
				              			</label>
                            <label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Tu aporte asegurará la estabilidad, crecimiento y expansión de la Red.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Con u$d 100 mensuales'))?>" data-trigger="hover">
				              				<input  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio4" value="100"> u$d 100
				              			</label>
				              			<!-- <label class="btn btn-default" rel="popover" data-placement="bottom" data-original-title="Title" data-trigger="hover">
				              				<input id="donation-radio-extra-radio"  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio5" value="100"> <?php echo sprintf(_('+ Mas'));?>
				              			</label> -->
				                  </div>

				                  <div class="col-md-2 col-sm-2">
				                    <button id="donar-1" type="button" class="btn btn-primary" onclick="redirect('1')"><?php echo sprintf(_('Donar'))?></button>
				                  </div>
				                </div>
											</div> <!-- fin del row -->

											<div class="row">
												<div id="donation-radio-extra" class="form-group">
											    <label class="col-md-2 control-label" for="textinput"><?php echo sprintf(_('Ingresa otro valor'));?></label>
											    <div class="col-md-10">
														<div class="input-group">
														  <span id="donation-radio-extra-input-icon" class="input-group-addon">u$d</span>
											        <input data-min="51" value="100" name="donation_box" id="donation-radio-extra-input-1" type="text" class="form-control donation-radio-extra-input" aria-describedby="donation-radio-extra-input-icon">
														</div>
											    </div>
											  </div>
											</div>

											<div class="row beneficios">
												<label class="col-md-2 col-sm-12 control-label" for="textinput"><?php echo sprintf(_('Beneficios'));?></label>
											  <div class="col-md-9">
													<span class="tooltip-on" data-content="<?php echo sprintf(_('Recibirás novedades y actualizaciones de forma regular sobre el desarrollo del proyecto.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Reportes periódicos'))?>" data-trigger="hover"><i class="glyphicon glyphicon-bullhorn" aria-hidden="true"></i> <?php echo sprintf(_('Reportes'))?></span>
													<span class="tooltip-on" data-content="<?php echo sprintf(_('Tendras acceso a nuestras producciones de forma anticipada en diversos soportes.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Contenidos anticipados'))?>" data-trigger="hover"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i> <?php echo sprintf(_('Contenidos anticipados'))?></span>
													<span class="tooltip-on" data-content="<?php echo sprintf(_('Asesoramiento y apoyo directo de parte de referentes de educación alternativa de diversas partes del mundo.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Asesoramiento de expertos'))?>" data-trigger="hover"><i class="glyphicon glyphicon-briefcase" aria-hidden="true"></i> <?php echo sprintf(_('Asesoramiento'))?></span>
												</div>
											</div>

			              </div>
			            </div>
			          </div>
			          <!-- FIN: Caja de aporte individual  -->
 			          <!-- Caja de aporte colectivo  -->
			          <div class="panel panel-default">
			            <div class="panel-heading" role="tab" id="headingTwo">
			              <h4 class="panel-title collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
 			                  <?php echo sprintf(_('Membresía'))?> <b><?php echo sprintf(_('Colectiva'))?></b>
			              </h4>
			             </div>
			            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
			              <div class="panel-body">

			               <div class="row">
			               <div id="" class="form-group">
				                  <label class="col-sm-12" for="textinput"><?php echo sprintf(_('Para proyectos educativos, instituciones, colectivos, grupos, organizaciones sociales.'));?></label><br/>
			              </div>
			             </div>
											<div class="row">

				                <div id="donation-radio-2" class="form-group">
				                          <label class="col-md-2 col-sm-12 control-label" for="textinput"><?php echo sprintf(_('MONTO'));?></label>
				                 <div class="btn-group col-md-8 col-sm-10" data-toggle="buttons">
				              			<label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Estarás apoyando el sostenimiento diario de las herramientas de Reevo (la red social, el mapa, la difusión en redes) y contribuyendo con el desarrollo de contenidos en la Revista de Reevo y nuevas funcionalidades en nuestras herramientas.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Con u$d 10 mensuales'))?>" data-trigger="hover">
				              				<input type="radio" name="inlineRadioOptions" class="radio-inline-element" id="inlineRadio1" value="10"> u$d 10
				              			</label>
				              			<label class="btn btn-default active tooltip-on" data-content="<?php echo sprintf(_('Estarás apoyando la organización de eventos abiertos y gratuitos, y el abordaje de nuevas lineas de investigación.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Con u$d 20 mensuales'))?>" data-trigger="hover">
				              				<input type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio2" value="20"> u$d 20
				              			</label>
				              			<label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Estarás contribuyendo significativamente con el crecimiento de nuestro equipo dedicado de trabajo y el desarrollo de proyectos/acciones de incidencia en la realidad diaria de proyectos educativos alternativos.'))?>" rel="popover" data-placement="bottom" data-original-title="Con u$d 50 mensuales" data-trigger="hover">
				                      <input  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="50"> u$d 50
				              			</label>
                            <label class="btn btn-default tooltip-on" data-content="<?php echo sprintf(_('Tu aporte asegurará la estabilidad, crecimiento y expansión de la Red.'))?>" rel="popover" data-placement="bottom" data-original-title="Con u$d 100 mensuales" data-trigger="hover">
				                      <input  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio3" value="100"> u$d 100
				              			</label>
				              			<!-- <label class="btn btn-default" rel="popover" data-placement="bottom" data-original-title="Title" data-trigger="hover">
				              				<input id="donation-radio-extra-radio-2"  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio5" value="100"> <?php echo sprintf(_('+ Mas'));?>
				              			</label> -->
				                  </div>

		                  		<div class="col-md-2 col-sm-4">
				                    <button type="button" class="btn btn-primary" onclick="redirect('2')"><?php echo sprintf(_('Donar'))?></button>
				                  </div>
				                </div>
											</div> <!-- fin del row -->

											<div class="row">
												<div id="donation-radio-extra-2" class="form-group">
													<label class="col-md-2 control-label" for="textinput"><?php echo sprintf(_('Ingresa otro valor'));?></label>
													<div class="col-md-10">
														<div class="input-group">
															<span class="input-group-addon">u$d</span>
															<input id="donation-radio-extra-input-2" type="text" class="form-control" data-min="51" value="100" name="donation_box2">
														</div>
													</div>
												</div>
											</div>

											<div class="row beneficios">
												<label class="col-md-2 col-sm-12 control-label" for="textinput"><?php echo sprintf(_('Beneficios'));?></label>
												<div class="col-md-9">
													<span class="tooltip-on" data-content="<?php echo sprintf(_('Recibirás novedades y actualizaciones de forma regular sobre el desarrollo del proyecto.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Reportes periódicos'))?>" data-trigger="hover"><i class="glyphicon glyphicon-bullhorn" aria-hidden="true"></i> Reportes</span>
													<span class="tooltip-on" data-content="<?php echo sprintf(_('Tendrás acceso a nuestras producciones de forma anticipada en diversos soportes.'))?>" rel="popover" data-placement="bottom" data-original-title="<?php echo sprintf(_('Contenidos anticipados'))?>" data-trigger="hover"><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i> <?php echo sprintf(_('Contenidos anticipados'))?></span>
													<span class="tooltip-on" data-content="<?php echo sprintf(_('Asesoramiento y apoyo directo de parte de referentes de educación alternativa de diversas partes del mundo.'))?>" rel="popover" data-placement="bottom" data-original-title="Asesoramiento de expertos" data-trigger="hover"><i class="glyphicon glyphicon-briefcase" aria-hidden="true"></i> <?php echo sprintf(_('Asesoramiento'))?></span>
													<span class="tooltip-on" data-content="<?php echo sprintf(_('El proyecto tendrá un registro destacado como miembro en el Mapa de Reevo.'))?>" rel="popover" data-placement="bottom" data-original-title="Registro destacado en el Mapa" data-trigger="hover"><i class="glyphicon glyphicon-certificate" aria-hidden="true"></i> <?php echo sprintf(_('Reconocimiento'))?></span>
												</div>
											</div>
			              </div>
			            </div>
			          </div> <!-- FIN: Caja de aporte por proyectos  -->
			        </div>
			      </div>
			    </div>
			    <div class="col-md-1"></div>
			  </div>
			</div>
	</section>

	 <section id="section3" class="cd-section">
		<div id="cuentas" class="row" style="">
      <div class="col-md-2"></div>
			<div id="presupuesto" class="col-xs-12 col-sm-6 col-md-4 vertical-center">

				<div class="jumbotron shake col-md-6">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading" id="headingOne">
								<h4 class="panel-title">
								<?php echo sprintf(_('Presupuesto 2017'))?>
								</h4>
							</div>
							<div  class="presupuesto">
								<div class="pieID pie"></div>
								<ul class="pieID legend">
								  <li>
								   <em class="tooltip-on" data-content="<?php echo sprintf(_('Acciones concretas para lograr los diferentes objetivos en una escala territorial. Organización y participación de eventos y encuentros, sistematización de talleres y materiales de formación, apoyo, acompañamiento y articulación de proyectos educativos.'))?>" rel="popover" data-original-title="16.61% - u$d 5900.- <?php echo sprintf(_('anuales'))?> " data-placement="bottom" data-trigger="hover"><?php echo sprintf(_('Activismo'))?></em>
								    <span>9950</span>
								  </li>
								  <li>
								    <em class="tooltip-on" data-content="<?php echo sprintf(_('Desarrollo y sostenimiento de estrategias y dispositivos de comunicación del proyecto: la edición de la Revista Digital, divulgación redes sociales, producción audiovisual, identidad gráfica y envios masivos.'))?>" rel="popover" data-original-title="33.42% - u$d 11900.- <?php echo sprintf(_('anuales'))?> " data-placement="bottom" data-trigger="hover"><?php echo sprintf(_('Comunicación'))?></em>
								    <span>11900</span>
								  </li>
								  <li>
                    <em class="tooltip-on" data-content="<?php echo sprintf(_('Seguimiento de novedades editoriales, producción de conocimientos, cuidado del mapeo colectivo y planificación del centro de contenidos, además de iniciativas vinculadas con el campo académico-científico.'))?>" rel="popover" data-original-title="13.49% - u$d 4800.- <?php echo sprintf(_('anuales'))?> " data-placement="bottom" data-trigger="hover"><?php echo sprintf(_('Investigación'))?></em>
								    <span>4800</span>
								  </li>
								  <li>
								     <em class="tooltip-on" data-content="<?php echo sprintf(_('Mantenimiento y desarrollo de herramientas virtuales, tanto el pago de servicios y administración de la infraestructura tecnológica como el desarrollo de nuevas herramientas de software.'))?>" rel="popover" data-original-title="16.61% - u$d 5900.- <?php echo sprintf(_('anuales'))?> " data-placement="bottom" data-trigger="hover"><?php echo sprintf(_('Desarrollo Web'))?></em>
								    <span>5900</span>
								  </li>
								  <li>
								     <em class="tooltip-on" data-content="<?php echo sprintf(_('Administración y diseño de herramientas para la generación de estrategias y proyectos que permitan la obtención de recursos, así como los gastos administrativos e impositivos de la organización.'))?>" rel="popover" data-original-title="33.42% - u$d 3000.- <?php echo sprintf(_('anuales'))?> " data-placement="bottom" data-trigger="hover"><?php echo sprintf(_('Financiamiento'))?></em>
								    <span>3000</span>
								  </li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
      <div class="col-md-1"></div>
			<div id="ultimos_miembros" class="col-xs-12 col-sm-6 col-md-3 vertical-center">
				<div class="jumbotron shake col-md-6">
					<div class="panel-group">
						<div class="panel panel-default">
							<div class="panel-heading" id="headingOne">
								<h4 class="panel-title">
								<?php echo sprintf(_('Últimos Miembros'))?>
								</h4>
							</div>
							<div class="miembros">
  							<ul id="ultimosmiembros" class="list-group" style="list-style-type: none;"></ul>
                  <script type="text/javascript">
                    $(document).ready(function(e) {
                      $.getJSON("tmp/miembros.json", function(json) {
                          var limite = 5;
                          var arr = [];
                          // for(var i in json) {
                          //    var n = json[i]['name'];
                          //    var t = json[i]['type'];
                          //    var d = json[i]['date'].split("-");
                          //    arr.push({"id": i, "name": n, "type": t, "date":(new Date(d[0],d[1]-1,d[2]))});
                          // }
                          // arr.sort(function(a,b) { return a.date.valueOf() > b.date.valueOf();});
                          // arr.reverse();
                          for (var i = 0; i < limite; i++) {
                            switch(json[i]['type']) {
                              case 'Individual':
                                  var g = 'user';
                                  break;
                              case 'Organization':
                                  var g = 'home';
                                  break;
                            }
                            $( "#ultimosmiembros" ).append( '<li class="list-group-item" title="'+json[i]['date']+'"><i class="glyphicon glyphicon-'+g+'" aria-hidden="true"></i> '+json[i]['name']+'</li>' );
                          };

                          for (var j = 0; j < 1000; j++) {
                            switch(json[j]['type']) {
                              case 'Individual':
                                  $( "#ultimosmiembros_individuos" ).append( '<li title="'+json[j]['date']+'">'+json[j]['name']+'</li>' );
                                  break;
                              case 'Organization':
                                  $( "#ultimosmiembros_colectivos" ).append( '<li title="'+json[j]['date']+'">'+json[j]['name']+'</li>' );
                                  break;
                            }
                          }

                      });
                    });
                  </script>
                 <button type="button" class="btn btn-primary pull-right" data-toggle="modal" data-target="#myModal"><?php echo sprintf(_('Ver listado completo'));?></button>
							</div>
						</div>
					</div>
				</div>
			</div>
      <div class="col-md-1"></div>
		</div>
	</section>

	<nav>
		<ul class="cd-vertical-nav">
			<li id="nav-prev"><a href="#0" class="cd-prev inactive">¿POR QUE DONAR?<br/><span class="glyphicon glyphicon-chevron-up"></span></a></li>
			<li id="nav-next"><a href="#0" class="cd-next"><span class="glyphicon glyphicon-chevron-down"></span><br/>DONA AHORA</a></li>
		</ul>
	</nav> <!-- .cd-vertical-nav -->

</body>
<script>
function redirect(titulo) {
	monto = document.getElementById('donation-radio-extra-input-'+titulo).value;
  source = '<?php echo $source; ?>';
	url = '/form.php?l=<?php echo $lang; ?>&hide=1&tipo=1&monto=' + monto + '&titulo=' + titulo + '&source=' + source;
	window.location.replace(url);
	//alert(url);
}
</script>
<script src="js/index.js"></script>
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo sprintf(_('Todos nuestros miembros'));?></h4>
      </div>
      <div class="modal-body">
        <div class="col-sm-6 col-xs-12">
          <h4><i class="glyphicon glyphicon-user" aria-hidden="true"></i> <?php echo sprintf(_('Individuos'));?></h4>
          <ul id="ultimosmiembros_individuos"></ul>
        </div>
        <div class="col-sm-6 col-xs-12">
          <h4><i class="glyphicon glyphicon-home" aria-hidden="true"></i> <?php echo sprintf(_('Colectivos'));?></h4>
          <ul id="ultimosmiembros_colectivos"></ul>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo sprintf(_('Cerrar'));?></button>
      </div>
    </div>

  </div>
</div>

</html>
