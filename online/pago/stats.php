<?php
  ## este script se llama desde CRON

  require('/srv/reevo-web/www/blog/wp-blog-header.php');
  $ssl      = ( ! empty( $_SERVER[HTTPS] ) && $_SERVER[HTTPS] == 'on' );
  $sp       = strtolower( $_SERVER[SERVER_PROTOCOL] );
  $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
  $blog_link = str_replace("donar.","","http://$_SERVER[HTTP_HOST]");
  $actual_link = urlencode("$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
  // if (!is_user_logged_in()){
  //     echo 'No estás logueado en el CRM. <a href="'.$blog_link.'/wp-login.php?redirect_to='.$actual_link.'&reauth=1">Ingresa ahora</a>';
  //     exit;
  // };
  require_once "/srv/reevo-web/www/crm/civicrm.settings.php";
  require_once 'CRM/Core/Config.php';
  $config = CRM_Core_Config::singleton( );
  require_once 'api/api.php';

  // Inicia variables
  $filename = date("Y-m-d");
  $table = '';

  function GeneraStats($membership_type_id)  {
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
        if ( $status == 1 || $status == 2 ) { // Solo se listan membresias nuevas y activas
          if ($membership_type_id == 1) { // Solo si es recurrente
            $total = ($total + $valor[custom_3]);
            $miembros = ($miembros + 1);
          }
        }
    }

    // {
    //   "total": "11",
    //   "miembros": "22",
    //   "miembros_min": "70",
    // }
    //
    $miembros_min = ($total / 5); // Divide el total por la membresia mínima
    echo '{"total": "'.$total.'","miembros": "'.$miembros.'","miembros_min": "'.$miembros_min.'"}';
  }


  function ListarMiembros($membership_type_id, $membership_status, $member_type)  {
    $json = '';
    $params = array(
      'sequential' => 1,
      'membership_type_id' => $membership_type_id,
      'options' => array(
        'limit' => 500, //Limite
      ),
    );

    $result = civicrm_api3('membership', 'get', $params);
    // print_r($result);

    $ultimo = end($result[values]);
    foreach($result[values] as $valor) {
      if ( $membership_status == 1 && $valor[custom_2] == 'Transferencia' ) { continue; } // No lista transferencias no realizadas
      $id = $valor[contact_id];
      $status = $valor[status_id];
      if ( $status == $membership_status ) { // Solo se listan membresias con el estado solicitado
        $params = array(
          'contact_id' => $valor[contact_id]
        );
        $contacto = civicrm_api3('contact', 'get', $params);
        // apellido, nombre, id, tarjeta, monto, marca, tipo
        //switch ($contacto[values][$id][contact_type])
        if ($contacto[values][$id][contact_type] == $member_type) {
          switch ($member_type) {
            case 'Individual':
              $json .= '
              "'.$valor[contact_id].'": {
                "name": "'.$contacto[values][$id][first_name].' '.$contacto[values][$id][last_name].'",
                "type": "'.$contacto[values][$id][contact_type].'",
                "date": "'.$valor[join_date].'",
                "id": "'.$valor[contact_id].'"
              },';
              break;
            case 'Organization':
              $json .= '
              "'.$valor[contact_id].'": {
                "name": "'.$contacto[values][$id][organization_name].'",
                "type": "'.$contacto[values][$id][contact_type].'",
                "date": "'.$valor[join_date].'",
                "id": "'.$valor[contact_id].'"
              },';

              break;
          }
        }
      }
    }
    return $json;
  }

// ***********  Genera stats.json  *************
  ob_start();
  GeneraStats(1);
  $salida = ob_get_contents();
  ob_end_clean();
  file_put_contents('/srv/reevo/donar.reevo.org/tmp/stats.json', $salida);


// ***********  Genera miembros.json  *************
  $contenido .= '{';
  // $contenido .= ListarMiembros(1,1,'Individual'); // Individuos mensuales nuevo
  $contenido .= ListarMiembros(1,2,'Individual'); // Individuos mensuales activos
  $contenido .= ListarMiembros(3,2,'Individual');  // Individuos anuales activos
  // $contenido .= ListarMiembros(1,1,'Organization'); // Colectivos mensuales nuevos
  $contenido .= ListarMiembros(1,2,'Organization'); // Colectivos mensuales activos
  $contenido .= ListarMiembros(3,2,'Organization'); // Colectivos anuales activos
  $contenido  =  rtrim($contenido, ",");
  $contenido .= '}';

  // Lo covierte en Array para ordenarlo por fecha, luego vuelve a ser JSON
  $data = json_decode($contenido, true);
  // print_r($data);
  usort($data, function($a, $b) {
    return (strtotime($a['date']) < strtotime($b['date']) -1);
  });

  ob_start();
  echo json_encode($data, JSON_FORCE_OBJECT);
  $salida = ob_get_contents();
  ob_end_clean();
  file_put_contents('/srv/reevo/donar.reevo.org/tmp/miembros.json', $salida);

// ***********  Genera feed.rss  *************

$blog_link = 'http://reevo.org';

ob_start();
  echo '<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Miembros de Reevo</title>
    <link>http://donar.reevo.org/exportar.php</link>
    <description>Los últimos miembros activos</description>
    <language>es-ES</language>
    <atom:link href="https://donar.reevo.org/tmp/rss.xml" rel="self" type="application/rss+xml" />
  ';
  // print_r($data);
  $rss = '';
  foreach($data as $valor) {
    $rss .= '
    <item>
      <title>'.$valor['name'].'</title>
      <description>'.$valor['type'].'</description>
      <pubDate>'.date('r', strtotime($valor['date'])).'</pubDate>
      <link>http://reevo.org/wp-admin/admin.php?page=CiviCRM&#38;q=civicrm/contact/view&#38;cid='.$valor['id'].'</link>
    </item>';
  }
  echo $rss;
  echo '
  </channel>
</rss>';
$salida = ob_get_contents();
ob_end_clean();
file_put_contents('/srv/reevo/donar.reevo.org/tmp/rss.xml', $salida);


  //header("Location: tmp/stats.json");
?>
