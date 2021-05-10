<?php use \Carbon\Carbon; ?>
<?php setlocale(LC_TIME, "ES"); ?>
<?php setlocale(LC_TIME, "es_ES"); ?>
<div class="col-md-12 col-xs-12 push-20">
    <table class="table table-striped table-header-bg">
        <tbody>
        <tr>
            <td class="text-center bg-complete font-w800" rowspan="2">RESUMEN</td>
            <td class="text-center bg-complete font-w800">Nº Clientes</td>
            <td class="text-center bg-complete font-w800">CAJA</td>
            <td class="text-center bg-complete font-w800">BANCO</td>
            <td class="text-center bg-complete font-w800">TOTAL</td>
        </tr>
        <tr>
            <td class="text-center bg-complete"><?php echo count($aUsers); ?></td>
            <td class="text-center bg-complete"><?php echo $cash; ?> €</td>
            <td class="text-center bg-complete"><?php echo $bank; ?> €</td>
            <td class="text-center bg-complete"><?php echo ($cash + $bank); ?> €</td>
        </tr>
        </tbody>
    </table>
</div>
<div class="col-md-12 col-xs-12">
    <table class="table table-striped table-header-bg">
        <thead>
        <tr>
            <th class="text-center sorting_disabled"></th>
            <th class="text-center">Fecha</th>
            <th class="text-center">Nombre cliente</th>
            <th class="text-center">Cuota / Concepto</th>
            <th class="text-center">Importe</th>
            <th class="text-center">MES</th>
            <th class="text-center">Forma pago</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($extrasCharges as $charge): ?>
        <tr>
            <td class="text-center sorting_disabled"></td>
            <td class="text-center"><b>{{dateMin($charge->date)}}</b></td>
            <td class="text-center">
		        <b><?php  echo (!empty($charge->comment)) ?strtoupper($charge->comment) : strtoupper($charge->concept) ?></b>
            </td>
            <td class="text-center">
                <b><?php  echo strtoupper($charge->concept) ?></b>
            </td>
            <td class="text-center">
                <b><?php  echo $charge->import; ?> €</b>
            </td>
            <td class="text-center">
                <b>METALICO</b>
            </td>
        </tr>
        <?php endforeach ?>
		<?php foreach ($charges as $charge): ?>
        <tr>
            <td class="text-center sorting_disabled"></td>
            <td class="text-center"><b>{{dateMin($charge->date_payment)}}</b></td>
            <td class="text-center">
                <?php
                echo (isset($aUsers[$charge->id_user])) ? $aUsers[$charge->id_user] : ' - ';
                ?>
            </td>
            <td class="text-center">
                <?php
                echo (isset($aRates[$charge->id_rate])) ? $aRates[$charge->id_rate] : ' - ';
                ?>
            </td>
            <td class="text-center">{{moneda($charge->import,false,1)}}</td>
            <td class="text-center">
              <?php 
              if(isset($aURates[$charge->id])){
                $monthAux = $aURates[$charge->id];
                show_isset($monthAux,$months);
              }
              ?>
            </td>
            <td class="text-center">
                <?php 
                if ($charge->type_payment == "banco"): echo 'TARJETA';
		elseif ($charge->type_payment == "cash"): echo 'METALICO';
                endif; 
                ?>
            </td>
        </tr>
		<?php endforeach ?>
        </tbody>
    </table>
</div>