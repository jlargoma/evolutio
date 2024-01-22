<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Liquidación de <?php echo $mes ?> de {{ $user->name }}</title>
    <!-- Latest compiled and minified CSS & JS -->
    <style type="text/css">
        body {
            max-width: 800px;
            margin-top: 25px;
        }

        .table-borderless {
            border: none;
        }

        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }


        .table>thead>tr>th,
        .table>tfoot>tr>th {
            padding: 16px 10px 12px;
            font-family: "Source Sans Pro", "Open Sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
        }



        .table>tbody>tr>td,
        .table>tbody>tr>th,
        .table>tfoot>tr>td,
        .table>tfoot>tr>th,
        .table>thead>tr>td,
        .table>thead>tr>th {
            padding: 8px;
            line-height: 1.42857143;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        th {
            text-align: left;
        }

        .turnos small {
            margin-left: 1em;
            display: block;
            font-size: 12px;
        }

        h1 {
            color: #7b7b7b;
        }

        h1 b {
            color: #000000;
        }

        thead {
            background-color: #cfddef;
        }

        tfoot {
            background-color: #f0f0f0;
        }

        td.small {
            font-size: 11px;
        }
        td.name{
            min-width: 300px;
            width: 300px;
        }
        td.name b{
            font-size: 21px;
        }
        td.total1{
            min-width: 180px;
            width: 180px;
            text-align: center;
        }
        td.total2{
            min-width: 200px;
            width: 200px;
            text-align: right;
        }
        td.total2 .subline{
            width: 150px;
            border: 1px solid #c3c3c3;
            margin-left: 50px;
        }
        </style>
</head>

<body>
    <h1>Liquidación <?php echo $mes; ?></h1>
    <table>
        <tr>
            <td class="name">
                <b><?php echo strtoupper($user->name)?></b><br>{{$user->email}}
            </td>
            <td class="total1">
                <div>Total Sesiones PT: {{$totalClaseSimple}}</div>
                <div>Total Sesiones Clases: {{$totalClaseGrupal}}</div>
            </td>
            <td class="total2">
                <div>Salario Base: {{moneda($salary)}}</div>
                <div>Comisiones: {{moneda($commision)}}</div>
                <div class="subline"></div>
                <div>Total: {{moneda($salary+$commision)}}</div>
            </td>
        </tr>
    </table>
<br>
<br>
<br>
<br>

    @foreach($calendar['days'] as $k=>$days)
        @if(count($days)>0)
        <table class="table table-calendar">
            @php
                $showBody = false;
            @endphp
            <thead>
                <tr>
                    <th>Hora</th>
                    @foreach($days as $d)
                        <th>{{$d['day'].' '.$d['date']}}</th>

                        @php
                            if(isset($claseDay[$d['time']]))
                                $showBody = true;
                        @endphp

                    @endforeach
                </tr>
            </thead>
            @if($showBody)
            <tbody>
                @for($i = 8; $i <= 22; $i++)
                <tr>
                    <td class="small">
                        <?php
                            echo $i;
                        ?>
                    </td>
                    @foreach($days as $d)
                    <td class="small">
                        <?php
                        if (
                            isset($claseDay[$d['time']]) && 
                            isset($claseDay[$d['time']][$d['time'] + $i * 60 * 60])
                        ) 
                            echo $claseDay[$d['time']][$d['time'] + $i * 60 * 60];
                        ?>
                    </td>
                    @endforeach
                </tr>
                @endfor
            </tbody>
            @endif
        </table>
        @endif
    @endforeach
    <div class="text-center">
        <small>Evolutio <?php echo date('Y'); ?></small>
    </div>
</body>

</html>