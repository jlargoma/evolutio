@extends('layouts.admin-master')
@section('title') P&G- EVOLUTIO  @endsection
<?php 
function sumMonthValue($m){
  $t=0;
  foreach ($m as $k=>$v){
    if (is_numeric($k)){
      $t += $v;
    }
  }
  return ($t);
}?>
@section('content')
@include('admin.contabilidad._button-contabiliad')
<div class="content">
  <div class="row">
    <div class="col-12 col-md-4">
      <h2>Bonos</h2>
    </div>
    <div class="col-12 col-md-8 table-responsive">
      <table class="table t_resume">
        <tr>
          <td><h4>Total</h4> <?= moneda(array_sum($byFamily));?></td>
          @foreach($byFamily as $k=>$v)
          <td><h4><?php echo isset($aRates[$k]) ? $aRates[$k] : '' ?></h4> <?= moneda($v);?></td>
          @endforeach
        </tr>
      </table>
    </div>
  </div>
  


  


  
  <div class="table-responsive ">
    <div class="date-filter">
      <select class="form-control" id="bonoRate">
        <option value="">Todos los Bonos</option>
        <?php 
        foreach ($rateFilter as $k=>$v):
          $s = ($k == $filter)? 'selected' : '';
          echo '<option value="'.$k.'" '.$s.' class="b">'.$v['n'].'</option>';
          foreach ($v['l'] as $k2=>$v2):
            $s = ($k2 == $filter)? 'selected' : '';
            echo '<option value="'.$k2.'" '.$s.'>&nbsp; - '.$v2.'</option>';
          endforeach;
        endforeach; 
        ?>
      </select>
    </div>
    <table class="table table-striped" id="tableBonos">
      <thead>
        <tr>
          <th>Cliente</th>
          <th>Bonos</th>
          <th>Ingresos<br>{{$totals['i']}}</th>
          <th>Egresos<br>{{$totals['d']}}</th>
          <th>Balance<br>{{$totals['t']}}</th>
          <th>Cobrado<br>{{moneda($totals['p'])}}</th>
          <th>Historial</th>
        </tr>  
      </thead>
      <tbody>
        @foreach($aUB as $uID=>$ub)
        <?php $cname = isset($aUsers[$uID]) ? $aUsers[$uID] : 'Cliente'; ?>
        @foreach($ub as $ubID => $item)
        <tr>
          <td class="tleft">{{$cname}}</td>
          <?php 
          $nbono = '--';
          $rID = $item['type'];
          if (isset($aRates[$rID])) $nbono = $aRates[$rID];
          if (isset($rate_subf[$rID])) $nbono = $rate_subf[$rID];
          ?>
          <td>{{$nbono}}</td>
          <td>{{$item['i']}}</td>
          <td>{{$item['d']}}</td>
          <td>{{$item['t']}}</td>
          <td>{{moneda($item['p'])}}</td>
          <td class="history" data-id="{{$ubID}}" data-b="{{$nbono}}" data-c="{{$cname}}"><i class="fa fa-history"></i></td>
        </tr>
        @endforeach
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="modal fade" id="modalInfo" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="block block-themed block-transparent remove-margin-b">
        <div class="block-header bg-primary-dark">
          <ul class="block-options">
            <li>
              <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
            </li>
          </ul>
        </div>
        <div class="row block-content" >
          <h1 id="cnameLog"></h1>
          <h3>Bonos de <b id="cbonoLog"></b></h3>
          <div id="contentBonoLog"></div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
@include('admin.contabilidad.bonos.script')
<style>
  .t_resume td {
    min-width: 100px;
    border: 1px solid #2c343f;
    text-align: center;
    padding: 14px 10px !important;
  }
  .t_resume h4 {
    white-space: nowrap;
    font-size: 13px;
    text-align: center;
    margin-bottom: 4px;
}
  </style>
@endsection
