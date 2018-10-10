
<?php

// error_reporting(E_ALL);
// ini_set('display_errors', True);

// carga api de google contacts
require_once '/srv/reevo/donar.reevo.org/assets/vendor/autoload.php';
use rapidweb\googlecontacts\factories\ContactFactory;

require('/srv/reevo-web/www/blog/wp-blog-header.php');
require_once "/srv/reevo-web/www/crm/civicrm.settings.php";
require_once 'CRM/Core/Config.php';
$config = CRM_Core_Config::singleton( );
require_once 'api/api.php';


function EncryptData($source)
{
	// encriptamos los numeros de tarjeta con una clave asimetrica generada con:
	// openssl genrsa -des3 -out cc_private.pem 2048 && openssl rsa -in cc_private.pem -outform PEM -pubout -out cc_public.pem
  if (file_exists('cc_public.pem')) {
    $fp=fopen("cc_public.pem","r");
    $pub_key=fread($fp,8192);
    fclose($fp);
    openssl_get_publickey($pub_key);
    /*
     * NOTE:  Here you use the $pub_key value (converted, I guess)
     */
    openssl_public_encrypt($source,$crypttext,$pub_key);
    return(base64_encode($crypttext));
  } else {
    return $source;
  }
}



// Cargamos la versión traducida // TODO: Reemplazar esto con gettext
/*
if ($_GET['l']) {
	$nombre_fichero = 'lang/'.$_GET['l'].'.php';

	if (file_exists($nombre_fichero)) {
		  include_once('lang/'.$_GET['l'].'.php');
	} else {
	    include_once('lang/es.php');
	}

} else {
	include_once('lang/es.php');
}*/


