<?php 

/*
 *   Template Name: Professionals Abm Template
*/

global $wpdb;
if(!$user = isLoggedIn()){
    wp_redirect(home_url('/'),301);
}

$user = explode('-',$user);
//d($user);

$user = $wpdb->get_row( 'SELECT * FROM users WHERE actived_account = 1 AND rol = 1 AND email = "'.$user[3].'"');
if(!$user){
    wp_redirect(home_url('/'),301);
}

if(!empty($_POST)){
    $profList = !empty($_POST['profList']) ? $_POST['profList'] : [];


    if(!empty($profList)){
        $success = true;
        /*
            UPDATE users 
            SET actived_account = '0' 
            WHERE (token = 'XXX' and email = "mail@mail.com")
            OR (token = 'YYY' and email = "mail@mail.com")
            ...
        */
        $queryAuth = 'UPDATE users SET actived_account = "1" WHERE '; 
        $queryNoAuth = 'UPDATE users SET actived_account = "2" WHERE ';
        $execAuth = $execNoAuth = false;
        foreach($profList as $prof){
            $status = !empty($prof['status']) ? $prof['status'] : "";
            $token  = !empty($prof['token']) ? $prof['token'] : "";
            $email  = !empty($prof['email']) ? $prof['email'] : "";

            if(!empty($token) && !empty($email)){
                if($status == '1'){
                    $execAuth = true;
                    $queryAuth .= '(token = "'.$token.'" and email = "'.$email.'") OR ';
                }
                elseif($status == '2'){
                    $execNoAuth = true;
                    $queryNoAuth .= '(token = "'.$token.'" and email = "'.$email.'") OR ';
                }
            }
        }

        $queryAuth    = substr($queryAuth, 0, -3);
        $queryNoAuth  = substr($queryNoAuth, 0, -3);

        $successAuth    = ($execAuth)   ? $wpdb->query($wpdb->prepare($queryAuth))      : 0;
        $successNoAuth  = ($execNoAuth) ? $wpdb->query($wpdb->prepare($queryNoAuth))    : 0;

        if($successAuth === false || $successNoAuth === false){
            echo json_encode(['status'=>'ERROR', 'message'=>'Error al guardar los datos, intente nuevamente']);die;
        } else {
            // 0 or > 0 updated queries

            // emails
            foreach($profList as $prof){
                $emailTo = $prof['email'];

                $user = $wpdb->get_row('SELECT * FROM users WHERE email = "'.$emailTo.'"');
                if($user->actived_account == '1'){

                    // Mercado Pago preference data
                    $mp = inicializeMercadoPago();
                    $subscription = $wpdb->get_row('SELECT * FROM products WHERE type = "subscription"');

                    $aliasForPayment = ($user->email == 'msuvia@garbarinoviajes.com.ar')  ? 'test_user_25772596@testuser.com' : $user->email;

                    //$user->email = ($user->email == 'marcelo.suvia@gmail.com')      ? 'test_user_25772596@testuser.com' : $user->email;
                    //$user->email = ($user->email == 'gestioncontable1@outlook.com') ? 'test_user_25772596@testuser.com' : $user->email;

                    $preference_data = buildPreferenceData([
                        'itemId'            => $subscription->id,
                        'itemTitle'         => $subscription->title,
                        'itemDescription'   => $subscription->description,
                        'itemPictureUrl'    => $subscription->picture_url,
                        'itemPrice'         => $subscription->price,
                        'payerFirstName'    => $user->first_name,
                        'payerLastName'     => $user->last_name,
                        'payerEmail'        => $aliasForPayment,
                        'urlSuccess'        => home_url('/mercadopago/callback'),
                        'urlPending'        => home_url('/mercadopago/callback'),
                        'urlFailure'        => home_url('/mercadopago/callback'),
                        'userId'            => $user->id
                    ]);
                    $preference = $mp->create_preference($preference_data);

                    // payment log
                    $wpdb->insert('payment_log',[
                        'application'   => 'MP',
                        'operation'     => 'CREATE PREFERENCE',
                        'request'       => json_encode($preference_data),
                        'response'      => json_encode($preference)
                    ]);

                    // send email
                    $sendedEmail = sendProfessionalSuccessfulAuthorizationEmail([
                        'to'            => $user->email,
                        'firstName'     => $user->first_name,
                        'lastName'      => $user->last_name,
                        'linkForPayment'=> $preference['response']['init_point']
                    ]);

                    if($sendedEmail){
                        $wpdb->update('users', ['mail_sent'=>2], ['email'=>$user->email]);
                    }
                }
                elseif($user->actived_account == '2'){

                    $sendedEmail = sendProfessionalDeniedAuthorizationEmail([
                        'to'            => $user->email,
                        'firstName'     => $user->first_name,
                        'lastName'      => $user->last_name
                    ]);

                    if($sendedEmail){
                        $wpdb->update('users', ['mail_sent'=>3], ['email'=>$user->email]);
                    }
                }
            }

            echo json_encode(['status'=>'OK']);die;
        }
    }
}


