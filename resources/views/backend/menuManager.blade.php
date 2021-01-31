@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Menu Manager</title>
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('css/bootstrap-iconpicker/bootstrap-iconpicker.min.css') }}">
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>FRONTEND MENU MANAGER</h1>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-12 mr-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">Edit item menu</div>
                        <div class="card-body">
                        <form id="frmEdit" class="form-horizontal">
                        <div class="form-group">
                        <label for="text">Text</label>
                        <div class="input-group">
                        <input type="text" class="form-control item-menu" name="text" id="text" placeholder="Text">
                        <div class="input-group-append">
                        <button type="button" id="myEditor_icon" class="btn btn-outline-secondary"></button>
                        </div>
                        </div>
                        <input type="hidden" name="icon" class="item-menu">
                        </div>
                        <div class="form-group">
                        <label for="href">URL</label>
                        <input type="text" class="form-control item-menu" id="href" name="href" placeholder="URL">
                        </div>
                        <div class="form-group">
                        <label for="target">Target</label>
                        <select name="target" id="target" class="form-control item-menu">
                        <option value="_self">Self</option>
                        <option value="_blank">Blank</option>
                        <option value="_top">Top</option>
                        </select>
                        </div>
                        <div class="form-group">
                            <label for="role">role</label>
                            <input type="text" name="role" class="form-control item-menu" id="role" placeholder="fds">
                            </div>
                        </form>
                        </div>
                    <div class="card-footer">
                        <button type="button" id="btnUpdate" class="btn btn-info" disabled>Update item</button>
                        <button type="button" id="btnAdd" class="btn btn-primary">Add item</button>
                        <button type="button" id="btnSave" class="btn btn-success float-right">Save menu</button>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-sm-12">
                <ul id="myEditor" class="sortableLists list-group"></ul>
            </div>
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
<script src="{{ asset('js/bootstrap-iconpicker/iconset/fontawesome5-3-1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap-iconpicker/bootstrap-iconpicker.min.js') }}"></script>
<script src="{{ asset('js/jquery-menu-editor/jquery-menu-editor.js') }}"></script>
@endsection

@section('script')
    <script>
        // icon picker options
        var iconPickerOptions = {searchText: "Search", labelHeader: "{0}/{1}"};
        // sortable list options
        var sortableListOptions = {
            placeholderCss: {'background-color': "#cccccc"}
        };
        var editor = new MenuEditor('myEditor',
                    {
                    listOptions: sortableListOptions,
                    iconPicker: iconPickerOptions,
                    maxLevel: 5 // (Optional) Default is -1 (no level limit)
                    // Valid levels are from [0, 1, 2, 3,...N]
                    });
        editor.setForm($('#frmEdit'));
        editor.setUpdateButton($('#btnUpdate'));
        //Calling the update method
        $("#btnUpdate").click(function(){
            if($('#text').val() == ""){
                $('#text').addClass("is-invalid");
                return false;
            }
            else{
                $('#text').removeClass("is-invalid");
            }

            if($('#href').val() == ""){
                $('#href').addClass("is-invalid");
                return false;
            }
            else{
                $('#href').removeClass("is-invalid");
            }

            editor.update();
        });
        // Calling the add method
        $('#btnAdd').click(function(){
            if($('#text').val() == ""){
                $('#text').addClass("is-invalid");
                return false;
            }
            else{
                $('#text').removeClass("is-invalid");
            }

            if($('#href').val() == ""){
                $('#href').addClass("is-invalid");
                return false;
            }
            else{
                $('#href').removeClass("is-invalid");
            }

            editor.add();
        });

        var arrayJson = [{"href":"http://home.com","icon":"fas fa-home","text":"Home", "target": "_top", "title": "My Home"},{"icon":"fas fa-chart-bar","text":"Opcion2"},{"icon":"fas fa-bell","text":"Opcion3"},{"icon":"fas fa-crop","text":"Opcion4"},{"icon":"fas fa-flask","text":"Opcion5"},{"icon":"fas fa-map-marker","text":"Opcion6"},{"icon":"fas fa-search","text":"Opcion7","children":[{"icon":"fas fa-plug","text":"Opcion7-1","children":[{"icon":"fas fa-filter","text":"Opcion7-1-1"}]}]}];
        editor.setData(arrayJson);

        /*take value when save*/
        $("#btnSave").click(function(){
            $.post("{{ action('MenuController@saveMenu') }}",
            {
                menu: editor.getString(),
                _token: "{{csrf_token()}}"
            },
            function(data){
                if(data == true){
                    menuToast.fire({
                        icon: 'success',
                        title: '&nbsp;&nbsp;&nbsp;Menu saved successfully!'
                    })
                }
                else{
                    menuToast.fire({
                        icon: 'error',
                        title: '&nbsp;&nbsp;&nbsp;Menu saving error!'
                    })
                }
            });
        });

        const menuToast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 3000
        });
    </script>
@endsection

