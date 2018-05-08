<?php 

    /*
    *   Template Name: Clients Register Template
    */


    if(!empty($_POST)){
        $usersTable = 'users';
        $rolesTable = 'roles';
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        setlocale(LC_TIME, 'es_ES');

        $registerType=(!empty($_POST['registerType'])) ? $_POST['registerType'] : 'local';
        if($registerType=='local'){
            $validate = validateRegisterForm();
            if(!$validate['error']) {
                // database
                global $wpdb;
                $data = $validate['data'];
                $user = $wpdb->get_row( 'SELECT * FROM '.$usersTable.' WHERE email = "'.$data['email'].'"');
                if(!$user) {
                    $data['accountToken'] = generateToken(20);

                    $rol = $wpdb->get_row( 'SELECT * FROM '.$rolesTable.' WHERE name = "'.decryptIt($data['type']).'"');
                    $success=$wpdb->insert($usersTable, [
                        'first_name'    => $data['firstName'],
                        'last_name'     => $data['lastName'],
                        'email'         => $data['email'],
                        'password'      => encryptIt($data['password']),
                        'token'         => $data['accountToken'],
                        'rol'           => $rol->id,
                        'server'        => json_encode($_SERVER),
                        'timestamp'     => date('Y-m-d H:i:s', time())
                    ]);

                    if($success){
                        $data['to'] = $data['email'];
                        $sendedEmail = sendClientRegisterEmail($data);
                        if($sendedEmail){
                            $wpdb->update($usersTable, ['mail_sent'=>1], ['email'=>$data['email']]);
                        }
                    } else {
                        $warning = 'Hubo un problema en el guardado de sus datos, por favor, intente nuevamente y si persiste, contáctese con los administradores.';
                    }
                } else {
                    $warning = 'El email de registro ya se encuentra en uso, por favor elija otro y vuelva a registrarse.';
                }
            }
        }
        elseif($registerType=='facebook'){
            $fbUser = $_POST['user'];
            global $wpdb;
            $localUser = $wpdb->get_row( 'SELECT * FROM '.$usersTable.' WHERE email = "'.$fbUser['email'].'"');
            if(!$localUser) {
                // register
                $success=$wpdb->insert($usersTable, [
                    'first_name'        => $fbUser['first_name'],
                    'last_name'         => $fbUser['last_name'],
                    'email'             => $fbUser['email'],
                    'fb_user_id'        => $fbUser['id'],
                    'picture_url'       => $fbUser['picture']['data']['url'],
                    'actived_account'   => 1,
                    'rol'               => 3,
                    'server'            => json_encode($_SERVER),
                    'timestamp'         => date('Y-m-d H:i:s', time())
                ]);
                if($success){
                    echo json_encode(['status'=>'OK']);die;
                } else {
                    echo json_encode(['status'=>'ERROR']);die;
                }
            } else {
                echo json_encode(['status'=>'EXISTS']);die;
            }
        }
        
    }
    

    get_header();

?>






<?php

if(have_posts()):
    while (have_posts()): the_post(); ?>
        
        <?php if(!empty($success)):?>
        <div class="col-xs-10 col-xs-offset-1 alert alert-success" role="alert">
            <b>Sus datos se han guardado satisfactoriamente</b>
            <?php if(!empty($sendedEmail)):?>
            <p>
                Hemos enviado un email a su casilla de correo para que pueda activar su cuenta, por favor, revíselo y siga las instrucciones.<br>
                <b>Importante:</b> Recuerde verificar su casilla de correo no deseado, o esperar unos minutos para recibir el email.
            </p>
            <?php endif;?>
        </div>
        <?php elseif(isset($warning)):?>
        <div class="col-xs-10 col-xs-offset-1 alert alert-warning" role="alert">
            <?php echo $warning;?>
        </div>
        <?php endif;?>

        <article class="post page">

            <div class="col-xs-10 col-xs-offset-1 form-wrap box">

                <div class="col-xs-12">
                    <?php the_content();?>
                </div>

                <div class="col-xs-12 no-padding">
                    <div class="col-xs-6">
                        <?php set_query_var('type', encryptIt('user'));?>
                        <?php get_template_part('register-form');?>
                    </div>

                    <div class="col-xs-6">
                        <button type="button" id="fb-register-btn">
                            <i class="fab fa-facebook pull-left"></i>
                            <span class="pull-left">Registrarse con Facebook</span>
                        </button>
                        <button type="button" id="fb-login-btn" class="hidden">
                            <i class="fab fa-facebook pull-left"></i>
                            <span class="pull-left">Iniciar sesión con Facebook</span>
                        </button>
                    </div>

                </div>
                
            </div>

            <div class="col-xs-4">
                &nbsp;
            </div>

        </article>

    <?php endwhile;

endif;

get_footer();

?>