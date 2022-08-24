@extends('layouts.app')
@section('content')

<div>
  <div class="row titH1">
    <div class="col-md-6 text-left">PEELING QUÍMICO </div>
    <div class="col-md-6 text-right">GET FIT VILLAVICIOSA</div>
  </div>
  <hr>
  @if (isset($already))
  <div class="alert alert-success">
    Firmada
  </div>
  @else

  @if (session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
  @endif
  @if (session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @else

  
  <form action="/autorizacion-{{$type}}" method="post" style="margin: 1em auto;" id="formAutorizaciones">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="_code" value="<?php echo $code; ?>">
    <input type="hidden" name="_control" value="<?php echo $control; ?>">
    <div class="text-left">

<div>
    <h5>RIESGOS DEL PEELING QUÍMICO</h5>
    <p>Existen riesgos y complicaciones asociados con los peelings químicos.</p>
    <p>La infección es infrecuente, pero pueden ocurrir infecciones bacterianas o víricas. Si  usted tiene antecedentes de infecciones por Herpes Simplex virus alrededor de la boca,  es posible que una infección pueda recurrir después de un peeling químico.  Deben prescribirse  medicaciones específicas antes del peeling con la intención de evitar una infección por este  virus. Si ocurre cualquier tipo de infección en la piel puede ser necesario tratamiento adicional,  incluyendo antibióticos.</p>
    <h5>Cambio de color</h5>
    <p>Los agentes de peeling químico pueden aclarar de forma permanente el  color natural de la piel. Existe la posibilidad de variaciones irregulares de color en la piel, con  áreas   que   pueden   ser   más   claras   o   más   oscuras.   Se   han   descrito   oscurecimientos  permanentes de la piel después de peelings químicos,  la  mayoría de las veces asociadas a  una inadecuada protección solar durante el periodo post-peeling. </p>
    <p>Puede darse una línea de demarcación entre la piel normal y la piel tratada con peeling.  Un enrojecimiento después de un peeling puede persistir por un periodo de tiempo corto.</p>
    <h5>Recurrencia de las lesiones cutáneas</h5>
    <p>Las lesiones de la piel pueden volver a aparecer en  algunas situaciones después de un peeling químico. Puede necesitarse tratamiento adicional. </p>
    <h5>Reacciones alérgicas</h5>
    <p>En casos muy raros se han descrito reacciones a alérgicas a agentes  usados en el peeling químico. </p>
    <p>En casos raros se han visto alergias locales a conservantes  usados en cosméticos o preparados tópicos.  Las reacciones sistémicas,  que  son más serias,  pueden ocurrir por medicaciones utilizadas durante el pro ceñimiento o prescritas después. Las  reacciones alérgicas pueden requerir tratamiento adicional. Cáncer o enfermedades de la piel.</p>
    <p>Los peelings y tratamientos de la piel no ofrecen  protección contra el desarrollo del cáncer de piel u otras enfermedades en un futuro.</p>
    <h5>Quemaduras solares</h5>
    <p>Ciertos tipos de agentes de peeling químico pueden favorecer de forma  permanente las quemaduras cutáneas por sol.  La piel después de un peeling químico puede  perder la habilidad normal para teñirse (oscurecerse)  cuando se expone a la luz solar. Utilice siempre foto protección y evite la exposición solar durante los 7 días posteriores al tratamiento.</p>
      </div>
   
    <div class="my-4 text-center">
    He sido informado y acepto la realización del tratamiento.
    </div>
    <div class="my-4">
      Nombre: <?php hQblock($data, 'Esthetic_peeling_name', 'f1', 'Nombre Del Cliente'); ?><br/>
      D.N.I: <?php hQblock($data, 'Esthetic_peeling_dni','f2'); ?><br/>
    </div>
    <div class="my-4">
      Fecha: <?php hQDate($data, 'Esthetic_peeling_date'); ?><br/>
    </div>
    </div>
    <div class="bShadow">
      <h5>Firma:</h5>
      <input type="hidden" name="sign" id="sign" value="">
      <div class="sing-box">
        <canvas width="320" height="300" id="cSign"></canvas>
      </div>
      <button class="btn btn-danger" type="button" id="clearSign">
        <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
      </button>
    </div>
  <button class="btn btn-success" type="button" id="sendForm">
    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
  </button>
  </form>
</div>

@endif
@endif

@endsection
@section('scripts')

@include('customers.Esthetic.forms_styles')

@endsection