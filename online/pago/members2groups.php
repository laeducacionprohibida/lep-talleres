<?php
// Este scipt se usa regularmente para actualizar la asignación a los grupos relacionadoscon las membresias.

error_reporting(E_ALL);
ini_set('display_errors', True);

require('/srv/reevo-web/www/blog/wp-blog-header.php');
require_once "/srv/reevo-web/www/crm/civicrm.settings.php";
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton( );
require_once 'api/api.php';

// Asignamos id de grupos
$g_miembros = '125';
$g_nuevos = '126';
$g_activos = '127';
$g_nuevos_transferencia = '288';

function CheckGrupo($contact_id, $grupo) {
  $encontrado = FALSE;
  $result = civicrm_api3('GroupContact', 'get', array(
    'sequential' => 1,
    'contact_id' => $contact_id,
  ));
  foreach ($result['values'] as $value) {
    if ($value['group_id'] == $grupo) { $encontrado = TRUE; }
  }
  return $encontrado;
}

function GrupoAgregar ($contact_id, $grupo) {
  $yaengrupo = CheckGrupo($contact_id, $grupo);
  if ($yaengrupo == FALSE) {
    echo "\n";
    $data = array(
      'contact_id' 		=> $contact_id,
      'group_id' 			=> $grupo,
    );
    $new_grupos_agregar = civicrm_api3('group_contact', 'create', $data);
    echo 'El contacto '.$contact_id.' se agregó al grupo '.$grupo.';';
    echo "\n";
  }
}

function GrupoSacar ($contact_id, $grupo) {
  $yaengrupo = CheckGrupo($contact_id, $grupo);
  if ($yaengrupo == TRUE) {
    echo "\n";
    $data = array(
      'contact_id' 		=> $contact_id,
      'group_id' 			=> $grupo,
    );
    $new_grupos_agregar = civicrm_api3('group_contact', 'delete', $data);
    echo 'El contacto '.$contact_id.' se quito del grupo '.$grupo.';';
    echo "\n";
  }
}

$params = array(
  'sequential' => 1,
  'options' => array(
    'limit' => 1000, //Limite
  ),
);
$result = civicrm_api3('membership', 'get', $params);
// print_r($result);


foreach($result[values] as $miembro) {
  $id = $miembro[contact_id];
  $status = $miembro[status_id];

  // Agrega a todos los miembros al grupo general de membresías
  GrupoAgregar($id, $g_miembros);
  // Determinamos si es Activa o nueva
  switch ($status) {
    case 2:
      // Membresia activa
      GrupoAgregar($id, $g_activos);
      GrupoSacar($id, $g_nuevos);
      break;
    case 1:
      // Membresia nueva
      GrupoAgregar($id, $g_nuevos);
      GrupoSacar($id, $g_activos);
      if ( $miembro[custom_2] == 'Transferencia' ) {
        // Grupo de miembros nuevos que aun no pagaron por transferencia
        GrupoAgregar($id, $g_nuevos_transferencia);
      }
      break;
    default:
      // No se asigna a ningun grupo porque no hay grupo de membresias
      break;
  }
}






?>
