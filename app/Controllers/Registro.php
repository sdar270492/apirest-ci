<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Registro extends Controller
{
    public function index() 
    {
        $json = array(
            "detalle" => "No encontrado"
        );

        return json_encode($json, true);
    }

    /*
    Crear un registro
    */
    public function create(){
        $request = \Config\Services::request();

        $datos = array(
            "primer_nombre" => $request->getVar("primer_nombre"),
            "primer_apellido" => $request->getVar("primer_apellido"),
            "email" => $request->getVar("email")
        );
        echo '<pre>';print_r($datos);echo '</pre>';
    }


}