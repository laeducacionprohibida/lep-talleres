<?php
/* Este script se usa para cambiar rapidamente el estado de una membresias
Si un miembro entra al estado activo y no tiene usuario en red.reevo, se le genera un usuario y se lo suma al grupo de miembros

*/
error_reporting(E_ALL);
ini_set('display_errors', True);

require('/srv/reevo-web/www/blog/wp-blog-header.php');
require_once "/srv/reevo-web/www/crm/civicrm.settings.php";
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton( );
require_once 'api/api.php';

$cid = $_GET['cid']; // contact id
$mid = $_GET['mid']; // member id
$status = $_GET['status']; // nuevo status

// Almacena el estado anterior
$params = array(
  'id' => $mid,
  // 'contact_id' => $cid,
);
$old_status = civicrm_api3('membership', 'get', $params);

// Genero un nuevo estado
$create_status = array(
  'id' => $mid,
  // 'contact_id' => $cid,
  'is_override' => 1,
  'status_id' => $status,
);

try{
  $result = civicrm_api3('membership', 'create', $create_status);
  $params = array(
    'contact_id' => $cid,
  );
  $contacto = civicrm_api3('contact', 'get', $params);

  switch ($status) {
    case 1:
      $estado = 'Nueva';
      break;
    case 2:
      $estado = 'Activa';
      echo 'El email procesado es: ' . $contacto[values][$cid][email];
      $ruta = 'php /srv/reevo-web/bin/red/generateuser/from_email.php email='.$contacto[values][$cid][email].' groups=miembros msg=new_active_member.es.tpl';
      exec($ruta, $output);
      $user_profile = $output[0];

      break;
    case 4:
      $estado = 'Suspendida'; // en el CRM figura como Expirada
      break;
    case 6:
      $estado = 'Cancelada';
      break;
  }

  echo "<h1>La membresia de <i>{$contacto[values][$cid][first_name]} {$contacto[values][$cid][last_name]}</i> cambio a estado: <i>$estado</i></h1>";

  echo "<a href='{$user_profile}'>{$user_profile}</a>";
  if ($old_status['values'][$mid]['status_id'] == '2' AND $status != '2') {
    $output = shell_exec('php /srv/reevo-web/bin/red/generateuser/from_email.php email='.$contacto[values][$cid][email].' groupsout=miembros');
    echo 'Se quito al contacto del grupo de miembros de la red.';
  }

  echo "<pre>";
  print_r($result);
  echo "</pre>";
}
catch (CiviCRM_API3_Exception $e) {
  // handle error here
  $errorMessage = $e->getMessage();
  $errorCode = $e->getErrorCode();
  $errorData = $e->getExtraParams();
  return array('error' => $errorMessage, 'error_code' => $errorCode, 'error_data' => $errorData);
}





?>
