<?php 

    //$mp = inicializeMercadoPago();
    //dd($mp->create_test_user());
    //dd($mp->get_payment_info('3649850873'));

    date_default_timezone_set("America/Argentina/Buenos_Aires");
    setlocale(LC_TIME, 'es_ES');

    if (!empty($_POST)) {
        $hasError = false;

        if(!isLoggedIn()){
            $hasError = true;
            $warningTitle = 'No hemos podido procesar su consulta';
            $warningText = 'Recuerde que para consultarnos usted debe tener una cuenta activa en Mesa Profesional. Para crear una cuenta o iniciar sesión haga click <a href="'.home_url('/login').'">aquí</a>.';
        }
        else{

            // check question quantity
            if(!hasQuestions()){
                $hasError = true;
                $warningTitle = 'No hemos podido procesar su consulta';
                $warningText = 'Usted ya no dispone de consultas para realizar, por favor abone uno de nuestros packs de preguntas para seguir consultando.';
            } else {
                $data = [];

                if(empty($_POST['first-name']) || trim($_POST['first-name']) === '') {
                    $nameError = 'Por favor, ingrese su nombre';
                    $hasError = true;
                } else {
                    $data['firstName'] = trim($_POST['first-name']);
                }

                if(empty($_POST['last-name']) || trim($_POST['last-name']) === '') {
                    $nameError = 'Por favor, ingrese su apellido';
                    $hasError = true;
                } else {
                    $data['lastName'] = trim($_POST['last-name']);
                }

                if(empty($_POST['email']) || trim($_POST['email']) === '')  {
                    $emailError = 'Por favor, ingrese su email';
                    $hasError = true;
                } else if (!preg_match("/^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$/i", trim($_POST['email']))) {
                    $emailError = 'Por favor, ingrese un email válido';
                    $hasError = true;
                } else {
                    $data['email'] = trim($_POST['email']);
                }

                if(empty($_POST['category']) || trim($_POST['category']) === '')  {
                    $emailError = 'Por favor, ingrese una categoría';
                    $hasError = true;
                } else {
                    $data['category'] = trim($_POST['category']);
                }

                if(empty($_POST['question']) || trim($_POST['question']) === '') {
                    $commentError = 'Por favor, ingrese su consulta';
                    $hasError = true;
                } else {
                    if(function_exists('stripslashes')) {
                        $data['question'] = stripslashes(trim($_POST['question']));
                    } else {
                        $data['question'] = trim($_POST['question']);
                    }
                }
            }
        }

        //dd($data);

        if(!$hasError) {

            // database
            global $wpdb;
            $usersTable = 'users';
            $questionsTable = 'questions';
            $user = $wpdb->get_row( 'SELECT * FROM '.$usersTable.' WHERE email = "'.$data['email'].'"');

            if($user){
                $question = $wpdb->get_row( 'SELECT * FROM '.$questionsTable.' WHERE user_id = "'.$user->id.'" AND category_id = "'.$data['category'].'" AND text = "'.$data['question'].'" AND mail_sent = "1"');
                
                if(!$question){
                    // save questions
                    
                    $questionToken = generateToken(20);
                    $success=$wpdb->insert($questionsTable,[
                        'user_id'       => $user->id,
                        'text'          => $data['question'],
                        'category_id'   => $data['category'],
                        'token'         => $questionToken,
                        'timestamp'     => date('Y-m-d H:i:s', time())
                    ]);

                    //dd($success);

                    if($success){
                        $sendedEmail = sendClientConsultationEmail([
                            'to'            => $data['email'],
                            'firstName'     => $data['firstName'],
                            'lastName'      => $data['lastName'],
                            'question'      => $data['question'],
                            'linkToDetail'  => home_url('/questions?token='.$questionToken)
                        ]);
                        if($sendedEmail){
                            $wpdb->update($questionsTable, ['mail_sent'=>1], ['token'=>$questionToken]);
                        }
                    } else {
                        $hasError = true;
                    }
                }
            }
            else {
                $hasError = true;
            }
        }
    }

    get_header();

?>






<?php

