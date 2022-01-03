@extends('layouts.app')

@section('content')
<?php if ($error): ?>
  <p class="alert alert-danger"><?php echo "$error"; ?> </p>
  <?php
else:
  ?>
  <h1>CONTRATO CON EL CENTRO - {{$tit}}</h1>
  <?php
  if ($sign):
    ?>
    <div class="text-center mY-1em">
      <a href="{{$url}}" class="btn btn-success">Descargar</a>
      <br/>
      <br/>
      <br/>
    </div>
    <?php
  else:
    ?>

    <div class="text-left" style="font-size: 13px">
      <?php echo $text; ?>
    </div>
    <h5 class="formTit">DNI Y FIRMA DEL USUARO (o tutor legal en caso de menores de edad)</h5>
    <form  action="{{ $url }}" method="post" style="width: 325px; margin: 1em auto;"> 
      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="sign"  id="sign" value="">

      <h5>Firma</h5>
      <div class="sing-box">
        <canvas width="320" height="300" id="cSign"></canvas>
      </div>
      <input type="text" name="dni" id="dni" class="form-control" placeholder="DNI">
      <p class="alert alert-danger" id="errDNI" style="display:none;">Ingrese su email  (o tutor legal en caso de menores de edad) para continuar </p>
      <br/>
      <button class="btn btn-success" type="button" id="saveSign">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
      </button>
      <button class="btn btn-danger" type="button" id="clearSign">
        <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
      </button>
    </form>
  <?php
  endif;
endif;
?>
@endsection
@section('scripts')
<style>
  .content-box{
    max-width: 840px;
  }
  h1 {
    font-size: 24px;
    background-color: #f7f7f7;
    padding: 15px 0;
    margin: -39px 0 10px 0px;
  }
  h2 {
    font-weight: bold;
    font-size: 19px;
    margin-top: 2em;
  }
  h3 {
    margin-bottom: 14px;
    font-size: 14px;
    text-align: center;
    font-weight: bold;
  }
  ul {
    padding-left: 15px;
  }
  ol{
    padding-left: 20px;
  }
  ul li {
    list-style: disc;
    text-align: left;
    margin: auto;
  }

  li {
    text-align: left;
    margin: auto;
  }

  h5.formTit{
    background-color: #4aa771;
    padding: 8px;
    color: #FFF;
    border-radius: 4px;
  }

  .sing-box {
    border: 1px solid;
    width: 325px;
    padding: 5px;
    margin: 1em auto;
  }
  .alert-danger {
    font-size: 11px;
    padding: 3px;
  }
  div.saltopagina{
      display: none;
   }
</style>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    var canvas = document.querySelector("canvas");
    var signaturePad = new SignaturePad(canvas);
    $('#clearSign').on('click', function (e) {
        signaturePad.clear();
    });
    $('#saveSign').on('click', function (e) {
        e.preventDefault();
        if ($('#dni').val().length < 6) {
            $('#errDNI').show();
            return null;
        }
        $('#sign').val(signaturePad.toDataURL()); // save image as PNG
        $(this).closest('form').submit();
    });

    $('#dni').on('keypress', function () {
        $('#errDNI').hide();
    });
});

</script>
@endsection