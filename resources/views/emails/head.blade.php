<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, user-scalable=no">
        <title><?php echo $tit; ?></title>
        <style>
            .t-content{
                max-width: 98%;
                margin: 1em auto;
                width: 720px;
            }

            img.img-logo {
                width: 250px;
                margin: auto;
            }
            tr.t-head,
            tr.t-footer {
                text-align: center;
                background-color: #e0f5e9;
            }
            .t-content td {padding: 7px;}
            .box-content {padding: 25px 15px; min-height: 220px;}
            .t-body {border-left: 1px double #cecece; border-right: 1px double #cecece;}
           
            h1 {
                font-weight: bold;
                font-size: 22px;
            }
            .table{
                border: 1px solid;
                width: 100%;
            }
            .table th {
                padding: 4px;
                background-color: #e0f5e9;
            }
            .table td {
                padding: 4px; 
            }
            .nowrap{
                white-space: nowrap;
            }
            .tCenter{
                text-align: center;
            }
            .tLeft{
                text-align: left;
            }
            .tRight{
                text-align: right;
            }
            .tVal{
                text-align: right;
                width: 70px;
            }
            <?php $style; ?>
        </style>
    </head>
    <body>
        <table class="t-content">
            <tr class="t-head">
                <td>
                    <img src="{{url('/assets/logo-retina.png')}}" class="img-logo" >
                    <h1><?php echo $tit; ?></h1>
                </td>
            </tr>
            <tr class="t-body">
                <td>
                    <div class="box-content">

