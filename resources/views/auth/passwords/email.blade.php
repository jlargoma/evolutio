@extends('backend.authBase')

@section('content')
<div class="section nopadding nomargin" style="width: 100%; height: 100%; position: absolute; left: 0; top: 0; background: url('{{ asset('fix/img/login.jpg') }}') center center no-repeat; background-size: cover;"></div>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12" style="max-width: 430px; margin: 1em auto;">
      <div class="card-group">
        <div class="card p-1">
          <div class="card-body p-1">
            <h1>Recuperar Contraseña</h1>
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
              {{ csrf_field() }}
              <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-4 control-label">E-Mail</label>

                <div class="">
                  <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">

                  @if ($errors->has('email'))
                  <span class="help-block">
                    <strong>{{ $errors->first('email') }}</strong>
                  </span>
                  @endif
                </div>
              </div>

              
              <div class="form-group row">
                <div class="col-md-8">
                  <button type="submit" class="btn btn-primary">
                    <i class="fa fa-btn fa-envelope"></i> Enviar link para cambiar password
                  </button>
                </div>
                <div class="col-md-4">
                  <a href="/" title="volver" class="btn btn-close">
                    Volver
                  </a>
                </div>
              </div>
              
              @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>

@endsection

@section('javascript')

@endsection