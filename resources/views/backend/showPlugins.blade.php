@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Plugin List</title>
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
                <h3 class="card-title">List of all installed plugins</h3>
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
                                <a href="">Download</a>
                                <a href="">Modify</a>
                                <a href="">Delete</a>
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

@section('script')
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false
    });
@endsection

