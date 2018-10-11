<?php
error_log('Cargado!!', 0);
$crm = '/srv/reevo-web/www/crm/civicrm.settings.php';

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


if (file_exists($crm)) {
    // Solo carga las funciones si tenemos el CRM
    include("funciones.php");
}


?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo sprintf(_('Reevo: Tejiendo alternativas en educación'));?></title>
  <link rel="icon" href="http://assets.reevo.org/logo/favicon/favicon.ico">

	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300italic,300,700,700italic' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Lato:400,700,900' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/bootstrap-min.css">
  <link rel="stylesheet" href="css/bootstrap-theme-flatly-min.css">
	<link rel="stylesheet" href="css/bootstrap-formhelpers-min.css" media="screen">
	<link rel="stylesheet" href="css/bootstrapValidator-min.css"/>
	<link rel="stylesheet" href="css/bootstrap-select.css"/>
  <link rel="stylesheet" href="css/bootstrap-datepicker.css"/>
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" />
	<link rel="stylesheet" href="css/bootstrap-side-notes.css" />
  <link rel="stylesheet" href="css/intlTelInput.css">
  <link rel="stylesheet" href="css/style-form.css">

	<!-- Usado para permitir la carga de provincias/estados del CRM -->
	<script src="js/jquery-2.1.4.js"></script>
  <!-- <script type="text/javascript" src="js/crm-assets/jquery-1.11.1.min.js"></script> -->


<?php if (file_exists($crm)) { ?>
  <!-- libreria para cargar dinamicamente las provincias -->
  <script type="text/javascript" src="js/crm-loadstates/loadstates.js"></script>
  <script type="text/javascript">
     $( document ).ready(function() {
       IniciaCRMLoadStates('country-1','state_province-1','1010');
       IniciaCRMLoadStates('organization_country-1','organization_state_province-1','1010');
     });
  </script>
<?php }?>



