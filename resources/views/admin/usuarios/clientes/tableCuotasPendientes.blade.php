<!--    TABLA                                  -->
<h2 class="font-w600">
  Listado de cuotas pendientes año {{$year}}</b>
</h2>
<div id="extra-hours-table-acc" class="table-responsive">
  <table class="table ticomes">
    <thead>
      <tr>
        <th class="static thBlue">MES</th>
        <!-- <th>ESTADO</th> -->
        <th>DEUDA TOTAL</th>
      </tr>
    </thead>
    <tbody>
      @foreach($data as $index=>$mes)
      <tr id="mes_{{$index}}" class="d1" data-k="{{$index}}">
        <td class="static" style="font-size: 2rem;"><i class="fa fa-eye"></i> {{$mes['name']}}</td>
        <!-- <td><b>Activo</b></td> -->
        <td><b style="color:red;font-size: 2rem;" id="mes_{{$index}}_total" data-total="{{isset($mes['totalDebt']) ? $mes['totalDebt'] : 0}}">{{moneda(isset($mes['totalDebt']) ? $mes['totalDebt'] : 0, true, 2)}}</b></td>
        
      </tr>

        @if(count($mes['users'])>0)
          @foreach($mes['users'] as $index2=>$user)
            <tr id="user_{{$index2}}" class="d2 d1_{{$index}} " data-k="{{$index}}_{{$index2}}">
              <td class="static"><i class="fa fa-eye"></i> {{$user['name']}}</td>
              <!-- <td class="first-col"></td> -->
              <td><b style="color:red;" id="user_{{$index2}}_total" data-total="{{isset($user['totalDebt']) ? $user['totalDebt'] : 0}}">{{moneda(isset($user['totalDebt']) ? $user['totalDebt'] : 0, true, 2)}}</b></td>
              
            </tr>
            @foreach($user['debtDetails'] as $index3 => $item)
              <tr  class="d3 d2_{{$index}}_{{$index2}}">
                <td class="static">
                  <div>{!!$item['details']['s']!!} @if(isset($item['details']['date']) && !empty($item['details']['date'])) ({{$item['details']['date']}}) @endif</div>
                </td>
                <td>
                  <span style="color:red;">{{moneda(isset($item['price']) ? $item['price'] : 0, true, 2)}}</span>
                </td>
              </tr>
            @endforeach
            
          @endforeach
        @endif        
      @endforeach
    </tbody>
  </table>
</div>
<!--    TABLA                                  -->