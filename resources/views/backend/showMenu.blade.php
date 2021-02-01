@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Menu List</title>
@endsection

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('css/datatables-bs4/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatables-responsive/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/datatables-buttons/buttons.bootstrap4.min.css') }}">
<!-- iCheck for checkboxes and radio inputs -->
<link rel="stylesheet" href="{{ asset('css/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>MENU MANAGER</h1>
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
                <h3 class="card-title" style="padding-top: 8px;">List of all menu</h3>
                <a href="{{ action('MenuController@createMenu') }}" class="float-right"><button type="button" class="btn btn-block btn-success">Create Menu</button></a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Visibility</th>
                    <th>Created at</th>
                    <th>Last Update</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach ($menuList as $menu)
                        <tr>
                            <td>{{ $menu->id }}</td>
                            <td>{{ $menu->name }}</td>
                            <td>
                                <div class="icheck-success d-inline">
                                    <input type="radio" name="visible" @if($menu->visible == 1) checked @endif id="{{$menu->id}}">
                                    <label for="{{$menu->id}}">
                                    </label>
                                </div>
                            </td>
                            <td>{{ $menu->created_at }}</td>
                            <td>{{ $menu->updated_at }}</td>
                            <td>
                                <a href="{{ action('MenuController@deleteMenu', $menu->id) }}" class="float-right mr-2"><button type="button" id="del{{$menu->id}}" @if($menu->visible == 1) disabled @endif class="btn btn-block btn-danger">Delete</button></a>
                                <a href="{{ action('MenuController@modifyMenu', $menu->id) }}" class="float-right mr-2"><button type="button" class="btn btn-block btn-warning">Modify</button></a>
                            </td>
                        </tr>

                        @if($menu->visible == 1)
                        <script> let btn = "{{$menu->id}}"; </script>
                        @endif
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

        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000
        });

        $("input[name=visible]:radio").change(function () {
            let menu = $(this).attr("id");
            $.post("{{ action('MenuController@changeVisibility') }}",
            {
                menu: menu,
                _token: "{{csrf_token()}}"
            },
            function(data){
                if(data == true){
                    Toast.fire({
                        icon: 'success',
                        title: '&nbsp;&nbsp;&nbsp;Menu updated successfully!'
                    })
                    $("#del"+btn).removeAttr("disabled");
                    btn = menu;
                    $("#del"+btn).attr("disabled", "disabled");
                }
                else{
                    Toast.fire({
                        icon: 'error',
                        title: '&nbsp;&nbsp;&nbsp;Menu saving error!'
                    })
                }
            });
        })

        @if(session()->has('deletedSuccessMenu'))
            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;Menu deleted successfully!'
            })
        @endif

        @if(session()->has('deletedFailMenu'))
            Toast.fire({
                icon: 'error',
                title: '&nbsp;&nbsp;&nbsp;Menu deleted error!'
            })
        @endif
    </script>
@endsection

