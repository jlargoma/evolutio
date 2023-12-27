<?php
global $tResultado;
$perc_mensual = $oRepartoMensual->perc_mensual;
$perc_acumulado = $oRepartoMensual->perc_acumulado;
$benef = $perc_mensual + $perc_acumulado;
$oRepartoMensual = $oRepartoMensual->toArray();
foreach ($lstMonths as $k => $v)
    $tResultado[$k] = floor($tResultado[$k] / 100 * $benef);

$totalReparto = 0;
foreach ($oRepartoMensual as $k => $v){
    if (str_contains($k,'month_'))
    $totalReparto += $v;
}
//dd($oRepartoMensual);

?>
<div class="table-responsive nowrap benef_dpto">
    <table class="table">
        <thead>
            <tr>
                <th class="static thBlue">% Beneficio Departamento <span>{{$benef}}%</span></th>
                <th class="first-col"></th>
                <th >Total <br />({{ moneda(array_sum($tResultado))}})</th>
                @foreach($lstMonths as $k=>$v)
                <th>{{$v}} <br />({{ moneda($tResultado[$k])}})</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="static">Reparto Mensual <input type="text" value="<?= $perc_mensual ?>" class="upd_percent" data-k="perc_mensual">%</td>
                <td class="first-col"></td>
                <td><b class="tReparto">{{ mformat($totalReparto) }}</b><b>€</b></td>
                @foreach($lstMonths as $k=>$v)
                <td><input type="text" value="<?= $oRepartoMensual['month_' . $k] ?>" data-k="<?= $k ?>" data-resultado="{{$tResultado[$k]}}" class="upd_reparto">€</td>
                @endforeach
            </tr>
            <tr>
                <td class="static">Acumulado Diciembre <input type="text" value="<?= $perc_acumulado ?>" class="upd_percent" data-k="perc_acumulado">%</td>
                <td class="first-col"></td>
                <td><b  class="tAcumulado ">{{ mformat(array_sum($tResultado) - ($totalReparto)) }}</b><b>€</b></td>
                @foreach($lstMonths as $k=>$v)
                <?php $value = floor($tResultado[$k]-$oRepartoMensual['month_'.$k]); ?>
                <td><span class="acumulado m{{$k}}" data-v="{{$value}}">{{mformat($value)}}</span>€</td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <div class="msg_ajax"><p class="alert "></p></div>
</div>

<style>
    .benef_dpto input {
        width: 5em;
        padding: 4px 0px 0 4px;
        border: none;
        background-color: #efeeee;
    }

    .benef_dpto .upd_percent {
        width: 2em;
    }
    .benef_dpto th {
        background-color: yellow;
    }
</style>
@section('scripts')

<script type="text/javascript">
    $(document).ready(function() {


        const formatoMoneda = (number) => {
            const exp = /(\d)(?=(\d{3})+(?!\d))/g;
            const rep = '$1.';
            return number.toString().replace(exp,rep);
        }



        $('.upd_reparto').on('change', function() {
            var resultado = $(this).data('resultado');
            var month = $(this).data('k');
            var value = $(this).val();
            
            $.ajax({
                url: '/admin/dpto/save_reparto',
                type: 'POST',
                data: {
                    month: month,
                    value: value,
                    dpto: "{{$dptoName}}",
                    '_token': "{{csrf_token()}}"
                },
                success: function(response) {
                    if (response === 'ok') {
                        $('.acumulado.m'+month).text(formatoMoneda(resultado-value));
                        $('.acumulado.m'+month).data('v',(resultado-value));

                        var tReparto = 0;
                        $('.upd_reparto').each(function(){
                            var aux = parseInt($(this).val());
                            if(aux) tReparto += aux;
                            console.log(formatoMoneda(aux));
                        });
                        console.log(formatoMoneda(tReparto));

                        $('.tReparto').text(formatoMoneda(tReparto));

                        var tAcumulado = 0;
                        $('.acumulado').each(function(){
                            var aux = parseInt($(this).data('v'));
                            if(aux) tAcumulado += aux;
                        });
                        $('.tAcumulado').text(formatoMoneda(tAcumulado));
                        
                        $('.msg_ajax p').text('Dato guardado');
                        $('.msg_ajax p').addClass('alert-success').removeClass('alert-danger');

                    } else {
                        $('.msg_ajax p').text('El campo no ha sido guardado.');
                        $('.msg_ajax p').addClass('alert-danger').removeClass('alert-success');
                    }
                },
                error: function(response) {
                    $('.msg_ajax p').text('No se ha podido obtener los detalles de la consulta.');
                    $('.msg_ajax p').addClass('alert-danger').removeClass('alert-success');
                }
            });

        });



        

        $('.upd_percent').on('change', function() {
            var field = $(this).data('k');
            var value = $(this).val();
            
            $.ajax({
                url: '/admin/dpto/save_percents',
                type: 'POST',
                data: {
                    field: field,
                    value: value,
                    dpto: "{{$dptoName}}",
                    '_token': "{{csrf_token()}}"
                },
                success: function(response) {
                    if (response === 'ok') {
                        location.reload();
                    } else {
                        $('.msg_ajax p').text('El campo no ha sido guardado.');
                        $('.msg_ajax p').addClass('alert-danger').removeClass('alert-success');
                    }
                },
                error: function(response) {
                    $('.msg_ajax p').text('No se ha podido obtener los detalles de la consulta.');
                    $('.msg_ajax p').addClass('alert-danger').removeClass('alert-success');
                }
            });

        });










    });
</script>

@endsection