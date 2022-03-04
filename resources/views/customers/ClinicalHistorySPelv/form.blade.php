@extends('layouts.app')


<?php
function Qblock($data, $qID) {
  ?>
  <label>{{$data[$qID]}}</label>
  <input type="text" id="<?= $qID ?>" name="<?= $qID ?>" value="" required="">
  <?php
}
function QTextarea($data, $qID) {
  ?>
  <label>{{$data[$qID]}}</label>
  <textarea id="<?= $qID ?>" name="<?= $qID ?>" value="" required=""></textarea>
  <?php
}

function QDate($data, $qID) {
  ?>
  <label>{{$data[$qID]}}</label>
  <input  size="10" maxlength="11" onKeyUp = "this.value = window.inputDate(this.value);" placeholder="DD-MM-YYYY" id="{{$qID}}" name="{{$qID}}" required="">
  <?php
}

 ?>
@section('content')
<h1>Hª CLÍNICA SUELO PÉLVICO</h1>
@if (isset($already))
<div class="alert alert-success">
  Completada
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
<form  action="/historia-clinica-suelo-pelvico" method="post" style="margin: 1em auto;" id="formClinicHistory"> 
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="_code" value="<?php echo $code; ?>">
  <input type="hidden" name="_control" value="<?php echo $control; ?>">
  <div class="text-left">
    @include('customers.ClinicalHistorySPelv.fields')
  </div>
  <button class="btn btn-success">
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
  .content-box{
    max-width: 840px;
  }
  h1 {
    font-size: 24px;
    background-color: #f7f7f7;
    padding: 15px 0;
  }
  h2 {
    font-weight: bold;
    font-size: 19px;
    margin-top: 2em;
  }
  h3 {
    font-size: 18px;
    font-weight: bold;
    margin-top: 2em;
    border-bottom: 2px solid;
  }
  ul {
    padding-left: 40px;
  }
  li {
    list-style: disc;
    text-align: left;
    margin: auto;
  }

  .radio {
    margin-left: 2em;
  }
  @media screen {
    .paging h1{
      padding-top: 2em;
    }
    .printBreak,.block-logo{
      display: none;
    }
  }
</style>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script type="text/javascript">

    $(document).ready(function () {
      $('.fromEncNutri .field').each(function () {
        var width = $(this).width();
        var label = $(this).find('label').width();
        $(this).find('input').width(width - label - 35);
      });
    
    $('.fromEncNutri .field').each(function () {
      var width = $(this).width();
      if($(this).find('input').attr('type') == 'text')
        $(this).find('input').val('Campo: '+$(this).find('label').text());
    });
    $('.fromEncNutri .fieldTextArea').each(function () {
      var width = $(this).width();
        $(this).find('textarea').val('Campo: '+$(this).find('label').text());
    });
    
 });

</script>
@endsection