if ($_POST) {

	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$first_name = ucwords(strtolower($_POST['first_name']));
	$last_name = ucwords(strtolower($_POST['last_name']));
	$sex = $_POST['sex'];
	$birthdate = $_POST['birthdate'];
	$personalid = $_POST['personalid'];

// 	$street_name= $_POST['street_name'];
// 	$street_number= $_POST['street_number'];
	$street_address = $_POST['street_address'];
	$city = ucwords(strtolower($_POST['city']));
	$zipcode = $_POST['zipcode'];
  $state_id = $_POST['state_id'];
	$country_id = $_POST['country_id'];

  $donationtype = $_POST['donationtype_radio_option'];
  $medio = $_POST['medio_radio_option'];
  $titulo = $_POST['titulo_radio_option'];


  $cardtype = $_POST['cardtype'];
  $cardnumber = $_POST['cardnumber'];
  $cardammount = $_POST['donation_box'];
  $cardexpiry = $_POST['card-expiry-month'].'/'.$_POST['card-expiry-year'];
  $cardcvv = $_POST['card-cvv'];


  if ($titulo == 1) { // Los grupos varian si el inviduo representa una organizacion o no
    $grupos_agregar = array("6","126"); // Grupos de CRM: suscriptores y "membresias solicitadas"
  } else {
    $grupos_agregar = array("6"); // Grupos de CRM: suscriptores
  }

  $date = date("Y-m-d");

  // Carga el referer (como parametro o el referer real al form.php)
  if ($_GET['source']) {
    $source = $_GET['source'];
  } else  {
    $referer_get = $_GET['ref'];
    $referer_url = $_SERVER['HTTP_REFERER'];

    if ($referer_get || $referer_url) {
      if ($referer_get) {
        $source_sep = ' | ';
      }
      $source = $referer_get . $source_sep . 'Referer: ' . $referer_url;
    } else {
      $source = 'Acceso directo';
    }
  }

  $yo = 1;

// Checks if email is already in database

  $email_exists = civicrm_api3('email', 'get', array('email' => $email));
  if ($email_exists['count'] == 0) {
    // El usuario no existe y debe ser creado
  	$data = array(
  		 'contact_type' 		=> 'Individual',
  		 'first_name' 			=> $first_name,
  		 'last_name' 				=> $last_name,
  		 'birth_date'				=> $birthdate,
  		 'legal_identifier'	=> $personalid,
  		 'gender_id'				=> $sex
  		 );

  	$contact = civicrm_api3('Contact','Create',$data);
    $contact_id = $contact[id];
  //  Attach email to the contact
  	$data = array(
  		  'contact_id'		=> $contact_id,
  		  'email' 				=> $email,
  		  'is_primary' 		=> 1,
  		);
  	$new_email = civicrm_api3('email', 'create', $data);

  // Attach phone to the contact
  	$data = array(
  	  'contact_id' => $contact_id,
  	  'location_type_id' 	=> 1, //Home
  	  'phone' => $phone,
  	  'is_primary' => 1,
  	  'phone_type_id' => 1,
  	);
    $new_phone = civicrm_api3('phone', 'create', $data);


  // 	Attach the adrress information to the contact
  	$data = array(
  	  'contact_id' 				=> $contact_id,
  	  'location_type_id' 	=> 1, //Home
  // 	  'street_name' 			=> $street_name,
  // 	  'street_number' 		=> $street_number,
  	  'street_address' 		=> $street_address,
  	  'state_province_id' => $state_id,
  	  'country_id' 				=> $country_id,
  	  'city' 							=> $city,
   	  'postal_code'				=> $zipcode,
  	  'is_primary' 				=> 1,
  	);
  	$new_address = civicrm_api3('address', 'create', $data);


  //	Add the contact to certain groups
  	if (!empty($grupos_agregar)) {
  		foreach($grupos_agregar as $value) {
  			$data = array(
  			  'contact_id' 		=> $contact_id,
  			  'group_id' 			=> $value,
  			);

  			$new_grupos_agregar = civicrm_api3('group_contact', 'create', $data);
  		}
  	}

  //	Tag the contact with certain tags
  	if (!empty($tags_agregar)) {
  		foreach($tags_agregar as $value) {
  			$data = array(
  			  'contact_id' 		=> $contact_id,
  			  'tag_id' 		=> $value,
  			);

  			$new_tags_agregar = civicrm_api3('entity_tag', 'create', $data);
  		}
  	}
  } else { // ********************************************************************************

	// Get the id of existing user
      if ( $email_exists['count'] >= 2) {
        echo "<h1>ERROR! Contacto duplicado, lo siento... Informelo a financiacion@reevo.org.</h1>";
        print_r($email_exists);
        // Para eliminar un e-mail huerfano, usar este bloque:
        // $params = array('id' => 75103);
        // $result = civicrm_api3('email', 'delete', $params);
        // print_r($result);
        exit();
      }
    	$contact_id = $email_exists['values'][$email_exists[id]]['contact_id'];
    	// Obtiene los datos anteriores
    	$data = array(
    		 'contact_type' 	=> 'Individual',
    		 'contact_id' 		=> $contact_id,
    		 );

    	$old = civicrm_api3('Contact','Get',$data);

     	if (empty($first_name)) {$first_name = $old['values'][$old[id]]['first_name'];}
     	if (empty($last_name)) {$last_name = $old['values'][$old[id]]['last_name'];}
     	if (empty($image_URL)) {$image_URL = $old['values'][$old[id]]['image_URL'];}


    	$data = array(
    		 'contact_type' 	=> 'Individual',
    		 'contact_id' 		=> $contact_id,
    		 'first_name' 		=> $first_name,
    		 'last_name' 		=> $last_name,
    		 'image_URL'		=> $image_URL,
         'birth_date'				=> $birthdate,
    		 'legal_identifier'	=> $personalid,
    		 'gender_id'				=> $sex
    		 );

    	$contact = civicrm_api3('Contact','Create',$data);

    // Attach phone to the contact
    	$data = array(
    	  'contact_id' => $contact_id,
    	  'location_type_id' 	=> 4, //Otro
    	  'phone' => $phone,
    	  'is_primary' => 0,
    	  'phone_type_id' => 1,
    	);
      $new_phone = civicrm_api3('phone', 'create', $data);

    //	Remove the previous address if "add address is not set"
    // 	if (empty($add_address)) {
    // 		$params = array(
    // 		  'contact_id' => $contact_id,
    // 		);
    //
    // 		$result = civicrm_api3('address', 'get', $params);
    // 		$prior_address_id = array_keys($result['values'])[0];
    // 		$result = civicrm_api3('address', 'delete', array('id' => $prior_address_id));
    // 	}

    // 	Attach the adrress information to the contact
    	$data = array(
    	  'contact_id' 			=> $contact_id,
    	  'location_type_id' 	=> 4, //Otro
    // 	  'street_name' 		=> $street_name,
    // 	  'street_number' 		=> $street_number,
    	  'street_address' 		=> $street_address,
    	  'state_province_id' 			=> $state_id,
    	  'country_id' 			=> $country_id,
    	  'city' 				=> $city,
    	  'is_primary' 			=> 0,
    	);
    	$new_address = civicrm_api3('address', 'create', $data);

    //	Add the contact to certain groups
    	if (!empty($grupos_agregar)) {
    		foreach($grupos_agregar as $value) {
    			$data = array(
    			  'contact_id' 		=> $contact_id,
    			  'group_id' 		=> $value,
    			);
    			$new_grupos_agregar = civicrm_api3('group_contact', 'create', $data);
    		}
    	}
  	} // Cierra el else que verifica existencia de contacto

    // Si es colectiva, primero crea el contacto de la organización
    if ($titulo == 2) {
      $org_name = ucwords(strtolower($_POST['organization_name']));
      $org_email = $_POST['organization_email'];
      $org_phone = $_POST['organization_phone'];
      $org_street_address = $_POST['organization_street_address'];
      $org_city = ucwords(strtolower($_POST['organization_city']));
      $org_zipcode = $_POST['organization_zipcode'];
      $org_state_id = $_POST['organization_state_id'];
      $org_country_id = $_POST['organization_country_id'];
      $grupos_agregar = array("6","126"); // Grupos de CRM: suscriptores y "membresias solicitadas"

      // Creo el contacto
      $data = array(
         'contact_type' 	=> 'Organization',
         'organization_name' 		=> $org_name,
         'primary_contact_id'   => $contact_id // Usa el individuo como contacto primario (https://civicrm.org/blog/nasact/use-cases-for-primarycontactid)
         );
      $org_contact = civicrm_api3('Contact','Create',$data);
      $org_contact_id = $org_contact[id];

    //  Attach email to the contact
    	$data = array(
    		  'contact_id'		=> $org_contact_id,
    		  'email' 				=> $org_email,
    		  'is_primary' 		=> 1,
    		);
    	$new_email = civicrm_api3('email', 'create', $data);

    // Attach phone to the contact
      $data = array(
        'contact_id' => $org_contact_id,
        'location_type_id' 	=> 4, //Otro
        'phone' => $org_phone,
        'is_primary' => 0,
        'phone_type_id' => 1,
      );
      $new_phone = civicrm_api3('phone', 'create', $data);

    // 	Attach the adrress information to the contact
      $data = array(
        'contact_id' 			=> $org_contact_id,
        'location_type_id' 	=> 4, //Otro
        'street_address' 		=> $org_street_address,
        'state_province_id' 			=> $org_state_id,
        'country_id' 			=> $org_country_id,
        'city' 				=> $org_city,
        'is_primary' 			=> 0,
      );
      $new_address = civicrm_api3('address', 'create', $data);

    //	Add the contact to certain groups
      if (!empty($grupos_agregar)) {
        foreach($grupos_agregar as $value) {
          $data = array(
            'contact_id' 		=> $org_contact_id,
            'group_id' 		=> $value,
          );
          $new_grupos_agregar = civicrm_api3('group_contact', 'create', $data);
        }
      }

      // Crea relación entre el responsable y la organización
      $params = array(
        'contact_id_a' => $contact_id,
        'contact_id_b' => $org_contact_id,
        'relationship_type_id' => 5, // Miembro de: /wp-admin/admin.php?page=CiviCRM&q=civicrm/admin/reltype&action=update&id=5&reset=1
        'start_date' => $date,
        'is_active' => 1,
        'note' => 'Relación creada por medio del formulario de membresías',
      );
      $result = civicrm_api3('relationship', 'create', $params);

    }

    //  Crea la membresia **************************************

    switch ($medio) {
      case 1: // Tarjeta
        $medio_label = sprintf(_('Tarjeta de credito'));
        $cardnumber = EncryptData($cardnumber); // Encriptamos el n° de la tc
        $cardexpiry = $cardexpiry; // la fecha de expiración
        $cardcvv = EncryptData($cardcvv); // Encriptamos el CVV
        break;
      case 2: // Transferencia
        $medio_label = sprintf(_('Transferencia bancaria'));
        $cardtype = "Transferencia";
        break;
      case 3: // Paypal
        $medio_label = sprintf(_('Paypal'));
        $cardtype = "Paypal";
        break;
      case 4: // Paypal
        $medio_label = sprintf(_('MercadoPago'));
        $cardtype = "MercadoPago";
        break;
    }

    if ($titulo != 2) { // Aporte individual
      $donation = array(
    	  'contact_id' => $contact_id,
    	  'membership_type_id' => $donationtype, // Usa el id del tipo de membresía
    	  'join_date' => $date, // '2014-09-21',
    	//'start_date' => '',
    	//'end_date' => '',
    	  'source' => $source,
    	//'is_override' => 1,
    	  'status_id' => 2, // Estado: Nuevo
    	  'custom_1' => $cardnumber,
    	  'custom_2' => $cardtype,
    	  'custom_3' => $cardammount,
        'custom_5' => $cardexpiry,
        'custom_6' => $cardcvv,
    	);
      $result = civicrm_api3('membership', 'create', $donation);

    //	Add a note to the contact
      switch ($donationtype) {
        case 1:
          $add_note_title = "Dió de alta su membresia";
          $add_note = 'Donará u$d'.$cardammount.' por mes';
          $donationtype_label = sprintf(_('Mensual'));
          break;
        case 2:
          $add_note_title = "Realizó una donación única";
          $add_note = 'Donó u$d'.$cardammount;
          $donationtype_label = sprintf(_('Única'));
          break;
      }
      if (!empty($add_note)) {
        $note = civicrm_api('Note','Create',array('entity_id' => $contact_id, 'note' => $add_note, 'subject' => $add_note_title, 'contact_id' => $yo, 'version' =>3, 'json' => '1'));
      }


    } else {
      // Se crea el aporte colectivo
      $donation = array(
        'contact_id' => $org_contact_id,
        'membership_type_id' => $donationtype, // Usa el id del tipo de membresía
        'join_date' => $date, // '2014-09-21',
        'source' => $source,
        'status_id' => 2, // Estado: Nuevo
        'custom_1' => $cardnumber,
        'custom_2' => $cardtype,
        'custom_3' => $cardammount,
        'custom_4' => $contact_id, // id del representante se usa como titular de tarjeta
        'custom_5' => $cardexpiry,
        'custom_6' => $cardcvv,
      );
      $result = civicrm_api3('membership', 'create', $donation);

    //	Add a note to the contact
      switch ($donationtype) {
        case 1:
          $add_note_title = "Dió de alta su membresia";
          $add_note = 'Donará u$d'.$cardammount.' por mes';
          $add_note_title_org = 'Dió de alta una membresia colectiva como representante';
          $add_note_org = 'Es el representante de la organización '.$name.' (ID:'.$org_contact_id.')que donará u$d'.$cardammount.' por mes';

          $donationtype_label = sprintf(_('Mensual'));
          break;
        case 2:
          $add_note_title = "Realizó una donación única";
          $add_note = 'Donó u$d'.$cardammount;
          $add_note_title_org = 'Hizo una donacion colectiva como representante';
          $add_note_org = 'Es el representante de la organización '.$name.' (ID:'.$org_contact_id.')que donará u$d'.$cardammount.' por única vez';
          $donationtype_label = sprintf(_('Única'));
          break;
      }
      if (!empty($add_note)) {
        // Nota para la organizacion
        $note = civicrm_api('Note','Create',array('entity_id' => $org_contact_id, 'note' => $add_note, 'subject' => $add_note_title, 'contact_id' => $yo, 'version' =>3, 'json' => '1'));
        // Nota para el representante
        $note = civicrm_api('Note','Create',array('entity_id' => $contact_id, 'note' => $add_note, 'subject' => $add_note_title, 'contact_id' => $yo, 'version' =>3, 'json' => '1'));
      }

    } // Fin del else de membresia colectiva

  // Envio de mensajes

    $ssl      = ( ! empty( $_SERVER[HTTPS] ) && $_SERVER[HTTPS] == 'on' );
    $sp       = strtolower( $_SERVER[SERVER_PROTOCOL] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $blog_link = str_replace("donar.","","http://$_SERVER[HTTP_HOST]"); // http harcodeado hasta habilitar SSL en todos los dominios
    $actual_link = urlencode("$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUST_URI]");

  //	Primero verifica si es una donacion o una membresia
    switch ($donationtype) {
      case 1: // Es una membresia

        switch ($medio) { // Revisamos el medio de pago
          case 1: // Tarjeta
            if ($titulo != 2) {
              // Mensaje para el miembro individual
              $mensaje = sprintf(_('email-notificacion-membresia-user'),$first_name,$cardammount,$cardtype);
            } else {
              // Mensaje para el miembro colectivo
              $mensaje = sprintf(_('email-notificacion-membresia-userorg'),$org_name,$cardammount,$cardtype,$first_name,$last_name);
              $mensaje_rep = sprintf(_('email-notificacion-membresia-userorg-rep'),$first_name,$org_name,$cardammount,$cardtype);
            }
            break;
          case 2: // Transferencia
            $monto_anual = ($cardammount * 12);
            if ($titulo != 2) {
              // Mensaje para el miembro individual
              $mensaje = sprintf(_('email-notificacion-membresia-user-2'),$first_name,$cardammount,$cardtype,$monto_anual);
            } else {
              // Mensaje para el miembro colectivo
              $mensaje = sprintf(_('email-notificacion-membresia-userorg-2'),$org_name,$cardammount,$cardtype,$first_name,$last_name, $monto_anual);
              $mensaje_rep = sprintf(_('email-notificacion-membresia-userorg-rep-2'),$first_name,$org_name, $cardammount,$donationtype_label,$monto_anual);
            }
            break;

          case 3: // Paypal
            $monto_anual = ($cardammount * 12);
            if ($titulo != 2) {
              // Mensaje para el miembro individual
              $mensaje = sprintf(_('email-notificacion-membresia-user-3'),$first_name,$cardammount,$cardtype,$monto_anual);
            } else {
              // Mensaje para el miembro colectivo
              $mensaje = sprintf(_('email-notificacion-membresia-userorg-3'),$org_name,$cardammount,$cardtype,$first_name,$last_name, $monto_anual);
              $mensaje_rep = sprintf(_('email-notificacion-membresia-userorg-rep-3'),$first_name,$org_name, $cardammount,$donationtype_label,$monto_anual);
            }
            $PaypalRedirect = "/paypal.php?l=es&val={$cardammount}&tipo=membresia&email={$email}&phone={$phone}&first_name={$first_name}&last_name={$last_name}&street_address={$street_address}&city={$city}&zipcode={$zipcode}&state_id={$state_id}&country_id={$country_id}";
            break;

          case 4: // MercadoPago
            $monto_anual = ($cardammount * 12);
            if ($titulo != 2) {
              // Mensaje para el miembro individual
              $mensaje = sprintf(_('email-notificacion-membresia-user-4'),$first_name,$cardammount,$cardtype,$monto_anual);
            } else {
              // Mensaje para el miembro colectivo
              $mensaje = sprintf(_('email-notificacion-membresia-userorg-4'),$org_name,$cardammount,$cardtype,$first_name,$last_name, $monto_anual);
              $mensaje_rep = sprintf(_('email-notificacion-membresia-userorg-rep-4'),$first_name,$org_name, $cardammount,$donationtype_label,$monto_anual);
            }

            $MPRedirect = "/mp.php?tipo=membresia&val=".$cardammount ;


            break;
        }

        if ($titulo != 2) {
          // Mensaje para el miembro individual
          $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
              'Reply-To: financiacion@reevo.org' . "\r\n" .
              'X-Mailer: PHP/' . phpversion();
          mail($email, sprintf(_('¡Recibimos tu solicitud de membresía!')), $mensaje, $cabeceras);

          // Mensaje para admin si el miembro es individual
          $link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$contact_id;

          $mensaje = "
          Nombre: $first_name $last_name
          Ciudad y País: $city, $country_id
          Monto: $cardammount
          Tipo: $donationtype_label
          Medio de pago: $medio_label
          Idioma: $idioma

          Perfil en CRM: $link_profile
          ";

          $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
              'Reply-To: '. $email . "\r\n" .
              'X-Mailer: PHP/' . phpversion();
          mail('financiacion@reevo.org', "Solicitud de membresía recibida: $first_name $last_name", $mensaje, $cabeceras);

        } else {
          // Mensaje para el miembro colectivo (se manda a la orga y a su representante)
          // para represenante
          $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
              'Reply-To: financiacion@reevo.org' . "\r\n" .
              'X-Mailer: PHP/' . phpversion();
          mail($email, sprintf(_('¡Recibimos tu solicitud de membresía!')), $mensaje-rep, $cabeceras);
          // para la organización
          mail($org_email, sprintf(_('¡Recibimos tu solicitud de membresía!')), $mensaje, $cabeceras);

          // Mensaje para admin si el miembro es colectivo
          $link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$contact_id;
          $org_link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$org_contact_id;

          $mensaje = "
          Nombre del proyecto: $org_name
          Ciudad y País: $org_city, $org_country_id
          Monto: $cardammount
          Tipo: $donationtype_label
          Medio de pago: $medio_label
          Idioma: $idioma

          Nombre del represenante: $first_name $last_name
          Ciudad y País: $city, $country_id
          ----
          Perfil en CRM de la organización: $org_link_profile
          Perfil en CRM del representante: $link_profile
          ";

          $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
              'Reply-To: '. $email . "\r\n" .
              'X-Mailer: PHP/' . phpversion();
          mail('financiacion@reevo.org', "Solicitud de membresía colectiva recibida: $org_name", $mensaje, $cabeceras);
        }

      break;
      case 2: // Es una donacion unica

      switch ($medio) { // Revisamos el medio de pago
        case 1: // Tarjeta
          if ($titulo != 2) {
            // Mensaje para el donacion individual
            $mensaje = sprintf(_('email-notificacion-donacion-user'),$first_name,$cardammount,$cardtype);
          } else {
            // Mensaje para el donacion colectiva
            $mensaje = sprintf(_('email-notificacion-donacion-userorg'),$org_name,$cardammount,$cardtype,$first_name,$last_name);
            $mensaje_rep = sprintf(_('email-notificacion-donacion-userorg-rep'),$first_name,$org_name,$cardammount,$cardtype);
          }

          break;
        case 2: // Transferencia
          $monto_anual = ($cardammount * 12);
          if ($titulo != 2) {
            // Mensaje para el miembro individual
            $mensaje = sprintf(_('email-notificacion-donacion-user-2'),$first_name,$cardammount,$cardtype,$monto_anual);
          } else {
            // Mensaje para el miembro colectivo
            $mensaje = sprintf(_('email-notificacion-donacion-userorg-2'),$org_name,$cardammount,$cardtype,$first_name,$last_name, $monto_anual);
            $mensaje_rep = sprintf(_('email-notificacion-donacion-userorg-rep-2'),$first_name,$org_name, $cardammount,$cardtype,$monto_anual);
          }
          break;
        case 3: // Paypal
          $monto_anual = ($cardammount * 12);
          if ($titulo != 2) {
            // Mensaje para el miembro individual
            $mensaje = sprintf(_('email-notificacion-donacion-user-3'),$first_name,$cardammount,$cardtype,$monto_anual);
          } else {
            // Mensaje para el miembro colectivo
            $mensaje = sprintf(_('email-notificacion-donacion-userorg-3'),$org_name,$cardammount,$cardtype,$first_name,$last_name, $monto_anual);
            $mensaje_rep = sprintf(_('email-notificacion-donacion-userorg-rep-3'),$first_name,$org_name, $cardammount,$cardtype,$monto_anual);
          }
          $PaypalRedirect = "/paypal.php?l=es&val={$cardammount}&tipo=donacion&email={$email}&phone={$phone}&first_name={$first_name}&last_name={$last_name}&street_address={$street_address}&city={$city}&zipcode={$zipcode}&state_id={$state_id}&country_id={$country_id}";

          break;
      }

      // Envio de mensajes
      if ($titulo != 2) {
        // Mensaje para el miembro individual
        $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
            'Reply-To: financiacion@reevo.org' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($email, sprintf(_('¡Gracias por tu donación!')), $mensaje, $cabeceras);

        // Mensaje para admin si el miembro es individual
        $link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$contact_id;

        $mensaje = "
        Nombre: $first_name $last_name
        Ciudad y País: $city, $country_id
        Monto: $cardammount
        Tipo: $donationtype_label
        Medio de pago: $medio_label
        Idioma: $idioma

        Perfil en CRM: $link_profile
        ";

        $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
            'Reply-To: '. $email . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail('financiacion@reevo.org', "Donación única recibida: $first_name $last_name", $mensaje, $cabeceras);

      } else {
        // Mensaje para el miembro colectivo (se manda a la orga y a su representante)
        // para represenante
        $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
            'Reply-To: financiacion@reevo.org' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($email, sprintf(_('¡Gracias por tu donación!')), $mensaje-rep, $cabeceras);
        // para la organización
        mail($org_email, sprintf(_('¡Gracias por su donación!')), $mensaje, $cabeceras);

        // Mensaje para admin si el miembro es colectivo
        $link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$contact_id;
        $org_link_profile = $blog_link.'/wp-admin/admin.php?page=CiviCRM&q=civicrm/contact/view&cid='.$org_contact_id;

        $mensaje = "
        Nombre del proyecto: $org_name
        Ciudad y País: $org_city, $org_country_id
        Monto: $cardammount
        Tipo: $donationtype_label
        Medio de pago: $medio_label
        Idioma: $idioma

        Nombre del represenante: $first_name $last_name
        Ciudad y País: $city, $country_id
        ----
        Perfil en CRM de la organización: $org_link_profile
        Perfil en CRM del representante: $link_profile
        ";

        $cabeceras = 'From: financiacion@reevo.org' . "\r\n" .
            'Reply-To: '. $email . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail('financiacion@reevo.org', "Solicitud de donación única colectiva recibida: $org_name", $mensaje, $cabeceras);
      }
      break;
    }


    // Agrega contacto a la Libreta de contactos de miembros@reevo.org (para uso en WhatsApp)

    $name = $first_name . ' ' . $last_name;
    $phoneNumber = $phone;
    $emailAddress = $email;
    $note = "Agregado via donar.reevo.org";

    $newContact = ContactFactory::create($name, $phoneNumber, $emailAddress, $note);
    print_r($newContact);

    // Reenvia a formulario de Paypal
    if ($medio == 3) {
      header('Location: '. $PaypalRedirect);
    }

    // Reenvia a formulario de MercadoPago
    if ($medio == 4) {
      header('Location: '. $MPRedirect);
    }

  error_log('A TITULO: '.$titulo, 0);

} // Fin del POST

?>
