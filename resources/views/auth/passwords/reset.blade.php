@extends('backend.authBase')

@section('content')
<div class="section nopadding nomargin" style="width: 100%; height: 100%; position: absolute; left: 0; top: 0; background: url('{{ asset('fix/img/login.jpg') }}') center center no-repeat; background-size: cover;"></div>

<div class="container">
  <div  style="max-width: 430px; margin: 1em auto;">
      <div class="card-group">
        <div class="card p-1">
          <div class="card-body p-1">
            <h1 style="margin: 1em auto;">Restablecer contraseña</h1>
    <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
      {{ csrf_field() }}

      <input type="hidden" name="token" value="{{ $token }}">

      <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} row">
        <label for="email" class="col-md-4 control-label">E-Mail</label>

        <div class="col-md-6">
          <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}">

          @if ($errors->has('email'))
          <span class="help-block">
            <strong>{{ $errors->first('email') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}  row">
        <label for="password" class="col-md-4 control-label">Password</label>

        <div class="col-md-6">
          <input id="password" type="password" class="form-control" name="password">

          @if ($errors->has('password'))
          <span class="help-block">
            <strong>{{ $errors->first('password') }}</strong>
          </span>
          @endif
        </div>
      </div>

      <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}  row ">
        <label for="password-confirm" class="col-md-4 control-label">Repetir Password</label>
        <div class="col-md-6">
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation">

          @if ($errors->has('password_confirmation'))
          <span class="help-block">
            <strong>{{ $errors->first('password_confirmation') }}</strong>
          </span>
          @endif
        </div>
      </div>
      <div class="form-group row">
        <div class="col-md-8">
            <button type="submit" class="btn btn-primary">
            <i class="fa fa-btn fa-refresh"></i> Restablecer contraseña
          </button>
        </div>
        <div class="col-md-4">
          <a href="/" title="volver" class="btn btn-close">
            Volver
          </a>
        </div>
      </div>

    </form>
            </div>
        </div>
      </div>
  </div>
</div>
</div>

@endsection

@section('javascript')
<style>
  .form-group.has-error.row {
    color: red;
}
</style>
@endsection