@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Plugin List</title>
@endsection

@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>PLUGIN MANAGER - CREATE</h1>
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
                    <h3 class="card-title">Plugin information</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ action('PluginController@save') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter plugin name" required value="{{ old('name') }}">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="decription" name="description" placeholder="Enter plugin description" required rows="4">{{ old('description') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control @error('author') is-invalid @enderror" id="author" name="author" placeholder="Enter plugin author name" required value="{{ old('author') }}">
                        </div>
                        <div class="form-group">
                            <label for="author-email">Author Email</label>
                            <input type="email" class="form-control" id="author-email" name="author-email" placeholder="Enter plugin author email" value="{{ old('author-email') }}">
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
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
        @if(session()->has('errorPlugin'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
                });

            Toast.fire({
                icon: 'error',
                title: '&nbsp;&nbsp;&nbsp;{!! session('errorPlugin') !!}'
            })
        @endif

        @if(session()->has('downloadPlugin'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;Plugin created! <br> &nbsp;&nbsp;&nbsp;Download started!'
            })
        @endif
    </script>
@endsection
