<?php 
    
    /*
     *  Template Name: Professionals Register Template
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
                    date_default_timezone_set("America/Argentina/Buenos_Aires");
                    setlocale(LC_TIME, 'es_ES');
                    $rol = $wpdb->get_row( 'SELECT * FROM '.$rolesTable.' WHERE name = "'.decryptIt($data['type']).'"');
                    $success=$wpdb->insert($usersTable, [
                        'first_name'    => $data['firstName'],
                        'last_name'     => $data['lastName'],
                        'email'         => $data['email'],
                        'password'      => encryptIt($data['password']),
                        'token'         => $accountToken,
                        'rol'           => $rol->id,
                        'server'        => json_encode($_SERVER),
                        'timestamp'     => date('Y-m-d H:i:s', time())
                    ]);

                    if($success){
                        $data['to'] = $data['email'];
                        $sendedEmail = sendProfessionalRegisterEmail($data);
                        if($sendedEmail){
                            $wpdb->update($usersTable, ['mail_sent'=>1], ['email'=>$data['email']]);    
                        }
                    } else {
                        $warning = 'Hubo un problema en el guardado de sus datos, por favor, intente nuevamente y si persiste, contáctese con los administradores.';
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
                <?php if(!empty($success)):?>
                <div class="col-xs-12 alert alert-success" role="alert">
                    <b>Sus datos se han guardado satisfactoriamente</b>
                    <?php if(!empty($sendedEmail)):?>
                    <p>
                        Le hemos enviado un email a su correo para que pueda seguir los próximos pasos, por favor, revise su correo y siga las instrucciones.<br>
                        <b>Importante:</b> Recuerde verificar su casilla de correo no deseado, o esperar unos minutos para recibir el email.
                    </p>
                    <?php endif;?>
                </div>
                <?php elseif(isset($warning)):?>
                <div class="col-xs-12 alert alert-warning" role="alert">
                    <?php echo $warning;?>
                </div>
                <?php endif;?>

                <div class="col-xs-10 col-xs-offset-1 form-wrap box">
                    <div class="col-xs-12 register-form">
                        <h4 class="col-xs-12">Profesionales: Registro</h4>
                        <?php set_query_var('type', encryptIt('professional'));?>
                        <?php get_template_part('register-form');?>
                    </div>
                </div>
            </div>

        </article>

        <?php if(empty($_POST)):?>
        <!-- Button trigger modal -->
        <button class="btn btn-primary btn-lg hidden" id="modal" data-toggle="modal" data-target="#professionalsRegisterModal"></button>
        <!-- Modal -->
        <div class="modal fade" id="professionalsRegisterModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="vertical-alignment-helper">
                <div class="modal-dialog vertical-align-center professionals-register-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Información importante para los profesionales</h3>
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="col-xs-12 no-padding">
                                <p>Los profesionales que se inscriban en Mesa Profesional deberán estar al tanto del proceso de inscripción al sitio. El proceso consta de 3 pasos:</p>
                                <ol>
                                    <li>Inscripción a través del formulario de registro</li>
                                    <li>Autorización por parte de los administradores</li>
                                    <li>Abono de la suscripción a través de Mercado Pago</li>
                                </ol>
                                <p>Una vez que los profesionales se inscriben a Mesa Profesional, la inscripción <b>quedará en estado pendiente</b> hasta que uno de nuestros administradores autorice la misma. Al autorizar el registro, se enviará un email al correo electrónico especificado por el profesional, indicando el estado de la autorización y el link para abonar la suscripción.</p>
                                <p>Posteriormente, el profesional deberá abonar la suscripción a través de Mercado Pago. Una vez concluído el pago, el profesional podrá acceder a las funcionalidades del sitio.</p>
                                <p>¡Muchas gracias por inscribirse en Mesa Profesional!</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="pull-right">
                                <button class="col-xs-12 btn btn-success ld-ext-right hovering submit">
                                    Aceptar
                                    <div class="ld ld-ring ld-spin"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif;?>





    <?php endwhile;

endif;

get_footer();

?>