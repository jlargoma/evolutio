<?php 
function printEvents($lst){
  foreach ($lst as $item){
    switch ($item['charged']){
      case 2:
        echo '<div '
          . 'data-id="'.$item['id'].'" '
          . 'data-name="--" '
          . 'class="eventType_'.$item['coach'].' events blocked">'
              .'<span></span><toltip/>'
          . '</div>';
        break;
      case 3:
        echo '<div '
          . 'data-id="'.$item['id'].'" '
          . 'data-name="--" '
          . 'class="eventType_'.$item['coach'].' events group">'
              .'<span>GRUPO</span><toltip/>'
          . '</div>';
        break;
      default :
        $payment = ($item['charged'] != 1) ? '<span class="no-pay"></span>' : '';
        $halfTime = ($item['halfTime']) ? '½ ' : '';
        echo '<div '
        . 'data-id="'.$item['id'].'" '
        . 'data-name="'. strtolower($item['name']).'" '
        . 'class="eventType_'.$item['coach'].' events">'
            .$payment.$halfTime.str_limit($item['name'],10)
            .'<toltip/>'
        . '</div>';
       break;
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
            @for($i=8; $i<23; $i++)
            <tr>
                @foreach($days as $k=>$d)
                <?php 
                $dk= $k+1; //corrige -> no hay domingos 
                $avail = '----';
                if (isset($avails[$d['time']]) && isset($avails[$d['time']][$i]) && count($avails[$d['time']][$i])){
                  $avail = '';
                  foreach ($avails[$d['time']][$i] as $cID)
                    $avail .='<span class="dateAvail coach_'.$cID.'"></span>';
                }
                  
                ?>
                    @if(isset($times[$dk]) && isset($times[$dk][$i]) && $times[$dk][$i] == 0)
                    <td class="time not">{{$i}}</td>
                    <td class="not editDate">
                      <div class="lst_events">
                      <?php
                      if (isset($aLst[$d['time']]) && isset($aLst[$d['time']][$i])){
                        printEvents($aLst[$d['time']][$i]);
                      }
                      ?>
                      </div>
                      <div style="display:none" class="availDate"><?= $avail?></div>
                    </td>
                    @else
                    <td class="time addDate" data-date="{{$d['time']}}" data-time="{{$i}}">{{$i}}</td>
                    <td class="editDate">
                      <div class="lst_events">
                      <?php
                      if (isset($aLst[$d['time']]) && isset($aLst[$d['time']][$i])){
                        printEvents($aLst[$d['time']][$i]);
                      }
                      ?>
                      </div>
                      <div style="display:none" class="availDate"><?= $avail?></div>
                    </td>
                @endif
                @endforeach
            </tr>
            @endfor
        </tbody>
    </table>
</div>
@endforeach