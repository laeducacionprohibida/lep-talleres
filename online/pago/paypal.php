<?php

// Este script redirige a la pantalla de Paypal segun el tipo aporte, simula el click en el boton del formulario e indica el valor de la donacion


$email = $_GET['email'];
$phone = $_GET['phone'];
$first_name = urlencode(ucwords(strtolower($_GET['first_name'])));
$last_name = urlencode(ucwords(strtolower($_GET['last_name'])));
$street_address = $_GET['street_address'];
$city = ucwords(strtolower($_GET['city']));
$zipcode = $_GET['zipcode'];
$state_id = $_GET['state_id'];
$country_id = $_GET['country_id'];



?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo sprintf(_('Reevo: Tejiendo alternativas en educación'));?></title>

	<script src="js/jquery-2.1.4.js"></script>

	<script type="text/javascript">
		// Shorthand for $( document ).ready()
		$(function() {
			var val = '<?php echo $_GET['val']; ?>';
			$('#os0').val(val);
			$( "#submit-<?php echo $_GET['tipo']; ?>" ).click();
		});

	</script>


</head>
<body>
	<form style="display:none;" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="QLV3FL93GCNUQ">

	<INPUT TYPE="hidden" NAME="first_name" VALUE="<?php echo $first_name; ?>">
	<INPUT TYPE="hidden" NAME="last_name" VALUE="<?php echo $last_name; ?>">
	<INPUT TYPE="hidden" NAME="address1" VALUE="<?php echo $street_address; ?>">
	<INPUT TYPE="hidden" NAME="address2" VALUE="">
	<INPUT TYPE="hidden" NAME="city" VALUE="<?php echo $city; ?>">
	<INPUT TYPE="hidden" NAME="state" VALUE="<?php echo $state_id; ?>">
	<INPUT TYPE="hidden" NAME="zip" VALUE="<?php echo $zipcode; ?>">
	<INPUT TYPE="hidden" NAME="email" VALUE="<?php echo $email; ?>">
	<INPUT TYPE="hidden" NAME="lc" VALUE="AR">


	<INPUT TYPE="hidden" NAME="night_phone_a" VALUE="<?php echo $phone; ?>">
	<INPUT TYPE="hidden" NAME="night_phone_b" VALUE="<?php echo $phone; ?>">
	<INPUT TYPE="hidden" NAME="night_phone_c" VALUE="<?php echo $phone; ?>">
	<table>
	<tr><td><input type="hidden" name="on0" value=""></td></tr><tr><td>
		<select name="os0" id="os0">
			<option value="5">5 : $5,00 USD - mensual</option>
			<option value="10">10 : $10,00 USD - mensual</option>
			<option value="20">20 : $20,00 USD - mensual</option>
			<option value="50">50 : $50,00 USD - mensual</option>
			<option value="100">100 : $100,00 USD - mensual</option>
		</select> </td></tr>
	</table>
	<input type="hidden" name="currency_code" value="USD">
	<input id="submit-membresia" type="image" src="https://www.paypalobjects.com/es_XC/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
	</form>
<?php
	echo '<center><img src="img/paypal.gif" /></center>';
?>

<form style="display:none;"  action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
	<input type="hidden" name="cmd" value="_donations">
	<input type="hidden" name="business" value="588D3J7P68XN2">

	<INPUT TYPE="hidden" NAME="first_name" VALUE="<?php echo $first_name; ?>">
	<INPUT TYPE="hidden" NAME="last_name" VALUE="<?php echo $last_name; ?>">
	<INPUT TYPE="hidden" NAME="address1" VALUE="<?php echo $street_address; ?>">
	<INPUT TYPE="hidden" NAME="address2" VALUE="">
	<INPUT TYPE="hidden" NAME="city" VALUE="<?php echo $city; ?>">
	<INPUT TYPE="hidden" NAME="state" VALUE="<?php echo $state_id; ?>">
	<INPUT TYPE="hidden" NAME="zip" VALUE="<?php echo $zipcode; ?>">
	<INPUT TYPE="hidden" NAME="email" VALUE="<?php echo $email; ?>">
	<INPUT TYPE="hidden" NAME="lc" VALUE="AR">

	<INPUT TYPE="hidden" NAME="night_phone_a" VALUE="<?php echo $phone; ?>">
	<INPUT TYPE="hidden" NAME="night_phone_b" VALUE="<?php echo $phone; ?>">
	<INPUT TYPE="hidden" NAME="night_phone_c" VALUE="<?php echo $phone; ?>">

	<input type="hidden" name="lc" value="AR">
	<input type="hidden" name="item_name" value="Donación a Reevo (Red de Educación Alternativa)">
	<input type="hidden" name="no_note" value="1">
	<input type="hidden" name="no_shipping" value="1">
	<input type="hidden" name="rm" value="1">
	<input type="hidden" name="amount" value="<?php echo $_GET['val']; ?>.00">
	<input type="hidden" name="return" value="https://donar.reevo.org">
	<input type="hidden" name="cancel_return" value="https://donar.reevo.org">
	<input type="hidden" name="currency_code" value="USD">
	<INPUT TYPE="hidden" name="charset" value="utf-8">
	<input type="hidden" name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHosted">
	<input id="submit-donacion" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/es_XC/i/scr/pixel.gif" width="1" height="1">
</form>




</body>
</html>
