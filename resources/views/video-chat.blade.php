@extends('layouts.app')
<link href="pnotify/dist/pnotify.css" rel="stylesheet">

<style>
    .dataTables_filter{
        text-align: right !important;
    }
    .form-control-sm {
        width: 300px !important;
        height:35px !important;
    }
</style>
@section('content')
  
    <video-chat :allusers="{{ $users }}" :authUserId="{{$idusuario_logueado}}" authUserTipo="{{$usuario_logueado}}" 
     datoUser="{{$dato_user->cedula}}" datoUserName="{{$dato_user->name}}" turn_url="{{ env('TURN_SERVER_URL') }}"
        turn_username="{{ env('TURN_SERVER_USERNAME') }}" turn_credential="{{ env('TURN_SERVER_CREDENTIAL') }}" />
   
    {{-- <video-chat></video-chat> --}}
        
@endsection


