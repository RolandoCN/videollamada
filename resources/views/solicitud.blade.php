<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Solicitud Turno</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <link href="pnotify/dist/pnotify.css" rel="stylesheet">
    <link href="pnotify/dist/pnotify.buttons.css" rel="stylesheet">
    <link href="pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
    <link rel="stylesheet" href="css/spinners.css">


    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>

        .login-page, .register-page{
            height:80vh !important;
        }

        .login-box, .register-box {
            width: 550px ;
        }
        .spinner-border{
            width: 1rem;
            height: 1rem;
        }

        @media  screen and (max-width: 767px){
            .login-box, .register-box {
                width: 300px !important;
            }
        }
       

    </style>
    </head>
    <body class="hold-transition login-page">

        @if(Session()->has('estadoP') )
            <input type="hidden" name="error" id="error" value="{{session('errorPInfoSolicitud')}}">
        @endif

        <div class="login-box">
            <div class="login-logo">
                <a href="#"><b>Solicitud Turno</b></a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Ingresa tus datos</p>

                    <form class="form-horizontal" onsubmit="return validarForm()" id="form_registro2" method="POST" autocomplete="off" action="{{ url('/registro-solicitante') }}">
                        {{ csrf_field() }}

                        <input type="hidden" class="form-control" id="password" name="password" placeholder="Numero Cedula" value="{{ old('password') }}">

                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Cedula</label>
                            <div class="col-sm-8">
                                <input type="number" autocomplete="of" class="form-control" id="cedula" name="cedula" placeholder="Numero Cedula" value="{{ old('cedula') }}">
                            </div>
                            <div class="col-sm-2">
                                <div id="divbtnbuscar">
                                <button type="button" onclick="buscar_datos()" class="btn btn-sm btn-info btn-block" style="margin-top:4px"> <i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Nombres</label>
                            <div class="col-sm-10">
                                <input type="text" autocomplete="of" class="form-control" id="nombre" placeholder="Nombres"  name="nombre"  readonly>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-2 col-form-label">Apellidos</label>
                            <div class="col-sm-10">
                                <input type="text" autocomplete="of" class="form-control" id="apellido" placeholder="Apellidos"name="apellido"  readonly>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="inputPassword3" class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary" id="btn_continuar" disabled>Continuar  </button>
                               
                            </div>
                         
                           
                        </div>
                        <div class="col-12">
                          
                            <center>
                                <a href="/login" style="text-align: center; margin-top:12px">Iniciar Sesión</a>
                            </center>
                            
                        </div>

                        
                        
                    </form>

              
            </div>
        </div>
        <!-- /.login-box -->

        <!-- jQuery -->
        <script src="../../plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../../dist/js/adminlte.min.js"></script>
    </body>
</html>

<script src="pnotify/dist/pnotify.js"></script>
<script src="pnotify/dist/pnotify.buttons.js"></script>
<script>

    // FUNCION PARA MOSTRAR UNA ALERTA DE PNotify PERSONALIZADA
    function alertNotificar(texto, tipo,time=7000){
        PNotify.removeAll()
        new PNotify({
            title: 'Mensaje de Información',
            text: texto,
            type: tipo,
            hide: true,
            delay: time,
            styling: 'bootstrap3',
            addclass: ''
        });
    }

    function buscar_datos(){
        var cedula=$('#cedula').val();
        if(cedula=="" || cedula==null){
            alertNotificar("Ingrese un numero de cedula","error");
            $('#cedula').focus();
            return;
        }
        if(cedula.length!=10){
            alertNotificar("El numero de digitos ingresados en el campo cedula no es valido","error");
            $('#cedula').focus();
            return;
        }
        $('#password').val(cedula);

        $('#divbtnbuscar').html(`<button id="btnBuscars" disabled=true  type="button"  class="btn btn-sm btn-info btn-block"><span class="spinner-border " role="status" aria-hidden="true"></span> </button>`);  

        $('#nombre').prop('readonly',true);
        $('#apellido').prop('readonly',true);
        $('#nombre').val('');
        $('#apellido').val('');

        $.get("/validar-dinardap/"+cedula, function(data){
            console.log(data)
            $('#btn_continuar').prop('disabled',false);
            $('#divbtnbuscar').html(` <button type="button" onclick="buscar_datos()" class="btn btn-sm btn-info btn-block"><i class="fa fa-search"></i> </button>`);
            if(data.error==true){
                alertNotificar(data.detalle,"error");
                $('#nombre').prop('readonly',false);
                $('#apellido').prop('readonly',false);
                if(data.valida!=undefined || data.valida=='N'){
                    $('#nombre').prop('readonly',true);
                    $('#apellido').prop('readonly',true);
                    $('#btn_continuar').prop('disabled',true);
                }
                return;
            }
            if(data.error==false){
                $('#nombre').prop('readonly',true);
                $('#apellido').prop('readonly',true);
                $('#nombre').val(data.detalle[9][0]);
                $('#apellido').val(data.detalle[9][1]);
                
            }
        

        }).fail(function(){
            $('#btn_continuar').prop('disabled',true);
            $('#divbtnbuscar').html(` <button type="button" onclick="buscar_datos()" class="btn btn-sm btn-info btn-block"><i class="fa fa-search"></i> </button>`);
            alertNotificar("Se produjo un error, por favor intentelo más tarde");  
        });
    }

    var sms_error=$('#error').val();
    if(sms_error!=undefined){
        alertNotificar(sms_error,"error")
    }

    function validarForm(){
        var cedula=$('#cedula').val();
        var nombre=$('#nombre').val();
        var apellido=$('#apellido').val();

        if(cedula=="" || cedula==null){
            alertNotificar("Ingrese un numero de cedula","error");
            $('#cedula').focus();
            return false;
        }
        if(nombre=="" || nombre==null){
            alertNotificar("Ingrese un nombre","error");
            $('#nombre').focus();
            return false;
        }
        if(apellido=="" || apellido==null){
            alertNotificar("Ingrese un apellido","error");
            $('#apellido').focus();
            return false;
        }

        if(cedula.length!=10){
            alertNotificar("El numero de digitos ingresados en el campo cedula no es valido","error");
            $('#cedula').focus();
            return false;
        }

        return true;
    }

    
	
    
</script>