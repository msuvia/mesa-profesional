$(document).ready(function(){

    $('.questions .answer-rating .offset button.btn-success').on('click',function(){
        var btn = $(this);
        if(btn.prop('disabled')){
            return;
        }
        btn.addClass('running').attr('disabled','disabled');
        
        var comment = $(this).parents('.question').find('textarea.comment').val();
        var token = $(this).parents('.question').find('.token').val();
        var rating;
        $('.questions .answer-rating .offset input[name="answer-rating"]').each(function(){
            if($(this).is(':checked'))
                rating = $(this).val()
        });
        
        $.post('/questions',{token: token, rating: rating, comment: comment}, function(data){
            btn.removeClass('running').removeAttr('disabled');
            if(data.status == 'OK'){
                btn.siblings('.status').addClass('success').html('<i class="fas fa-check text-success"></i><span class="text-success">Datos guardados correctamente</span>');
            }
            else {
                btn.siblings('.status').addClass('error').html('<i class="fas fa-times text-danger"></i><span class="text-danger">Error al intentar guardar los datos, intente nuevamente y, si persiste el problema, contáctese con el administrador</span>');
            }
        },"json");
    });



});