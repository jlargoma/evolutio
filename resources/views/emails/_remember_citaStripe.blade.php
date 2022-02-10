<?php
$tit = 'Su cita en Evolutio';
?>
@include('emails.head')

Hola! <?php echo $user->name ?><br><br>

<p style="color: black">
  Le recordamos su cita en nuestro centro de <b><?php echo $rate->name ?></b> en <strong> Evolutio</strong>
</p>

<p style="color: black;font-size: 18px;">
  - Nombre: <?php echo $user->name ?><br>
  <?php
  if ($obj->date_type == 'nutri')
    echo '- Nutricionista: ' . $oCoach->name . '<br>';
  if ($obj->date_type == 'fisio')
    echo '- Fisioterapeuta: ' . $oCoach->name . '<br>';
  if ($obj->date_type == 'pt')
    echo '- Entrenador: ' . $oCoach->name . '<br>';
  ?>
  - Servicio: <?php echo $rate->name ?><br>
  - Fecha: <?php echo $day; ?><br>
  - Hora: <?php echo $hour; ?><br>
  @if($importe)
  - Importe: <?php echo moneda($importe, true, 2) ?><br>
  @endif
</p>


@if($urlEntr)
<p style="text-align: center;background-color: #e0f5e9;padding: 9px;color: #000;font-size: 16px;font-weight: bold;margin: 32px 0;">
  No olvide completar la siguente encuesta antes de su cita<br/>
  <a href="{{$urlEntr}}" title="Encuesta nutrición">{{$urlEntr}}</a>
</p>
@endif
@if($urlSuelPelv)
<p style="text-align: center;background-color: #e0f5e9;padding: 9px;color: #000;font-size: 16px;font-weight: bold;margin: 32px 0;">
    Antes de si cita debe aceptar y firmar el documento de <b>CONSENTIMIENTO SUELO PELVICO</b><br/>
    <a href="{{$urlSuelPelv}}" title="CONSENTIMIENTO SUELO PELVICO">{{$urlSuelPelv}}</a>
    @if($urlIndiba)
      <br/><br/>También debe aceptar y firmar el documento de <b>CONSENTIMIENTO FISIOTERAPIA CON INDIBA</b> <br/>
      <a href="{{$urlIndiba}}" title="CONSENTIMIENTO FISIOTERAPIA CON INDIBA">{{$urlIndiba}}</a>
    </p>
    @endif
</p>
@else
  @if($urlIndiba)
  <p style="text-align: center;background-color: #e0f5e9;padding: 9px;color: #000;font-size: 16px;font-weight: bold;margin: 32px 0;">
    Antes de si cita debe aceptar y firmar el documento de <b>CONSENTIMIENTO FISIOTERAPIA CON INDIBA</b> <br/>
    <a href="{{$urlIndiba}}" title="CONSENTIMIENTO FISIOTERAPIA CON INDIBA">{{$urlIndiba}}</a>
  </p>
  @endif
@endif
<h5 style="color: black ;margin-bottom: 5px;">
  Muchas gracias por tu confianza en nosotros!! Tú compromiso es el nuestro
</h5>
@include('emails.footer')