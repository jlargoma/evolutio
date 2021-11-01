@extends('layouts.admin-master')

@section('title') Citas Fisioterapia Evolutio HTS @endsection
@section('headerTitle') Citas Fisioterapia @endsection
@section('headerButtoms')
<button type="button" class="btn btn-success addDate" data-date="{{time()}}" data-time="8">
    <i class="fa fa-plus-circle"></i></button>
    <a href="/admin/citas-fisioterapia/listado/" class="btn btn-success" style="float: right; margin-left: 3px;">Listado</a>
@endsection
@section('content')
<div class="content content-full bg-white">
	<div class="row">
            <div class="col-md-12">
                <input type="hidden" id="coachsFilter" value="{{$coach}}">
                <input type="hidden" id="selectMonth" value="{{$month}}">
                <div class="row">
                    <div class="col-md-10">
                      <div class="mbl-tabs">
                    <ul class="coachsFilter">
                       <li data-val="0" class="select_0 <?php echo ($coach == 0) ? 'active' : ''?>">
                           TODOS
                        </li>
                    @foreach($coachs as $item)
                    <li data-val="{{$item->id}}" class="select_<?php echo $item->id ?> <?php echo ($coach == $item->id) ? 'active' : ''?>">
                        {{$item->name}}
                    </li>
                    @endforeach
                    </ul>
                    </div>
                    </div>
                    <div class="col-md-2 mx-1em">
                        <button class="btn btn-horarios" data-toggle="modal" data-target="#modalIfrm">Horarios</button>
                        <button class="btn btn-bloqueo" data-toggle="modal" data-target="#modalIfrm">Bloqueos</button>
                    </div>
               </div>
                <div class="row">  
                <div class="col-md-2 col-xs-12  mx-1em">
                  <input type="search" id="search_cust" class="form-control" placeholder="Buscar clientes">
                </div>
                <div class="col-md-8 col-xs-12">
                  <div class="mbl-tabs">
                  <button class="btn btn-success btnAvails" type="button">DISPONIBLES</button>
                <ul class="selectDate">
                @foreach($aMonths as $k=>$v)
                <li data-val="{{$k}}" class="<?php echo ($month == $k) ? 'active' : ''?>">
                    {{$v}}
                </li>
                @endforeach
                </ul>
                </div>
                </div>
                <div class="col-md-2 col-xs-12 mx-1em">
                    <select id="servSelect" class="form-control">
                        <option value="0">Servicio</option>
                        <?php
                        if ($servLst){
                            foreach ($servLst as $k=>$v){
                                $selected = ($serv == $k) ? 'selected' : '';
                                echo '<option value="'.$k.'" '.$selected.'>'.$v.'</optiono>';
                            }
                        }
                        ?>
                    </select>
                    
                </div>
                </div>
                
                @include('calendars.calendar')
            </div>
	</div>
</div>
    @include('fisioterapia.modals')
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ asset('css/calendars.css?v1') }}">

<style>

    @foreach($tColors as $k=>$v)
    ul.coachsFilter li.select_{{$k}} {
            background-color: {{$v}};
            color: #FFF;
        }
    .eventType_{{$k}} {background-color: {{$v}};}
    .coach_{{$k}} {background-color: {{$v}};}
    @endforeach
    .time.not{
        background-color: #ddd;
        border-color: #c1c1c1 !important;
    }
</style>

<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js')}}"></script>
<script type="text/javascript">
    var dateForm = null;
    var timeForm = null;
$('.addDate').click(function(event){
    event.preventDefault();
    dateForm = $(this).data('date');
    timeForm = $(this).data('time');
    $('#ifrModal').attr('src','/admin/citas-fisioterapia/create/'+dateForm+'/'+timeForm);
    $('#modalIfrm').modal();
});
$('.editDate').on('click','.events',function(event){
    event.preventDefault();
    var id = $(this).data('id');
    $('#ifrModal').attr('src','/admin/citas-fisioterapia/edit/'+id);
    $('#modalIfrm').modal();
});
$('.editDate').on('mouseover',function(event){
    var obj = $(this).find('.detail');
    
    obj.css('top', (event.screenY-110));
    obj.css('left', (event.pageX-100));
    
});
$('.selectDate').on('click','li',function(event){
    event.preventDefault();
    var val = $(this).data('val');
    var type = $('#servSelect').val();
    var coach = $('#coachsFilter').val();
    location.assign("/admin/citas-fisioterapia/"+val+"/"+coach+"/"+type);
});
$('.coachsFilter').on('click','li',function(event){
    event.preventDefault();
    var coach = $(this).data('val');
    var month = $('#selectMonth').val();
    var type = $('#servSelect').val();
    location.assign("/admin/citas-fisioterapia/"+month+"/"+coach+"/"+type);
});
$('#servSelect').on('change',function(event){
    event.preventDefault();
    var type = $('#servSelect').val();
    var month = $('#selectMonth').val();
    var coach = $('#coachsFilter').val();
    location.assign("/admin/citas-fisioterapia/"+month+"/"+coach+"/"+type);
});

$('#modal_newUser').on('submit','#form-new',function(event){
    event.preventDefault();
   // Get some values from elements on the page:
    var $form = $( this );
    var url       = $form.attr( "action" );
    // Send the data using post
    var posting = $.post( url, $form.serialize() ).done(function( data ) {
        if (data == 'OK'){
            $('#content-add-date').load('/admin/citas-fisioterapia/create/'+dateForm+'/'+timeForm);
            $('#modal_newUser').modal('hide');
            $('#modal-add-date').modal();
        } else {
            alert(data);
        }
    });
//    
});

  $('.btn-horarios').click(function (e) {
    e.preventDefault();
    $('#ifrModal').attr('src','/admin/horariosEntrenador/');
  });
  $('.btn-bloqueo').click(function (e) {
    e.preventDefault();
    $('#ifrModal').attr('src','/admin/citas/bloqueo-horarios/fisio');
  });
  $('#search_cust').on('keyup',function(){
    var s = $(this).val();
    if (s != ''){
      s = s.toLowerCase();
      $('.events').each(function( index ) {
        if ($( this ).data('name').includes(s)){
          $( this ).show();
        } else {
          $( this ).hide();
        }
      });
    } else {
      $('.events').show();
    }
  });
  @if($detail)
    var details = {!!$detail!!};
  @endif
  $('.btnAvails').on('click',function(){
    if ( $('.availDate').css('display') == 'none' ){
      $('.editDate').find('.lst_events').hide();
      $('.editDate').find('.availDate').show();
    } else {
      $('.editDate').find('.lst_events').show();
      $('.editDate').find('.availDate').hide();
    }
        
  });
</script>
<script src="{{asset('/admin-css/assets/js/toltip.js')}}"></script>
@endsection