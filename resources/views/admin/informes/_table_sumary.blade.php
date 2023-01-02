<div class="col-md-12 col-xs-12 push-20">
    <table class="table table-striped table-header-bg">
        <tbody>
        <tr>
            <td class="text-center bg-complete font-w800" rowspan="2">RESUMEN</td>
            <td class="text-center bg-complete font-w800">Nº Clientes</td>
            <td class="text-center bg-complete font-w800">METALICO</td>
            <td class="text-center bg-complete font-w800">BANCO</td>
            <td class="text-center bg-complete font-w800">TARJ</td>
            <td class="text-center bg-complete font-w800">TOTAL</td>
        </tr>
        <tr>
            <td class="text-center bg-complete"><?php echo count($aUsers); ?></td>
            <td class="text-center bg-complete"><?php echo moneda($cash); ?> </td>
            <td class="text-center bg-complete"><?php echo moneda($bank); ?> </td>
            <td class="text-center bg-complete"><?php echo moneda($card); ?> </td>
            <td class="text-center bg-complete"><?php echo moneda($cash + $bank + $card); ?> </td>
        </tr>
        </tbody>
    </table>
</div>