$(document).ready(function(){

  
    //activa campos para registrar alumno

    $('.btn_new_alumno').click(function(e){

        e.preventDefault();
        alert('Al fin funciono');
            
        $('#nombre_alumno').removeAttr('disabled');
        $('#apellido_alumno').removeAttr('disabled');
        $('#dpi').removeAttr('disabled');
        $('#telefono_alumno').removeAttr('disabled');
        $('#direccion_alumno').removeAttr('disabled');

        $('#div_registro_alumno').slideDown();
    
    }); 

    //busqueda por medio de carnet
    $('#carnet').keyup(function (e) {
        
        e.preventDefault();

        var al = $(this).val();
        var action = 'buscar_alumno';

        //alert('carnet');

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action,alumno:al},

            success: function(response){
                console.log(response);

                if(response ==0){
                    $('#carnet_h').val('');
                    $('#nombre_alumno').val('');
                    $('#apellido_alumno').val('');
                    $('#dpi').val('');
                    $('#telefono_alumno').val('');
                    $('#direccion_alumno').val('');

                    //mostrar boton agregar
                    $('.btn_new_alumno').slideDown();
                }
                else{

                    var data=$.parseJSON(response);
                    $('#carnet_h').val(data.carnet);
                    $('#nombre_alumno').val(data.nombre);
                    $('#apellido_alumno').val(data.apellido);
                    $('#dpi').val(data.dpi);
                    $('#telefono_alumno').val(data.telefono);
                    $('#direccion_alumno').val(data.direccion);

                    //ocultar boton agregar
                    $('.btn_new_alumno').slideUp();

                    //Bloquear campos
                    $('#nombre_alumno').attr('disabled','disabled');
                    $('#apellido_alumno').attr('disabled','disabled');
                    $('#dpi').attr('disabled','disabled');
                    $('#telefono_alumno').attr('disabled','disabled');
                    $('#direccion_alumno').attr('disabled','disabled');

                    //ocultar boton guardar
                    $('#div_registro_alumno').slideUp();

                }
            },
            error: function(_error){

            }
        });

        
    });
    
    //Crear alumno
    $('#form_new_alumno').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: $('#form_new_alumno').serialize(),

            success: function(response){
                console.log(response);

                //agregar id a input hiden
                $('#carnet_h').val(response);

                //bloquear campos
                $('#nombre_alumno').attr('disabled','disabled');
                $('#apellido_alumno').attr('disabled','disabled');
                $('#dpi').attr('disabled','disabled');
                $('#telefono_alumno').attr('disabled','disabled');
                $('#direccion_alumno').attr('disabled','disabled');

                //ocultar boton guardar
                $('#div_registro_alumno').slideUp();

                //ocultar boton agregar
                $('.btn_new_alumno').slideUp();

            },
            error: function(_error){

            }
        });
    });

    //buscar cursos
    $('#txt_codigo').keyup(function(e) {
        e.preventDefault();

        var curso = $(this).val();
        var action = 'infoCurso';

        if(curso != ''){        
            
            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action, curso:curso},

                success: function(response){
                    console.log(response);

                    if(response != 0){

                        var data=$.parseJSON(response);
                        $('#txt_descripcion').html(data.descripcion);
                        $('#txt_facultad').html(data.facultad);
                        $('#txt_horario').val(data.horario);
                        $('#horario_h').val(data.idhorario);
                        $('#txt_colegiatura').html(data.precio);

                        $('#add_curso').slideDown();
                    
                    }
                    else{

                        $('#txt_descripcion').html('-');
                        $('#txt_facultad').html('-');
                        $('#txt_horario').html('-');
                        $('#horario_h').val('');
                        $('#txt_colegiatura').html('0.00');

                        $('#add_curso').slideUp();
                    }
                },
                error: function(_error){
                }
            });
        }
    });

    //agregar curso al detalle de matricula
    $('#add_curso').click(function (e) {

        e.preventDefault();

        var codcurso = $('#txt_codigo').val();
        var horario = $('#horario_h').val();
        var action = 'addCursoDetalle';

        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: {action:action,curso:codcurso,horario:horario},

            success: function (response) {
                console.log(response);
 
                    
                    //$('#horario_h').val(data.horario);

                    if(response != 'error'){
                        var info= JSON.parse(response);
                        //console.log(info);
                        $('#detalle_inscripcion').html(info.detalle);
                        $('#detalle_totales').html(info.total);

                        $('#txt_codigo').val('');
                        $('#txt_descripcion').html('-');
                        $('#txt_facultad').html('-');
                        $('#txt_horario').val('-');
                        $('#horario_h').val('');
                        $('#txt_colegiatura').html('0.00');

                        $('#txt_horario').attr('disabled','disabled');
                        $('#add_curso').slideUp();
                        //location.reload();

                    }
                    else{
                        console.log('no data');
                    }
                
            },
            error: function (error) {
                
            }
        });
        
    });

    //anular detalle de inscripcion
    $('#btn_anular').click(function (e) {
        
        e.preventDefault();

        var filas = $('#detalle_inscripcion tr').length;

        if(filas > 0){

            var action = 'anularInscripcion';

            $.ajax({
                url: 'ajax.php',
                type: "POST",
                async: true,
                data: {action:action},

                success: function (response){
                    console.log(response);

                    if(response != 'error'){
                        location.reload();
                    }
                },
                error: function(error){

                }
            });
        }

    });

    //Procesar detalle de inscripcion
    $('#btn_procesar').click(function (e) {
        
    e.preventDefault();

    var filas = $('#detalle_inscripcion tr').length;

    if(filas > 0){

        var action = 'procesarInscripcion';
        var carnet = $('#carnet_h').val();

        $.ajax({
            url: 'ajax.php',
            type: "POST",
            async: true,
            data: {action:action, carnet:carnet},

            success: function (response){
                console.log(response);

                if(response != 'error'){
                    
                    var info = JSON.parse(response);
                    //console.log(info); 

                    generarPDF(info.carnet,info.noboleta);
                    location.reload();
                }else{
                    console.log('no data');
                }
            },
            error: function(error){

            }
        });
    }

    });  


    //modal anulacion boleta
    $('.anular_boleta').click(function(e){

        e.preventDefault();
        var noboleta = $(this).attr('b');
        var action = 'infoBoleta';
        //alert(noboleta);
        //$('.modal').fadeIn();

        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: {action:action,noboleta:noboleta},

            success: function(response){

                if(response != 'error'){

                    var info = JSON.parse(response);
                        console.log(info);
                    $('.body').html('<form action="" method="post" name="form_anular" id="form_anular" onsubmit = "event.preventDefault(); anularBoleta();">'+
                                    '<h1><i class="fas fa-book" style="font-size: 45pt;" align="center"></i> <br><br>Anular Matricula </h1>'+
                                    '<p>¿Esta seguro de anular el siguiente registro?</p>'+

                                    '<p><strong>No. '+info.noboleta+'</strong></p>'+
                                    '<p><strong>Colegiatura: Q. '+info.total+'</strong></p>'+
                                    '<p><strong>Fecha: '+info.fecha+'</strong></p>'+
                                    '<input type="hidden" name="action" value="anularBoleta">'+
                                    '<input type="hidden" name="no_boleta" id="no_boleta" value="'+info.noboleta+'" required>'+

                                    '<div class="alert alert_matricula"></div>'+
                                    '<a href="#" class="btn_cancel" onclick="closeModal();"><i class="fas fa-ban"></i> Cerrar </a>'+
                                    '<button type="submit" class="btn_ok"><i class="fas fa-trash-alt"></i> Anular </button>'+
                                    '</form>');
                }
            },
            error:function(error){
                console.log(error);
            }
        });
        $('.modal').fadeIn();
    });

    //ver boleta
    $('.view_boleta').click(function(e){

        e.preventDefault();
        var carnet = $(this).attr('al');
        var noboleta = $(this).attr('b');

        generarPDF(carnet,noboleta);
    })
    
    //cambio de clave usuario
    $('.newPass').keyup(function(){
        //console.log($(this).val());
        validarClave();
    })

    //formulario cambio de clave
    $('#cambioClave').submit(function(e){
        e.preventDefault();

        var passActual = $('#clave').val();
        var passNuevo = $('#nuevaclave').val();
        var passConfirmar = $('#confirmarclave').val();
        var action = "cambiarClave";

        if(passNuevo != passConfirmar){
            $('.alertCambioClave').html('<p style="color:red;">Las claves no coinciden</p>');
            $('.alertCambioClave').slideDown();
            return false;
        }else{
            $('.alertCambioClave').slideUp();
        }
    
        if(passNuevo.length < 5){
            $('.alertCambioClave').html('<p style="color:red;">Las claves tienen que tener mas de 5 caracteres</p>');
            $('.alertCambioClave').slideDown();
            return false;
        }else{
            $('.alertCambioClave').slideUp();
        }

        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: {action:action,passActual:passActual,passNuevo:passNuevo},

            success: function(response){

                //console.log(response);
                if(response != 'error'){

                    var info = JSON.parse(response);

                    if(info.cod == '00'){
                        $('.alertCambioClave').html('<p style="color:green;">'+info.msg+'</p>');
                        $('#cambioClave')[0].reset();
                    }else{
                        $('.alertCambioClave').html('<p style="color:red;">'+info.msg+'</p>');
                    }
                    $('.alertCambioClave').slideDown();
                }
            
            },
            error:function(error){
                console.log(error);
            }
        });

    })

    //actualizar datos de universidad
    $('#frmUniversidad').submit(function(e){
        e.preventDefault();

        var tnombre = $('txt_nombre').val();
        var ttelefono = $('txt_tel').val();
        var tcorreo = $('txt_correo').val();
        var tdireccion = $('txt_direccion').val();

        if(tnombre==''||tcorreo==''||tdireccion==''||ttelefono==''){
            $('.alertUniversidad').html('<p style="color:red;">Todos los campos son obligatorios</p>');
            $('.alertUniversidad').slideDown();
            return false;
        }

        $.ajax({
            url: 'ajax.php',
            type:"POST",
            async: true,
            data: $('#frmUniversidad').serialize(),

            beforeSend:function(){
                $('.alertUniversidad').slideUp();
                $('.alertUniversidad').html('');
                $('#frmUniversidad input').attr('disabled','disabled');
            },

            success: function(response){

                console.log(response);
                    var info = JSON.parse(response);

                    if(info.cod == '00'){
                        $('.alertUniversidad').html('<p style="color:green;">'+info.msg+'</p>');
                        $('.alertUniversidad').slideDown();
                    }else{
                        $('.alertUniversidad').html('<p style="color:red;">'+info.msg+'</p>');
                    }
                    $('.alertFormUniversidad').slideDown();
                    $('#frmUniversidad input').removeAttr('disabled');
                
            
            },
            error:function(error){
                console.log(error);
            }
        });

    });

});
//end ready

