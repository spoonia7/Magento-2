require(['jquery'],function($){
function createCookie(name, value, days) {
    var expires;

    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toGMTString();
    } else {
        expires = "";
    }
    document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = encodeURIComponent(name) + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) === ' ')
            c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0)
            return decodeURIComponent(c.substring(nameEQ.length, c.length));
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}

  $(document).on('click','#checkout #shipping-method-buttons-container .button.action.continue, #checkout .iwd_opc_place_order_button',function(){
      
    var emlcok_nm = 'gustuser'
    var getemil = $("#checkout #customer-email").val();
    if (getemil == '' || getemil == undefined) {
        getemil = $('#checkout #iwd_opc_login input[type="email"]').val();
    }
    eraseCookie(emlcok_nm);
    createCookie(emlcok_nm,getemil,'7');
    var read_ck = readCookie(emlcok_nm);
    console.log(read_ck);
    console.log('4444');
  });

  $(document).on('keyup','#checkout #customer-email, #checkout #iwd_opc_login input[type="email"]',function(){
      
    var emlcok_nm = 'gustuser'
    var getemil = $("#checkout #customer-email").val();
    if (getemil == '' || getemil == undefined) {
        getemil = $('#checkout #iwd_opc_login input[type="email"]').val();
    }
    eraseCookie(emlcok_nm);
    createCookie(emlcok_nm,getemil,'7');
    var read_ck = readCookie(emlcok_nm);
    console.log(read_ck);
    console.log('8888');
  });

  jQuery('#checkout input[name=username]').on('blur', function() {
        alert('Handler for .blur() called1.');
    });
 }); 