get_header();



?>

<div class="row">

    <div class="col-xs-12 abm">

        <div class="col-xs-12 box">
            <?php
            if(have_posts()): ?>

                <?php
                while (have_posts()): the_post(); ?>

                    <article class="posts">
                        <?php setlocale(LC_TIME, "es_ES");?>
                        <h3><a href="<?php the_permalink();?>"><?php the_title();?></a></h3>

                        <div class="col-xs-12 no-padding">
                            <p>En esta página, se podrá dar de alta o de baja a los profesionales que se registran en Mesa Profesional. A continuación, se detallan tres listados:</p>
                            <ul>
                                <li>Profesionales pendiente de aprobación</li>
                                <li>Profesionales aceptados</li>
                                <li>Profesionales no aceptados</li>
                            </ul>
                        </div>

                        <?php 
                            $professionals = $wpdb->get_results('SELECT * FROM users WHERE rol = 2 order by id desc');
                            $pending = $authorized = $unauthorized = [];
                            foreach($professionals as $prof){
                                if($prof->actived_account == 0){
                                    $pending[] = $prof;
                                }
                                elseif($prof->actived_account == 1){
                                    $authorized[] = $prof;
                                }
                                elseif($prof->actived_account == 2){
                                    $unauthorized[] = $prof;
                                }
                            }
                        ?>

                        

                    
                        <div class="col-xs-12 no-padding list pending">
                            <h4>Profesionales pendiente de aprobación</h4>
                            <section class="col-xs-12 no-padding">
                                <?php if(count($pending) > 0):?>
                                    <?php foreach($pending as $key => $prof):?>
                                        <?php if($key==0):?>
                                            <table class="table table-striped"><thead><tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Apellido</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Registrado en</th>
                                                <th scope="col">Autorizado Sí/No</th>
                                            </tr></thead><tbody>
                                        <?php endif;?>
                                            <tr class="<?php echo ($key%2) ? 'even' : 'odd';?>">
                                                <th scope="row"><?php echo $key+1;?></th>
                                                <td><?php echo $prof->first_name;?></td>
                                                <td><?php echo $prof->last_name;?></td>
                                                <td><?php echo $prof->email;?></td>
                                                <td><?php echo ucwords(utf8_encode(strftime('%a, %e %h %Y, %H:%M', strtotime($prof->timestamp)))).' hs';?></td>
                                                <td>
                                                    <input type="hidden" name="token" value="<?php echo $prof->token;?>"/>
                                                    <input type="hidden" name="email" value="<?php echo $prof->email;?>"/>
                                                    <input type="hidden" name="changed" value="0"/>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Sí" data-off="No" data-onstyle="success">
                                                </td>
                                            </tr>
                                        <?php if($key==count($pending)-1):?>
                                        </tbody></table>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                <?php else:?>
                                    <div class="col-xs-12 no-results">
                                        No se encontraron resultados
                                    </div>
                                <?php endif;?>
                            </section>
                        </div>


                        <div class="col-xs-12 no-padding list authorized">
                            <h4>Profesionales aceptados</h4>
                            <section class="col-xs-12 no-padding">
                                <?php if(count($authorized) > 0):?>
                                    <?php foreach($authorized as $key => $prof):?>
                                        <?php if($key==0):?>
                                            <table class="table table-striped"><thead><tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Apellido</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Registrado en</th>
                                                <th scope="col">Autorizado Sí/No</th>
                                            </tr></thead><tbody>
                                        <?php endif;?>
                                            <tr class="<?php echo ($key%2) ? 'even' : 'odd';?>">
                                                <th scope="row"><?php echo $key+1;?></th>
                                                <td><?php echo $prof->first_name;?></td>
                                                <td><?php echo $prof->last_name;?></td>
                                                <td><?php echo $prof->email;?></td>
                                                <td><?php echo ucwords(utf8_encode(strftime('%a, %e %h %Y, %H:%M', strtotime($prof->timestamp)))).' hs';?></td>
                                                <td>
                                                    <input type="hidden" name="token" value="<?php echo $prof->token;?>"/>
                                                    <input type="hidden" name="email" value="<?php echo $prof->email;?>"/>
                                                    <input type="hidden" name="changed" value="0"/>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Sí" data-off="No" data-onstyle="success" checked>
                                                </td>
                                            </tr>
                                        <?php if($key==count($authorized)-1):?>
                                        </tbody></table>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                <?php else:?>
                                    <div class="col-xs-12 no-results">
                                        No se encontraron resultados
                                    </div>
                                <?php endif;?>
                            </section>
                        </div>


                        <div class="col-xs-12 no-padding list unauthorized">
                            <h4>Profesionales no aceptados</h4>
                            <section class="col-xs-12 no-padding">
                                <?php if(count($unauthorized) > 0):?>
                                    <?php foreach($unauthorized as $key => $prof):?>
                                        <?php if($key==0):?>
                                            <table class="table table-striped"><thead><tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Apellido</th>
                                                <th scope="col">Email</th>
                                                <th scope="col">Registrado en</th>
                                                <th scope="col">Autorizado Sí/No</th>
                                            </tr></thead><tbody>
                                        <?php endif;?>
                                            <tr class="<?php echo ($key%2) ? 'even' : 'odd';?>">
                                                <th scope="row"><?php echo $key+1;?></th>
                                                <td><?php echo $prof->first_name;?></td>
                                                <td><?php echo $prof->last_name;?></td>
                                                <td><?php echo $prof->email;?></td>
                                                <td><?php echo ucwords(utf8_encode(strftime('%a, %e %h %Y, %H:%M', strtotime($prof->timestamp)))).' hs';?></td>
                                                <td>
                                                    <input type="hidden" name="token" value="<?php echo $prof->token;?>"/>
                                                    <input type="hidden" name="email" value="<?php echo $prof->email;?>"/>
                                                    <input type="hidden" name="changed" value="0"/>
                                                    <input type="checkbox" data-toggle="toggle" data-on="Sí" data-off="No" data-onstyle="success">
                                                </td>
                                            </tr>
                                        <?php if($key==count($unauthorized)-1):?>
                                        </tbody></table>
                                        <?php endif;?>
                                    <?php endforeach;?>
                                <?php else:?>
                                    <div class="col-xs-12 no-results">
                                        No se encontraron resultados
                                    </div>
                                <?php endif;?>
                            </section>
                        </div>

                        <div class="col-xs-12 no-padding submit">
                            <button class="col-xs-1 pull-right btn btn-success ld-ext-right hovering" disabled="disabled">
                                Aceptar
                                <div class="ld ld-ring ld-spin"></div>
                            </button>
                            <div class="status pull-right"></div>
                        </div>

                    </article>

                <?php endwhile;

            endif;?>
        </div>
    </div>
</div>


<?php get_footer();?>