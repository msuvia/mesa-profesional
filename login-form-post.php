<?php 

    $formErrorMessages = [
        'emptyEmail'            => 'Por favor, ingrese su email',
        'invalidEmail'          => 'Por favor, ingrese un email válido',
        'emptyPassword'         => 'Por favor, ingrese una contraseña',
        'requiredField'         => 'Campo obligatorio'
    ];


    if (!empty($_POST)) {
        //d($_POST);
        $userExists = false;
        $hasError = false;
        $data = [];

        if(empty($_POST['login-email']) || trim($_POST['login-email']) === '')  {
            $data['emailError'] = $formErrorMessages['emptyEmail'];
            $hasError = true;
        } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['login-email']))) {
            $data['invalidEmailError'] = $formErrorMessages['invalidEmail'];
            $hasError = true;
        } else {
            $data['email'] = trim($_POST['login-email']);
        }

        if(empty($_POST['password']) || trim($_POST['password']) === '') {
            $data['passwordError'] = $formErrorMessages['emptyPassword'];
            $hasError = true;
        } else {
            $data['password'] = trim($_POST['password']);
        }

        //d($data);
    
        if(!$hasError) {

            // checking user mail...
            $login = logIn($data);
            if(!$login['error']){
                // redirect to ?
                wp_redirect(home_url('/'),301);
                //if(!(empty($_SERVER['REDIRECT_URL'])) && strpos($_SERVER['REDIRECT_URL'],'/login') !== false){
                //    wp_redirect(home_url('/'),301);    // Permanently redirect
                //}
            }
        }
    }
?>