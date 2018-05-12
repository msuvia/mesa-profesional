<?php 

/*
 *   Template Name: Questions Template
*/

if(!empty($_POST) && !empty($_POST['token']) && !empty($_POST['rating'])){
    $token = $_POST['token'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    global $wpdb;
    $question = $wpdb->get_row( 'SELECT * FROM questions WHERE token = "'.$token.'"');
    $updateData = ['rating'=>$rating];
    if(!empty($comment)){
        $updateData['comment'] = $comment;
    }
    $success = $wpdb->update('answers', $updateData, ['id'=>$question->answer_id]);

    if($success){
        echo json_encode(['status'=>'OK']);die;
    }
    else{
        echo json_encode(['status'=>'ERROR']);die;   
    }
}


get_header();



?>

<div class="row">

    <div class="col-xs-12 body">

        <div class="col-xs-9 no-padding left-col">

            <div class="col-xs-12 box">
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

                                if(!empty($_GET['key'])){
                                    //d(decryptIt($_GET['key']));

                                    $key = explode('-',decryptIt($_GET['key']));
                                    
                                    switch($key[0]){
                                        case 'userid':
                                            $key[0] = 'q.user_id';
                                            break;

                                        case 'categoryid':
                                            $key[0] = 'q.category_id';
                                            break;

                                        case 'profid':
                                            $answersSql = ' LEFT JOIN answers a ON a.id = q.answer_id ';
                                            $key[0] = 'a.user_id';
                                            break;

                                        default:
                                            break;
                                    }

                                    $filter = $key[0].'="'.$key[1].'"';
                                }


                                if(!empty($_GET['token'])){
                                    $filter = 'q.token="'.$_GET['token'].'"';
                                }

                                //d($filter);

                                global $wpdb;

                                $answersSql = (!empty($answersSql)) ? $answersSql : "";
                                $questions = $wpdb->get_results( 'SELECT u.id, u.first_name, u.last_name, u.email, q.*, c.*
                                                                    FROM questions q
                                                                    LEFT JOIN users u ON q.user_id = u.id
                                                                    LEFT JOIN categories c ON c.id = q.category_id
                                                                    '.$answersSql.'
                                                                    WHERE u.actived_account = 1
                                                                    AND '.$filter.'
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
                                            <input type="hidden" class="token" value="<?php echo $question->token;?>"/>
                                            <div class="col-xs-12 no-padding user-name">
                                                <span><?php echo $question->first_name;?></span> - 
                                                <?php $key = encryptIt('categoryid-'.$question->category_id);?>
                                                <a href="<?php echo home_url('/questions?key='.$key);?>"><?php echo $question->name;?></a>
                                            </div>
                                            <span class="col-xs-12 no-padding question-date"><span><?php echo utf8_encode(strftime('%a, %e %h %Y, %H:%M', strtotime($question->timestamp)));?></span>&nbsp;hs</span>
                                            <span class="col-xs-12 no-padding question-text"><?php echo $question->text;?></span>
                                        </div>
                                    </div>
                                    <?php if($question->answer_id):?>
                                        <?php $answer=$wpdb->get_row('SELECT * FROM answers a JOIN users u ON a.user_id = u.id WHERE a.id='.$question->answer_id);?>
                                        <?php if($answer->sended_email):?>
                                            <div class="col-xs-12 answer-inner">
                                                <i class="fas fa-level-up-alt pull-left"></i>
                                                <div class="pull-left user-image">
                                                    <?php if($answer->picture_url):?>
                                                        <img src="<?php echo home_url($answer->picture_url);?>" width="20"/>
                                                        <i class="fas fa-comments"></i>
                                                    <?php else:?>
                                                        <img src="https://mesaprofesional.com/wp-content/uploads/2018/03/perfil-150x150.jpeg"/>
                                                        <i class="fas fa-comments"></i>
                                                    <?php endif;?>
                                                </div>
                                                <div class="col-xs-11 answer-content">
                                                    <div class="col-xs-12 no-padding answer-text">
                                                        <?php echo str_replace("\n","<br>",$answer->text);?>
                                                    </div>
                                                    <div class="col-xs-12 no-padding answer-user-name">
                                                        <?php $key = encryptIt('profid-'.$answer->user_id);?>
                                                        <span class="pull-left">Respondida por</span>
                                                        <a href="<?php echo home_url('/questions?key='.$key);?>" class="professional-name text-success"><i class="fas fa-user pull-left"></i> <?php echo $answer->first_name.' '.$answer->last_name;?></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if($userData = isLoggedIn()):?>
                                                <?php $userDataArray = explode('-', $userData);?>
                                                <?php if($userDataArray[3] == $question->email):?>
                                                <div class="col-xs-12 answer-rating">
                                                <div class="pull-right offset">
                                                    <h6>Califique la respuesta de nuestro profesional</h6>
                                                    <div class="col-xs-12 no-padding professional-icon">
                                                        <small class="pull-left">Profesional:</small>
                                                        <?php $key = encryptIt('profid-'.$answer->user_id);?>
                                                        <a href="<?php echo home_url('/questions?key='.$key);?>" class="professional-name text-success">
                                                            <i class="fas fa-user pull-left"></i>
                                                            <?php echo $answer->first_name.' '.$answer->last_name;?>
                                                        </a>
                                                    </div>
                                                    <div class="col-xs-12 no-padding rating-content">
                                                        <label class="col-xs-12" for="stars-5">
                                                            <input type="radio" name="answer-rating" value="5" id="stars-5" class="pull-left" checked="checked">
                                                            <span class="pull-left stars stars-5"></span>
                                                            <small class="rating-text">5 estrellas</small>
                                                            <input type="hidden" class="average" value="5"/>
                                                        </label>
                                                        <label class="col-xs-12" for="stars-4">
                                                            <input type="radio" name="answer-rating" value="4" id="stars-4" class="pull-left">
                                                            <span class="pull-left stars stars-4"></span>
                                                            <small class="rating-text">4 estrellas</small>
                                                            <input type="hidden" class="average" value="4"/>
                                                        </label>
                                                        <label class="col-xs-12" for="stars-3">
                                                            <input type="radio" name="answer-rating" value="3" id="stars-3" class="pull-left">
                                                            <span class="pull-left stars stars-3"></span>
                                                            <small class="rating-text">3 estrellas</small>
                                                            <input type="hidden" class="average" value="3"/>
                                                        </label>
                                                        <label class="col-xs-12" for="stars-2">
                                                            <input type="radio" name="answer-rating" value="2" id="stars-2" class="pull-left">
                                                            <span class="pull-left stars stars-2"></span>
                                                            <small class="rating-text">2 estrellas</small>
                                                            <input type="hidden" class="average" value="2"/>
                                                        </label>
                                                        <label class="col-xs-12" for="stars-1">
                                                            <input type="radio" name="answer-rating" value="1" id="stars-1" class="pull-left">
                                                            <span class="pull-left stars stars-1"></span>
                                                            <small class="rating-text">1 estrella</small>
                                                            <input type="hidden" class="average" value="1"/>
                                                        </label>
                                                    </div>
                                                    <div class="col-xs-12 no-padding answer-comments">
                                                        <h6>¿Desea añadir algún comentario más?<span> (esta información es confidencial y no será divulgada)</span></h6>

                                                        <p><span class="asterisk">*</span> NOTA: No es una nueva pregunta, es una simple observación a la respuesta anterior.</p>

                                                        <textarea class="col-xs-12 form-control comment" placeholder="Escriba su comentario aquí... (opcional)"></textarea>
                                                    </div>
                                                    <div class="col-xs-12 no-padding submit">
                                                        <button class="btn btn-success ld-ext-right hovering">
                                                            Enviar
                                                            <div class="ld ld-ring ld-spin"></div>
                                                        </button>
                                                        <div class="status"></div>
                                                    </div>
                                                </div>
                                            </div>
                                                <?php endif;?>
                                            <?php endif;?>
                                        <?php endif;?>
                                    <?php endif;?>
                                </div>
                                <?php endforeach;?>
                            </div>
                            <?php endif;?>
                                

                            <!--<?php the_excerpt();?>-->
                        </article>

                    <?php endwhile;

                endif;?>
            </div>
        </div>

        <div class="col-xs-3 no-padding-right right-col">
            <div class="professionals-box col-xs-12 box">
                <h5>Ranking de profesionales</h5>
                <!--<?php dynamic_sidebar('sidebar1');?>-->
                <?php 
                    global $wpdb;
                    $professionals = $wpdb->get_results('SELECT avg(rating) as rating, u.first_name, u.last_name, u.picture_url FROM answers a LEFT JOIN users u ON a.user_id = u.id WHERE rol IN (1,2) group by email');
                ?>

                <?php foreach($professionals as $professional):?>
                <?php if(!is_null($professional->rating)):?>
                    <div class="col-xs-12 no-padding professional-info">
                        <?php if($professional->picture_url):?>
                            <img class="pull-left" src="<?php echo home_url($professional->picture_url);?>" width="20"/>
                        <?php else:?>
                            <img class="pull-left" src="https://mesaprofesional.com/wp-content/uploads/2018/03/perfil-150x150.jpeg"/>
                        <?php endif;?>
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


<?php get_footer();?>