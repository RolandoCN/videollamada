<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\StartVideoChat;
use App\Models\User;

class VideoChatController extends Controller
{   
    public function index(){
        try{
            $idusuariolog=Auth::id();
            $users = User::where('id', '<>', Auth::id())->get();
            $usuario=User::where('id', Auth::id())->first();
        
            $usuario->estado_video_call="Libre";
            $usuario->save();

            $usuariolog=$usuario->tipo_usuario;
            $idusuariolog=$usuario->id;
            $dato_user=$usuario;
            
        
            return view('video-chat', ['users' => $users, 'usuario_logueado' => $usuariolog, 
            'idusuario_logueado' => $idusuariolog, "dato_user"=>$dato_user]);
        }catch (\Throwable $e) {
            
            Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage());
            return back();
        }
    }
    

    // llamada realizada desde un usuario admin (a un invitadoo)
    public function callUser(Request $request)
    {
        // verifico el estado del usuario;
        $estado_user=User::where('estado_video_call','Libre')->where('id',$request->user_to_call)
        ->get()->last();
        if(is_null($estado_user)){
            return ['ocupado'=>'S'];
        }else{
            $actualiza_estado=User::where('id', Auth::id())->update(['estado_video_call'=>'Ocupado']);
            $data['userToCall'] = $request->user_to_call;
            $data['signalData'] = $request->signal_data;
            $data['from'] = Auth::id();     
            $data['type'] = 'incomingCall';

            broadcast(new StartVideoChat($data))->toOthers();
        }

        
    }

    // llamada realizada desde un usuario no admin
    public function callSolicitante(Request $request)
    {
        //verifo el estado del administrador
        $estado_adm=User::where('tipo_usuario','admin')->where('estado_video_call','Libre')
        ->get()->last();
        if(is_null($estado_adm)){
            return ['ocupado'=>'S'];
        }else{
            
            $actualiza_estado=User::where('id', Auth::id())->update(['estado_video_call'=>'Ocupado']);
            $data['userToCall'] = $estado_adm->id;
            $data['signalData'] = $request->signal_data;
           
            $data['from'] = Auth::id();   
            $data['type'] = 'incomingCall';
    
            broadcast(new StartVideoChat($data))->toOthers();
        }
      
    }

  
    public function acceptCall(Request $request)
    {
        $actualiza_estado=User::where('id', Auth::id())->update(['estado_video_call'=>'Ocupado']);
        $data['signal'] = $request->signal;
        $data['to'] = $request->to;
        $data['type'] = 'callAccepted';
        broadcast(new StartVideoChat($data))->toOthers();
    }

    public function endCall(Request $request)
    {
        $actualiza_estado=User::where('id', Auth::id())->update(['estado_video_call'=>'Libre']);
    }
}
