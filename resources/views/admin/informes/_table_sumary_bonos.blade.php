<div class="col-md-12 col-xs-12 push-20">
    <table class="table table-striped table-header-bg">
        <tbody>
        <tr>
            <td class="text-center bg-complete font-w800" rowspan="2">RESUMEN</td>
            <td class="text-center bg-complete font-w800">Nº Ventas</td>
            <td class="text-center bg-complete font-w800">METALICO</td>
            <td class="text-center bg-complete font-w800">BANCO</td>
            <td class="text-center bg-complete font-w800">TARJ</td>
            <td class="text-center bg-complete font-w800">Invit. Evolutio</td>
            <td class="text-center bg-complete font-w800">TOTAL</td>
        </tr>
        <tr>
            <td class="text-center bg-complete"><?php echo count($oLstBonos); ?></td>
            <td class="text-center bg-complete"><?= moneda($cTotalBonos['cash']); ?></td>
            <td class="text-center bg-complete"><?= moneda($cTotalBonos['banco']); ?></td>
            <td class="text-center bg-complete"><?= moneda($cTotalBonos['card']); ?></td>
            <td class="text-center bg-complete"><?= moneda($cTotalBonos['invita']); ?></td>
            <td class="text-center bg-complete"><?= moneda(array_sum($cTotalBonos)); ?></td>
        </tr>
        </tbody>
    </table>
</div>