<!-- 	Componentes de bootstrap  -->
	<script src="js/bootstrap-min.js"></script>
	<!-- <script src="js/bootstrap-formhelpers-min.js"></script> -->
	<script type="text/javascript" src="js/bootstrapValidator-min.js"></script>
  <!-- <script src="js/bootstrap-datepicker.min.js"></script> -->
  <script src="js/moment.js"></script>
  <script src="js/combodate.js"></script>
	<script src="js/bootstrap-select.js"></script>
  <script src="js/intlTelInput.js"></script>
  <!-- <script src="js/phone.js"></script> -->


	<script type="text/javascript">
      // When the document is ready
    // $(document).ready(function () {
    //   $('#birthdate').datepicker({
    //   format: "mm/dd/yyyy",
    //   language: "es"
    //   });
    // });



    function PagoSegunPais() {
      if ($("#country-1").val() == '1010') {
        console.log('Argentina');
        $( "#medio-radio-tarjetamp-lbl" ).click();
        $( "#medio-radio-tarjetamp-lbl" ).show();

        $( "#medio-radio-tarjeta-lbl" ).hide();
        $( "#medio-radio-paypal-lbl" ).hide();

      } else {
        $( "#medio-radio-paypal-lbl" ).click();
        $( "#medio-radio-paypal-lbl" ).show();

        $( "#medio-radio-tarjeta-lbl" ).hide();
        $( "#medio-radio-tarjetamp-lbl" ).hide();

      }

    }



    $(document).ready(function() {

      $('#payment-form')
          .find('[name="phone"]')
              .intlTelInput({
                  utilsScript: '/js/utils.js',
                  autoPlaceholder: true,
                  initialCountry: "auto",
                  geoIpLookup: function(callback) {
                    $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                      var countryCode = (resp && resp.country) ? resp.country : "";
                      callback(countryCode);
                    });
                  },
              });

      $('#payment-form')
          .find('[name="organization_phone"]')
              .intlTelInput({
                  utilsScript: '/js/utils.js',
                  autoPlaceholder: true,
                  initialCountry: "auto",
                  // geoIpLookup: function(callback) {
                  //   $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                  //     var countryCode = (resp && resp.country) ? resp.country : "";
                  //     callback(countryCode);
                  //   });
                  // },
              });

      $('#payment-form')
        .on('click', '.country-list', function() {
            $('#phone').trigger('input');
        });
      $('#payment-form')
        .on('click', '.country-list', function() {
            $('#organization_phone').trigger('input');
        });


      $( "#country-1" ).change(function() {
        PagoSegunPais();
      });

			$('#payment-form').bootstrapValidator({
				message: '<?php echo sprintf(_('Este valor no es válido'));?>',
				feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
// 			submitHandler: function(validator, form, submitButton) {
// 	                    var chargeAmount = 3000; //amount you want to charge, in cents. 1000 = $10.00, 2000 = $20.00 ...
// 	                    // createToken returns immediately - the supplied callback submits the form if there are no errors
// 	                    alert("submit");
// 	                    return false; // submit from callback
// 	        },
        fields: {
            street: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
										stringLength: {
                        min: 6,
                        max: 96,
                        message: '<?php echo sprintf(_('Este campo debe tener entre 6 y 96 carácteres'));?>'
                    }
                }
            },
            city: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    }
                }
            },
						zip: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
										stringLength: {
                        min: 3,
                        max: 9,
                        message: '<?php echo sprintf(_('Este campo debe tener entre 3 y 9 carácteres'));?>'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
                    emailAddress: {
                        message: '<?php echo sprintf(_('El ingresado no es un e-mail válido'));?>'
                    },
												stringLength: {
                        min: 6,
                        max: 65,
                        message: '<?php echo sprintf(_('Este campo debe tener entre 6 y 65 carácteres'));?>'
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
                    callback: {
                        message: '<?php echo sprintf(_('El número ingresado no es válido, asegurate de seleccionar correctamente el país y el formato indicado'));?>',
                        callback: function(value, validator, $field) {
                          return value === '' || $("#phone").intlTelInput('isValidNumber');
                        }
                    }
                }
            },
            // birthdate: {
            //     validators: {
            //         notEmpty: {
            //           message: '<?php echo sprintf(_('Debe completar este campo'));?>'
            //         },
            //         date: {
            //           format: 'MM/DD/YYYY',
            //           message: '<?php echo sprintf(_('La fecha ingresada no es válida, debe estar en el formato MM/DD/AAAA'));?>'
            //         }
            //     }
            // },
            organization_name: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    }
                }
            },
            organization_street_address: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
										stringLength: {
                        min: 6,
                        max: 96,
                        message: '<?php echo sprintf(_('Este campo debe tener entre 6 y 96 carácteres'));?>'
                    }
                }
            },
            organization_city: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    }
                }
            },
						organization_zipcode: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
										stringLength: {
                        min: 3,
                        max: 9,
                        message: '<?php echo sprintf(_('Este campo debe tener entre 3 y 9 carácteres'));?>'
                    }
                }
            },
            organization_email: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
                    emailAddress: {
                        message: '<?php echo sprintf(_('El ingresado no es un e-mail válido'));?>'
                    },
										stringLength: {
                        min: 6,
                        max: 65,
                        message: '<?php echo sprintf(_('Este campo debe tener entre 6 y 65 carácteres'));?>'
                    }
                }
            },
            organization_phone: {
              validators: {
                  notEmpty: {
                      message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                  },
                  callback: {
                      message: '<?php echo sprintf(_('El número ingresado no es válido, asegurate de seleccionar correctamente el país y respetar el formato indicado'));?>',
                      callback: function(value, validator, $field) {
                        return value === '' || $("#organization_phone").intlTelInput('isValidNumber');
                      }
                  }
              }
            },
            donation_box: {
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('No puede estar vacío'));?>'
                    },
                }
            },
						cardnumber: {
								selector: '#cardnumber',
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('Debe completar este campo'));?>'
                    },
					creditCard: {
						message: '<?php echo sprintf(_('El número de tarjeta ingresado no es válido'));?>'
					},
                }
            },
						expMonth: {
                selector: '[data-stripe="exp-month"]',
                validators: {
                    notEmpty: {
                        message: 'The expiration month is required'
                    },
                    digits: {
                        message: 'The expiration month can contain digits only'
                    },
                    callback: {
                        message: '<?php echo sprintf(_('Tarjeta expirada'));?>',
                        callback: function(value, validator) {
                            value = parseInt(value, 10);
                            var year         = validator.getFieldElements('expYear').val(),
                                currentMonth = new Date().getMonth() + 1,
                                currentYear  = new Date().getFullYear();
                            if (value < 0 || value > 12) {
                                return false;
                            }
                            if (year == '') {
                                return true;
                            }
                            year = parseInt(year, 10);
                            if (year > currentYear || (year == currentYear && value > currentMonth)) {
                                validator.updateStatus('expYear', 'VALID');
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                }
            },
            expYear: {
                selector: '[data-stripe="exp-year"]',
                validators: {
                    notEmpty: {
                        message: 'The expiration year is required'
                    },
                    digits: {
                        message: 'The expiration year can contain digits only'
                    },
                    callback: {
                        message: '<?php echo sprintf(_('Tarjeta expirada'));?>',
                        callback: function(value, validator) {
                            value = parseInt(value, 10);
                            var month        = validator.getFieldElements('expMonth').val(),
                                currentMonth = new Date().getMonth() + 1,
                                currentYear  = new Date().getFullYear();
                            if (value < currentYear || value > currentYear + 100) {
                                return false;
                            }
                            if (month == '') {
                              validator.updateStatus('expMonth', 'VALID');
                                return false;
                            }
                            month = parseInt(month, 10);
                            if (value > currentYear || (value == currentYear && month > currentMonth)) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                }
            },
			cvv: {
		selector: '#cvv',
                validators: {
                    notEmpty: {
                        message: '<?php echo sprintf(_('No puede estar vacío'));?>'
                    },
					cvv: {
                        message: '<?php echo sprintf(_('El código de seguridad ingresado no es válido'));?>',
                        creditCardField: 'cardnumber'
                    }
                }
            },
        }
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

    // Slider Informacion de Pago con Tarjeta

    $("#medio-radio input:radio").change(function () {
      if ($("#medio-radio-tarjeta").is(":checked")) {
        // $( "#medio-radio-extra" ).show(400,"swing");
        $( "#medio-radio-extra" ).slideDown();
        $( "#medio-radio-extra1" ).slideUp();
        $( "#medio-radio-extra2" ).slideUp();
      };

      if ($("#medio-radio-tarjetamp").is(":checked")) {
        // $( "#medio-radio-extra" ).show(400,"swing");
        $( "#medio-radio-extra2" ).slideDown();
        $( "#medio-radio-extra1" ).slideUp();
        $( "#medio-radio-extra" ).slideUp();
      };

      if ($("#medio-radio-paypal").is(":checked")) {
        // $( "#medio-radio-extra" ).show(400,"swing");
        $( "#medio-radio-extra1" ).slideDown();
        $( "#medio-radio-extra2" ).slideUp();
        $( "#medio-radio-extra" ).slideUp();
      };
    });



        // Slider Tipo de Membresía
    if ($("#donationname-radio-individual").is(":checked")) {
      $( ".donationname-radio-colectivo-extra" ).slideUp(0);
    };

    $("#donationname-radio input:radio").change(function () {
      if ($("#donationname-radio-individual").is(":checked")) {
        // $( "#medio-radio-extra" ).show(400,"swing");
        $( "#donationname-radio-individual-extra" ).slideDown();
        $( ".donationname-radio-colectivo-extra" ).slideUp();
      } else {
        // $( "#medio-radio-extra" ).hide(400,"swing");
        $( ".donationname-radio-colectivo-extra" ).slideDown();
        $( "#donationname-radio-individual-extra" ).slideUp();
      }
    });

    <?php if ($_GET['monto']) {
      echo '$("#donation-radio-extra-input").val( "'.$_GET['monto'].'" );';
    } else {
      echo '$("#donation-radio-extra-input").val( "10" );';
    }
    ?>
    $("#donation-radio .btn-group").change(function () {
      x = $("#donation-radio input[type='radio']:checked").val();
      $("#donation-radio-extra-input").val( x );
    })


    // Habilitar tooltips para opciones de donación
    $(".tooltip-on").popover({ trigger: "hover" });

    PagoSegunPais();

    $( "#payment-form" ).submit(function( event ) {

      var match = $('#email').val() == $('#organization_email').val();
      if (match) {
        alert('<?php echo sprintf(_('¡ERROR! El correo electrónico de la organización debe ser diferente al de su representante.'))?>');
        event.preventDefault();
      }

      // formate los telefonos correctamente
      $("#phone").val($("#phone").intlTelInput("getNumber"));
      $("#organization_phone").val($("#phone").intlTelInput("getNumber"));

    });

  });

	</script>

</head>
<body id="crm-container">

  <h3 id="titulo" class="shake"><span><?php echo sprintf(_('Campaña de financiamiento colectivo 2017 de Reevo'))?>:</span><?php echo sprintf(_('Tejiendo alternativas en educación'))?></h3>

<?php
if ($_POST) {
  if ($donationtype = 1) { // Mensaje si es membresía
    echo '<div class="col-md-8 col-md-offset-2"><div class="jumbotron">'.sprintf(_('gracias-membresia')).'</div></div>';

  } else { // Mensaje si es donacion
    echo '<div class="col-md-8 col-md-offset-2"><div class="jumbotron">'.sprintf(_('gracias-donacion')).'</div></div>';

  }
} else {

?>


<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="payment-form" class="form-horizontal">
  <div class="row row-centered">
  <div class="col-md-8 col-md-offset-2">
    <!-- <h1><?php //echo $i18n->getLabel("Servicios'));?></h1> -->



  <noscript>
    <div class="bs-callout bs-callout-danger">
      <h4><?php echo sprintf(_('JavaScript no está habilitado!'));?></h4>
      <p><?php echo sprintf(_('Este formulario de donaciones require que tenga activado JavaScript en su navegador. Activelo y recargue esta página. Revisa <a href="http://enable-javascript.com" target="_blank">este sitio</a> para saber cómo hacerlo.'));?></p>
    </div>
  </noscript>

  <fieldset>
    <?php

  	// The value of the variable name is found


  	?>
  <?php if ($_GET['hide']) {	echo '<div class="hide">'; } ?>
    <legend><?php echo sprintf(_('Su donación a Reevo'));?></legend>

    <!-- Opciones de donación -->
    <div id="donation-radio" class="form-group">
      <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Quiero donar'));?></label>
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

        <!-- <label <?php if (!$_GET['donar']) {	echo 'style="display: none !important"'; } ?> class="btn btn-default" rel="popover" data-placement="bottom" data-original-title="Title" data-trigger="hover">
          <input id="donation-radio-extra-radio"  type="radio" name="inlineRadioOptions" class="radio-inline-element" type="radio" name="inlineRadioOptions" id="inlineRadio5" value="100"> <?php echo sprintf(_('+ Mas'));?>
        </label> -->
      </div>
    </div>
    <div id="donation-radio-extra" class="form-group">
      <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Ingresa otro valor'));?></label>
      <div class="col-sm-6">
        <div class="input-group">
          <span class="input-group-addon">u$d</span>
          <input id="donation-radio-extra-input" type="text" class="form-control bfh-number" data-min="51" value="100" name="donation_box">
        </div>
      </div>
    </div>

    <!-- Donación recurrente o única -->
    <div <?php if ($_GET['donar']) {	echo 'style="display: block !important"'; } ?> id="donationtype-radio" class="form-group">
      <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('De forma'));?></label>
      <div class="btn-group col-sm-8" data-toggle="buttons">
  			<label class="btn btn-default tooltip-on <?php if ($_GET['tipo'] != '2') {	echo 'active'; } ?>" data-content="<?php echo sprintf(_('Se debitará el monto todos los meses, puedes cancelarlo cuando lo desees.'));?>" rel="popover" data-placement="bottom" data-trigger="hover">
  				<input  type="radio" name="donationtype_radio_option" class="radio-inline-element" id="inlineRadio1" value="1" checked="true"> <?php echo sprintf(_('Mensual'));?></input>
  			</label>
  			<label class="btn btn-default  tooltip-on <?php if ($_GET['tipo'] == '2') {	echo 'active'; } ?>" data-content="<?php echo sprintf(_('Donar una sola vez'));?>" rel="popover" data-placement="bottom" data-trigger="hover">
  				<input  type="radio" name="donationtype_radio_option" class="radio-inline-element" type="radio" id="inlineRadio2" value="2"> <?php echo sprintf(_('Única'));?></input>
  			</label>
      </div>
    </div>

     <!-- Donación individual o colectiva -->
    <div id="donationname-radio" class="form-group">
      <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('A título'));?></label>
      <div class="btn-group col-sm-8" data-toggle="buttons">
  			<label class="btn btn-default tooltip-on <?php if ($_GET['titulo'] != '2') {	echo 'active'; } ?>" data-content="<?php echo sprintf(_('La donación es a título personal.'));?>" rel="popover" data-placement="bottom" data-trigger="hover">
  				<input  type="radio" name="titulo_radio_option" class="radio-inline-element" id="donationname-radio-individual" value="1" <?php if ($_GET['titulo'] != '2') {	echo 'checked="true"'; } ?>> <?php echo sprintf(_('Individual'));?></input>
  			</label>
  			<label class="btn btn-default  tooltip-on <?php if ($_GET['titulo'] == '2') {	echo 'active'; } ?>" data-content="<?php echo sprintf(_('La donación es en nombre de un proyecto o institución'));?>" rel="popover" data-placement="bottom" data-trigger="hover">
  				<input  type="radio" name="titulo_radio_option" class="radio-inline-element" id="donationname-radio-colectiva" value="2" <?php if ($_GET['titulo'] == '2') {	echo 'checked="true"'; } ?>> <?php echo sprintf(_('Colectivo'));?></input>
  			</label>
      </div>
    </div>


  <?php if ($_GET['hide']) {	echo '</div>'; } ?>

<div class="donationname-radio-colectivo-extra">

<legend><?php echo sprintf(_('Datos del proyecto'));?></legend>
 <!-- Nombre del Proyecto -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Nombre del Proyecto'));?></label>
    <div class="col-sm-6">
      <input type="text" name="organization_name" placeholder="<?php echo sprintf(_('Nombre del Proyecto'));?>" class="form-control">
    </div>
  </div>

    <!-- Email -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Correo electrónico'));?></label>
    <div class="col-sm-6">
      <input type="text" id="organization_email" name="organization_email" maxlength="65" placeholder="<?php echo sprintf(_('E-mail del proyecto'));?>" class="email form-control">
    </div>
  </div>

    <!-- Phone -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Teléfono'));?></label>
    <div class="col-sm-6">
      <input id="organization_phone" type="tel" name="organization_phone" maxlength="65" class="phone form-control">
    </div>
  </div>

   <!-- Country -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('País'));?></label>
    <div class="col-sm-6">
				  <select id="organization_country-1" name="organization_country_id" class="form-control">
				  </select>
      <!--input type="text" name="country" placeholder="Country" class="country form-control"-->