function validarClave() {
    var passNuevo = $('#nuevaclave').val();
    var passConfirmar = $('#confirmarclave').val();

    if(passNuevo != passConfirmar){
        $('.alertCambioClave').html('<p style="color:red;">Las claves no coinciden</p>');
        $('.alertCambioClave').slideDown();
        return false;
    }else{
        $('.alertCambioClave').slideUp();
    }

    if(passNuevo.length < 5){
        $('.alertCambioClave').html('<p style="color:red;">Las claves tienen que tener mas de 5 caracteres</p>');
        $('.alertCambioClave').slideDown();
        return false;
    }else{
        $('.alertCambioClave').slideUp();
    }
}

function anularBoleta(){
    var noboleta = $('#no_boleta').val(); //vienen del hidden del modal
    var action = 'anularBoleta';

    $.ajax({
        url: 'ajax.php',
        type:"POST",
        async: true,
        data: {action:action,noboleta:noboleta},

        success: function (response) {
            //console.log(response);  
            if(response=='error'){
                $('.alert_matricula').html('<p style="color:red;">Error al anular el registro.</p>'); 
            }else{
                $('row_'+noboleta+' .estado').html('<span class="anulado">Anulado</span>');
                $('#form_anular .btn_ok').remove();
                $('row_'+noboleta+' .div_boletas').html('<button class="btn_anular anular_boleta inactive"><i class="fas fa-ban"></i></button>');
                $('.alert_matricula').html('<p> Boleta Anulada con exito </p>');
            }
                     
        },
        error: function(error){

        }
    });

}

