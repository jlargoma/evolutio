@extends('layouts.master_login_register')

@section('title') No autorizado - Evolutio HTS @endsection

@section('content')
<section id="content">

    <div class="content-wrap nopadding">

        <div class="section nopadding nomargin" style="width: 100%; height: 100%; position: absolute; left: 0; top: 0; background: url('{{ asset('assets/login.jpg') }}') center center no-repeat; background-size: cover;"></div>

        <div class="container vertical-middle divcenter clearfix">
            <div class="panel panel-default divcenter noradius noborder" style="    margin: 6em auto;max-width: 400px; background-color: rgba(255,255,255,0.93);">
                <h1 style="margin: 1em 0px">Acceso no autorizado</h1>
                <h4>Ooopps.! Lo siento <b class="text-green"><?php echo $user->name ?></b>, No puedes acceder a esta sección.</h4>
                <p style="margin: 1em 0px">
                Prueba a logearte con otro usuario 
                <form action="{{ url('/logout') }}" method="POST">
                    {{ csrf_field() }}
                    <button class="alert alert-success">pincha aquí</button>
                </form>
            </p>
            </div>
        </div>
    </div>
</section>
<style>
    .container {
        position: absolute;
        width: 100% !important;
        text-align: center;
    }
    </style>
@endsection