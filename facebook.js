$(document).ready(function(){
    $('#fb-login-btn').on('click',function(){
      FB.login(function (response){
        //console.log(response);
        if (response.authResponse){
            // get userData
            FB.api('/me?fields=email,first_name,last_name,picture,age_range', function (user) {
                //user.picture.data.url = user.picture.data.url.replace('https://','');
                //console.log(user);
                var loginType='facebook';
                $.ajax({
                  url: '/login',
                  type: 'POST',
                  data: {user: user, loginType: loginType}
                })
                .done(function( data ) {
                  if (typeof data === 'string' || data instanceof String) {
                    data = JSON.parse(data);
                  }
                  if(data.status == 'OK') {
                      window.location.href = 'https://'+window.location.host;
                  } else {
                      var message = 'Hubo un error al iniciar sesión, intente nuevamente';
                      var html = '<div class="alert alert-warning" role="alert"><b>Error al iniciar sesión</b><br>'+message+'</div>';
                      $('#fb-login-btn').parent().prepend(html);
                  }
                })
                .fail(function() {
                    var message = 'Hubo un error inesperado, por favor intente más tarde';
                    var html = '<div class="alert alert-warning" role="alert"><b>Error al iniciar sesión</b><br>'+message+'</div>';
                    $('#fb-login-btn').parent().prepend(html);
                });
            });
        }
        else{
            console.log('No autorizado');
        }
      },{
          //scope:'email,public_profile,age_range'
          scope:'email,public_profile,user_friends'
      });
    });


    $('#fb-register-btn').on('click',function(){
        FB.login(function (response){
            console.log(response);
            if (response.authResponse){
              FB.api('/me?fields=email,first_name,last_name,picture,age_range', function (user) {
                user.picture.data.url = user.picture.data.url.replace('https://','');
                console.log(user);
                var registerType='facebook';
                var operation='register';
                $.ajax({
                  url: '/clientes/registro',
                  type: 'POST',
                  data: {user: user, registerType: registerType, operation: operation}
                })
                .done(function( data ) {
                  if (typeof data === 'string' || data instanceof String) {
                    data = JSON.parse(data);
                  }
                  if(data.status == 'OK') {
                      $('#fb-login-btn').trigger('click');
                  } else if(data.status == 'EXISTS'){
                      $('#fb-login-btn').trigger('click');
                  } else {
                      var message = 'Hubo un error al registrar el usuario, por favor intente más tarde';
                      var html = '<div class="alert alert-warning" role="alert"><b>Error en el proceso de registro</b><br>'+message+'</div>';
                      $('#fb-register-btn').parent().prepend(html);
                  }
                })
                .fail(function() {
                    var message = 'Hubo un error inesperado, por favor intente más tarde';
                    var html = '<div class="alert alert-warning" role="alert"><b>Error en el proceso de registro</b><br>'+message+'</div>';
                    $('#fb-register-btn').parent().prepend(html);
                });
            });
          }
        });
      });


});