function closeModal(){
    $('.modal').fadeOut();
    location.reload();
}

function generarPDF(alumno,boleta){

    var ancho = 1000;
    var alto = 800;

    //calcular x e y para centrar la ventana

    var x = parseInt((window.screen.width/2)-(ancho/2));
    var y = parseInt((window.screen.height/2)-(alto/2));

    $url = 'boleta/generarBoleta.php?al='+alumno+'&b='+boleta;

    window.open($url, "boleta", "left="+x+",top="+y+",height="+alto+",width="+ancho+",scrollbar=si,location=no,resizable=si,menubar=si");
}

//esta funcion sirve para que al refrescar la pantalla se mantenga la informacion visible en el navegadior
function searchDetalle(id){

    var action = 'searchDetalle';
    var user = id;

    $.ajax({
        url: 'ajax.php',
        type:"POST",
        async: true,
        data: {action:action,user:user},

        success: function (response) {
            console.log(response);
            
            if(response != 'error'){
                var info= JSON.parse(response);
                //console.log(info);
                $('#detalle_inscripcion').html(info.detalle);
                $('#detalle_totales').html(info.total);

            }
            else{
                console.log('no data');
            }     
        },
        error: function (error) {
            
        }
    });

}

function eliminar_detalle(correlativo_temp){

    var action = 'eliminar_detalle';
    var id_detalle = correlativo_temp;



    $.ajax({
        url: 'ajax.php',
        type:"POST",
        async: true,
        data: {action:action,id_detalle:id_detalle},

        success: function (response) {
            console.log(response);

            if(response != 'error'){

                var info = JSON.parse(response);

                

                $('#detalle_inscripcion').html(info.detalle);
                $('#detalle_totales').html(info.total);

                $('#txt_codigo').val('');
                $('#txt_descripcion').html('-');
                $('#txt_facultad').html('-');
                $('#txt_horario').val('-');
                $('#horario_h').val('');
                $('#txt_colegiatura').html('0.00');

                $('#txt_horario').attr('disabled','disabled');
                $('#add_curso').slideUp();

            }
            else{
                

                $('#detalle_inscripcion').html('');
                $('#detalle_totales').html('');


            }
            // viewProcesar();
    
        },
        error: function (error) {
            
        }
    });
}

/* function viewProcesar() {

    if($('#detalle_inscripcion tr').length > 0){
        $('#btn_procesar').show();
    }
    else{
        $('#btn_procesar').hide();
    }
    
} */









/* $(document).ready(function () {
    
    //activa campos para registrar alumno

    $('.btn_new_alumno').click(function(e) {

        e.preventDefault();

        $('#nombre_alumno').removeAttr('disabled');
        $('#apellido_alumno').removeAttr('disabled');
        $('#dpi').removeAttr('disabled');
        $('#telefono_alumno').removeAttr('disabled');
        $('#direccion_alumno').removeAttr('disabled');

        $('#div_registro_alumno').slideDown();
    
    });
});
 */
