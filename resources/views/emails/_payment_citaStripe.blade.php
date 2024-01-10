<?php
$tit = 'Su cita en Evolutio';
?>
@include('emails.head')

¡Hola! <?php echo $user->name ?><br><br>

<p style="color: black">
  <?php if($is_senial){
      echo 'Pago de la señal para su ';
    } else {
      echo ($remember) ? 'Le recordamos su' : 'Tienes una';
    }?> cita en nuestro centro de <b><?php echo $rate->name ?></b> en <strong> Evolutio <?php if($obj->date_type == 'fisioG') echo ' / Getafe'; ?></strong>
</p>

<p style="color: black;font-size: 18px;">
    - Nombre: <?php echo $user->name ?><br>
    <?php 
    
    if ($obj->date_type == 'nutri')
      echo '- Nutricionista: '.$oCoach->name.'<br>';    
    if ($obj->date_type == 'fisio' || $obj->date_type == 'fisioG')
      echo '- Fisioterapeuta: '.$oCoach->name.'<br>';  
    ?>
    - Servicio: <?php echo implode(', ',$rateLst) ?><br>
    - Fecha: <?php echo $day; ?><br>
    - Hora: <?php echo $hour; ?><br>
    - Importe: <?php echo moneda($importe,true,2) ?><br>
</p>
@if($pStripe)
<p>
    @if($is_senial)
    Para realizar el pago de la señal, haz click en este enlace o copia y pega en tu navegador habitual.<br/>
    @else
    Para realizar el pago, haz click en este enlace o copia y pega en tu navegador habitual.<br/>
    @endif
    
    <a href="{{$pStripe}}" title="link pago Stripe">{{$pStripe}}</a>
</p>
@endif
@if($urlEntr)
<p style="text-align: center;background-color: #e0f5e9;padding: 9px;color: #000;font-size: 16px;font-weight: bold;margin: 32px 0;">
    Por favor, completa la siguiente encuesta antes de tu cita:<br/>
    <a href="{{$urlEntr}}" title="Encuesta nutrición">{{$urlEntr}}</a>
</p>
@endif
@if($urlSuelPelv)
<p style="text-align: center;background-color: #e0f5e9;padding: 9px;color: #000;font-size: 16px;font-weight: bold;margin: 32px 0;">
Antes de tu cita debes aceptar y firmar el documento de <b>CONSENTIMIENTO SUELO PELVICO</b><br/>
    <a href="{{$urlSuelPelv}}" title="CONSENTIMIENTO SUELO PELVICO">{{$urlSuelPelv}}</a>
    @if($urlIndiba)
      <br/><br/>También debes aceptar y firmar el documento de <b>CONSENTIMIENTO FISIOTERAPIA CON INDIBA</b> <br/>
      <a href="{{$urlIndiba}}" title="CONSENTIMIENTO FISIOTERAPIA CON INDIBA">{{$urlIndiba}}</a>
    </p>
    @endif
</p>
@else
  @if($urlIndiba)
  <p style="text-align: center;background-color: #e0f5e9;padding: 9px;color: #000;font-size: 16px;font-weight: bold;margin: 32px 0;">
    Antes de tu cita debes aceptar y firmar el documento de <b>CONSENTIMIENTO FISIOTERAPIA CON INDIBA</b> <br/>
    <a href="{{$urlIndiba}}" title="CONSENTIMIENTO FISIOTERAPIA CON INDIBA">{{$urlIndiba}}</a>
  </p>
  @endif
@endif
<p style="font-weight: bold; color: red; font-size: 12px;">
  Las citas podrán ser modificadas o canceladas con una antelación mínima de 24 horas. En caso de cancelar pasado ese plazo, deberá abonarse o ser descontada de bono (excepto en casos de urgencia justificada). Por favor, ten en cuenta que la persona de nuestro equipo ha reservado esa hora para atenderte.
</p>
<h5 style="color: black ;margin-bottom: 5px;">
  Muchas gracias por tu confianza. Tu compromiso es el nuestro.
</h5>
@include('emails.footer')