<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ClientesModel;

class Registro extends Controller
{
    public function index() 
    {
        $json = array(
            "detalle" => "No encontrado"
        );

        return json_encode($json, true);
    }

    /*=================================================
    Crear un registro
    =================================================*/
    public function create(){

        $request = \Config\Services::request();
        $validation = \Config\Services::validation();

        // Tomar datos
        $datos = array(
            "primer_nombre" => $request->getVar("primer_nombre"),
            "primer_apellido" => $request->getVar("primer_apellido"),
            "email" => $request->getVar("email")
        );

        if (!empty($datos)) {

            // Validar datos
    
            $validation->setRules([
                "primer_nombre" => "required|string|max_length[255]",
                "primer_apellido" => "required|string|max_length[255]",
                "email" => "required|valid_email|is_unique[clientes.email]"
            ]);
    
            $validation->withRequest($this->request)->run();
    
            if ($validation->getErrors()) {
                $errors = $validation->getErrors();
                $json = array(
                    "status" => 404,
                    "detalle" => $errors
                );
        
                return json_encode($json, true);
            } else {
                $id_cliente = crypt($datos["primer_nombre"].$datos["primer_apellido"].$datos["email"], '$2a$07$3bffa4ebdf4874e506c2b12405796aa5$');
                $llave_secreta = crypt($datos["email"].$datos["primer_apellido"].$datos["primer_nombre"], '$2a$07$3bffa4ebdf4874e506c2b12405796aa5$');
                // echo '<pre>';print_r($id_cliente);echo '</pre>';
                // echo '<pre>';print_r($llave_secreta);echo '</pre>';
    
                $data = array(
                    "primer_nombre" => $datos["primer_nombre"],
                    "primer_apellido" => $datos["primer_apellido"],
                    "email" => $datos["email"],
                    "id_cliente" => str_replace('$', 'a', $id_cliente),
                    "llave_secreta" => str_replace('$', 'b', $llave_secreta)
                );
    
                $clientesModel = new ClientesModel;
                $clientesModel->save($data);
    
                $json = array(
                    "status" => 200,
                    "detalle" => "Regitro exitoso, tome sus credenciales y guárdelas",
                    "credenciales" => array(
                        "id_cliente" => str_replace('$', 'a', $id_cliente),
                        "llave_secreta" => str_replace('$', 'b', $llave_secreta)
                    )
                );
        
                return json_encode($json, true);
            }
            
        } else {
            $json = array(
                "status" => 404,
                "detalle" => "Registro con errores"
            );
        
            return json_encode($json, true);
        }

        
    }
}