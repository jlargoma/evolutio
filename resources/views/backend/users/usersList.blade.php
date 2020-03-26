@extends('backend.base')
@section('content')

        <div class="container-fluid">
          <div class="animated fadeIn">
            <div class="row">
              <div class="col-sm-12 col-md-12 ">
                <div class="card">
                    <div class="card-header">
                      <div class="pull-left">
                      <i class="fa fa-align-justify"></i>{{ __('Users') }}
                      </div>
                      <div class="pull-right">
                      <a href="{{route('user.new')}}" title="Nuevo usuario">Nuevo</a>
                      </div>
                    </div>
                    <div class="card-body">
                      @include('flash-message')
                        <table class="table table-responsive-sm table-striped">
                        <thead>
                          <tr>
                            <th>Username</th>
                            <th>E-mail</th>
                            <th>Rol</th>
                            <th>Últ. modif.</th>
                            <th></th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach($users as $user)
                            <tr>
                              <td>{{ $user->name }}</td>
                              <td>{{ $user->email }}</td>
                              <td>{{ $user->role }}</td>
                              <td>{{ convertDateToShow($user->updated_at) }}</td>
                              <td>
                                <a href="{{ route('user.edit',$user->id) }}" class="btn btn-block btn-primary">Edit</a>
                              </td>
                              <td>
                                @if( $you->id !== $user->id )
                                <form action="{{ route('users.destroy', $user->id ) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button class="btn btn-block btn-danger" onclick="return confirm('Eliminar usuario?')">Delete User</button>
                                </form>
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@endsection


@section('javascript')

@endsection

