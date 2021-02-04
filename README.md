<p align="center">
<a href="https://packagist.org/packages/billingplus/datlarobj"><img src="https://poser.pugx.org/billingplus/datlarobj/v/unstable"></a>
<a href="https://packagist.org/packages/billingplus/datlarobj"><img src="https://poser.pugx.org/billingplus/datlarobj/v"></a>
<a href="https://packagist.org/packages/billingplus/datlarobj"><img src="https://poser.pugx.org/billingplus/datlarobj/downloads"></a>
<a href="https://packagist.org/packages/billingplus/datlarobj"><img src="https://poser.pugx.org/billingplus/datlarobj/license"></a>
</p>

## About

(EN) This repository is created in order to give an alternative to the datatables with a class based on the eloquent ORM of laravel instead of the classic SSP, is pending improvements and help from users.

(ES) Este repositorio se crea con el fin de dar una alternativa al datatables con una clase basada en el ORM eloquent de laravel en vez del clásico SSP, está pendiente a mejoras y ayudas de los usuarios.

## Installation

<code>composer require billingplus/datlarobj</code>

## Example

```php
<?php
/*
|--------------------------------------------------------------------------
| Register The Auto Loader Composer
|--------------------------------------------------------------------------
|
| (EN) Class loader using composer for the entire application
| (ES) Cargador de clases mediante composer para toda la aplicacion
|
*/
//Eloquent namespace
use User\Users;
//Class Datatable namespace
use BillingPlus\Datatables\Datatables;


if (file_exists('../vendor/autoload.php')) {
    require '../vendor/autoload.php';
}

//Eloquent Model object
$model = new Users;
//Fields to bring from  database
$fields = ["id", "name", "email", "created_at", "active", "admin", "devices_logged"];
//Filters to update with in view-data from
?>
```
[Js_ile](https://github.com/billingPLus/Datatables_backend/blob/master/tests/Assets/index.js)
```php
//Array key is db_fiels and Array value is POST key from js
$filter_fields = ["active" => "active"];

//New object
$datatables = new Datatables($model, $fields, $_POST);
//Filter from Js file
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
if(count($wheres) == 0){
    //Simple data to show
    echo json_encode($datatables->simple());
}else{
    //Complex data with custom filters
    echo json_encode($datatables->complex($wheres));
}

?>
```
## More Examples
 
(EN) For more examples you can see the tests folder to see MVC structure  [Tests](https://github.com/billingPLus/Datatables_backend/blob/master/tests/)<br/>
(ES) Para mas ejemplos pueden ver la estructura MVC en tests    [Tests](https://github.com/billingPLus/Datatables_backend/blob/master/tests/)

## License

(EN) This repo is under licence of open source   [LGPL](https://opensource.org/licenses/gpl-license).<br/>
(ES) Este repositorio está bajo la licencia de código abierto [LGPL](https://opensource.org/licenses/gpl-license).
