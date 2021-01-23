@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Edit Profile</title>
@endsection

@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>USER MANAGER - MODIFY</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <!-- general form elements -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">User information (leave fields empty if you wouldn't change it - for change username and email ask to Administrator)</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ action('ProfileController@saveProfile') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-3 col-xs-6">
                                <label for="username">username</label>
                                @if(Roles::checkRole(['administrator']))
                                  <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" placeholder="Enter username" value="{{ $user->username }}">
                                @else
                                  <p>{{ $user->username}}</p>
                                @endif
                            </div>
                            <div class="form-group col-md-3 col-xs-6">
                                <label for="password">New password </label>
                                <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter new password">
                            </div>
                            <div class="form-group col-md-3 col-xs-6">
                                <label for="author">Email</label>
                                @if(Roles::checkRole(['administrator']))
                                  <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email" value="{{ $user->email }}">
                                @else
                                  <p>{{ $user->email}}</p>
                                @endif
                            </div>
                            <div class="form-group col-md-3 col-xs-6">
                                <label for="avatar">Avatar</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept=".png,.jpg,.jpeg">
                                        <label class="custom-file-label" for="avatar">Choose avatar</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        {!! ReCaptcha::printField() !!}
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
                <div class="col-1">
                    <a href="{{ action('PluginController@show') }}">
                        <button class="btn btn-block bg-gradient-secondary">Back</button>
                    </a>
                </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('script')
    <script>
        @if(session()->has('errorProfile'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
                });

            Toast.fire({
                icon: 'error',
                title: '&nbsp;&nbsp;&nbsp;Profile not updated!'
            })
        @endif

        @if(session()->has('saveProfile'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;Profile updated!'
            })
        @endif

        $(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
