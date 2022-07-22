@extends('layouts.popup')
@section('content')
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/select2/select2-bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker3.min.css') }}">
<link rel="stylesheet" href="{{ assetV('css/custom.css') }}">

<a class="back" href="<?php echo $urlBack; ?>">X</a>
<?php 

$date_type_u = "Entrenador";
$date_type = 'pt'
?>
@if($blocked)<!-- es un bloqueo -->
  @if($isGroup)
    @include('calendars.editGroup')
  @else
    @include('calendars.editBlock')
  @endif
@else
  @include('citasPT.editDate')
@endif

@if($id<1) 
  @include('calendars.blockDate')
@endif
@endsection
@section('scripts')
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/bootstrap-datetimepicker/moment.min.js')}}"></script>
<script src="{{asset('/admin-css/assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script type="text/javascript">
jQuery(function () {
    App.initHelpers(['datepicker', 'select2']);
    

        $("#id_user").change(function() {
            var id = $(this).val();
            $.get('/admin/get-mail/' + id, function(data) {
                $('#NC_email').val(data[0]);
                $('#NC_phone').val(data[1]);
            });
            $( "#id_rate").val(-1)
            $.get('/admin/get-rates/' + id, function(data) {
              
              $( "#id_rate option" ).each(function( index ) {
                var dis = true;
                var opt = parseInt($( this ).val());
                for (const i in data) {
                  if (data[i] == opt) dis = false;
                }
                $( this ).attr('disabled',dis);
              })
            });
        });
        
        $('.sendForm').on('click', function(){
          var oForm = $('#'+$(this).data('id'));
          var data = {
            id: {{$id}},
            date: oForm.find('#date').val(),
            time: oForm.find('#hour').val(),
            uID: oForm.find('#id_user').val(),
            cID: oForm.find('#id_coach').val(),
            type: 'pt',
            _token: '{{csrf_token()}}',
          };
          $.post('/admin/citas/checkDisp',data, function(resp) {
                        
            if (resp == 'bloqueo'){
              window.show_notif('error', 'Horario bloqueado para el Entrenador');
              return;
            }
            
            if (resp>1){
              window.show_notif('error', 'Horario no disponible');
            } else {
              if (resp == 1){
                if (confirm('Ya hay una cita para ese momento, continuar de todas maneras?')){
                  oForm.submit();
                }
              } else {
                oForm.submit();
              }
            }
          });
        });
        
        $(".btnDeleteCita").click(function () {
            if (confirm('Eliminar la Cita?'))
                location.assign("/admin/citas/delete/{{$id}}");
        });

        $('.btn-user').click(function (e) {
           e.preventDefault();
           var id = $(this).attr('data-idUser');
           location.href = '/admin/usuarios/informe/' + id;

        });
                    
        $('#is_group').click(function (e) {
          if($(this).is(':checked')){
            $('#id_user').val('0').attr('disabled',true);
            $('#NC_email').val('').attr('disabled',true);
            $('#NC_phone').val('').attr('disabled',true);
          } else {
            $('#id_user').val('0').attr('disabled',false);
            $('#NC_email').val('').attr('disabled',false);
            $('#NC_phone').val('').attr('disabled',false);
          }
        });
               
        $("#hour").change(function () {
          var val = $(this).find(':selected').val();
          $('#customTime').val(val+':00');
        });

        
        @if($id<1)
        $('body').on('change','#date,#hour',function(){
          checkAvail();
        });

        availCoach = [];
        function checkAvail(){
          var data = {
            id: {{$id}},
            date: $('#date').val(),
            time: $('#hour').val(),
            type: 'pt',
            _token: '{{csrf_token()}}',
          };
          $.post('/admin/citas/checkDispCoaches',data, function(resp) {
            for(cID in resp){
              if (resp[cID] == 0 || resp[cID] == 1){
                availCoach[cID] = '';
              } else {
                availCoach[cID] = 's_disable';
              }
            }
          });
        }
        checkAvail();
        @endif

       

        function formatCoach (coach) {
          // console.log(coach);
          if (!coach.id) {
            return coach.text;
          }
          var class_css = 's_avail_' + coach.id + ' ';
          if (typeof availCoach != 'undefined' && typeof availCoach[coach.id] != 'undefined') 
            class_css += availCoach[coach.id];
            
          var $coach = $(
            '<span class="' + class_css  + '"><b class="cColors coach_' + coach.id + '" /> ' + coach.text + '</span>'
          );
          return $coach;
        };
        
        $(".js-select2-coach").select2({
          templateResult: formatCoach
        });

    });

</script>
<style>
  input#customTime {padding: 0px 4px;}
  #tit_user{
      width: 100% !important;
    }

  #tit_user span{
      float: right;
      padding-right: 11px;
      margin-top: -3px;
    }
  #tit_user span input{
      margin-right: 6px;
      height: 13px;
    }
      a.back{
      display: none;
    }
    
    .cColors{
      width: 10px;
      height: 10px;
      background-color: red;
      display: inline-block;
      border-radius: 50%;
    }
    span.select2-selection.select2-selection--single {
      padding: 7px;
    }
    span.s_disable {
    text-decoration: line-through;
}
    @foreach($tColors as $k=>$v)
     .coach_{{$k}} {background-color: {{$v}};}
    @endforeach

    @media (max-width: 780px) {  
        a.back {
        display: block;
        font-weight: bold;
        float: right;
        width: 31px;
        background-color: #6e6e6e;
        color: #FFF;
        text-align: center;
        font-size: 22px;
        border-radius: 6px;
      }
    }
</style>
@endsection