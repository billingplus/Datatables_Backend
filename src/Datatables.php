<?php

namespace BillingPlus;

/**
 * (EN) Datatable class for serverside data manegement to show user
 * (ES) clase datatable para la manipulcaión de datos de parte de servidor
 * @author Andrés Felipe Delgado <andresfdel13@hotmail.com>
 */
class Datatables
{
    /**
     * @access public
     * @var array $output
     * (EN) Output data to controller
     * (ES) Salida de data a controlador
     */
    public  $output;
    /**
     * @access public
     * @var object $model
     * (EN) Eloquent model object
     * (ES) Objeto de modelo de eloquent cuando tiene una relación se usa con método statico ::with('Model_method_name')
     */
    public $model;
    /**
     * @access public
     * @var array $fields
     * (EN) Data fields to get from model
     * (ES) Campos de datos a traer del modelo
     */
    public $fields;
    /**
     * @access public
     * @var array $request
     * (EN) Data array POST, GET ... etc 
     * (ES) Array de datos POST, GET... etc
     */
    public $request;
    /**
     * @access public
     * @var bool 
     * (ES) Define si el datatable tiene datos con otras tablas relacionadas
     */
    public $relation;
    /**
     * @var array
     * @access public
     * (ES)Array asociativo que va a dar los nombres a los campos relacionadors
     * Su estructura es array('Model_method_name' => array('nombre a dar en campo DT' => 'campo de la tabla relacionada')...)
     */
    public $indexes;
    /**
     * @access public
     * (EN) construct
     * (ES) Constructor
     * @param object $model
     * (EN) model object
     * (ES) objeto de modelo
     * @param array $fields
     * (EN) fields array to get from model
     * (ES) array de campos a traer
     * @param array $request
     * (EN) Array data to process, POST, GET... etc
     * (ES) Datos en array a procesar desde POST, GET ... etc
     */
    public function __construct(object  $model, array $fields = [], array $request, bool $relation = false, array $indexes = [])
    {
        $this->model = $model;
        $this->fields  = $fields;
        $this->request = $request;
        $this->relation = $relation;
        $this->indexes = $indexes;
    }
    /**
     * @access public
     * (EN) Set the data return in pretty mode
     * (ES) Ajusta los datos para salir
     * @param object $model
     * (EN) Data model after searching to set
     * (ES) Datos del modelo después de la conulta a ajustar
     * @param array $fields
     * (EN) Data array of fields
     * (ES) Array de datos de campos
     * @return array $data
     * (EN) Data output
     * (ES) Salida de datos
     */
    public function setData(object $model, array $fields = []): array
    {
        $data = array();
        $colums = array();
        foreach ($model as $key => $value) {
            foreach ($fields as $key2) {
                $colums[$key2] = $value->$key2;
            }
            //Si la tabla tiene datos relacionados
            if ($this->relation) {
                $count = 0;
                //Iteramos sobre los indexes
                foreach ($this->indexes as $key3 => $value3) {
                    //Iteramos sobre los campos de los indices
                    foreach ($value3 as $key4 => $value4) {
                        //Definimos que el nombre de la columna dtatable que viene de indexes
                        $colums[$key4] = $value->$key3->{$value4} ?? null;
                        $count++;
                    }
                    $count = 0;
                }
            }
            array_push($data, $colums);
        }
        return $data;
    }
    /**
     * @access public
     * (EN) Draw data
     * (ES) Draw datos
     * @return int 
     */
    public function getDraw(): int
    {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                if (isset($_GET["draw"])) :
                    return intval($_GET["draw"]);
                else :
                    return 0;
                endif;
                break;
            case 'POST':
                if (isset($this->request["draw"])) :
                    return intval($this->request["draw"]);
                else :
                    return 0;
                endif;
                break;
            default:
                if (isset($_GET["draw"])) :
                    return intval($_GET["draw"]);
                else :
                    return 0;
                endif;
        }
    }
    /**
     * @access public
     * @param object $model
     * (EN) model object afterr consulting
     * (ES) Objeto de modelo después de la consulta
     * @param array $fields
     * (EN) Data array of fields
     * (ES) Array de datos de campos
     * @return array $output
     * (EN) data exit directly to datatable
     * (ES) Salida de datos para datatble
     */
    public function OutData(object $modelData, array $fields, int $count): array
    {

        $this->output = array(
            'draw'    => $this->getDraw(),
            //El total de registros en el modelo enviado al constructor en el datatable
            'recordsTotal'  => intval($this->model->get()->count()),
            'recordsFiltered' => $count,
            //Ambos de arriba son necesarios para la pginacion desde serverside datatables
            'data'    => $this->setData($modelData, $fields),
            'total'    => intval($modelData->count())
        );
        return $this->output;
    }
    /**
     * @access public
     * (EN) Simple method to datatable response with single filtering
     * (ES) método simple para salida de datatble con filtro sencillo
     * @param array $fields
     * (EN) Data array of fields
     * (ES) Array de datos de campos
     * @return array 
     * Salida de datos
     */
    public function simple() //: array
    {
        $filter =  $this->filter($this->request, $this->fields);
        $order =  $this->order($this->request, $this->fields) ?? array();
        $limit = $this->limit();
        if (is_bool($filter) && $filter == false) {
            //si $filter es booleano y es falso muestra todos los registros
            $user = $this->model;
            if (!empty($order)) {
                $ordered = "";
                foreach ($order as $key => $value) {
                    $ordered .= implode(" ", $value) . ",";
                }
                $user = $user->orderByRaw(substr($ordered, 0, -1));
            }
        } else {
            // $si filter NO es un booleano y es un array con datos
            // se crea modelo con funcion para evaluar las posiciones con in OR where
            $user = $this->model->select($this->fields)->where(function ($q) use ($filter) {
                foreach ((array) $filter as $key => $value) {
                    $q->orWhere($value[0], "LIKE", "%{$value[1]}%");
                }
            });
            if (!empty($order)) {
                $ordered = "";
                foreach ($order as $key => $value) {
                    $ordered .= implode(" ", $value) . ",";
                }
                $user = $user->orderByRaw(substr($ordered, 0, -1));
            }
        }
        $count = $user->get()->count();
        if (!empty($limit)) {
            $user = $user->skip($limit['skip'])->limit($limit['limit'])->get();
        } else {
            $user = $user->get();
        }
        return $this->OutData($user, $this->fields, $count);
    }
    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return array SQL limit clause
     */
    public function limit(): array
    {
        $limit = array();
        if (isset($this->request['start']) && $this->request['length'] != -1) {
            $limit = array("skip" => intval($this->request['start']), "limit" => intval($this->request['length']));
        }
        return $limit;
    }
    /**
     * @access public
     * (EN) complex  method to add customized query with an array
     * (ES) metodo complex para añadir wheres personalizados
     * @param array $datos
     * (EN) Data array with customized wheres
     * (ES) Array de datos con los qheres personalizados
     * @param array $fields
     * (EN) Data array of fields
     * (ES) Array de datos de campos
     * @return array 
     * Salida de datos
     */
    public function complex(array $datos)
    {
        $filter =  $this->filter($this->request, $this->fields);
        $order =  $this->order($this->request, $this->fields) ?? array();
        $limit = $this->limit();
        if (is_bool($filter) && $filter == false) {
            //si $filter es booleano y es falso, solo aplica los wheres personalizados en $datos
            $user = $this->model->select($this->fields)->where(function ($q) use ($datos) {
                foreach ((array) $datos as $key => $value) {
                    if (!isset($value['type']) || $value['type'] == null) {
                        $q->where($value['field'], $value['operator'], $value['value']);
                    } else {
                        switch ($value['type']) {
                            case 'YEAR':
                                $q->whereYear($value['field'], $value['operator'], $value['value']);
                                continue 2;
                            default:

                                break;
                        }
                    }
                }
            });
            if (!empty($order)) {
                $ordered = "";
                foreach ($order as $key => $value) {
                    $ordered .= implode(" ", $value) . ",";
                }
                $user = $user->orderByRaw(substr($ordered, 0, -1));
            }
        } else {
            //Si $filter es array con datos para buscar usa un where complejo para buscar similitudes y los personalizados en $datos
            $user = $this->model->select($this->fields)->where(function ($q) use ($filter) {
                foreach ((array) $filter as $key => $value) {
                    $q->orWhere($value[0], "LIKE", "%{$value[1]}%");
                }
            })->where(function ($q) use ($datos) {
                error_reporting(0);
                foreach ((array) $datos as $key => $value) {
                    if (!isset($value['type']) || $value['type'] == null) {
                        $q->where($value['field'], $value['operator'], $value['value']);
                    } else {
                        switch ($value['type']) {
                            case 'YEAR':
                                $q->whereYear($value['field'], $value['operator'], $value['value']);
                                continue 2;
                            default:

                                break;
                        }
                    }
                }
            });
            if (!empty($order)) {
                $ordered = "";
                foreach ($order as $key => $value) {
                    $ordered .= implode(" ", $value) . ",";
                }
                $user = $user->orderByRaw(substr($ordered, 0, -1));
            }
        }
        $count = $user->get()->count();
        if (count($limit) > 0) {
            $user = $user->skip($limit['skip'])->limit($limit['limit'])->get();
        } else {
            $user = $user->get();
        }
        return $this->OutData($user, $this->fields, $count);
    }
    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables by $_POST
     *  @param  array $columns Column information array
     *  @return void 
     */
    public function filter(array $request, array $columns)
    {
        $globalSearch = array();
        $columnSearch = array();
        //Por ahora solo se usa para busqueda con input de busqueda
        if (isset($request['search']) && $request['search']['value'] != '') {
            $str = $request['search']['value'];
            for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search($requestColumn['data'], $columns);
                $column = $columns[$columnIdx];
                if ($requestColumn['searchable'] == 'true') {
                    $globalSearch[] = array($column, $str);
                }
            }
        }
        // Combine the filters into a single string
        if (is_array($globalSearch) && count($globalSearch) >= 1) {
            return (array) $globalSearch;
        } else {
            return false;
        }
    }
    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return array
     */
    public function order($request, $columns)
    {
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = $columns;
            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
                if ($requestColumn['orderable'] == 'true') {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'asc' :
                        'desc';
                    $orderBy[] =  array($column, $dir);
                }
            }
            if (count($orderBy) > 0) {
                return $orderBy;
            } else {
                return array();
            }
        }
    }
}
