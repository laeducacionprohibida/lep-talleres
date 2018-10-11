var telInput = $("#phone");
var errorMsg = $("#error-msg");
var validMsg = $("#valid-msg");

// initialise plugin
telInput.intlTelInput({
  utilsScript: "/js/utils.js"
});

var reset = function() {
  telInput.removeClass("error");
  errorMsg.addClass("hide");
  validMsg.addClass("hide");
};

// on blur: validate
telInput.blur(function() {
  reset();
  if ($.trim(telInput.val())) {
    if (telInput.intlTelInput("isValidNumber")) {
      console.log('VALIDO');

      validMsg.removeClass("hide");
    } else {
      console.log('NO VALIDO');

      telInput.addClass("error");
      errorMsg.removeClass("hide");
    }
  }
});

// on keyup / change flag: reset
telInput.on("keyup change", reset);
