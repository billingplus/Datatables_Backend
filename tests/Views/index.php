<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Test</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
    <script type="text/javascript" src="Assets/index.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <div class="btn-group">
                    <div class="dropdown">
                        <select class="btn btn-sm btn-default" id="filter">
                            <option value="" selected="" data-icon="icono-caretDown">Filtro</option>
                            <option value="1">Activo</option>
                            <option value="2">Desvinculado</option>
                            <option value="3">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-md-5 text-right">
                <h1 class="h3 d-none d-md-inline-block">
                </h1>
                <br class="d-md-none">
            </div>
        </div>
        <div class="table-responsive">
            <table id="datatable" class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center">column0</th>
                        <th class="text-center">column1</th>
                        <th class="text-center">column2</th>
                        <th class="text-center">column3</th>
                    </tr>
                </thead>
            </table>
        </div>

    </div>
</body>