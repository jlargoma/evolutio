@extends('layouts.admin-master')

@section('title') Cuotas Pendientes - Evolutio HTS @endsection


@section('headerButtoms')
<li class="text-center">
  <a href="/admin/clientes" class="btn btn-sm btn-success font-s16 font-w300">
    Volver
  </a>
</li>
@endsection

@section('content')
<div class="content  content-full bg-white">
  <div class="row mb-3">
    <div class="col-xs-12">
      @if(session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
      @endif
    </div>
  </div>
  <div class="row mb-5">
    
  </div>

  <div class="row">
    <div class="col-xs-12 mb-3">
    @include('/admin/usuarios/clientes/tableCuotasPendientes')
    </div>
  </div>
</div>


@endsection

@section('scripts')
<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">

<script type="text/javascript">

  function limitDecimals(element) {
    let value = element.value;
    // Check if the input has more than two decimal places
    if (value.includes('.') && value.split('.')[1].length > 2) {
      // If more than two decimal places, truncate to two decimal places
      element.value = parseFloat(value).toFixed(2);
    }
  }

  $(document).ready(function() {
    $('#extra-hours-table-acc').on('click', '.d1', function(){
        var k = $(this).data('k');
        
        $('.d1_'+k).each(function(){
          if ($(this).css('display') != 'none'){
            var k = $(this).data('k');
            
            $('.d2_'+k).each(function(){
              if ($(this).css('display') != 'none'){
                var k = $(this).data('k');
                $('.d3_'+k).each(function(){
                  if ($(this).css('display') != 'none'){
                    var k = $(this).data('k');
                    $('.d4_'+k).hide();
                  }
                });
                $('.d3_'+k).hide();
              }
            });
            $('.d2_'+k).hide();
          }
        });
        
        $('.d1_'+k).toggle();
    });


    
    $('#extra-hours-table-acc').on('click', '.d2', function(){
        var k = $(this).data('k');
        
        $('.d2_'+k).each(function(){
          if ($(this).css('display') != 'none'){
            var k = $(this).data('k');
            $('.d3_'+k).each(function(){
              if ($(this).css('display') != 'none'){
                var k = $(this).data('k');
                $('.d4_'+k).hide();
              }
            });
            $('.d3_'+k).hide();
          }
        });
        
        $('.d2_'+k).toggle();
    });

  });

  $('#copyCuotasPendientesLink').on('click', function () {

    const urlPieces = [location.protocol, '//', location.host, location.pathname]
    let url = urlPieces.join('')

    if(window.location.search){
      url += window.location.search + '&year={{$year}}';
    } else {
      url +='?year={{$year}}';
    }

    navigator.clipboard.writeText(url);
    window.show_notif('success', 'Link copiado al portapapeles!');

  });

  $('#filterByRateCuotasPendientes').on('change', function () {
    let $this = $(this);
    
    const urlPieces = [location.protocol, '//', location.host, location.pathname]
    let url = urlPieces.join('');;

    $filterFamily = $this.val();

    if($filterFamily) {
      url += "?fFamily=" + $filterFamily;
    } 

    window.location.href = url;

  });
</script>
<style>
  .filtIncome {
    cursor: pointer;
  }

  .filtIncome.active {
    border: 1px solid #0046a0;
    background-color: #0067ea !important;
  }

  #copyCuotasPendientesLink {
    cursor: pointer;
    font-size: 2rem;
  }
</style>
@endsection