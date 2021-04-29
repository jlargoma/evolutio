@extends('layouts.admin-master')

@section('title') Citas Entrenador Personal -  Evolutio HTS @endsection
@section('headerTitle') Citas Entrenador Personal @endsection
@section('headerButtoms')
<button type="button" class="btn btn-success addDate" data-date="{{time()}}" data-time="8">
    <i class="fa fa-plus-circle"></i></button>
    <a href="/admin/citas-pt/listado/" class="btn btn-success" style="float: right; margin-left: 3px;">Listado</a>
@endsection
@section('content')
<div class="content content-full bg-white">
	<div class="row">
            <div class="col-md-12">
                <input type="hidden" id="coachsFilter" value="{{$coach}}">
                <input type="hidden" id="selectMonth" value="{{$month}}">
                <div class="row">
                    <div class="col-xs-10">
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
                    <div class="col-xs-2 mx-1em">
                        <button class="btn btn-horarios" data-toggle="modal" data-target="#modalIfrm">Horarios</button>
                    </div>
                <div class="col-xs-10">
                <ul class="selectDate">
                @foreach($aMonths as $k=>$v)
                <li data-val="{{$k}}" class="<?php echo ($month == $k) ? 'active' : ''?>">
                    {{$v.' '.$year}}
                </li>
                @endforeach
                </ul>
                </div>
                <div class="col-xs-2 mx-1em">
                    <select id="selectType" class="form-control">
                        <option value="0">Servicio</option>
                        <?php
                        if ($types){
                            foreach ($types as $k=>$v){
                                $selected = ($type == $k) ? 'selected' : '';
                                echo '<option value="'.$k.'" '.$selected.'>'.$v.'</optiono>';
                            }
                        }
                        ?>
                    </select>
                    
                </div>
                </div>
                
                @include('citasPT.calendar')
            </div>
	</div>
</div>
    @include('citasPT.modals')
@endsection

@section('scripts')
<link rel="stylesheet" href="{{ asset('css/calendars.css') }}">

<style>

    @foreach($tColors as $k=>$v)
    ul.coachsFilter li.select_{{$k}} {
            background-color: {{$v}};
            color: #FFF;
        }
    .eventType_{{$k}} {background-color: {{$v}};}
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
    $('#ifrModal').attr('src','/admin/citas-pt/create/'+dateForm+'/'+timeForm);
    $('#modalIfrm').modal();
});
$('.editDate').on('click','div',function(event){
    event.preventDefault();
    var id = $(this).data('id');
    $('#ifrModal').attr('src','/admin/citas-pt/edit/'+id);
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
    var type = $('#selectType').val();
    var coach = $('#coachsFilter').val();
    location.assign("/admin/citas-pt/"+val+"/"+coach+"/"+type);
});
$('.coachsFilter').on('click','li',function(event){
    event.preventDefault();
    var coach = $(this).data('val');
    var month = $('#selectMonth').val();
    var type = $('#selectType').val();
    location.assign("/admin/citas-pt/"+month+"/"+coach+"/"+type);
});
$('#selectType').on('change',function(event){
    event.preventDefault();
    var type = $('#selectType').val();
    var month = $('#selectMonth').val();
    var coach = $('#coachsFilter').val();
    location.assign("/admin/citas-pt/"+month+"/"+coach+"/"+type);
});

$('#modal_newUser').on('submit','#form-new',function(event){
    event.preventDefault();
   // Get some values from elements on the page:
    var $form = $( this );
    var url       = $form.attr( "action" );
    // Send the data using post
    var posting = $.post( url, $form.serialize() ).done(function( data ) {
        if (data == 'OK'){
            $('#content-add-date').load('/admin/citas-pt/create/'+dateForm+'/'+timeForm);
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

</script>
@endsection