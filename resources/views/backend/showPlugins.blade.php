@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Plugin List</title>
@endsection

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('css/datatables-bs4/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatables-responsive/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatables-buttons/buttons.bootstrap4.min.css') }}">
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>PLUGIN MANAGER</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title" style="padding-top: 8px;">List of all installed plugins</h3>
                <a href="{{ action('PluginController@create') }}" class="float-right"><button type="button" class="btn btn-block btn-success">Create Plugin</button></a>
                <a href="{{ action('PluginController@upload') }}" class="float-right mr-2"><button type="button" class="btn btn-block btn-primary">Upload Plugin</button></a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Author</th>
                    <th>Author Email</th>
                    <th>Installed at</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($plugins as $plugin)
                        <tr>
                            <td>{{ $plugin->name }}</td>
                            <td>{{ $plugin->description }}</td>
                            <td>{{ $plugin->author }}</td>
                            <td>{{ $plugin->author_email }}</td>
                            <td>{{ $plugin->created_at }}</td>
                            <td>
                                <a href="{{ action('PluginController@delete', $plugin->id) }}" class="float-right mr-2"><button type="button" class="btn btn-block btn-danger">Delete</button></a>
                                <a href="{{ action('PluginController@modify', $plugin->id) }}" class="float-right mr-2"><button type="button" class="btn btn-block btn-warning">Modify</button></a>
                                <a href="{{ action('PluginController@downloadZip', $plugin->id) }}" class="float-right mr-2"><button type="button" class="btn btn-block btn-info">Download</button></a>
                            </td>
                        </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
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

@section('js')
<!-- DataTables  & Plugins -->
<script cookie-consent="strictly-necessary" src="{{ asset('js/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/datatables-bs4/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/datatables-responsive/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/datatables-responsive/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/datatables-buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('js/datatables-buttons/buttons.bootstrap4.min.js') }}"></script>
@endsection

@section('script')
    <script>
        $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false
        });

        @if(session()->has('installedPlugin'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;Plugin installed successfully!'
            })
        @endif

        @if(session()->has('deletedSuccessPlugin'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;Plugin deleted successfully!'
            })
        @endif

        @if(session()->has('deletedFailPlugin'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'error',
                title: '&nbsp;&nbsp;&nbsp;Plugin deleted error!'
            })
        @endif
    </script>
@endsection

