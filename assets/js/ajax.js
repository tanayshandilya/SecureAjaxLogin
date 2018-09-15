!function(){"use strict";window.addEventListener("load",function(){var t=document.getElementsByClassName("needs-validation");Array.prototype.filter.call(t,function(e){e.addEventListener("submit",function(t){!1===e.checkValidity()?(t.preventDefault(),t.stopPropagation()):dom_lock_inputs(),e.classList.add("was-validated")},!1)})},!1)}();

function dom_lock_inputs(){
    $('input').attr({'readonly':''});
    $('textarea').attr({'readonly':''});
    $('button[type="submit"]').attr({'disabled':''});
    $('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i>');
}

function dom_unlock_inputs(){
    $('input').removeAttr('readonly');
    $('button[type="submit"]').removeAttr('disabled');
    $('button[type="submit"]').html('Login');
}

function check_validation(){
	var FormData = $('.needs-validation'); response = [];
	for (var i = 0; i < FormData[0].length; i++){
		response[i] = FormData[0][i].checkValidity();
	}
	if ( jQuery.inArray( false,response ) > -1 ) {
		return false;
	}else{
		return true;
	}
}

function getFormData(form){
	var FormData = {};
	for (var i = 0; i < form.length-1; i++) {
		FormData[form[i].name] = form[i].value;
	}return JSON.stringify(FormData);
}

$('.login-form').on('submit', function(e){
	e.preventDefault();
	if ( check_validation() ) {
		$.get('/app/GetKey.php', { GetEncryptionKey : 1, KeyLength : 512 }, function(key){
			var iv = CryptoJS.enc.Utf8.parse('r0dkbnVQhklNeUGA');
			var FormData = CryptoJS.AES.encrypt(getFormData( $('.login-form')[0]), key, key,{iv: iv,mode: CryptoJS.mode.CBC,padding: CryptoJS.pad.Pkcs7}).toString();
			$.post('/app/UserLogin.php', { FormData : FormData }, function(response){
				response = JSON.parse(response);
				if ( response.status === 'success' ) {
					var url = '/'+response.token;
					window.location.replace(url);
				}else {
					$('#ajax-error').html(response.detail).fadeIn().delay(500).fadeOut();
					dom_unlock_inputs();
				}
			});
		});
	}else{
		dom_unlock_inputs();
	}
});