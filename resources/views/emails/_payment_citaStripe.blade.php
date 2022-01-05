<?php
$tit = 'Su cita en Evolutio';
?>
@include('emails.head')

Hola! <?php echo $user->name ?><br><br>

<p style="color: black">
    Tienes una cita en nuestro centro de <b><?php echo $rate->name ?></b> en <strong> Evolutio</strong>
</p>

<p style="color: black;font-size: 18px;">
    - Nombre: <?php echo $user->name ?><br>
    <?php 
    
    if ($obj->date_type == 'nutri')
      echo '- Nutricionista: '.$oCoach->name.'<br>';    
    if ($obj->date_type == 'fisio')
      echo '- Fisioterapeuta: '.$oCoach->name.'<br>';  
    ?>
    - Servicio: <?php echo $rate->name ?><br>
    - Fecha: <?php echo $day; ?><br>
    - Hora: <?php echo $hour; ?><br>
    - Importe: <?php echo moneda($importe,true,2) ?><br>
</p>
@if($pStripe)
<p>
    Para realizar el pago, haga click en el enlace o copie y pegue el mismo en su navegador de preferencia<br/>
    <a href="{{$pStripe}}" title="link pago Stripe">{{$pStripe}}</a>
</p>
@endif
<h5 style="color: black ;margin-bottom: 5px;">
    Muchas gracias por tu confianza en nosotros!! Tú compromiso es el nuestro
</h5>
@include('emails.footer')