<?php
$count = 1;
?>
<div class="row formValora">
  <div class="col-md-6 ">
    <div class="form-material mt-2">
      <label for="name">NOMBRE</label>
      <div class="field">{{$user->name}}</div>
    </div>
    <div class="form-material mt-2">
      <label for="telefono">EDAD</label>
      <div class="field">{{$valora['valora_years']}}</div>
    </div>
    <div class="form-material mt-2">
      <label for="telefono">FECHA DE VALORACIÓN</label>
      <div class="field">{{convertDateToShow($valora['valora_date'],true)}}</div>
    </div>
  </div>
  <div class="col-md-6 ">
    <div class="form-material mt-2">
      <label>APELLIDOS</label>
      <div class="field">{{$valora['valora_lastname']}}</div>
    </div>
    <div class="form-material mt-2">
      <label for="name">*FIRMA DEL TUTOR (en menores de edad)</label>
      <div class="field">{{$user->dni}}</div>
    </div>
  </div>

  <div class="col-md-12 mt-1 qstion1">
    <h4 class="mt-1">ANTECEDENTES MÉDICOS Y DE ACTIVIDAD </h4>
    <u>Por favor, conteste a estas preguntas con sinceridad y de forma detallada cuando proceda:</u>
    @foreach($valora['qstion1'] as $i=>$q)
    <div class="form-material mt-2">
      <label>{{$count.'. '.$q}}</label>
      <div class="field">{{$valora[$i]}}</div>
      <?php $count++; ?>
    </div>
    @endforeach
  </div>
  </div>
  