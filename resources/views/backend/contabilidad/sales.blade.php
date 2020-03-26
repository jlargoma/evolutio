@extends('backend.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="box">
      @include('backend.contabilidad.boxs.navs')
      <div class="row">
        <div class="col-sm-12 col-md-4 pt-3">
          <canvas id="chart_totals_months"></canvas>
        </div>
        <div class="col-sm-12 col-md-4 pt-3">
          <canvas id="chart_clients_months"></canvas>
        </div>
        <div class="col-sm-12 col-md-4 pt-3">
          <div class="row text-center">
            <div class="col-sm-6 col-md-6">
              <div class="card text-white bg-gradient-primary p-2">
                <div>Ingresos</div>
                <div class="text-value-lg">{{ moneda($total_byMonth[0])}}</div>
              </div>
            </div>
            <div class="col-sm-6 col-md-6">
              <div class="card text-white bg-gradient-primary p-2">
                <div>Clientes</div>
                <div class="text-value-lg">{{$totalClientes}}</div>
              </div>
            </div>
            <div class="col-sm-6 col-md-6">
              <div class="card text-white bg-gradient-primary p-2">
                <div>Media Ingresos/Mes</div>
                <div class="text-value-lg">{{ moneda($total_byMonth[0]/12)}}</div>
              </div>
            </div>
             <div class="col-sm-6 col-md-6">
              <div class="card text-white bg-gradient-primary p-2">
                <div >Media Ingresos/Clientes</div>
                <div class="text-value-lg">{{ moneda($total_byMonth[0]/$totalClientes)}}</div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-8 ">
          <div class="table-responsive">
            <table class="table nowrap">
              <thead>
                <tr>
                  <th class="static"></th>
                  <th class="first-col">
                    total<br/>{{ moneda($total_byMonth[0])}}
                  </th>
                  <th> % </th>
                  @foreach($lstMonths as $k => $month)
                    <th>
                      {{$month}}<br/>
                      <?php
                      if (isset($total_byMonth[$k]) && $total_byMonth[$k]>1){
                        echo moneda($total_byMonth[$k],false);
                      } else {
                        echo '--';
                      }
                      ?>
                    </th>
                  @endforeach
              </thead>
              <tbody>
                @foreach($lstSales as $k => $v)
                <!--Rates Types-->
                <tr class="contabl_type" data-id="{{$k}}" >
                  
                  <th class="static open_rate_type">
                    <i class="cil-plus"></i>
                    @if(isset($aRateType[$k]))
                    {{$aRateType[$k]}}
                    @endif
                  </th>
                  <th class="first-col">
                    @if(isset($v['totals'][0]))
                    {{moneda($v['totals'][0],false)}}
                    @endif
                  </td>
                  <th> {{round($v['totals'][0]/$total_byMonth[0]*100)}}% </th>
                  @foreach($lstMonths as $k_month => $month)
                    @if(isset($v['totals'][$k_month]))
                      <td>{{moneda($v['totals'][$k_month],false)}}</td>
                    @else
                      <td>--</td>
                    @endif
                  @endforeach
                </tr>
                <!--END: Rates Types-->
                <!--BEGIN: Rates-->
                 @foreach($v as $k2 => $v2)
                 
                 <?php if($k2 == 'totals'){ continue;} ?>
                 
                <tr class="contabl_rate contabl_type_{{$k}} tr-close">
                  
                  <th class="static">@if(isset($aRates[$k2]))
                    {{$aRates[$k2]}}
                    @endif</th>
                  <th class="first-col">
                    @if(isset($v2[0]))
                    {{moneda($v2[0],false)}}
                    @endif
                  </td>
                  <td></td>
                  @foreach($lstMonths as $k_mont => $month)
                    @if(isset($v2[$k_mont]))
                      <td>{{moneda($v2[$k_mont],false)}}</td>
                    @else
                      <td>--</td>
                    @endif
                  @endforeach
                </tr>
                @endforeach
                <!--END: Rates-->
                
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-4">
          <canvas id="chart_totals_temp"></canvas>
          
          
          <div class="table-responsive">
            <table class="table nowrap">
              <thead>
                <tr>
                  <th class="static"></th>
                  <th class="first-col"></th>
                  <th> % </th>
                  @foreach($lstMonths as $k => $month)
                    <th>
                      {{$month}}<br/>
                      <?php
                      if (isset($total_byMonth[$k]) && $total_byMonth[$k]>1){
                        echo moneda($total_byMonth[$k],false);
                      } else {
                        echo '--';
                      }
                      ?>
                    </th>
                  @endforeach
              </thead>
              <tbody>
              @foreach($salesBy_TypePay as $k => $v)
                <!--Rates Types-->
                <tr >
                  <td class="static">{{$k}}</td>
                  <td class="first-col">{{moneda($v['total'])}}</td>
                  <td >{{$v['percent']}}%</td>
                  @foreach($lstMonths as $k_m => $month)
                  <td>
                    <?php
                      if (isset($v[$k_m])){
                        echo moneda($v[$k_m],false);
                      } else {
                        echo '--';
                      }
                      ?>
                  </td>
                  @endforeach
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
    </div>
  </div>
</div>

@endsection

@section('javascript')
<link href="{{ asset('css/coreui-chartjs.css') }}" rel="stylesheet">
<script src="{{ asset('js/Chart.min.js') }}"></script>
<script type="text/javascript">
$(document).ready(function () {

function addMiles(nStr)
{
  var length = nStr.length;
  var aux = '';
  for(var i = length; i>=0;i--){
    aux += nStr[i];
    if ((i%3) == 0) aux += '.'
  }
 
    return aux;
}

 $('.contabl_type').on('click',function(){
        var id = $(this).data('id');
        console.log(id);
        if($(this).hasClass('open')){
          $(this).removeClass('open');
          $('.contabl_type_'+id).addClass('tr-close');
          
        } else {
          $(this).addClass('open');
          $('.contabl_type_'+id).removeClass('tr-close');
        }
      });
        
const chart_clients_months = new Chart(document.getElementById('chart_clients_months'), {
  type: 'line',
  data: {
    labels : [
      @foreach($lstMonths as $k_month => $month) '{{$month}}',  @endforeach
    ],
    datasets : [
      
       {
        label: "Clientes por mes",
        data : [
        @foreach($lstMonths as $k_month => $month) {{round($clients_byMonth[$k_month])}},  @endforeach
        ],
        borderColor : 'rgba({{$aColors[1]}}, 0.8)',
        highlightFill : 'rgba({{$aColors[1]}}, 0.75)',
        highlightStroke : 'rgba({{$aColors[1]}}, 1)',
      },
    ]
  },
  options: {
    responsive: true,
  }
})

const chart_totals_months = new Chart(document.getElementById('chart_totals_months'), {
  type: 'line',
  data: {
    labels : [
      @foreach($lstMonths as $k_month => $month) '{{$month}}',  @endforeach
    ],
    datasets : [
      
       {
        label: "Ingresos por mes",
        data : [
        @foreach($lstMonths as $k_month => $month) {{round($total_byMonth[$k_month])}},  @endforeach
        ],
        borderColor : 'rgba({{$aColors[1]}}, 0.8)',
        highlightFill : 'rgba({{$aColors[1]}}, 0.75)',
        highlightStroke : 'rgba({{$aColors[1]}}, 1)',
      },
    ]
  },
  options: {
    responsive: true,
   tooltips: {
      intersect: true,
      callbacks: {
        label: function(tooltipItem, chart) {
          console.log(tooltipItem.yLabel,addMiles(tooltipItem.yLabel));
          return addMiles(tooltipItem.yLabel);
        }
      }
    }
  }
})

const lineChart = new Chart(document.getElementById('chart_totals_temp'), {
  type: 'line',
  data: {
    labels : [
      @foreach($lstMonths as $k_month => $month)
      '{{$month}}',
      @endforeach
    ],
    
    datasets : [
      <?php $i = 1; ?>
      @foreach($temporadas_month as $k => $v)
      {
        label: "{{$k}}",
        data : [
          @foreach($lstMonths as $k_month => $month)
            @if(isset($v[$k_month])) {{round($v[$k_month])}},
              @else 0,
            @endif
          @endforeach
        ],
        borderColor : 'rgba({{$aColors[$i]}}, 0.8)',
        highlightFill : 'rgba({{$aColors[$i]}}, 0.75)',
        highlightStroke : 'rgba({{$aColors[$i]}}, 1)',
      },
      <?php $i++; ?>
      @endforeach
    ]
  },
  options: {
    responsive: true
  }
})



        })
</script>

<style>
  .open_rate_type{
    cursor: pointer;
  }
  .tr-close{
    display: none;
  }
  .table-resume-1{
    max-width: 250px;
    margin: 0 auto;
  }
  .contabl_type,
  .contabl_type th.static{
    background-color: #4ba3eb;
    color: #FFF;
  }
</style>
@endsection