<!--       <div class="country bfh-selectbox bfh-countries" name="country_id" placeholder="Select Country" data-flags="true" data-filter="true"> </div> -->
    </div>
  </div>

  <!-- State -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Estado / Provincia'));?></label>
    <div class="col-sm-6">
<!--       <input type="text" name="state_id" maxlength="65" placeholder="State" class="state form-control"> -->
      <select id="organization_state_province-1" name="organization_state_id" class="state form-control"></select>
    </div>
  </div>

  <!-- City -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Ciudad'));?></label>
    <div class="col-sm-6">
      <input type="text" name="organization_city" placeholder="<?php echo sprintf(_('Ciudad'));?>" class="city form-control">
    </div>
  </div>

  <!-- Street -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Calle'));?></label>
    <div class="col-sm-6">
      <input type="text" name="organization_street_address" placeholder="<?php echo sprintf(_('Incluya también númeración y otros detalles'));?>" class="address form-control">
    </div>
  </div>


  <!-- Postcal Code -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Código Postal'));?></label>
    <div class="col-sm-6">
      <input type="text" name="organization_zipcode" maxlength="9" placeholder="<?php echo sprintf(_('Código Postal'));?>" class="zip form-control">
    </div>
  </div>




</div>



<div>

  <legend id="donationname-radio-individual-extra" class="<?php if ($_GET['titulo'] == '2') {	echo 'hide'; }?>"><?php echo sprintf(_('Sus datos personales'));?></legend>

  <legend class="donationname-radio-colectivo-extra<?php if ($_GET['titulo'] == '2') {	echo '1'; }?>"><?php echo sprintf(_('Datos del Representante'));?></legend>

  <!-- Name -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Nombre'));?></label>
    <div class="col-sm-6">
      <input type="text" name="first_name" placeholder="<?php echo sprintf(_('Nombre'));?>" class="form-control">
    </div>
  </div>

  <!-- Surname -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Apellido'));?></label>
    <div class="col-sm-6">
      <input type="text" name="last_name" placeholder="<?php echo sprintf(_('Apellido'));?>" class="form-control">
    </div>
  </div>

  <!-- Genre -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Género'));?></label>
    <div class="col-sm-6">
			  <select name="sex" class="form-control">
				      <option value="1"><?php echo sprintf(_('Mujer'));?></option>
				      <option value="2"><?php echo sprintf(_('Varón'));?></option>
				      <option value="3"><?php echo sprintf(_('Otro'));?></option>
				  </select>
    </div>
  </div>

  <!-- Birth -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Fecha de nacimiento'));?></label>
    <div class="col-sm-6">
      <!-- <input id="birthdate" type="text" name="birthdate" placeholder="MM/DD/AAAA" class="form-control"> -->
      <input id="birthdate" type="text" name="birthdate" data-format="YYYY-MM-DD" data-template="DD MM YYYY">
      <script>
      $(function(){
          var fecha = moment().subtract(18, 'years').format("YYYY-MM-DD");
          $('#birthdate').combodate({
            useWithBootstrap: true,
            smartDays: true,
            minYear: '1900',
            firstItem: 'none|none|none',
            value: fecha.replace(/\//g,'-')
          });
      });
      </script>
    </div>
  </div>

  <!-- ID -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('N° Identidad / Pasaporte'));?></label>
    <div class="col-sm-6">
      <input type="text" name="personalid" placeholder="ID" class="form-control">
    </div>
  </div>

  <!-- Email -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Correo electrónico'));?></label>
    <div class="col-sm-6">
      <input type="text" id="email" name="email" maxlength="65" placeholder="<?php echo sprintf(_('Tu e-mail'));?>" class="email form-control">
    </div>
  </div>


  <!-- Phone -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Teléfono personal'));?><br/><small><?php echo sprintf(_('Preferentemente un móvil o celular'));?></small></label>

    <div class="col-sm-6">
      <!-- <input id="phone" type="text" name="phone" maxlength="65" placeholder="<?php echo sprintf(_('Sin espacios'));?>" class="phone form-control"> -->
      <input id="phone" type="tel" name="phone" maxlength="65" class="phone form-control">
    </div>
  </div>

  <!-- Country -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('País'));?></label>
    <div class="col-sm-6">
				  <select id="country-1" name="country_id" class="form-control">
				  </select>
      <!--input type="text" name="country" placeholder="Country" class="country form-control"-->
