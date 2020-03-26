@extends('backend.base')
@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-6 col-md-5 col-lg-4 col-xl-3">
                <div class="card">
                    <div class="card-header">
                      <i class="fa fa-align-justify"></i>Nuevo Usuario</div>
                    <div class="card-body">
                        <br>
                        <form method="POST" action="{{ route('user.store') }}">
                            @csrf
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                      <svg class="c-icon c-icon-sm">
                                          <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-user"></use>
                                      </svg>
                                    </span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('Name') }}" name="name" value="" required autofocus>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@</span>
                                </div>
                                <input class="form-control" type="text" placeholder="{{ __('E-Mail Address') }}" name="email" value="" required>
                            </div>
                            <div class="input-group mb-3">
                              <div class="input-group-prepend">
                                  <span class="input-group-text">Rol</span>
                              </div>
                              <select class="form-control" name="role">
                                <option value="admin">Administrador</option>
                              </select>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="c-icon cil-lock-locked"></i>
                                </span>
                                </div>
                                <input class="form-control" type="password" placeholder="{{ __('Password') }}" name="password">
                            </div>
                            <div class="input-group mb-4">
                                <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="c-icon cil-lock-locked"></i>
                                </span>
                                </div>
                                <input class="form-control" type="password" placeholder="{{ __('Confirm Password') }}" name="password_confirm">
                            </div>
                            <button class="btn btn-block btn-success" type="submit">{{ __('Save') }}</button>
                            <a href="{{ route('users.index') }}" class="btn btn-block btn-primary">{{ __('Return') }}</a> 
                        </form>
                       @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection

@section('javascript')

@endsection