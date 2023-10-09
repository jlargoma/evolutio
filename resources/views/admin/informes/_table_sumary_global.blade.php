<div class="col-md-12 col-xs-12 push-20">
    <table class="table table-striped table-header-bg">
        <tbody>
        <tr>
            <td class="text-center bg-complete font-w800" rowspan="4">RESUMEN</td>
            <td class="text-center bg-complete font-w800"></td>
            <td class="text-center bg-complete font-w800"></td>
            <td class="text-center bg-complete font-w800">METALICO</br><?php echo moneda($cash+$cTotalBonos['cash']); ?></td>
            <td class="text-center bg-complete font-w800">BANCO</br><?php echo moneda($bank+$cTotalBonos['banco']); ?></td>
            <td class="text-center bg-complete font-w800">TARJ</br><?php echo moneda($card+$cTotalBonos['card']); ?></td>
            <td class="text-center bg-complete font-w800">Invit. Evolutio</br><?php echo moneda($invita+$cTotalBonos['invita']); ?></td>
            <td class="text-center bg-complete font-w800">TOTAL</br><?php echo moneda($cash + $bank + $card+$toPay+$invita+array_sum($cTotalBonos)); ?></td>
        </tr>
        <tr>
            <td class="text-center bg-complete ">SERVICIOS</td>
            <td class="text-center">Nº Clientes <b><?php echo isset($tCustomer) ? $tCustomer : count($aUsers); ?></b></td>
            <td class="text-center"><?php echo moneda($cash); ?> </td>
            <td class="text-center"><?php echo moneda($bank); ?> </td>
            <td class="text-center"><?php echo moneda($card); ?> </td>
            <td class="text-center"><?php echo moneda($invita); ?> </td>
            <td class="text-center"><?php echo moneda($cash + $bank + $card + $invita); ?> </td>
        </tr>
        <tr>
            <td class="text-center bg-complete ">BONOS</td>
            <td class="text-center">Nº Ventas <b><?php echo count($oLstBonos); ?></b></td>
            <td class="text-center"><?= moneda($cTotalBonos['cash']); ?></td>
            <td class="text-center"><?= moneda($cTotalBonos['banco']); ?></td>
            <td class="text-center"><?= moneda($cTotalBonos['card']); ?></td>
            <td class="text-center"><?= moneda($cTotalBonos['invita']); ?></td>
            <td class="text-center"><?= moneda(array_sum($cTotalBonos)); ?></td>
        </tr>
        <tr>
            <td class="text-center bg-complete ">Pendientes</td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center toPay"><?= moneda($toPay); ?></td>
        </tr>
        </tbody>
    </table>
</div>
