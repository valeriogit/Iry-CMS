@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Plugin List</title>
@endsection

@section('css')
    <!-- Codemirror -->
    <link rel="stylesheet" href="{{ asset('css/codemirror/lib/codemirror.css') }}">
    <link rel="stylesheet" href="{{ asset('css/codemirror/theme/monokai.css') }}">
    <!-- tree view -->
    <link rel="stylesheet" href="{{ asset('css/tree/default.css') }}" />
@endsection

@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>PLUGIN MANAGER - MODIFY</h1>
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
                    <h3 class="card-title">Modify Plugin</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form>
                    @csrf
                    <div class="card-body row">
                        <div class="form-group col-md-9 col-xs-12">
                            <textarea id="code" name="code">{{ $content }}</textarea>
                        </div>
                        <div class="col-md-3 col-xs-12">
                            {!! $code !!}
                        </div>
                    </div>

                    <!-- /.card-body -->

                    <div class="card-footer">
                        {!! ReCaptcha::printField() !!}
                        <button type="button" class="btn btn-primary" onclick="saveFile()">Save file</button>
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

@section('js')
    <!-- Codemirror -->
    <script src="{{ asset('js/codemirror/lib/codemirror.js') }}"></script>
    <script src="{{ asset('js/codemirror/addon/edit/matchbrackets.js') }}"></script>
    <script src="{{ asset('js/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>
    <script src="{{ asset('js/codemirror/mode/xml/xml.js') }}"></script>
    <script src="{{ asset('js/codemirror/mode/javascript/javascript.js') }}"></script>
    <script src="{{ asset('js/codemirror/mode/css/css.js') }}"></script>
    <script src="{{ asset('js/codemirror/mode/clike/clike.js') }}"></script>
    <script src="{{ asset('js/codemirror/mode/php/php.js') }}"></script>
    <script src="{{ asset('js/codemirror/addon/selection/active-line.js') }}"></script>
    <script src="{{ asset('js/codemirror/addon/edit/closebrackets.js') }}"></script>
    <!-- tree view -->
    <script src="{{ asset('js/tree/php_file_tree.js') }}"></script>
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

        var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            styleActiveLine: true,
            autoCloseBrackets: true
        });

        editor.setOption("theme", "monokai");

        function saveFile(){

            let file = getUrlVars()["file"];
            let content = editor.getValue();

            if(typeof file == 'undefined'){
                file = "";
            }

            $.post("{{action('PluginController@saveModify', $id)}}",
            {
                _token:"{{csrf_token()}}",
                file: file,
                content: content
            }, function(status){
                Swal.queue([{
                    icon: status.code,
                    title: status.state,
                    confirmButtonText: 'Ok',
                    text: status.message,
                }]);
            });
        }

        function getUrlVars()
        {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }
    </script>
@endsection
