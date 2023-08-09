<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CursosModel;

class Cursos extends Controller
{
    /*=================================================
    Mostrar todos los cursos
    =================================================*/
    public function index() 
    {
        $cursosModel = new CursosModel();
        $cursos = $cursosModel->findAll();
        // $json = array();

        if (!empty($cursos)) {
            $json = array(
                "status" => 200,
                "total_results" => count($cursos),
                "message" => $cursos
            );
        } else {
            $json = array(
                "status" => 404,
                "total_results" => 0,
                "message" => "Ningún curso cargado"
            );
        }

        echo json_encode($json, true);
    }
}