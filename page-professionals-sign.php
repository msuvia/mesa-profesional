<?php 
    
    /*
     *  Template Name: Two Columns 50/50
     */

    if(!empty($_POST)){
        if(!empty($_POST['type'])){
            $validate = validateRegisterForm();
        
            if(!$validate['error']) {
                // database
                global $wpdb;
                $usersTable = 'users';
                $rolesTable = 'roles';
                $accountToken = generateToken(20);
                $data = $validate['data'];

                $user = $wpdb->get_row( 'SELECT * FROM '.$usersTable.' WHERE email = "'.$data['email'].'"');

                if(!$user) {
                    $rol = $wpdb->get_row( 'SELECT * FROM '.$rolesTable.' WHERE name = "'.decryptIt($data['type']).'"');
                    $success=$wpdb->insert($usersTable, [
                        'first_name'    => $data['firstName'],
                        'last_name'     => $data['lastName'],
                        'email'         => $data['email'],
                        'password'      => encryptIt($data['password']),
                        'token'         => $accountToken,
                        'rol'           => $rol->id,
                        'server'        => json_encode($_SERVER)
                    ]);

                    if($success){
                        $emailTo = $data['email'];
                        if (!isset($emailTo) || ($emailTo == '') ){
                            $emailTo = get_option('admin_email');
                        }

                        $link = home_url('/');
                        $subject = '¡Gracias por registrarse a Mesa Profesional!';
                        
                        $body  = 'Hola '.$data['firstName'].' '.$data['lastName']. '!<br><br>';
                        $body .= 'Gracias por registrarte a Mesa Profesional.<br><br>';
                        $body .= 'Su registro se encuentra pendiente de moderación, le enviaremos un email cuando los administradores aprueben o no, su solicitud.<br><br>';
                        $body .= $link.'<br><br>';
                        $body = htmlspecialchars_decode($body);

                        wp_mail($emailTo, $subject, $body, $headers);
                        $wpdb->update($usersTable, ['mail_sent'=>1], ['email'=>$data['email']]);
                    }
                }
                else {
                    $warning = 'El email de registro ya se encuentra en uso, por favor elija otro y vuelva a registrarse.';
                }
            } else {
                $warning = $validate['error'];
            }
        } else {
            $validate = validateLoginForm();

            if(!$validate['error']){
                $login = logIn($validate['data']);
                if(!$login['error']){
                    // redirect to ?
                    wp_redirect(home_url('/profesionales/dashboard'),301);
                } else {
                    $warning = 'No se pudo iniciar sesión, por favor, intente nuevamente';
                }
            } else {
                $warning = $validate['error'];
            }
        }
    }


    get_header();

?>



<?php

if(have_posts()):
    while (have_posts()): the_post();?>
        
        <article class="page professionals-sign">
            <div class="col-xs-12">
                <?php if(isset($success) && true==$success):?>
                <div class="alert alert-success" role="alert">
                    <div class="col-xs-12 no-padding"><b>Sus datos se han guardado satisfactoriamente</b></div>
                    <span>Le hemos enviado un email a su correo para que pueda seguir los próximos pasos, por favor, revise su correo y siga las instrucciones</span>
                </div>
                <?php elseif(isset($warning)):?>
                <div class="col-xs-12 alert alert-warning" role="alert">
                    <?php echo $warning;?>
                </div>
                <?php endif;?>

                <div class="col-xs-12 form-wrap box">
                    <div class="col-xs-4 col-xs-offset-1 register-form">
                        <h4 class="col-xs-12">Profesionales: Registro</h4>
                        <?php set_query_var('type', encryptIt('professional'));?>
                        <?php get_template_part('register-form');?>
                    </div>
                    <div class="col-xs-4 col-xs-offset-2 login-form">
                        <h4 class="col-xs-12">Profesionales: Iniciar sesión</h4>
                        <?php get_template_part('login-form');?>
                    </div>
                </div>
            </div>

        </article>

    <?php endwhile;

endif;

get_footer();

?>