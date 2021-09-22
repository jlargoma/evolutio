<?php
$tit = 'Nueva Compra en Evolutio';
?>
@include('emails.head')

Hola! <?php echo $user->name ?><br><br><br>

<p style="color: black">
    Te adjuntamos el comprobante de la compra que acabas de realizar en <strong> Evolutio</strong>
</p>

<p style="color: black;font-size: 18px;">
    - Nombre: <?php echo $user->name ?><br>
    - Fecha : <?php echo date('d/m/Y') ?><br>
    - Importe: <?php echo moneda($bono->price) ?><br>
    - Método: <?php echo ($typePayment == "cash") ? "Metalico" : "Tarjeta"; ?><br>
    - <?php echo $bono->name ?>: <b>{{$bono->quantity}}</b> Bonos de <b>{{moneda($bono->value)}}</b> C/U<br>
</p>
<h5 style="color: black ;margin-bottom: 5px;">
    Muchas gracias por ponerte en forma!!  Si tú te comprometes, nosotros más!!
</h5>
@include('emails.footer')