<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Clientes;

class ClientesController extends Controller
{
    public function index(){

        $json = array(
        
            "detalle" => "no encontrado"
        );

        return json_encode($json, true);
    }

    /*=============================
      Crear un registro
    ==============================*/
    public function store(Request $request){

        //Recoger datos
        $datos = array("nombre" => $request->input("nombre"),
                       "apellido" => $request->input("apellido"),
                       "email" => $request->input("email"));
        //echo '<pre>'; print_r($datos); echo '</pre>';


        if(!empty($datos)){

                // Validar datos
                $validator = Validator::make($datos, [
                    'nombre' => 'required|string|max:255',
                    'apellido' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:clientes'
                ]);

                // Si falla la validación
                
                if ($validator->fails()) {

                    $errores=$validator->errors();

                    $json = array(
                        "status" => 404,
                        "detalle" => $errores
                    );
            
                    return json_encode($json, true);

                }else{

                    $id_cliente = Hash::make($datos["nombre"].$datos["apellido"].$datos["email"]);
                    $llave_secreta = Hash::make($datos["email"].$datos["apellido"].$datos["nombre"],['rounds' => 12]);

                    $cliente = new Clientes();
                    $cliente->nombre = $datos["nombre"];
                    $cliente->apellido = $datos["apellido"];
                    $cliente->email = $datos["email"];
                    $cliente->id_cliente = str_replace('$', 'a', $id_cliente);
                    $cliente->llave_secreta = str_replace('$', 'o', $llave_secreta);

                    $cliente->save();

                    $json = array(
                        
                        "status" => 200,
                        "detalle"=>"Registro exitoso, tome sus credenciales y guardelas",
                        "credenciales"=>array("id_cliente"=>str_replace('$', 'a', $id_cliente),"llave_secreta"=>str_replace('$', 'o', $llave_secreta))
                    );

                    return json_encode($json, true); 


                    //echo '<pre>'; print_r($datos); echo '</pre>';

                    // $id_cliente = Hash::make($datos["nombre"].$datos["apellido"].$datos["email"]);
                    // $llave_secreta = Hash::make($datos["email"].$datos["apellido"].$datos["nombre"],['rounds' => 12]);
                    // echo '<pre>'; print_r($id_cliente); echo '</pre>';
                    // echo '<pre>'; print_r($llave_secreta); echo '</pre>';

                }
                
            }else{
                $json = array(
                    
                    "status" => 404,
                    "detalle" => "Registros con errores"
                );
        
                return json_encode($json, true);
            }
        }
}
