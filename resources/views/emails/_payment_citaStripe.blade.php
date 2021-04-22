<?php
$tit = 'Nueva cita en Evolutio';
?>
@include('emails.head')

Hola! <?php echo $user->name ?><br><br>

<p style="color: black">
    Te adjuntamos el enlace para el pago de si cita de <?php echo $rate->name ?> en <strong> Evolutio</strong>
</p>

<p style="color: black;font-size: 18px;">
    - Nombre: <?php echo $user->name ?><br>
    <?php 
    if ($obj->date_type == 'nutri'):
    echo '- Nutricionista:'.$oCoach->name.'<br>';    
    endif; 
    ?>
    - Servicio: <?php echo $rate->name ?><br>
    - Fecha: <?php echo $day; ?><br>
    - Hora: <?php echo $hour; ?><br>
    - Importe: <?php echo moneda($importe,true,2) ?><br>
</p>
<p>
    Para realizar el pago, haga click en el enlace o copie y pegue el mismo en su navegador de preferencia<br/>
    <a href="{{$pStripe}}" title="link pago Stripe">{{$pStripe}}</a>
</p>
<h5 style="color: black ;margin-bottom: 5px;">
    Muchas gracias por ponerte en forma!!  Si tú te comprometes, nosotros más!!
</h5>
@include('emails.footer')