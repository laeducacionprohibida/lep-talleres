<?php
$val = $_GET['val'];
$tipo = $_GET['tipo'];

if ($tipo == 'membresia') {
	switch ($val) {
		case 5:
			$MPRedirect = "http://mpago.la/q995";
			break;
		case 10:
			$MPRedirect = "http://mpago.la/71YI";
			break;
		case 20:
			$MPRedirect = "http://mpago.la/xQud";
			break;
		case 50:
			$MPRedirect = "http://mpago.la/86lV";
			break;
		case 100:
			$MPRedirect = "http://mpago.la/A6aP";
			break;
	}
} else {
	// donacion unica
}

header('Location: '. $MPRedirect);

?>
