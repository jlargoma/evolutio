<?php
$tit = 'Nueva cita en Evolutio';
?>
@include('emails.head')

Hola! <?php echo $user->name ?><br><br><br>

<p style="color: black">
    Te adjuntamos el comprobante del pago que acabas de realizar en <strong> Evolutio</strong>
</p>

<p style="color: black;font-size: 18px;">
    - Nombre: <?php echo $user->name ?><br><br>
    - Tarifa: <?php echo $rate->name ?><br><br>
    - Fecha : <?php echo ucfirst($date->copy()->formatLocalized('%d %B %Y')) ?><br><br>
    - Importe: <?php echo round($importe) ?> €<br><br>
    - Método: <?php echo ($typePayment == "cash") ? "Metalico" : "Tarjeta"; ?><br><br>

</p>
<h5 style="color: black ;margin-bottom: 5px;">
    Muchas gracias por ponerte en forma!!  Si tú te comprometes, nosotros más!!
</h5>
@include('emails.footer')