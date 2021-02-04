<?php


use BillingPlus\Datatables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//require_once "../Models/Model.php";
class Controller
{

     /**
      *  Fieldsdatatables()
      * (EN) Edit method for profile view
      * (ES) Método que edita la información base del perfil
      * @access public
      */
     public function Datatables()
     {
          try {
               if ($_SERVER['REQUEST_METHOD']) {
                    //New Eloquent Model
                    $this->model = new Model;
                    //Test Line # 26
                    //Fields to get From Eloquent model
                    $fields = ["id", "name", "description", "table_", "tag", "updated_at"];
                    /**
                     * associative arrays to filter on screen 
                     * key = Db_field
                     * value = id from view, this case status
                     */
                    $filter_fields = ["tag" => "status"];
                    //Init Datatables object
                    $datatables = new Datatables($this->model, $fields, $_POST);
                    $wheres = array();
                    foreach ($filter_fields as $key => $value) {
                         if (isset($_POST[$value]) && !empty($_POST[$value])) {
                              array_push($wheres, array(
                                   "field"    => $key,
                                   "operator" => "=",
                                   "value"    => $_POST[$value],
                              ));
                         }
                    }
                    //Si hay filtros en pantalla devuelve datos filtrados
                    header('Content-Type: application/json');
                    if (count($wheres) > 0) {
                         echo json_encode($datatables->complex($wheres), JSON_PRETTY_PRINT);
                    } else {
                         echo json_encode($datatables->simple(), JSON_PRETTY_PRINT);
                    }
               }
          } catch (\Throwable $th) {
               echo json_encode([
                    'draw'    => 1,
                    //El total de registros en el modelo enviado al constructor en el datatable
                    'recordsTotal'  => 1,
                    'recordsFiltered' => 1,
                    //Ambos de arriba son necesarios para la pginacion desde serverside datatables
                    'data'    => [[
                         "id" => 1,
                         "name" => "Andres Felipe",
                         "description" => "test",
                         "table_" => "test",
                         "tag" => "test",
                         "updated_at" => date("Y-m-d H:i:s")
                    ]],
                    'total'    => 1
                    ], JSON_PRETTY_PRINT);
          }
     }
}
$dat = new Controller;
echo $dat->Datatables();
