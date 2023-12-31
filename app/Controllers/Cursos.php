<?php namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CursosModel;
use App\Models\ClientesModel;

class Cursos extends Controller
{
    /*=================================================
    Mostrar todos los cursos
    =================================================*/
    public function index() 
    {
        $request = \Config\Services::request();
        $headers = $request->getHeaders();

        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->findAll();

        $db = \Config\Database::connect();

        foreach ($clientes as $key => $value) {

            if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
                // echo '<pre>';print_r($request->getHeader('Authorization'));echo '</pre>';
                // return;
                if ($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])) {
                    // $cursosModel = new CursosModel();
                    // $cursos = $cursosModel->findAll();

                    if (isset($_GET["page"])) {

                        $cantidad = 10;
                        $desde = ($_GET["page"]-1)*$cantidad;

                        $query = $db->query('CALL SP_CONSULTA_CURSOS_POR_CLIENTES_BY_PAGE('.$cantidad.','.$desde.')');
                        // $query = $db->query('
                        //     SELECT cursos.id, titulo, descripcion, instructor, imagen, precio, id_creador, primer_nombre, primer_apellido 
                        //     FROM cursos 
                        //     INNER JOIN clientes 
                        //     ON cursos.id_creador = clientes.id 
                        //     LIMIT '.$cantidad.' OFFSET '.$desde.'
                        // ');
                    } else {
                        $query = $db->query('CALL SP_CONSULTA_CURSOS_POR_CLIENTES()');
                        // $query = $db->query('
                        //     SELECT cursos.id, titulo, descripcion, instructor, imagen, precio, id_creador, primer_nombre, primer_apellido 
                        //     FROM cursos 
                        //     INNER JOIN clientes 
                        //     ON cursos.id_creador = clientes.id
                        // ');
                    }

                    
                    $cursos = $query->getResult();

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

                } else {
                    $json = array(
                        "status" => 404,
                        "detalle" => "El token es inválido"
                    );
                }

            } else {
                $json = array(
                    "status" => 404,
                    "detalle" => "No està autorizado para recibir los registros"
                );
            }
        }        
        

        return json_encode($json, true);
    }

    /*=================================================
    Crear nuevo curso
    =================================================*/
    public function create(){

        $request = \Config\Services::request();
        $validation = \Config\Services::validation();


        $headers = $request->getHeaders();

        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->findAll();

        foreach ($clientes as $key => $value) {

            if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
                // echo '<pre>';print_r($request->getHeader('Authorization'));echo '</pre>';
                // return;
                if ($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])) {
                    
                    // Tomar datos
                    // $datos = array(
                    //     "titulo" => $request->getVar("titulo"),
                    //     "descripcion" => $request->getVar("descripcion"),
                    //     "instructor" => $request->getVar("instructor"),
                    //     "imagen" => $request->getVar("imagen"),
                    //     "precio" => $request->getVar("precio")
                    // );

                    $datos = $this->request->getRawInput();
                    
                    if (!empty($datos)) {

                        // Validar datos
                
                        $validation->setRules([
                            "titulo" => "required|string|max_length[255]|is_unique[cursos.titulo]",
                            "descripcion" => "required|string|max_length[255]|is_unique[cursos.descripcion]",
                            "instructor" => "required|string|max_length[255]",
                            "imagen" => "required|string|max_length[255]",
                            "precio" => "required|numeric"
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
                            $data = array(
                                "titulo" => $datos["titulo"],
                                "descripcion" => $datos["descripcion"],
                                "instructor" => $datos["instructor"],
                                "imagen" => $datos["imagen"],
                                "precio" => $datos["precio"],
                                "id_creador" => $value["id"],
                            );
                
                            $cursosModel = new CursosModel;
                            $cursosModel->save($data);
                
                            $json = array(
                                "status" => 200,
                                "detalle" => "Regitro exitoso, su curso ha sido guardado",
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


                } else {
                    $json = array(
                        "status" => 404,
                        "detalle" => "El token es inválido"
                    );
                }

            } else {
                $json = array(
                    "status" => 404,
                    "detalle" => "No està autorizado para guardar los registros"
                );
            }
        }       

        return json_encode($json, true);  
    }

    /*=================================================
    Mostrar un solo curso
    =================================================*/
    public function show($id) 
    {
        $request = \Config\Services::request();
        $headers = $request->getHeaders();

        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->findAll();

        foreach ($clientes as $key => $value) {

            if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
                // echo '<pre>';print_r($request->getHeader('Authorization'));echo '</pre>';
                // return;
                if ($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])) {
                    $cursosModel = new CursosModel();
                    $curso = $cursosModel->find($id);
                    // $json = array();

                    if (!empty($curso)) {
                        $json = array(
                            "status" => 200,
                            "message" => $curso
                        );
                    } else {
                        $json = array(
                            "status" => 404,
                            "total_results" => 0,
                            "message" => "Ningún curso cargado"
                        );
                    }

                } else {
                    $json = array(
                        "status" => 404,
                        "detalle" => "El token es inválido"
                    );
                }

            } else {
                $json = array(
                    "status" => 404,
                    "detalle" => "No està autorizado para recibir los registros"
                );
            }
        }        
        

        return json_encode($json, true);
    }

    /*=================================================
    Editar un curso
    =================================================*/
    public function update($id){

        $request = \Config\Services::request();
        $validation = \Config\Services::validation();


        $headers = $request->getHeaders();

        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->findAll();

        foreach ($clientes as $key => $value) {

            if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
                // echo '<pre>';print_r($request->getHeader('Authorization'));echo '</pre>';
                // return;
                if ($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])) {
                    
                    // Tomar datos
                    // $datos = array(
                    //     "titulo" => $request->getVar("titulo"),
                    //     "descripcion" => $request->getVar("descripcion"),
                    //     "instructor" => $request->getVar("instructor"),
                    //     "imagen" => $request->getVar("imagen"),
                    //     "precio" => $request->getVar("precio")
                    // );
                    
                    $datos = $this->request->getRawInput();

                    if (!empty($datos)) {

                        // Validar datos
                
                        $validation->setRules([
                            "titulo" => "required|string|max_length[255]",
                            "descripcion" => "required|string|max_length[255]",
                            "instructor" => "required|string|max_length[255]",
                            "imagen" => "required|string|max_length[255]",
                            "precio" => "required|numeric"
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

                            $cursosModel = new CursosModel();
                            $curso = $cursosModel->find($id);

                            if ($value["id"] == $curso["id_creador"]) {
                                
                                $data = array(
                                    "titulo" => $datos["titulo"],
                                    "descripcion" => $datos["descripcion"],
                                    "instructor" => $datos["instructor"],
                                    "imagen" => $datos["imagen"],
                                    "precio" => $datos["precio"]
                                );
                    
                                $cursosModel = new CursosModel;
                                $cursosModel->update($id, $data);
                    
                                $json = array(
                                    "status" => 200,
                                    "detalle" => "Regitro exitoso, su curso ha sido actualizado",
                                );
                        
                                return json_encode($json, true);
                            } else {
                                $json = array(
                                    "status" => 404,
                                    "detalle" => "No está autorizado para modificar este curso"
                                );
                            
                                return json_encode($json, true);
                            }

                        }
                        
                    } else {
                        $json = array(
                            "status" => 404,
                            "detalle" => "Registro con errores"
                        );
                    
                        return json_encode($json, true);
                    }


                } else {
                    $json = array(
                        "status" => 404,
                        "detalle" => "El token es inválido"
                    );
                }

            } else {
                $json = array(
                    "status" => 404,
                    "detalle" => "No està autorizado para editar los registros"
                );
            }
        }       

        return json_encode($json, true);  
    }

    /*=================================================
    Borrar un curso
    =================================================*/
    public function delete($id) {

        $request = \Config\Services::request();
        $headers = $request->getHeaders();

        $clientesModel = new ClientesModel();
        $clientes = $clientesModel->findAll();

        foreach ($clientes as $key => $value) {

            if (array_key_exists('Authorization', $headers) && !empty($headers['Authorization'])) {
                if ($request->getHeader('Authorization') == 'Authorization: Basic '.base64_encode($value["id_cliente"].":".$value["llave_secreta"])) {
                    $cursosModel = new CursosModel();
                    $validar = $cursosModel->find($id);
                    // echo '<pre>';print_r($validar);echo '</pre>';
                    // return;
                    if (!empty($validar)) {
                        if ($value["id"] == $validar["id_creador"]) {
                            // $cursosModel = new CursosModel();
                            $cursosModel->delete($id);
                                            
                            $json = array(
                                "status" => 200,
                                "detalle" => "Se ha borrado su curso con éxito",
                            );
                    
                            return json_encode($json, true);
                        } else {
                            $json = array(
                                "status" => 404,
                                "detalle" => "No está autorizado para eliminar este curso"
                            );
                        
                            return json_encode($json, true);
                        }
                    } else {
                        $json = array(
                            "status" => 404,
                            "message" => "El curso no existe"
                        );
                    }

                } else {
                    $json = array(
                        "status" => 404,
                        "detalle" => "El token es inválido"
                    );
                }

            } else {
                $json = array(
                    "status" => 404,
                    "detalle" => "No està autorizado para eliminar los registros"
                );
            }
        }        
        

        return json_encode($json, true);
    }

}