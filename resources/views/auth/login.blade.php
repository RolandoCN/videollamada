<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>

        .login-page, .register-page{
            height:80vh !important;
        }

        .login-box, .register-box {
            width: 550px ;
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
                <a href="#"><b>Inicio de Sesión</b></a>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">Ingresa tus datos</p>
                    <form method="POST" action="{{ route('login') }}" class="form-horizontal">
                        @csrf

                        <div class="form-group row">
                            <label for="cedula" class="col-md-3 col-form-label text-md-right">{{ __('Usuario') }}</label>

                            <div class="col-md-9">
                                <input id="cedula" type="cedula" class="form-control @error('cedula') is-invalid @enderror" name="cedula" value="{{ old('cedula') }}" required autocomplete="cedula" autofocus>

                                @error('cedula')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-3 col-form-label text-md-right">{{ __('Contraseña') }}</label>

                            <div class="col-md-9">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Iniciar Sesión') }}
                                </button>
                             

                            </div>
                        </div>
                        <div class="col-12" style="margin-top:15px">
                            <center>
                                <a href="/" style="text-align: center; margin-top:12px">Solicitar Turno</a>
                            </center>
                        </div>
                    </form>
                
                </div>
            </div>
        </div>
    </body>
</html>

