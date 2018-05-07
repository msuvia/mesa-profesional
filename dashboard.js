$(document).ready(function(){

    $('.dashboard .sidebar ul li a.question').on('click',function(ev){
        ev.preventDefault();ev.stopPropagation();

        $('.dashboard .sidebar ul li a.question').removeClass('selected');
        $(this).addClass('selected');
        
        $('html, body').animate({ scrollTop: ($('.question-detail').offset().top) }, 500);
        $('.dashboard .main .panel .ld-over').addClass('running');
        
        var token = $(this).find('input.token').val();
        $.post('/profesionales/dashboard',{token: token}, function(data){
            if(data.status == 'ERROR'){
                return;
            }
            $('.question-detail .ld-over').removeClass('running');
            $('.question-detail .send-email').removeAttr('checked disabled');
            $('.question-detail .answer-text').removeAttr('readonly').empty();
            $('.question-detail .btn-success').removeAttr('disabled');
            $('.question-detail .status').empty();
            
        	$('.question-detail .user-name').text(data.question.first_name+' '+data.question.last_name);
        	$('.question-detail .user-email').text(data.question.email);
        	$('.question-detail .question-date').text(data.question.timestamp);
        	$('.question-detail .question-link').html('<a href="'+data.question.url+'" target="_blank">'+data.question.url+'</a>');
        	$('.question-detail .question-text').text(data.question.question_text);
            $('.question-detail .question-token').val(token);
            
            if(data.question.answer_text){
                $('.question-detail .answer-text').text(data.question.answer_text);
            }

            if(data.question.sended_email == "1"){
                $('.question-detail .send-email').attr('disabled','disabled').attr('checked','checked');
                $('.question-detail .answer-text').attr('readonly','readonly');
                $('.question-detail .btn-success').attr('disabled','disabled');
            }
        }, "json");
    });


    $('.dashboard .main .question-detail .btn-success').on('click',function(){
        var btn = $(this);
        if(btn.prop('disabled')){
            return;
        }
        btn.addClass('running').attr('disabled','disabled');
        var token = btn.parents('.panel-body').find('.question-token').val();
        var answer = btn.parents('.panel-body').find('.answer-text').val();
        var sendEmail = btn.parents('.panel-body').find('.send-email').prop('checked');
        $.post('/profesionales/dashboard',{token: token, answer: answer, sendEmail: sendEmail}, function(data){
            btn.removeClass('running').removeAttr('disabled');
            if(data.status == 'OK'){
                btn.siblings('.status').html('<i class="fas fa-check text-success"></i><span class="text-success">Datos guardados correctamente</span>');
            }
            else {
                btn.siblings('.status').html('<i class="fas fa-times text-danger"></i><span class="text-danger">Error al intentar guardar los datos, intente nuevamente y, si persiste el problema, contáctese con el administrador</span>');
            }
        },"json");
    });



});