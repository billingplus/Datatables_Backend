<?php

require_once "Clases/Datatables.php";
require_once "Models/Model.php";
class Controller

{
     public function __construct()
     {
     }
     /**
      *  Fieldsdatatables()
      * (EN) Edit method for profile view
      * (ES) Método que edita la información base del perfil
      * @access public
      */
     public function Datatables()
     {
          if ($_SERVER['REQUEST_METHOD'] == "POST") {
               //New Eloquent Model
               $this->model = new Model;
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
     }
}
