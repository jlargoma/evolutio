@extends('backend.base')

@section('content')

<div class="container-fluid">
  <div class="fade-in">
    <div class="box">
      @include('backend.contabilidad.boxs.navs')
      <div class="row pt-3">
        <div class="col-md-8 ">
          <div class="table-responsive">
            <table class="table nowrap text-center">
              <thead>
                <tr>
                  <th class="static"></th>
                  <th class="first-col">
                    SESIONES	
                  </th>
                  <th>SALARIO</th>
                  @foreach($lstMonths as $k_month => $month)
                    <th colspan="2">
                      {{$month}}
                    </th>
                  @endforeach
              </thead>
              <tbody>
                <tr>
                  <td class="static"></td>
                  <td class="first-col"></td>
                  <td ></td>
                  @foreach($lstMonths as $k_month => $month)
                  <td>Sessiones</td>
                  <td>Salario</td>
                  @endforeach
                </tr>
                @foreach($lst_items as $usr => $v)
                <!--Rates Types-->
                <tr class="contabl_type" data-id="{{$usr}}" >
                  
                  <th class="static open_rate_type text-left">
                    <i class="cil-plus"></i>
                    @if(isset($aEntrenadores[$usr]))
                    {{$aEntrenadores[$usr]}}
                    @endif
                  </th>
                  <th class="first-col">
                    @if(isset($totals[$usr][0]))
                    {{$totals[$usr][0]['c']}}
                    @endif
                  </th>
                  <th >
                    @if(isset($totals[$usr][0]))
                    {{moneda($totals[$usr][0]['t'],false)}}
                    @endif
                  </th>
                  @foreach($lstMonths as $k_month => $month)
                    @if(isset($totals[$usr][$k_month]))
                      <td>
                        {{$totals[$usr][$k_month]['c']}}
                        </td>
                        <td>
                        {{moneda($totals[$usr][$k_month]['t'],false)}}
                      </td>
                    @else
                      <td>--</td>
                      <td>--</td>
                    @endif
                  @endforeach
                </tr>
                <!--END: Rates Types-->
                 @foreach($v as $k2 => $v2)
                <tr class="contabl_rate contabl_type_{{$usr}} tr-close">
                  
                  <th class="static text-left">@if(isset($aRateType[$k2]))
                    {{$aRateType[$k2]}}
                    @endif</th>
                  <th class="first-col">
                    {{$v2[0]['c']}}
                  </th>
                  <th class="first-col">
                    {{moneda($v2[0]['t'])}}
                  </th>
                  @foreach($lstMonths as $k3 => $month)
                    <td>{{$v2[$k3]['c']}}</td>
                    <td>{{moneda($v2[$k3]['t'],false)}}</td>
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
                  <th class="static-forse"></th>
                  <th class="first-col-forse"></th>
                  <th> % </th>
                  @foreach($lstMonths as $k => $month)
                    <th>{{$month}}</th>
                  @endforeach
              </thead>
              <tbody>
                @foreach($aRateType as $k => $v)
                <tr>
                  <td class="static-forse">{{$v}}</td>
                  <td class="first-col-forse">{{moneda($lst_byRate[$k][0])}}</td>
                  <td>{{round($lst_byRate[$k][0]/$total*100)}}%</td>
                  @foreach($lstMonths as $k_m => $month)
                  <td>{{moneda($lst_byRate[$k][$k_m])}}</td>
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
      @foreach($temporadas as $k => $v)
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