if(have_posts()):
    while (have_posts()): the_post(); ?>

        <?php if(isset($hasError) && false===$hasError):?>
        <div class="alert alert-success" role="alert">
            <div class="title"><b>Hemos recibido tu consulta satisfactoriamente!</b></div>
            <?php if(!empty($sendedEmail)):?>
            <p>Te hemos enviado un email donde tendrás una dirección para que puedas ver tu consulta y la respuesta de los profesionales.</p>
            <?php endif;?>
        </div>
        <?php endif;?>


        <?php if(isset($hasError) && true===$hasError && !empty($warningTitle)):?>
        <div class="alert alert-warning" role="alert">
            <div class="title"><b><?php echo $warningTitle;?></b></div>
            <p><?php echo $warningText;?></p>
        </div>
        <?php endif;?>


        <?php if(!empty($_SESSION['paymentStatus']) && $_SESSION['paymentStatus'] == 'approved'):?>
        <div class="alert alert-success alert-payment-approved" role="alert" style="display:none;">
            <div class="title"><b>¡Gracias por su compra en Mesa Profesional!</b></div>
            <p>Su pago está aprobado y ya se encuentra habilitado para seguir realizando consultas.</p>
        </div>
        <?php endif;?>



        <article class="post page">

            <div class="col-xs-12 box">
                <div class="col-xs-12">
                    <h2><?php the_title();?></h2>
                </div>

                <div class="col-xs-12">
                    <?php the_content();?>
                </div>

                <div class="col-xs-12">
                    <p><span class="asterisk">*</span> Campos obligatorios</p>
                </div>

                <div class="col-xs-12">

                    <form role="consultation" method="post" id="consultation-form" action="">
                        <div class="col-xs-6">
                            <label class="col-xs-12 no-padding" for="first-name">Nombre <span class="asterisk">*</span></label>
                            <input class="col-xs-12 form-control" type="text" value="" name="first-name" id="first-name" placeholder="Nombre"/>
                            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
                        </div>

                        <div class="col-xs-6">
                            <label class="col-xs-12 no-padding" for="last-name">Apellido <span class="asterisk">*</span></label>
                            <input class="col-xs-12 form-control" type="text" value="" name="last-name" id="last-name" placeholder="Apellido"/>
                            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
                        </div>

                        <div class="col-xs-12">
                            <label class="col-xs-12 no-padding" for="email">Email de contacto <span class="asterisk">*</span></label>
                            <input class="col-xs-12 form-control" type="text" value="" name="email" id="email" placeholder="Email de contacto"/>
                            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
                            <small class="col-xs-12 no-padding error invalid-email hidden">El email es inválido</small>
                        </div>

                        <div class="col-xs-12">
                            <?php global $wpdb;?>
                            <?php $categories = $wpdb->get_results('SELECT * FROM categories');?>
                            <label class="col-xs-12 no-padding" for="email">Categoría <span class="asterisk">*</span></label>
                            <select class="col-xs-12 form-control required" value="" name="category" id="category">
                                <option value="-">Seleccione una categoría</option>
                                <?php foreach($categories as $category):?>
                                <option value="<?php echo $category->id;?>"><?php echo $category->name;?></option>
                                <?php endforeach;?>
                            </select>
                            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
                        </div>

                        <div class="col-xs-12">
                            <label class="col-xs-12 no-padding" for="question">¿Cuál es tu consulta? <span class="asterisk">*</span></label>
                            <textarea class="col-xs-12 form-control" type="textarea" value="" name="question" id="question" placeholder="Escribinos aquí tu consulta..." maxlength="500"/></textarea>
                            <div class="col-xs-12 no-padding char-count">Quedan <span>500</span> caracteres</div>
                            <small class="col-xs-12 no-padding error hidden">Campo obligatorio</small>
                        </div>

                        <div class="col-xs-2">
                            <!--<input type="submit" value="Aceptar"/>-->
                            <button class="col-xs-12 btn btn-success ld-ext-right hovering submit">
                                Aceptar
                                <div class="ld ld-ring ld-spin"></div>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <div class="col-xs-4">
                &nbsp;
            </div>

        </article>

        <?php if(!$userData = isLoggedIn()):?>
            <!-- Button trigger modal -->
            <button class="btn btn-primary btn-lg hidden" id="modal" data-toggle="modal" data-target="#loginModal"></button>
            <!-- Modal -->
            <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
                <div class="vertical-alignment-helper">
                    <div class="modal-dialog vertical-align-center login-modal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                            </div>
                            <div class="modal-body">
                                <div class="col-xs-12 no-padding">
                                    <div class="col-xs-7 mp-login-form no-padding">
                                        <?php get_template_part('login-form');?>
                                    </div>
                                    <div class="col-xs-5">
                                        <div id="fb-root"></div>
                                        <script>(function(d, s, id) {
                                          var js, fjs = d.getElementsByTagName(s)[0];
                                          if (d.getElementById(id)) return;
                                          js = d.createElement(s); js.id = id;
                                          js.src = 'https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v2.12&appId=1641925725889638&autoLogAppEvents=1';
                                          fjs.parentNode.insertBefore(js, fjs);
                                        }(document, 'script', 'facebook-jssdk'));</script>
                                        <div class="fb-login-button" data-width="280" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-auto-logout-link="false" data-use-continue-as="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else:?>

            <?php if((!$remainingQuestions = hasQuestions()) || (!empty($_SESSION['paymentStatus']))):?>
                <?php 
                    $userDataArray = explode('-', $userData);
                    $mp = inicializeMercadoPago();
                ?>
                <!-- Button trigger modal -->
                <button class="btn btn-primary btn-lg hidden" id="modal" data-toggle="modal" data-target="#packsModal"></button>
                <!-- Modal -->
                <div class="modal fade" id="packsModal" tabindex="-1" role="dialog" aria-labelledby="packsModalLabel" aria-hidden="true">
                    <div class="vertical-alignment-helper">
                        <div class="modal-dialog vertical-align-center packs-modal">
                            <div class="modal-content <?php echo (!empty($_SESSION['paymentStatus']) && $_SESSION['paymentStatus'] == 'approved') ? 'ld-over running' : '';?>">

                                <?php if(!empty($_SESSION['paymentStatus'])):?>
                                    <input type="hidden" class="mercado-pago-result" value="<?php echo $_SESSION['paymentStatus'];?>"/>
                                    <?php if($_SESSION['paymentStatus'] == 'approved'):?>
                                        <div style="font-size:48px;color:#999" class="ld ld-ring ld-spin loading"></div>
                                    <?php endif;?>
                                <?php endif;?>
                                
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"></span></button>
                                </div>

                                <div class="modal-body">

                                    <?php if(!empty($_SESSION['paymentStatus']) && $_SESSION['paymentStatus'] == 'in_process'):?>
                                    <div class="alert alert-warning" role="alert">
                                        <div class="title"><b>Su pago está pendiente</b></div>
                                        <p>Hemos detectado que su pago se encuentra en proceso, le avisaremos cuando termine la operación.</p>
                                    </div>
                                    <?php endif;?>

                                    <div class="col-xs-12 no-padding">
                                        <h3>Ups, se acabaron las preguntas...</h3>
                                        <p>¡Compre uno de nuestros packs de preguntas para seguir consultando!</p>
                                        <div class="col-xs-12 no-padding">
                                            <?php 
                                            global $wpdb;
                                            $packs = $wpdb->get_results('SELECT * FROM products WHERE type = "pack"');
                                            foreach($packs as $kp => $pack):?>

                                            <div class="col-xs-12 pack pack-<?php echo $kp;?> media">
                                                <a class="pull-left" href="#">
                                                    <img class="media-object" src="<?php echo home_url($pack->picture_url);?>">
                                                </a>
                                                <div class="media-body">
                                                    <div class="col-xs-9">
                                                        <h4 class="media-heading"><?php echo $pack->title;?></h4>
                                                        <p class="col-xs-12 no-padding"><?php echo $pack->description;?></p>
                                                    </div>
                                                    <div class="col-xs-3 no-padding">
                                                        <?php
                                                            $aliasForPayment = ($userDataArray[3] == 'mail@test.com') ? 'test_user_63938795@testuser.com' : $userDataArray[3];
                                                            $preference_data = buildPreferenceData([
                                                                'itemId'            => $pack->id,
                                                                'itemTitle'         => $pack->title,
                                                                'itemDescription'   => $pack->description,
                                                                'itemPictureUrl'    => $pack->picture_url,
                                                                'itemPrice'         => $pack->price,
                                                                'payerFirstName'    => $userDataArray[1],
                                                                'payerLastName'     => $userDataArray[2],
                                                                'payerEmail'        => $aliasForPayment,
                                                                'urlSuccess'        => home_url('/mercadopago/callback'),
                                                                'urlPending'        => home_url('/'),
                                                                'urlFailure'        => home_url('/mercadopago/callback'),
                                                                'userId'            => $userDataArray[0]
                                                            ]);
                                                            $preference = $mp->create_preference($preference_data);
                                                        ;?>
                                                        <?php if($preference['status'] == -1):?>
                                                            <div class="no-item">No disponible</div>
                                                        <?php else:?>
                                                            <div class="col-xs-12 price"><span>A sólo <b>$<?php echo $pack->price;?></b></span></div>
                                                            <div class="col-xs-12 no-padding media-btn">
                                                                <a class="col-xs-8 col-xs-offset-2" href="<?php echo $preference['response']['init_point']; ?>" name="MP-Checkout" mp_mode="redirect" target="_blank">Comprar</a>
                                                                <span class="mp-cards"></span>
                                                            </div>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach;?>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(!empty($_SESSION['paymentStatus'])):?>
                    <?php unset($_SESSION['paymentStatus']);?>
                <?php endif;?>
            <?php endif;?>
        <?php endif;?>
    <?php endwhile;

else:
    echo '<p>Page-ingreso.php: No content found</p>';

endif;

get_footer();

?>