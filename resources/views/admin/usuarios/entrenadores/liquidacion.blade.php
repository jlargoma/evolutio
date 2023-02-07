<div class="col-xs-12 row" style="border: 1px solid; margin: 6px auto;padding: 7px;">
    <div class="col-md-4">
        <h3>Liquidación {{$mes}}</h3>
    </div>
    <div class="col-md-4 text-center">
        <b>Total Sesiones PT:</b> {{$totalClaseSimple}}<br />
        <b>Total Sesiones Clases:</b> {{$totalClaseGrupal}}
    </div>
    <div class="col-md-4  text-center">
        <b>Salario Base:</b> {{moneda($salary)}}<br>
        <div style="border-bottom: 2px solid;max-width: 200px; margin: auto;"><b>Comisiones:</b> {{moneda($commision)}}</div>
        <b>Total:</b> {{moneda($salary+$commision)}}
    </div>
</div>

<div class="col-xs-12 table-responsive">
    <table class="table table-borderless table-striped table-vtop">
        <thead>
            <tr>
                <th class="text-center">#</th>
                <th class="text-left">Concepto</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 1;
            $total = $salary;
            ?>
            <tr>
                <td class="text-center"><strong><?php echo $i ?></strong></td>
                <td class="text-left font-s20" style="text-transform: uppercase;">Salario Base</td>
                <td class="text-right font-s20"><strong><?php echo $salary; ?>€</strong></td>
            </tr>
            <?php foreach ($classLst as $key => $pago) : ?>
                <?php $i++ ?>
                <tr>
                    <td class="text-center"><strong><?php echo $i ?></strong></td>
                    <td class="text-left font-s18 turnos">

                        <?php echo $classLst[$key] ?>
                        <?php
                        if (isset($pagosClase[$key])) :
                            foreach ($pagosClase[$key] as $turno) {
                                echo '<br/><small>' . $turno . '</small>';
                            }
                        endif;
                        ?>
                    </td>
                    <td class="text-right font-s20"><strong><?php echo $totalClase[$key] ?>€</strong></td>
                </tr>
                <?php $total += $totalClase[$key]; ?>
            <?php endforeach ?>
            <?php foreach ($totalExtr as $key => $pago) : ?>
                <?php $i++ ?>
                <tr>
                    <td class="text-center"><strong><?php echo $i ?></strong></td>
                    <td class="text-left font-s18 turnos">{{$nExtr[$key]}}</td>
                    <td class="text-right font-s20"><strong><?php echo $pago ?>€</strong></td>
                </tr>
                <?php $total += $pago; ?>
            <?php endforeach ?>
            <tr class="success">
                <td colspan="2" class="text-right text-uppercase"><strong>Total:</strong></td>
                <td class="text-right font-s24"><strong><?php echo $total ?>€</strong></td>
            </tr>
        </tbody>
    </table>
</div>