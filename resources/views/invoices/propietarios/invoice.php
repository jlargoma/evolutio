<?php   use \Carbon\Carbon;
setlocale(LC_TIME, "ES"); 
setlocale(LC_TIME, "es_ES"); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="{{ asset('/frontend/css/bootstrap.css') }}" media="all" />
    <script type="text/javascript" src="{{ asset('/frontend/js/jquery.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/frontend/js/bootstrap.css') }}"></script>
    <style type="text/css">
    #main{
        padding:12px;
    }
    .offset-margin{
        margin-top:-10px;
    }
    td{
        padding-left:10px;
    }
    .description{
        min-height:120px;
        margin-bottom:90px;
    }
    .blank{
        border-top:solid 1px #eee;
        border-left:solid 1px #eee;
        min-height:120px;
        padding:0px !important;
    }
    .panel-body{
        padding-bottom:0 !important;
    }
    .content{
        margin-top:40px; 
        padding-bottom:20px !important;
    }
    .table{
        margin-bottom: 0px !important;
    }
</style>
</head>
<body>
    <div class="row">
        <div class="col-lg-8 col-md-8 col-sm-8 col-lg-offset-2">
            <div class="panel panel-default main">

                <div class="panel-body">

                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <h1>Factura #SN<?php echo Carbon::CreateFromFormat('Y-m-d',$book->start)->format('y'); ?><?php echo $book->id ?></h1>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12 pull-right" style="padding-top: 40px; padding-bottom: 10px;">
                           Razón social: <b><?php echo $book->user->name_business;?></b><br>
                           NIF/CIF/DNI: <b><?php echo $book->user->nif_business;?></b><br>
                           Dirección: <b><?php echo $book->user->address_business;?></b><br>
                        </div>
                    </div>
                    <!--row-->

                    <hr>
                    <div class="row">
                        <div class="col-lg-9 col-md-9 col-sm-9">
                            <h3><?php echo ucfirst($book->customer->name) ?></h3>
                            <p class="offset-margin">DNI: <?php echo $book->customer->DNI ?></p>
                            <p class="offset-margin">Dirección: <?php echo $book->customer->address ?></p>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-3">
                        
                        </div>
                    </div>
                    <!--row-->
                    <div class="row">
                        <div class="col-lg-10 col-md-10 col-sm-10 col-lg-offset-1 col-md-offset-1 col-sm-offset-1 content">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h1 class="panel-title">Detalles de la factura</h1>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class=" col-lg-12 col-md-12 col-sm-12 description">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Descripción</th>
                                                        <th>Importe</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            Alojamiento en apartamento edificio MiramarSKI (<?php echo substr($book->room->nameRoom , 0,2)?>)<br>
                                                            <b>Fechas: del <?php echo Carbon::CreateFromFormat('Y-m-d',$book->start)->formatLocalized('%d %B %Y'); ?> al <?php echo Carbon::CreateFromFormat('Y-m-d',$book->finish)->formatLocalized('%d %B %Y'); ?></b>
                                                        </td>
                                                        <td>
                                                             <b><?php echo number_format($book->total_price, 2, ',','.') ?>€</b>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>


                                        </div>

                                        <div class="col-lg-8 pull-left" style="border-top:solid #ddd 1px;"></div>

                                        <div class="col-lg-4 col-md-4 col-sm-4 pull-right blank">

                                            <table class="table table bordered">
                                                <tbody>
                                                    <tr style="background-color:#eee;">
                                                        <td><label>TOTAL:</label><span class="pull-right"> <b><?php echo number_format($book->total_price, 2, ',','.') ?>€</b></span></td>
                                                    </tr>
                                                </tbody>

                                            </table>
                                        </div>
                                    </div>
                                    <!--row-->
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</body>
</html>