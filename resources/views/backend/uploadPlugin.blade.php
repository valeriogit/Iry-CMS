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
            <h1>PLUGIN MANAGER - UPLOAD</h1>
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
                    <h3 class="card-title">Upload Plugin</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ action('PluginController@uploaded') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body col-6">
                        <div class="form-group">
                            <label for="zip">Upload Plugin ZIP</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('zip') is-invalid @enderror" id="zip" name="zip" required accept=".zip">
                                    <label class="custom-file-label" for="zip">Choose ZIP file</label>
                                </div>
                            </div>
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

        $(function () {
            bsCustomFileInput.init();
        });
    </script>
@endsection
