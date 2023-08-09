<?php namespace App\Controllers;

use CodeIgniter\Controller;

class Clientes extends Controller
{
    public function index() 
    {
        $json = array(
            "detalle" => "No encontrado"
        );

        return json_encode($json, true);
    }
}