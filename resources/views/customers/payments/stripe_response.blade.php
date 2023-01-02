@extends('layouts.app')

@section('content')

<div class="card">
  <div class="card-header"></div>

  <div class="card-body">
    @if ($success)
    <div class="alert " role="alert">
      <h3>Pago realizado con éxito</h3>
      <p class="text-center">Muchas Gracias..!</p>
    </div>
    @endif
    @if ($cancel)
    <div class="alert alert-danger" role="alert">
      <h3>Pago cancelado</h3>
    </div>
    <p class="text-center">Por favor, vuelva a intentarlo o póngase en contacto con nosotros.</p>
    @endif

  </div>

  @endsection