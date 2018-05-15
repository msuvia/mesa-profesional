<?php 

    if(!empty($_POST)){
        // Professionals Profile - get questions detail
        if(empty($_POST['token'])){
            echo json_encode(['status'=>'ERROR','message'=>'Empty token']);
            die;
        }

        global $wpdb;
        $token = $_POST['token'];
        $question = $wpdb->get_row( 'SELECT u.first_name, u.last_name, u.email, q.timestamp, q.token, q.text as question_text, q.answer_id, a.text as answer_text, a.sended_email
                                        FROM questions q 
                                        JOIN users u ON q.user_id = u.id 
                                        LEFT JOIN answers a ON q.answer_id = a.id 
                                        WHERE q.token = "'.$token.'"');
        if(!empty($_POST['answer'])){
            $answer = $_POST['answer'];

            // insert or update ?
            if($question->answer_id){
                // update old answer
                $success = $wpdb->update('answers', ['text'=>$answer,'timestamp'=>date('Y-m-d H:i:s')], ['id'=>$question->answer_id]);
                if($success){
                    if(isset($_POST['sendEmail']) && 'true'==$_POST['sendEmail'] && !$question->sended_email){
                        $sendedEmail = sendAnswerEmail($question);
                        if($sendedEmail){
                            $wpdb->update('answers', ['sended_email'=>1], ['id'=>$question->answer_id]);
                        }
                    }
                    echo json_encode(['status'=>'OK']);die;
                }
                else {
                    echo json_encode(['status'=>'ERROR','message'=>'Error: cant update on answers table']);die;
                }
            }
            else{
                // insert new answer
                // get professional user data
                $userData = explode('-',isLoggedIn());   // id-firstName-lastName-email
                //
                date_default_timezone_set("America/Argentina/Buenos_Aires");
                $answerId=$wpdb->insert('answers',['user_id' => $userData[0],'text' => $answer,'timestamp'=>date('Y-m-d H:i:s', time())]);
                //
                if($answerId){
                    $success = $wpdb->update('questions', ['answer_id'=>$wpdb->insert_id], ['token'=>$token]);
                    if($success){
                        if(isset($_POST['sendEmail']) && 'true'==$_POST['sendEmail'] && !$question->sended_email){
                            $sendedEmail = sendAnswerEmail($question);
                            if($sendedEmail){
                                $wpdb->update('answers', ['sended_email'=>1], ['id'=>$answerId]);
                            }
                        }
                        echo json_encode(['status'=>'OK']);die;
                    }
                    else{
                        echo json_encode(['status'=>'ERROR','message'=>'Error: cant update on questions table']);die;
                    }
                }
                else{
                    echo json_encode(['status'=>'ERROR','message'=>'Error: cant insert on answers table']);die;
                }
            }
        }
        else {
            $question->url = home_url('/questions?key='.encryptIt('token-'.$_POST['token']));
            echo json_encode(['status' => 'OK','question' => $question]);die;
        }
        
    }
    

    /*
        Template Name: Dashboard Template
    */

    get_header();


if(have_posts()):
    while (have_posts()): the_post(); ?>

        <article class="post">

            <div class="col-sm-3 col-md-2 no-padding sidebar">
                <?php 
                    global $wpdb;
                    $questions = $wpdb->get_results( 'SELECT u.id, u.first_name, u.last_name, q.* 
                                                        FROM questions q
                                                        LEFT JOIN users u
                                                        ON q.user_id = u.id 
                                                        WHERE u.actived_account = 1
                                                        ORDER BY q.timestamp desc');                                   
                    $newQuestionQty = 0;
                    foreach($questions as $question){
                        if(!$question->answer_id){
                            $newQuestionQty += 1;
                        }
                    }
                ;?>
                <?php if($newQuestionQty > 0):?>
                    <label class="col-xs-12 new-questions-qty">Nuevas preguntas (<?php echo $newQuestionQty;?>)</label>
                <?endif;?>
                <ul class="nav nav-sidebar">
                    <?php setlocale(LC_TIME, "es_ES");?>
                    <?php foreach($questions as $key => $question):?>
                        <li>
                            <a class="col-xs-12 question <?php echo ($key%2==0) ? 'pair' : 'odd' ?> <?php echo (!$question->answer_id) ? 'new' : '';?>" href="">
                                <span class="col-xs-12 no-padding user-name"><?php echo $question->first_name.' '.$question->last_name;?></span>
                                <span class="col-xs-12 no-padding question-date">
                                    <span><?php echo utf8_encode(strftime('%a, %e %h %Y, %H:%M', strtotime($question->timestamp)));?></span>&nbsp;hs
                                </span>
                                <span class="col-xs-12 no-padding question-text">
                                    <?php $limitStr = 50;?>
                                    <?php echo (strlen($question->text) <= $limitStr) ? $question->text : substr($question->text,0,$limitStr).'...';?>
                                </span>
                                <?php if($question->answer_id):?>
                                    <span class="col-xs-12 no-padding status-detail answered-by">
                                        <?php $answer = $wpdb->get_row('SELECT * FROM answers a JOIN users u ON a.user_id = u.id WHERE a.id = '.$question->answer_id);?>
                                        <i class="fas fa-check text-success"></i>
                                        <span>Respondida por <i class="text-success"><?php echo $answer->first_name.' '.$answer->last_name;?></i></span>
                                    </span>
                                    <span class="col-xs-12 no-padding status-detail">
                                    <?php if($answer->sended_email):?>
                                        <i class="fas fa-check text-success"></i>
                                        <span><i class="text-success">Email enviado</i></span>
                                    <?php else:?>
                                        <i class="fas fa-times text-danger"></i>
                                        <span><i class="text-danger">Email no enviado</i></span>
                                    <?php endif;?>
                                    </span>
                                <?php endif;?>
                                

                                <input type="hidden" class="token" value="<?php echo $question->token;?>"/>
                            </a>
                        </li>
                    <?php endforeach;?>
                </ul>
            </div>

            <div class="col-sm-9 col-md-10 main">
                <div class="col-xs-12">
                    <h3>Preguntas</h3>
                </div>

                <div class="col-xs-12">
                    <h4>Filtrar por</h4>
                    <div class="col-xs-12 no-padding filters">
                        <div class="col-xs-6">
                            <label class="col-xs-12" for="">
                                <?php get_search_form();?>
                            </label>
                            <label class="col-xs-12" for="filter-range-from">
                                <span>Desde</span>
                                <input type="text" id="filter-range-from" class="">
                            </label>
                            <label class="col-xs-12" for="">
                                <span>Hasta</span>
                                <input type="text" id="filter-range-to" class="">
                            </label>
                        </div>
                        <div class="col-xs-6">
                            <label class="col-xs-12" for="no-answered">
                                <input type="checkbox" id="filter-no-answered">
                                <span>No respondidas</span>
                            </label>
                            <label class="col-xs-12" for="">
                            </label>
                            <label class="col-xs-12" for="">
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 question-detail">
                    <h4>Detalle de la pregunta</h4>
                    <div class="col-xs-12 no-padding panel panel-default">
                        <div class="panel-body ld-over">
                            <div style="font-size:48px;color:#999" class="ld ld-ring ld-spin"></div>
                            <input type="hidden" class="question-token" value=""/>
                            <div class="col-xs-12 no-padding">
                                <label class="col-xs-2 no-padding">Nombre:</label>
                                <span class="user-name">-</span>
                            </div>
                            <div class="col-xs-12 no-padding">
                                <label class="col-xs-2 no-padding">Email:</label>
                                <span class="user-email">-</span>
                            </div>
                            <div class="col-xs-12 no-padding">
                                <label class="col-xs-2 no-padding">Fecha de la consulta:</label>
                                <span class="question-date">-</span>
                            </div>
                            <div class="col-xs-12 no-padding">
                                <label class="col-xs-2 no-padding">Link:</label>
                                <span class="question-link">-</span>
                            </div>
                            <div class="col-xs-12 question-content">
                                <label>Pregunta</label>
                                <textarea class="form-control question-text" readonly="readonly" placeholder="Pregunta del usuario..."></textarea>
                            </div>
                            <div class="col-xs-12 no-padding answer-content">
                                <label>Respuesta</label>
                                <textarea class="form-control answer-text" readonly="readonly" placeholder="Escriba su respuesta..."></textarea>
                            </div>
                            <div class="col-xs-12 no-padding mail-content">
                                <input type="checkbox" value="1" name="send-email" id="send-email" class="send-email"/>
                                <label for="send-email">Enviar correo al usuario</label>
                            </div>
                            <div class="col-xs-12 no-padding">
                                <div class="col-xs-12 no-padding panel-bottom">
                                    <button class="btn btn-success ld-ext-right hovering" disabled="disabled">
                                        Responder
                                        <div class="ld ld-ring ld-spin"></div>
                                    </button>
                                    <div class="status">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </article>

    <?php endwhile;

endif;

get_footer();

?>