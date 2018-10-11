var langs = ['en','es'];
var langCode = '';
var langJS = null;


var translate = function (jsdata)
{	
	$("[tkey]").each (function (index)
	{
		var strTr = jsdata [$(this).attr ('tkey')];
	    $(this).html (strTr);
	});
}

langCode = navigator.language.substr (0, 2);

if (langs.indexOf(langCode) > -1) {
	$.getJSON('lang/'+langCode+'.json', translate);
	console.log("LOG: cargando traducción para "+langCode);
}
else {
	$.getJSON('lang/en.json', translate);
	console.log("LOG: no hay tradución");
}


