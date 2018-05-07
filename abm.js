$(document).ready(function(){

    $('button.btn-success').on('click',function(){
        if(confirm('¿Está seguro de confirmar esta operación? Se les enviará un email a aquellos profesionales a los que se les ha modificado la autorización.')){
            var profList = [];
            var btn = $(this);
            btn.addClass('running');
            btn.siblings('.status').empty();

            $('.list').each(function(){
                if($(this).find('table').length > 0){
                    $(this).find('table tbody tr').each(function(){
                        if($(this).find('input[name="changed"]').val() == '1'){
                            var token   = $(this).find('input[name="token"]').val();
                            var email   = $(this).find('input[name="email"]').val();
                            var status  = $(this).find('input[type="checkbox"]').is(':checked') ? 1 : 2;
                            profList.push({token: token, email: email, status: status});    
                        }
                    });
                }
            });

            $.post('/profesionales/altas',{profList: profList},function(data){
                btn.removeClass('running');
                if(data.status=='OK'){
                    btn.siblings('.status').html('<i class="fas fa-check text-success"></i><span class="text-success">Datos guardados correctamente</span>');
                } else {
                    btn.siblings('.status').html('<i class="fas fa-times text-danger"></i><span class="text-danger">Error al guardar</span>');
                }
            },'json');
        }
    });



    $('.toggle[data-toggle="toggle"]').on('change',function(ev){
        $(this).siblings('input[name="changed"]').val('1');
        $('button.btn.btn-success').removeAttr('disabled');
    });







    


});