<!--       <div class="country bfh-selectbox bfh-countries" name="country_id" placeholder="Select Country" data-flags="true" data-filter="true"> </div> -->
    </div>
  </div>

  <!-- State -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Estado / Provincia'));?></label>
    <div class="col-sm-6">
<!--       <input type="text" name="state_id" maxlength="65" placeholder="State" class="state form-control"> -->
      <select id="state_province-1" name="state_id" class="state form-control"></select>
    </div>
  </div>

  <!-- City -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Ciudad'));?></label>
    <div class="col-sm-6">
      <input type="text" name="city" placeholder="<?php echo sprintf(_('Ciudad'));?>" class="city form-control">
    </div>
  </div>

  <!-- Street -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Calle'));?></label>
    <div class="col-sm-6">
      <input type="text" name="street_address" placeholder="<?php echo sprintf(_('Incluya también númeración y otros detalles'));?>" class="address form-control">
    </div>
  </div>


  <!-- Postcal Code -->
  <div class="form-group">
    <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Código Postal'));?></label>
    <div class="col-sm-6">
      <input type="text" name="zipcode" maxlength="9" placeholder="<?php echo sprintf(_('Código Postal'));?>" class="zip form-control">
    </div>
  </div>


  </div>

  <fieldset>
    <legend><?php echo sprintf(_('Datos sobre el pago'));?></legend>

	<div id="content">
	<!-- Donación por tarjeta o membresia -->
		<div id="medio-radio" class="form-group">
			<label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Medio de pago'));?></label>
			<div class="btn-group col-sm-8" data-toggle="buttons">
				<label id="medio-radio-tarjeta-lbl" class="btn btn-default tooltip-on active" rel="popover" data-placement="bottom" data-trigger="hover">
					<input  type="radio" name="medio_radio_option" class="radio-inline-element" id="medio-radio-tarjeta" value="1" checked="true"> <?php echo sprintf(_('Tarjeta de crédito'));?>
				</label>
        <label id="medio-radio-tarjetamp-lbl" class="btn btn-default tooltip-on" rel="popover" data-placement="bottom" data-trigger="hover">
          <input  type="radio" name="medio_radio_option" class="radio-inline-element" id="medio-radio-tarjetamp" value="4" checked="false"> <?php echo sprintf(_('Tarjeta de crédito (MP)'));?>
        </label>
        <label id="medio-radio-paypal-lbl" class="btn btn-default tooltip-on" rel="popover" data-placement="bottom" data-trigger="hover">
          <input  type="radio" name="medio_radio_option" class="radio-inline-element" id="medio-radio-paypal" value="3" checked="false"> <?php echo sprintf(_('Paypal'));?>
        </label>
				<label <?php if (!$_GET['trans']) {	echo 'style="display: none !important"'; } ?> class="btn btn-default tooltip-on " rel="popover" data-placement="bottom" data-trigger="hover">
					<input  type="radio" name="medio_radio_option" <?php if (!$_GET['trans']) {	echo 'style="display: none !important"'; } ?> class="radio-inline-element" id="medio-radio-transferencia" type="radio" value="2"> <?php echo sprintf(_('Transferencia bancaria'));?>
				</label>

			</div>
		</div>
                <!-- Inicio Datos Tarjeta -->
		<div id="medio-radio-extra" class="tab-content">
			<div class="tab-pane active" id="tarjeta">
			 <!-- Card type -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Tipo de tarjeta'));?></label>
					<div class="col-sm-6">
						<select name="cardtype" class="selectpicker form-control">
							<option><?php echo sprintf(_('VISA'));?></option>
							<option><?php echo sprintf(_('MasterCard'));?></option>
						 </select>
					</div>
				</div>
			<!-- Card Number -->
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Número de tarjeta de crédito'));?></label>
					<div class="col-sm-6">
						<input name="cardnumber" type="text" id="cardnumber" maxlength="19" placeholder="<?php echo sprintf(_('Sólo números'));?>" class="card-number form-control">
					</div>
				</div>

        <!-- Expiry-->

        <div class="form-group">
  	      <label class="col-sm-4 control-label" for="textinput"><?php echo sprintf(_('Fecha de vencimiento'));?></label>
  	      <div class="col-sm-6">
  	        <div class="combodate row">
              <div class="col-xs-4"></div>
              <div class="col-xs-4">
                <select name="card-expiry-month" data-stripe="exp-month" class="card-expiry-month stripe-sensitive required form-control">
                  <option value="01" selected="selected">01</option>
                  <option value="02">02</option>
                  <option value="03">03</option>
                  <option value="04">04</option>
                  <option value="05">05</option>
                  <option value="06">06</option>
                  <option value="07">07</option>
                  <option value="08">08</option>
                  <option value="09">09</option>
                  <option value="10">10</option>
                  <option value="11">11</option>
                  <option value="12">12</option>
                </select>
              </div>
              <div class="col-xs-4">
                <select name="card-expiry-year" data-stripe="exp-year" class="card-expiry-year stripe-sensitive required form-control"></select>
              </div>

              <script type="text/javascript">
                var select = $(".card-expiry-year"),
                year = new Date().getFullYear();

                for (var i = 0; i < 12; i++) {
                    select.append($("<option value='"+(i + year)+"' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
                }
              </script>
            </div>
          </div>
        </div>

        <!-- CVV -->
        <div class="form-group">
          <label class="col-sm-4 control-label" for="card-cvv"><?php echo sprintf(_('Código de seguridad (CVV)'));?></label>
          <div class="col-sm-6">
            <input name="card-cvv" type="text" id="card-cvv" placeholder="CVV" maxlength="4" class="card-cvc form-control">
          </div>
        </div>
			</div>

      <div class="form-group">
        <label class="col-sm-4 control-label" for="textinput"></label>
        <div class="col-sm-6">
          <div class="alert alert-success alert-dismissible" role="alert">
            <span class="glyphicon glyphicon-lock"></span> <?php echo sprintf(_('<b>Puedes dejar tus datos con confianza.</b> Esta página contiene un formulario seguro. La información suministrada viaja de forma encriptada a través de la red.'));?>
          </div>
        </div>
      </div>
    </div>

	<!-- </div> -->

    <!-- Inicio Datos MercadoPago -->
    <div id="medio-radio-extra2" class="tab-content">
      <div class="tab-pane active" id="tarjetamp">
        <div class="form-group">
          <label class="col-sm-4 control-label" for="textinput"></label>
          <div class="col-sm-6">
            <div class="alert alert-success alert-dismissible" role="alert">
              <span class="glyphicon glyphicon-lock"></span>
              <?php echo sprintf(_('<b>Pago por MercadoPago:</b> Puedes pagar con cualquier tarjeta de crédito. Serás redirigido a un formulario de MercadoPago para completar el pago.'));?>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Inicio Datos Paypal -->
    <div id="medio-radio-extra1" class="tab-content">
			<div class="tab-pane active" id="paypal">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput"></label>
					<div class="col-sm-6">
						<div class="alert alert-success alert-dismissible" role="alert">
							<span class="glyphicon glyphicon-lock"></span>
							<?php echo sprintf(_('<b>Pago por Paypal:</b> Puedes pagar con cualquier tarjeta de crédito o con saldo de tu cuenta de Paypal si ya tienes una. Serás redirigido a Paypal para completar el pago.'));?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Inicio Datos Transferencia -->
		<div id="medio-radio-extra1" class="tab-content">
			<div class="tab-pane active" id="transferencia">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput"></label>
					<div class="col-sm-6">
							<b><?php echo sprintf(_('Te enviaremos la información de la cuenta bancaria a tu correo electrónico.'));?></b>

					</div>
				</div>
			</div>
			<div class="tab-pane active" id="transferencia">
				<div class="form-group">
					<label class="col-sm-4 control-label" for="textinput"></label>
					<div class="col-sm-6">
						<div class="alert alert-success alert-dismissible" role="alert">
							<span class="glyphicon glyphicon-lock"></span>
							<?php echo sprintf(_('<b>Importante:</b> Ten en cuenta que, debido a los costos administrativos, esta opción sólo es válida para realizar el depósito o tranferencia del monto total anual de la membresía.'));?>
						</div>
					</div>
				</div>
			</div>
		</div>
  </div>

    <!-- Submit -->
    <div class="form-group">
      <label class="col-sm-4 control-label" for="textinput"></label>
      <div class="col-sm-6 col-xs-12">
        <button class="col-xs-12 btn btn-success" type="submit"><?php echo sprintf(_('Enviar mi solicitud ahora'));?></button>
      </div>
    </div>

	</div>






	<script type="text/javascript">
          jQuery(document).ready(function ($) {
              $('#tabs').tab();
          });
	</script>
<?php } ?>




  </fieldset>
</form>
</body>
</html>
