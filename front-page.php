<?php get_header();?>

<div class="row">

    <div class="col-xs-12 body">

        <?php if(!empty($_SESSION['paymentStatus']) && $_SESSION['paymentStatus'] == 'approved'):?>    
            <div class="col-xs-12 alert alert-success" role="alert">
                <div class="title"><b>¡Bienvenido a Mesa Profesional!</b></div>
                <p>Su pago está aprobado y ya se encuentra habilitado para usar las funcionalidades del sitio. Para iniciar sesión, puede hacerlo en la parte superior derecha o <a href="<?php echo home_url('/login');?>">aquí</a>.</p>
            </div>
            <?php unset($_SESSION['paymentStatus']);?>
        <?php endif;?>


        <?php if(!empty($_SESSION['welcome'])):?>
            <div class="col-xs-12 alert alert-success" role="alert">
                <div class="title"><b>¡Bienvenido a Mesa Profesional!</b></div>
                <p>
                    Ya se encuentra habilitado para contactarse con nuestros profesionales y obtener la mejor respuesta a su consulta. Antes que nada debe iniciar sesión, para ello puede hacerlo en la parte superior derecha o haciendo click <a href="<?php echo home_url('/login');?>">aquí</a>.<br>
                    Esperamos que nuestro sitio le resulte útil. Muchas gracias, el equipo de Mesa Profesional.
                </p>
            </div>
            <?php unset($_SESSION['welcome']);?>
        <?php endif;?>


        <?php if($userData = isLoggedIn()):?>
            <?php $userDataArray = explode('-', $userData);?>
            <?php global $wpdb;$user=$wpdb->get_row('SELECT * FROM users WHERE email = "'.$userDataArray[3].'"');?>
            <?php if($user->first_time):?>
                <div class="col-xs-12 alert alert-success" role="alert">
                    <div class="title"><b>¡Bienvenido a Mesa Profesional!</b></div>
                    <p>
                        Ya se encuentra habilitado para contactarse con nuestros profesionales y obtener la mejor respuesta a su consulta.<br>
                        Esperamos que nuestro sitio le resulte útil. Muchas gracias, el equipo de Mesa Profesional.
                    </p>
                </div>
                <?php $updateData = ['first_time'=>0];?>
                <?php $sentEmail = sendFacebookRegisterEmail([
                    'to'        => $user->email,
                    'firstName' => $user->first_name,
                    'lastName'  => $user->last_name
                ]);?>

                <?php if($sentEmail){$updateData['mail_sent']=1;};?>
                <?php $wpdb->update('users',$updateData,['email'=>$userDataArray[3]]);?>
            <?php endif;?>
        <?php endif;?>


        <div class="col-xs-12 no-padding">
            <div class="col-xs-9 no-padding left-col">

                <div class="col-xs-12 no-padding box">
                    <div class="card col-xs-8 col-xs-offset-2">
                        <a href="<?php echo home_url('consulta');?>">
                            <img src="https://mesaprofesional.com/wp-content/uploads/2018/03/card.jpg?v=1"/>
                            <span>Escriba su consulta aquí</span>
                        </a>
                    </div>
                    <div class="col-xs-12">
                        <?php
                        if(have_posts()): ?>

                            <!--<h2><?php print_r(getPostType());?></h2>-->

                            <?php
                            while (have_posts()): the_post(); ?>

                                <article class="posts">
                                    <!--<h2><a href="<?php the_permalink();?>"><?php the_title();?></a></h2>-->

                                    <!--<p class="post-info"><?php echo the_time('F jS, Y g:i a');?> | por <a href="<?php echo get_author_posts_url(get_the_author_meta('ID'));?>"><?php the_author();?></a> | Posted in-->

                                    <!--<?php

                                    $categories = get_the_category();
                                    $separator = ', ';
                                    $output = '';

                                    if($categories){
                                        foreach($categories as $category){
                                            $output .= '<a href="'.get_category_link($category->term_id).'">'.$category->cat_name.'</a>'.$separator;
                                        }
                                        echo trim($output,$separator);
                                    }

                                    ;?>

                                    </p>-->

                                    <?php the_content();?>

                                    <?php
                                        global $wpdb;
                                        $questions = $wpdb->get_results( 'SELECT u.id, u.first_name, u.last_name, q.*, c.*
                                                                            FROM questions q
                                                                            LEFT JOIN users u ON q.user_id = u.id 
                                                                            LEFT JOIN categories c ON c.id = q.category_id
                                                                            WHERE u.actived_account = 1
                                                                            ORDER BY q.timestamp desc');
                                        //d($questions);
                                    ?>

                                    <?php if(count($questions) == 0): ?>

                                            <b>No se encontraron preguntas.</b>

                                    <?php else:?>
                                    <?php setlocale(LC_TIME, "es_ES");?>
                                    <div class="col-xs-12 no-padding questions">
                                        <?php foreach ($questions as $key => $question): ?>
                                        <div class="col-xs-12 no-padding question <?php echo ($key%2==0) ? 'even' : 'odd' ?>">
                                            <div class="col-xs-12 question-inner <?php echo ($question->answer_id) ? 'answered' : '';?>">
                                                <div class="pull-left user-image">
                                                    <img src="https://mesaprofesional.com/wp-content/uploads/2018/03/perfil-150x150.jpeg"/>
                                                    <i class="fas fa-comment-dots"></i>
                                                </div>
                                                <div class="col-xs-11 question-content">
                                                    <div class="col-xs-12 no-padding user-name">
                                                        <span><?php echo $question->first_name;?></span> - 
                                                        <?php $key = encryptIt('categoryid-'.$question->category_id);?>
                                                        <a href="<?php echo home_url('/questions?key='.$key);?>"><?php echo $question->name;?></a>
                                                    </div>
                                                    <span class="col-xs-12 no-padding question-date"><span><?php echo utf8_encode(strftime('%a, %e %h %Y, %H:%M', strtotime($question->timestamp)));?></span>&nbsp;hs</span>
                                                    <a class="col-xs-12 no-padding question-text" href="<?php echo home_url('/questions?token='.$question->token);?>">
                                                        <?php $limitStr = 150;?>
                                                        <?php echo (strlen($question->text) <= $limitStr) ? $question->text : substr($question->text,0,$limitStr).'...';?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php if($question->answer_id):?>
                                                <?php $answer=$wpdb->get_row('SELECT * FROM answers a JOIN users u ON a.user_id = u.id WHERE a.id='.$question->answer_id);?>
                                                <?php if($answer->sended_email):?>
                                                <div class="col-xs-12 answer-inner">
                                                    <i class="fas fa-level-up-alt pull-left"></i>
                                                    <div class="pull-left user-image">
                                                        <img src="https://mesaprofesional.com/wp-content/uploads/2018/03/perfil-150x150.jpeg"/>
                                                        <i class="fas fa-comments"></i>
                                                    </div>
                                                    <div class="col-xs-11 answer-content">
                                                        <div class="col-xs-12 no-padding answer-text">
                                                            <?php echo $answer->text;?>
                                                        </div>
                                                        <div class="col-xs-12 no-padding answer-user-name">
                                                            <?php $key = encryptIt('profid-'.$answer->user_id);?>
                                                            <span class="pull-left">Respondida por</span>
                                                            <a href="<?php echo home_url('/questions?key='.$key);?>" class="professional-name text-success"><i class="fas fa-user pull-left"></i> <?php echo $answer->first_name.' '.$answer->last_name;?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif;?>    
                                            <?php endif;?>
                                        </div>
                                        <?php endforeach;?>
                                    </div>
                                    <?php endif;?>
                                        

                                    <!--<?php the_excerpt();?>-->
                                </article>

                            <?php endwhile;?>

                        <?php endif;?>
                    </div>
                </div>                
            </div>

            <div class="col-xs-3 no-padding-right right-col">
                <div class="professionals-box col-xs-12 box">
                    <h5>Ranking de profesionales</h5>
                    <!--<?php dynamic_sidebar('sidebar1');?>-->
                    <?php 
                        global $wpdb;
                        $professionals = $wpdb->get_results('SELECT avg(rating) as rating, u.first_name, u.last_name FROM answers a LEFT JOIN users u ON a.user_id = u.id WHERE rol IN (1,2) group by email');
                    ?>

                    <?php foreach($professionals as $professional):?>
                    <?php if(!is_null($professional->rating)):?>
                    <div class="col-xs-12 no-padding professional-info">
                        <img class="pull-left" src="https://mesaprofesional.com/wp-content/uploads/2018/03/perfil-150x150.jpeg"/>
                        <div class="prof-name">
                            <?php echo $professional->first_name.' '.$professional->last_name;?>
                        </div>
                        <div class="jrating">
                            <div class="rating">
                                <span class="pull-left stars"></span>
                                <small><?php echo $professional->rating;?></small>
                                <input type="hidden" class="average" value="<?php echo $professional->rating;?>"/>
                            </div>
                        </div>
                    </div>
                    <?php endif;?>
                    <?php endforeach;?>
                    
                </div>
            </div>
        </div>
    </div>
</div>


<?php get_footer();?>