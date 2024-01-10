<?php
$tit = 'Su cita en Evolutio';
?>
@include('emails.head')

¡Hola! <?php echo $user->name ?><br><br>

<p style="color: black">
  Su plazo para el pago de la señal de su cita de <b><?php echo $rate->name ?></b> ha inspirado, si necesita solicitar hora comuniquese con nosotros. 
</p>

<p style="color: black;font-size: 18px;">
    - Nombre: <?php echo $user->name ?><br>
    <?php 
    if ($date_type == 'nutri')
      echo '- Nutricionista: '.$oCoach->name.'<br>';    
    if ($date_type == 'fisio' || $date_type == 'fisioG')
      echo '- Fisioterapeuta: '.$oCoach->name.'<br>';  
    ?>
    - Servicio: <?php echo implode(', ',$rateLst) ?><br>
    - Fecha: <?php echo $day; ?><br>
    - Hora: <?php echo $hour; ?><br>
</p>

<h5 style="color: black ;margin-bottom: 5px;">
  Muchas gracias por tu confianza. Tu compromiso es el nuestro.
</h5>
@include('emails.footer')