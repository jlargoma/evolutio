@extends('layouts.app')


<?php
function Qblock($data, $qID, $type = 'f1')
{
  $val = isset($data[$qID]) ? $data[$qID] : '';
?>
  <span class="inline_<?= $type ?>">
    <input type="text" id="<?= $qID ?>" name="<?= $qID ?>" value="{{$val}}" required="">
  </span>
<?php
}
?>
@section('content')
<h1>Autorizacion Infantil</h1>
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
<form action="/autorizacion" method="post" style="margin: 1em auto;" id="formAutorizaciones">
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="_code" value="<?php echo $code; ?>">
  <input type="hidden" name="_control" value="<?php echo $control; ?>">
  <div class="text-left">
    @include('customers.AutorizacionInfantil.fields')
  </div>
  <br /><br /><br />
  <table class="table-sing">
    <tr>
      <td>
        <h5>PADRE/MADRE o TUTOR</h5>
        <input type="hidden" name="sign" id="sign" value="">
        <div class="sing-box">
          <canvas width="320" height="300" id="cSign"></canvas>
        </div>
        <button class="btn btn-danger" type="button" id="clearSign">
          <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
        </button>
      </td>
      <td>
        <h5>FIRMA GERENTE</h5>
        <input type="hidden" name="sign2" id="sign2" value="">
        <div class="sing-box">
          <canvas width="320" height="300" id="cSign2"></canvas>
        </div>
        <button class="btn btn-danger" type="button" id="clearSign2">
          <i class="fa fa-trash" aria-hidden="true"></i> Limpiar
        </button>
      </td>
    </tr>
    <tr>
      <td>Fdo. <?php Qblock($data, 'autoriz_tutorFdo', 'f2'); ?></td>
      <td>Fdo. <?php Qblock($data, 'autoriz_gerenteFdo', 'f2'); ?></td>
    </tr>
  </table>


  <button class="btn btn-success" type="button" id="sendForm">
    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
  </button>
</form>


@endif
@endif

@endsection
@section('scripts')
<style>
  .container {
    font-size: 14px;
  }

  .content-box {
    max-width: 840px;
  }

  h1 {
    font-size: 24px;
    background-color: #f7f7f7;
    padding: 15px 0;
  }

  .inline_f1 input {
    border: none;
    border-bottom: 1px dashed;
    min-width: 350px;
  }

  .inline_f2 input {
    border: none;
    border-bottom: 1px dashed;
    min-width: 150px;
  }
  .inline_f3 input {
    border: none;
    border-bottom: 1px dashed;
    width: 80px;
    text-align: center;
  }

  table.table-sing {
    width: 100%;
    padding: 1em;
    margin: 2em 0 3em;
  }

  .table-sing td {
    width: 49%;
  }

  .table-sing .sing-box {
    border: 1px solid #c3c3c3;
    padding: 7px;
    width: 85%;
    margin: 1em auto;
  }

  @media screen {
    .paging h1 {
      padding-top: 2em;
    }

    .printBreak,
    .block-logo {
      display: none;
    }
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {

    var canvas = document.querySelector("#cSign");
    var signTutor = new SignaturePad(canvas);
    var canvas = document.querySelector("#cSign2");
    var signGerent = new SignaturePad(canvas);
    $('#clearSign').on('click', function(e) {
      signTutor.clear();
    });
    $('#clearSign2').on('click', function(e) {
      signGerent.clear();
    });
    $('#saveSign').on('click', function(e) {
      e.preventDefault();
      $('#sign').val(signaturePad.toDataURL()); // save image as PNG
      $(this).closest('form').submit();
    });




    $('#formAutorizaciones').on('click', '#sendForm', function(e) {
      e.preventDefault();
      $('#sign').val(signTutor.toDataURL()); // save image as PNG
      $('#sign2').val(signGerent.toDataURL()); // save image as PNG
      $('#formAutorizaciones').submit();
    });




  });
</script>
@endsection