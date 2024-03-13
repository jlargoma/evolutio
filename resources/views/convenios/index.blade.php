@extends('layouts.admin-master')

@section('title') Convenios - Evolutio HTS @endsection

@section('headerButtoms')
<li class="text-center">
    <button class="btn btn-sm btn-success new-convenio" data-toggle="modal" data-target="#modal-convenio">
        <i class="fa fa-plus"></i> Convenios
    </button>
</li>
@endsection

@section('content')
<div class="content content-full bg-white">
    <h2 class="font-w600">
        Listado de Convenios de <b><?php echo $year ?></b>
    </h2>
    <div class="table-responsive">
        <table class="table ticomes">
            <thead>
                <tr>
                    <th class="static thBlue">Convenio</th>
                    <th class="first-col"></th>
                    <th class="first-col"></th>
                    <th class="">Total<br>{{moneda(array_sum($totals))}}</th>
                    @foreach($lstMonths as $k=>$v)
                    <th>{{$v}}<br />{{moneda($totals[$k])}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($lstObjs as $oConve)
                <tr class="d1" data-k="{{$oConve->id}}">
                    <td class="static convenio-name-display">
                        <i class="fa fa-eye"></i> {{$oConve->name}}
                    </td>
                    <td class="static first-col">
                        <button 
                            data-id="{{$oConve->id}}" 
                            style="margin-top:5px;margin-right:5px;" class="btn btn-sm btn-primary btn-url-convenio pull-right"
                        >
                            <i data-id="{{$oConve->id}}" class="fa fa-external-link btn-url-inner"></i>
                        </button>
                    </td>
                    <td class="static first-col">
                        <button 
                            data-id="{{$oConve->id}}" 
                            data-name="{{$oConve->name}}" 
                            data-comision="{{$oConve->comision_fija / 100}}" 
                            style="margin-top:5px;" class="btn btn-sm btn-primary btn-edit-convenio"
                        >
                            Editar
                        </button>
                    </td>
                    <td><b>{{moneda(array_sum($tConvenio[$oConve->id]))}}</b></td>
                    @foreach($lstMonths as $km=>$vm)
                    <td>{{moneda($tConvenio[$oConve->id][$km])}}</td>
                    @endforeach
                </tr>
                @if(count($convLstRates[$oConve->id])>0)
                @foreach($convLstRates[$oConve->id] as $k2=>$d2)
                <tr class="d2 d1_{{$oConve->id}} " data-k="{{$oConve->id}}_{{$k2}}">
                    <td class="static">{{$lstRateTypes[$k2]}}</td>
                    <td class="first-col"></td>
                    <td class="first-col"></td>
                    <td><b>{{moneda(array_sum($d2))}}</b></td>
                    @foreach($lstMonths as $km=>$vm)
                    <td>{{moneda($d2[$km])}}</td>
                    @endforeach
                </tr>
                @endforeach
                @endif

                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="modal-convenio" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary-dark">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                </div>
                <div class="row block-content" id="content-bono">
                    @include('convenios.new')
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-convenio-update" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="block block-themed block-transparent remove-margin-b">
                <div class="block-header bg-primary-dark">
                    <ul class="block-options">
                        <li>
                            <button data-dismiss="modal" type="button"><i class="si si-close"></i></button>
                        </li>
                    </ul>
                </div>
                <div class="row block-content" id="content-bono">
                    @include('convenios.update')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<link rel="stylesheet" href="{{ assetV('css/contabilidad.css') }}">

<script type="text/javascript">
    $(document).ready(function() {

        $('body').on("click", ".btn-edit-convenio", function(event) {
            event.stopPropagation();
            let element = $(event.target);
            
            $('#convenio-name-update').val(element.data('name'));
            $('#convenio-comision-update').val(element.data('comision'));
            $('#convenio-id-update').val(element.data('id'));
            
            $("#modal-convenio-update").modal("show");
        });

        $('body').on("click", ".btn-url-convenio", function(event) {
            event.stopPropagation();
            let element = $(event.target);
            let convenio = element.data('id');

            $.get('/admin/convenios/url', {convenio:convenio}, function(resp){
                if (resp.status == 'OK'){

                    navigator.clipboard.writeText(resp.details.url)
                    .then(function() {
                        console.log('Text copied to clipboard');
                    })
                    .catch(function(err) {
                        console.error('Unable to copy text to clipboard:', err);
                    });

                    window.show_notif('success', 'Link copiado al portapapeles!');
                } else {
                    window.show_notif('error', 'Error!');
                }
            });
        });

        $('.d1').on('click', function() {
            let element = $(event.target);
            if (!element.hasClass("btn-edit-convenio") && !element.hasClass("btn-url-convenio") && !element.hasClass("btn-url-inner")) {
                var k = $(this).data('k');
                $('.d1_' + k).toggle();
            }else if (element.hasClass("btn-edit-convenio")) {

                $('#convenio-name-update').val(element.data('name'));
                $('#convenio-comision-update').val(element.data('comision'));
                $('#convenio-id-update').val(element.data('id'));
                $("#modal-convenio-update").modal("show");

            }
                
        });
        
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
</style>
@endsection