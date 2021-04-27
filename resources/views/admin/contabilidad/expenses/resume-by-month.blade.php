<div class="row">
  <div class="col-md-7 col-xs-12">
    <h3>Resumen Gastos / Mes</h3>
    <div class=" table-responsive">
      <table class="table table-resumen">
        <thead>
          <tr class="resume-head">
            <th class="static">Concepto</th>
            <th class="static-2">Total</th>
            <th class="first-col-2"></th>
            @foreach($lstMonths as $k => $month)
            <?php if ($k==0) continue; ?>
            <th>{{$month}}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          
          @foreach($listGasto_g as $k=>$item)
          <tr>
            <td class="static">{{$gTypeGroup[$k] ?? $k}}</td>
            <td class="static-2">{{moneda($item[0],false)}}</td>
            <td class="first-col-2"></td>
            @foreach($lstMonths as $k_month=>$month)
            <?php if ($k_month==0) continue; ?>
            <td>{{moneda($item[$k_month],false)}}</td>
            @endforeach
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
  <div class="col-md-5 col-xs-12">
         <canvas id="chart_1" width="150" height="150"></canvas>
    </div>
</div>

