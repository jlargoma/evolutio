<?php 
function printEvents($lst){
  foreach ($lst as $item){
    if ($item['charged'] == 2){
      echo '<div '
      . 'data-id="'.$item['id'].'" '
      . 'data-name="--" '
      . 'class="eventType_'.$item['coach'].' events blocked">'
          .'<span></span><toltip/>'
      . '</div>';
    } else {
      $payment = ($item['charged'] != 1) ? '<span class="no-pay"></span>' : '';
      echo '<div '
      . 'data-id="'.$item['id'].'" '
      . 'data-name="'. strtolower($item['name']).'" '
      . 'class="eventType_'.$item['coach'].' events">'
          .$payment.str_limit($item['name'],10)
          .'<toltip/>'
      . '</div>';
    }
  }
}
                        
?>
@foreach($calendar as $days)
<?php if (empty($days)) continue ?>
<div class="table-responsive">
    <table class="table table-calendar">
        <thead>
            <tr>
                @foreach($days as $d)
                <th colspan="2">{{$d['day'].' '.$d['date']}}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @for($i=8; $i<22; $i++)
            <tr>
                @foreach($days as $k=>$d)
                <?php $dk= $k+1; //corrige -> no hay domingos ?>
                    @if(isset($times[$dk]) && isset($times[$dk][$i]) && $times[$dk][$i] == 0)
                    <td class="time not">{{$i}}</td>
                    <td class="not editDate">
                      <?php
                      if (isset($aLst[$d['time']]) && isset($aLst[$d['time']][$i])){
                        printEvents($aLst[$d['time']][$i]);
                      }
                      ?>
                    </td>
                    @else
                    <td class="time addDate" data-date="{{$d['time']}}" data-time="{{$i}}">{{$i}}</td>
                    <td class="editDate">
                      <?php
                      if (isset($aLst[$d['time']]) && isset($aLst[$d['time']][$i])){
                        printEvents($aLst[$d['time']][$i]);
                      }
                      ?>
                    </td>
                @endif
                @endforeach
            </tr>
            @endfor
        </tbody>
    </table>
</div>
@endforeach