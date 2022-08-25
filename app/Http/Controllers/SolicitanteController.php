<?php

namespace App\Http\Controllers;
use DB;
use Log;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 
use GuzzleHttp\Client;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class SolicitanteController extends Controller
{
 
    private $objLoguear = null;
    private $clienteDinard = null;
    public function __construct() {

        try{
            $this->clienteDinard = new Client([
                'base_uri' => env('URL_SERVICE_GAD'),
                'verify' => false,
            ]);

            $this->objLoguear = new LoginController();

        } catch (\Throwable $e) {
            Log::error('SolicitanteController,function __construct, '.$e->getMessage());
        }
    }

    public function consultaDinardap($cedula,$index=false){
        try{
            $tipoIdentificacion=$this->validarIdentificacion($cedula);
            if($tipoIdentificacion=="E"){ // incorrecto
                return response()->json([
                    "error"=>true,
                    "detalle"=>"Cédula no valida",
                    "valida"=>"N"
                ]);
            }

            
            $resultado = $this->clienteDinard->request('GET', "api/registrocivil/{$cedula}",[
                    'headers' => [
                        'Authorization' => env('URL_SERVICE_GAD_APIKEY')
                    ] 
                ]); 
            $resultado= json_decode((string) $resultado->getBody());
           
            $resultado[9]=$this->separarNombre($resultado[9]->valor);
            
            return [
                'error'=>false,
                'detalle'=>$resultado,
            
            ];

        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return [
                'error'=>true,
                'detalle'=>"Incovenientes al consultar información "
            ];
        } 
    }

    // funcion para validar si es una cedula o un ruc
    public function validarIdentificacion($identificacion){
        $valida=false; // almacena si es una identificacion valida
        $tipoIdentificacion="E"; //C: cedula R:ruc E:error
        if(validarCedula($identificacion)){ // si es una cedula valida
            $valida=true;
            $tipoIdentificacion="C"; // es  una cedula
        }else if(!$valida && validarRucPersonaNatural($identificacion)){
            $valida=true;
            $tipoIdentificacion="R";
        }else if(!$valida && validarRucSociedadPrivada($identificacion)){
            $valida=true;
            $tipoIdentificacion="R";
        }else if(!$valida && validarRucSociedadPublica($identificacion)){
            $valida=true;
            $tipoIdentificacion="R";
        }
        return $tipoIdentificacion;
    }

    public function separarNombre($full_name){

        $datos =  [];
    
        /* separar el nombre completo en espacios */
        $tokens = explode(' ', trim($full_name));
        /* arreglo donde se guardan las "palabras" del nombre */
        $names = array();
        /* palabras de apellidos (y nombres) compuetos */
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');
        
        $prev = "";
        foreach($tokens as $token) {
            $_token = strtolower($token);
            if(in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $names[] = $prev. $token;
                $prev = "";
            }
        }
        
        $num_nombres = count($names);
        $nombres = $apellidos = "";
        switch ($num_nombres) {
            case 0:
                $nombres = '';
                break;
            case 1: 
                $nombres = $names[0];
                break;
            case 2:
                $nombres    = $names[0];
                $apellidos  = $names[1];
                break;
            case 3:
                $apellidos = $names[0] . ' ' . $names[1];
                $nombres   = $names[2];
            default:
                $apellidos = $names[0] . ' '. $names[1];
                unset($names[0]);
                unset($names[1]);
                
                $nombres = implode(' ', $names);
                break;
        }

        $datos =  [];

        $datos[0]=$nombres;

        $datos[1]=$apellidos;
        
        return $datos;

    }
    
    public function registroSolicitante(Request $request){
       
        $transaction=DB::transaction(function() use ($request){
            try{
                $validator = Validator::make($request->all(), [
                    'cedula' => 'required|max:10',
                    'nombre' => 'required',
                    'apellido' => 'required',
                ]);
                
                if($validator->fails()){
                    return back()->withInput()->with(['errorPInfoSolicitud'=>'Complete todos los datos del formulario correctamente','estadoP'=>'danger']);
                }
                
                $tipoIdentificacion=$this->validarIdentificacion($request->cedula);
                if($tipoIdentificacion=="E"){ // incorrecto
                    return back()->withInput()->with(['errorPInfoSolicitud'=>'Cedula no valida','estadoP'=>'danger']);
                }
                //verificamops si ya existe
                $existe_usuario=User::where('cedula',$request->cedula)->first();
                if(!is_null($existe_usuario)){
                    try{
                        return $this->objLoguear->login($request);
                    }catch (\Throwable $e) {
                       return redirect('/login');
                    }
                       
                }else{
                    //lo registramos
                    $guarda=new User();
                    $guarda->cedula=$request->cedula;
                    $guarda->name=$request->nombre." ".$request->apellido;
                    $guarda->password=$cedula=Hash::make($request->cedula);
                    $guarda->tipo_usuario='Invitado';
                    $guarda->estado_video_call='Libre';
                    // $guarda->email='test'.$request->cedula.'@test.com';
                    $guarda->save();
                  
                  
                    return $this->objLoguear->login($request);
                    
                }

               
            }catch (\Throwable $e) {
                DB::rollback();
                Log::error(__CLASS__." => ".__FUNCTION__." => Mensaje =>".$e->getMessage());
                return back()->withInput()->with(['errorPInfoSolicitud'=>'Ocurrio un error','estadoP'=>'danger']);
            }
        });
        return ($transaction);
    }


}
