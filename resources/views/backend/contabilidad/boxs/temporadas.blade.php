<div class="row">
  
  @foreach($temporadas as $k=>$v)
    <div class="col-sm-6 col-md-4 p-1">
      <div class="card text-white bg-gradient-primary p-2">
        <div class="text-value-lg">Temp. {{$k}}</div>
        <div>Cuotas: {{moneda($v['cuota'])}}</div>
        <div>Otros: {{moneda($v['otro'])}}</div>
        <hr>
        <div>Total {{moneda($v['otro']+$v['cuota'])}}</div>
      </div>
    </div>
  @endforeach
</div>