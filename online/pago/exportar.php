<?php
error_reporting(E_ALL);
ini_set('display_errors', True);


  require('/srv/reevo-web/www/blog/wp-blog-header.php');



  $ssl      = ( ! empty( $_SERVER[HTTPS] ) && $_SERVER[HTTPS] == 'on' );
  $sp       = strtolower( $_SERVER[SERVER_PROTOCOL] );
  $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
  $blog_link = str_replace("donar.","blog.","http://$_SERVER[HTTP_HOST]");
  $actual_link = urlencode("$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
  // if (!is_user_logged_in()){
  //     echo 'No estás logueado en el CRM. <a href="'.$blog_link.'/wp-login.php?redirect_to='.$actual_link.'&reauth=1">Ingresa ahora</a>';
  //     exit;
  // };

  //
  // if (!is_user_logged_in()){
  //     echo "No estás logueado en el CRM. <a href='$blog_link/wp-login.php?redirect_to=$actual_link&reauth=1'>Ingresa ahora</a>";
  //     exit;
  // };


  require_once "/srv/reevo-web/www/crm/civicrm.settings.php";
  require_once 'CRM/Core/Config.php';
  $config = CRM_Core_Config::singleton( );
  require_once 'api/api.php';

  $filename = date("Y-m-d");

  // Obtiene cotizacion actual del dolar en $AR
  $json = file_get_contents('http://api.bluelytics.com.ar/v2/latest');
  $obj = json_decode($json);
  $cotizacion = $obj->blue->value_sell;

  $table = '';

  function ListarMiembros($membership_type_id, $membership_status)  {
    global $table;
    global $cotizacion;
    global $blog_link;
    $table = "";
    $total_mensual = "0";

    switch ($membership_type_id) {
      case 1:
        $tipo = 'Mensual';
        break;
      case 2:
        $tipo = 'Unica';
        break;
      case 3:
        $tipo = 'Anual';
        break;
    }

    switch ($membership_status) {
      case 1:
        $estado = 'Nueva';
        break;
      case 2:
        $estado = 'Activa';
        break;
      case 4:
        $estado = 'Suspendida'; // en el CRM figura como Expirada
        break;
      case 6:
        $estado = 'Cancelada';
        break;
    }

    $params = array(
      'sequential' => 1,
      'membership_type_id' => $membership_type_id,
      'options' => array(
        'limit' => 500, //Limite
      ),
    );

    $result = civicrm_api3('membership', 'get', $params);
    // print_r($result);

    foreach($result[values] as $valor) {
        // error_log(print_r($contacto[values],TRUE));

        $id = $valor[contact_id];
        $status = $valor[status_id];
        if ( $status == $membership_status ) { // Solo se listan membresias con el estado solicitado

          if ($membership_status == '2' and $membership_type_id == '1') {
            $total_mensual = $total_mensual + $valor[custom_3];
          }

          $params = array(
            'contact_id' => $valor[contact_id],
          );
          $contacto = civicrm_api3('contact', 'get', $params);
          $montoenpesosar = $valor[custom_3] * $cotizacion; // Pesifica los dolares
          $montosindecimales = explode('.', $valor[custom_3]);
          $link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$id;
          $link_membership = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view/membership&action=update&reset=1&cid='.$id.'&id='.$valor[id].'&context=membership&selectedChild=member';
          $link_notes = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view/note&cid='.$id;
          // http://blog.reevo.dev/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view/note&reset=1&cid=87881
          $paypal_link = "https://donar.reevo.org/paypal.php?l=es&val={$montosindecimales[0]}&tipo=membresia&email={$contacto[values][$id][email]}&phone={$contacto[values][$id][phone]}&first_name={$contacto[values][$id][first_name]}&last_name={$contacto[values][$id][last_name]}&street_address={$contacto[values][$id][street_address]}&city={$contacto[values][$id][city]}&zipcode={$contacto[values][$id][postal_code]}&state_id={$contacto[values][$id][state_id]}&country_id={$contacto[values][$id][country_id]}";
          $paypal_link_org = "https://donar.reevo.org/paypal.php?l=es&val={$montosindecimales[0]}&tipo=membresia&email={$contacto[values][$id][email]}&phone={$contacto[values][$id][phone]}&first_name={$valor[custom_4]}&last_name=&street_address={$contacto[values][$id][street_address]}&city={$contacto[values][$id][city]}&zipcode={$contacto[values][$id][postal_code]}&state_id={$contacto[values][$id][state_id]}&country_id={$contacto[values][$id][country_id]}";



          $notes = civicrm_api('Note', 'Get', array('entity_table' => 'civicrm_contact', 'entity_id' => $id, 'version' => 3));

          $max = 0;
          $ultima_nota = "";
          $con_nota = "";
          foreach($notes as $valor3) {
              if (is_array($valor3)) {
                $notafinal = array_pop($valor3);
                // print_r($notafinal);
                  if ($notafinal['contact_id'] != '1' AND $notafinal['subject'] == 'Membresia') {
                    $ultima_nota = "{$notafinal['note']} ({$notafinal['modified_date']})";
                  }
                  // $autor = civicrm_api('Contact','Get', array('id' => $valor4['contact_id'], 'version' =>3));
                  //   foreach ((array) $autor as $valor5) { foreach((array) $valor5 as $valor6) {
                  //       echo '<td>' . $valor6['first_name'] . '</td>';
                  //     }
                  //   }
              }
          }


           // End notes

          switch ($contacto[values][$id][contact_type]) {
            case 'Individual':
              $hacer_nuevo = "/memberstatus.php?cid={$valor[contact_id]}&mid={$valor[id]}&status=1";
              $hacer_activo = "/memberstatus.php?cid={$valor[contact_id]}&mid={$valor[id]}&status=2";
              $hacer_suspendido = "/memberstatus.php?cid={$valor[contact_id]}&mid={$valor[id]}&status=4";
              $hacer_cancelado = "/memberstatus.php?cid={$valor[contact_id]}&mid={$valor[id]}&status=6";
              $card = mb_substr($valor[custom_1], 0, 5);
              if ($valor[custom_2] == 'Transferencia') { $marca = 'Trans'; } else { $marca = $valor[custom_2]; }
              if ($ultima_nota != '') { $con_nota = "style='background:#FDD583;'"; }
              $table .= "<tr {$con_nota}> <td scope='row'><i class='glyphicon glyphicon-user' aria-hidden='true'></i><b>{$contacto[values][$id][last_name]}, {$contacto[values][$id][first_name]}</b></td> <td class='hidden-xs'>{$contacto[values][$id][email]}</td> <td>{$marca}</td> <td>{$valor[custom_3]}</td> <td class='hidden-xs'>{$valor[join_date]}</td> <td>{$tipo}</td> <td><div class='btn-group'><a href='{$link_profile}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Ver perfil'><span class='glyphicon glyphicon-user'></span></a><a href='{$link_membership}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Editar membresía'><span class='glyphicon glyphicon-list-alt'></span></a><a href='{$paypal_link}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Cargar en Paypal'><span class='glyphicon glyphicon-heart'></span></a></div><div class='btn-group' style='margin-left:5px;'><a href='{$hacer_nuevo}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Nuevo'><span class='glyphicon glyphicon-plus-sign'></span></a><a href='{$hacer_activo}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Activo'><span class='glyphicon glyphicon-ok-sign'></span></a><a href='{$hacer_suspendido}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Suspendido'><span class='glyphicon glyphicon-question-sign'></span></a><a href='{$hacer_cancelado}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Cancelado'><span class='glyphicon glyphicon-remove-sign'></span></a></div>";
              if ($ultima_nota != '') {
                $table .= "<div class='btn-group btn-group-note' style='margin-left:5px;'><a href='{$link_notes}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='{$ultima_nota}'><span class='glyphicon glyphicon-tag'></span></a></div>";
              }
              $table .= "</td></tr>";
              break;
            case 'Organization':
              $hacer_nuevo = "/memberstatus.php?cid={$valor[custom_4_id]}&mid={$valor[id]}&status=1";
              $hacer_activo = "/memberstatus.php?cid={$valor[custom_4_id]}&mid={$valor[id]}&status=2";
              $hacer_suspendido = "/memberstatus.php?cid={$valor[custom_4_id]}&mid={$valor[id]}&status=4";
              $hacer_cancelado = "/memberstatus.php?cid={$valor[custom_4_id]}&mid={$valor[id]}&status=6";
              $card = mb_substr($valor[custom_1], 0, 5);
              if ($valor[custom_2] == 'Transferencia') { $marca = 'Trans'; } else { $marca = $valor[custom_2]; }
              if ($ultima_nota != '') { $con_nota = "style='background:#FDD583;'"; }
              $table .= "<tr {$con_nota}> <td scope='row'><i class='glyphicon glyphicon-home' aria-hidden='true'></i><b>{$contacto[values][$id][organization_name]} <br/><small>({$valor[custom_4]})</small></b></td> <td class='hidden-xs'>{$contacto[values][$id][email]}</td> <td>{$marca}</td> <td>{$valor[custom_3]}</td> <td class='hidden-xs'>{$valor[join_date]}</td> <td>{$tipo}</td> <td><div class='btn-group'><a href='{$link_profile}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Ver perfil'><span class='glyphicon glyphicon-user'></span></a><a href='{$link_membership}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Editar membresía'><span class='glyphicon glyphicon-list-alt'></span></a><a href='{$paypal_link_org}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Cargar en Paypal'><span class='glyphicon glyphicon-heart'></span></a></div><div class='btn-group' style='margin-left:5px;'><a href='{$hacer_nuevo}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Nuevo'><span class='glyphicon glyphicon-plus-sign'></span></a><a href='{$hacer_activo}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Activo'><span class='glyphicon glyphicon-ok-sign'></span></a><a href='{$hacer_suspendido}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Suspendido'><span class='glyphicon glyphicon-question-sign'></span></a><a href='{$hacer_cancelado}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='Hacer Cancelado'><span class='glyphicon glyphicon-remove-sign'></span></a></div>";
              if ($ultima_nota != '') {
                $table .= "<div class='btn-group btn-group-note' style='margin-left:5px;'><a href='{$link_notes}' role='button' target='_blank' class='btn btn-default btn-xs' data-toggle='tooltip' data-placement='bottom' title='{$ultima_nota}'><span class='glyphicon glyphicon-tag'></span></a></div>";
              }
              $table .= "</td></tr>";
              break;
          }

        }
    }
    if ($total_mensual > 0) {
      echo 'Total mensual: <b>' . $total_mensual . '</b> (sin contar anuales)';
    }
    return $table;


  }
  function GenerarCSV($membership_type_id, $membership_status)  {
    global $table;
    global $cotizacion;
    global $blog_link;

    switch ($membership_type_id) {
      case 1:
        $tipo = 'Mensual';
        break;
      case 2:
        $tipo = 'Unica';
        break;
      case 3:
        $tipo = 'Anual';
        break;
    }

    $params = array(
      'sequential' => 1,
      'membership_type_id' => $membership_type_id,
      'options' => array(
        'limit' => 500, //Limite
      ),
    );

    $result = civicrm_api3('membership', 'get', $params);

    foreach($result[values] as $valor) {

        $id = $valor[contact_id];
        $status = $valor[status_id];
        if ( $status == $membership_status ) { // Solo se listan membresias con el estado solicitado
          $params = array(
            'contact_id' => $valor[contact_id]
          );
          $contacto = civicrm_api3('contact', 'get', $params);
          $montoenpesosar = $valor[custom_3] * $cotizacion; // Pesifica los dolares

          // apellido, nombre, id, tarjeta, monto, marca, vencimiento, cvv, tipo

          switch ($contacto[values][$id][contact_type]) {
            case 'Individual':
              if ( $valor[custom_2] != 'Transferencia' ) { // No exporta Transferencia
                echo $contacto[values][$id][last_name].','.$contacto[values][$id][first_name].','.$id.','.$valor[custom_1].','.$montoenpesosar.','.$valor[custom_2].','.$valor[custom_5].','.$valor[custom_6].','.$tipo;
                echo "\n";
              }
              break;
            case 'Organization':
              if ( $valor[custom_2] != 'Transferencia' ) { // No exporta Transferencia
                echo $valor[custom_4].','.$id.'('.$contacto[values][$id][organization_name].'),'.$valor[custom_1].','.$montoenpesosar.','.$valor[custom_2].','.$valor[custom_5].','.$valor[custom_6].','.$tipo;
                echo "\n";
              }
              break;
          }
        }
    }
  }

  ob_start();
  GenerarCSV(1,1);
  GenerarCSV(1,2);
  GenerarCSV(3,1);
  $salida = ob_get_contents();
  ob_end_clean();
  file_put_contents('tmp/'.$filename.'.csv', $salida);

  if('POST' == $_SERVER['REQUEST_METHOD']) {
   header("Location: tmp/$filename.csv");
  }

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gestion de membresías</title>

  <link rel="icon" href="http://assets.reevo.org/logo/favicon/favicon.ico">
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,300italic,300,700,700italic' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="css/bootstrap-min.css">
	<link rel="stylesheet" href="css/bootstrap-formhelpers-min.css" media="screen">
	<link rel="stylesheet" href="css/bootstrapValidator-min.css"/>
	<link rel="stylesheet" href="css/bootstrap-datepicker.css"/>
	<link rel="stylesheet" href="css/bootstrap-select.css"/>
	<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" />
	<link rel="stylesheet" href="css/bootstrap-side-notes.css" />
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/sortable-theme-bootstrap.css">


  <style>
    i.glyphicon {padding-right: 1em;}
  </style>

	<script type="text/javascript" src="/js/crm-assets/jquery-1.11.1.min.js"></script>

<!-- 	Componentes de bootstrap  -->
	<script src="js/bootstrap-min.js"></script>
	<script src="js/bootstrap-formhelpers-min.js"></script>
	<script type="text/javascript" src="js/bootstrapValidator-min.js"></script>
  <!-- <script src="js/bootstrap-datepicker.js"></script> -->
  <script src="js/bootstrap-select.js"></script>
  <script src="js/sortable.min.js"></script>


  <script type="text/javascript">
    function CountRows(tabla) {
      var totalRowCount = 0;
      var rowCount = 0;
      var table = document.getElementById(tabla);
      var rows = table.getElementsByTagName("tr")
      for (var i = 0; i < rows.length; i++) {
          totalRowCount++;
          if (rows[i].getElementsByTagName("td").length > 0) {
              rowCount++;
          }
      }
      document.getElementById(tabla + '-filas').innerHTML = '(' + rowCount + ')';
      // console.log('la tabla ' + tabla + ' tiene ' + rowCount);
    }

    $( document ).ready(function() {
      $('.table a').tooltip();
      CountRows('tabla-nuevas');
      CountRows('tabla-activas');
      CountRows('tabla-suspendidas');
      CountRows('tabla-canceladas');
    });
  </script>
</head>
<body>
  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="payment-form" class="form-horizontal">
    <div>
      <div class="col-md-8 col-md-offset-2">
        <div class="page-header">
          <h2 class="gdfg">Listado de miembros- <?php echo $filename ?></h2>
        </div>
        <fieldset>

          <h3><span class="glyphicon glyphicon-plus-sign"></span> Membresías nuevas <span id='tabla-nuevas-filas'></span> <small>(aun no se cobraron ni una vez)</small></h3>
          <table data-sortable id="tabla-nuevas" class="table table-hover sortable-theme-bootstrap">
            <thead>
              <tr>
                <th>Nombre</th>
                <th class="hidden-xs">E-mail</th>
                <th>Medio</th>
                <th>u$d</th>
                <th class="hidden-xs">Inicio</th>
                <th>Tipo</th>
              </tr>
            </thead>
            <tbody>
              <?php echo ListarMiembros(1,1); ?>
              <?php echo ListarMiembros(3,1); ?>
            </tbody>
          </table>

          <h3><span class="glyphicon glyphicon-ok-sign"></span> Membresías activas <span id='tabla-activas-filas'></span> <small>(se estan cobrando todos los meses)</small></h3>
          <table data-sortable id="tabla-activas" class="table table-hover sortable-theme-bootstrap">
            <thead>
              <tr>
                <th>Nombre</th>
                <th class="hidden-xs">E-mail</th>
                <th>Medio</th>
                <th>u$d</th>
                <th class="hidden-xs">Inicio</th>
                <th>Tipo</th>
              </tr>
            </thead>
            <tbody>
              <?php echo ListarMiembros(1,2); ?>
              <?php echo ListarMiembros(3,2); ?>
            </tbody>
          </table>

          <h3><span class="glyphicon glyphicon-question-sign"></span> Membresías suspendidas <span id='tabla-suspendidas-filas'></span> <small>(el miembro no confirmo la baja)</small></h3>
          <table data-sortable id="tabla-suspendidas" class="table table-hover sortable-theme-bootstrap">
            <thead>
              <tr>
                <th>Nombre</th>
                <th class="hidden-xs">E-mail</th>
                <th>Medio</th>
                <th>u$d</th>
                <th class="hidden-xs">Inicio</th>
                <th>Tipo</th>
              </tr>
            </thead>
            <tbody>
              <?php echo ListarMiembros(1,4); ?>
              <?php echo ListarMiembros(3,4); ?>
            </tbody>
          </table>

          <h3><span class="glyphicon glyphicon-remove-sign"></span> Membresías canceladas <span id='tabla-canceladas-filas'></span> <small>(el miembro confirmo la baja)</small></h3>
          <table data-sortable id="tabla-canceladas" class="table table-hover sortable-theme-bootstrap">
            <thead>
              <tr>
                <th>Nombre</th>
                <th class="hidden-xs">E-mail</th>
                <th>Medio</th>
                <th>u$d</th>
                <th class="hidden-xs">Inicio</th>
                <th>Tipo</th>
              </tr>
            </thead>
            <tbody>
              <?php echo ListarMiembros(1,6); ?>
              <?php echo ListarMiembros(3,6); ?>
            </tbody>
          </table>

          <h3>Donaciones únicas pendientes</h3>
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Nombre</th>
                <th class="hidden-xs">E-mail</th>
                <th>Medio</th>
                <th>Venc.</th>
                <th>u$d</th>
                <th class="hidden-xs">Inicio</th>
                <th>Tipo</th>
              </tr>
            </thead>
            <tbody>
              <?php echo ListarMiembros(2,1); ?>
            </tbody>
          </table>

          <!-- <label class="col-sm-4 control-label" for="textinput"></label>
          <div class="control-group">
            <div class="controls">
              <div class="col-sm-6">
               <button class="btn btn-success" type="submit">Descargar listado</button>
              </div>
            </div>
          </div> -->
        </fieldset>
        <hr />
        <!-- <small>(*) La cotización actual promedio, usada para la pesificación, es de <?php echo $cotizacion; ?> $AR. Fuente: <a href="http://bluelytics.com.ar/#/">bluelytics</a></small> -->
      </div>
    </div>
  </form>


</body>
</html>
