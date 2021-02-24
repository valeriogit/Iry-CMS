@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Create Post</title>
@endsection

@section('css')
    <!-- summernote -->
    <link href="{{ asset('css/summernote/summernote-bs4.min.css')}}" rel="stylesheet">
    <!-- bootstrap-switch -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-toggle/bootstrap-toggle.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('css/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/select2/select2-bootstrap4.min.css') }}">

    @if(Post::canUploadFile())
        <!-- dropzone -->
        <link href="{{ asset('css/dropzone/dropzone.min.css')}}" rel="stylesheet">
    @endif
@endsection

@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>POST MANAGER - CREATE</h1>
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
                    <h3 class="card-title">Post data</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ action('ProfileController@saveNewUser') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" placeholder="Post Title">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="basic-url">Post URL</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon3">{{ asset('') }}</span>
                                    </div>
                                    <input type="text" class="form-control" id="slug" name="slug" aria-describedby="basic-addon3">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-10">
                                <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#fileManager">
                                    <i class="fas fa-photo-video mr-2"></i> Manage media
                                </button>
                            </div>
                            <div class="col-md-2">
                                <div class="row">
                                    <div class="col-md-5 offset-md-1 mt-2">
                                        {!! ReCaptcha::printField() !!}
                                        <button type="button" id="draftSave" class="btn btn-primary">Save draft</button>
                                    </div>
                                    <div class="col-md-5 offset-md-1 mt-2">
                                        <button type="button" id="draftPublish" class="btn btn-success">Publish</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- modal for file - START -->
                            <div class="modal fade" id="fileManager" tabindex="-1" aria-labelledby="fileManagerLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="fileManagerLabel">Manage Media</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                @if(Post::canUploadFile())
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link" id="upload-tab" data-toggle="tab" href="#upload" role="tab" aria-controls="upload" aria-selected="false">Upload Files</a>
                                                    </li>
                                                @endif
                                                <li class="nav-item" role="presentation">
                                                    <a class="nav-link active" id="manage-tab" data-toggle="tab" href="#manage" role="tab" aria-controls="manage" aria-selected="true">Manage Files</a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                @if(Post::canUploadFile())
                                                    <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                                                        <h5 class="mt-4 mb-4 text-center">You can upload a file! The clicked one will automatically be inserted into the document.</h5>
                                                        <div id="uploadZone" class="dropzone"></div>
                                                    </div>
                                                @endif
                                                <div class="tab-pane fade show active" id="manage" role="tabpanel" aria-labelledby="manage-tab">
                                                    <h5 class="mt-4 mb-4 text-center">Choose file! The clicked one will automatically be inserted into the document.</h5>
                                                    <div class="row text-center" id="contentMedia">
                                                        @foreach($media as $file)
                                                            <div class="col-6 col-md-2 mb-5" onclick="addElementSummernote('{{$file->path}}', '{{$file->name}}', '{{$file->type}}')">
                                                                @if(stripos($file->type,"image") !== false)
                                                                    <img src="{{ asset($file->path) }}" class="img-thumbnail">
                                                                @else
                                                                    @if(strpos($file->type,"audio") !== false)
                                                                        <img src="{{ asset('img/audioicon.png') }}" class="img-thumbnail">
                                                                    @else
                                                                        <img src="{{ asset('img/fileicon.png') }}" class="img-thumbnail">
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    @if($totMedia > 1)
                                                        <div class="row">
                                                            <div class="col-12" id="paginationMedia">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- modal for file - END -->

                        <div class="row">
                            <div class="form-group col-md-10">
                                <div id="summernote" name="content"></div>
                            </div>
                            <div class="col-md-2">
                                <div class="row mb-4">
                                    <label>Cover</label>
                                    <a href="javascript:void(0)"><img src="{{ asset('img/cover.png') }}" id="coverImg" class="img-fluid" data-toggle="modal" data-target="#coverManager"></a>
                                    <input type="hidden" name="coverImg" id="coverImgHidden">
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-12 mt-2">
                                        <label class="mr-3">Pinged Top</label>
                                        <input id="pinged" name="pinged" type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-2">
                                        <label class="mr-3">Comments</label>
                                        <input id="comment" name="comment" type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-5">
                                        <label class="mr-4">Password</label>
                                        <input id="passwordChoose" name="passwordChoose" type="checkbox" data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-2">
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="password" name="password" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mt-3">
                                        <label class="mr-3" for="category">Category</label>
                                        <select class="form-control select2bs4" name="category">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @if($category->id==1) selected @endif>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- modal for cover image - START -->
                                <div class="modal fade" id="coverManager" tabindex="-1" aria-labelledby="coverManagerLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="coverManagerLabel">Manage Media</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <ul class="nav nav-tabs" id="myTab1" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <a class="nav-link active" id="manage-tab1" data-toggle="tab" href="#manage1" role="tab" aria-controls="manage" aria-selected="true">Manage Files</a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="myTabContent1">
                                                    <div class="tab-pane fade show active" id="manage1" role="tabpanel" aria-labelledby="manage-tab1">
                                                        <h5 class="mt-4 mb-4 text-center">Choose cover file!</h5>
                                                        <div class="row text-center" id="contentMedia1">
                                                            @foreach($media as $file)
                                                                <div class="col-6 col-md-2 mb-5" onclick="addCover('{{asset($file->path)}}', {{$file->id}})">
                                                                    @if(stripos($file->type,"image") !== false)
                                                                        <img src="{{ asset($file->path) }}" class="img-thumbnail">
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        @if($totMedia > 1)
                                                            <div class="row">
                                                                <div class="col-12" id="paginationCover">
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- modal for cover image - END -->

                        </div>
                    </div>
                    <!-- /.card-body -->
                </form>
            </div>
            <div class="col-md-1 mb-4 mt-4">
                <a href="{{ action('ProfileController@showUser') }}">
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
    <!-- summernote -->
    <script src="{{ asset('js/summernote/summernote-bs4.min.js')}}"></script>
    <!-- bootstrap-switch -->
    <script src="{{ asset('js/bootstrap-toggle/bootstrap-toggle.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('js/select2/select2.full.min.js') }}"></script>

    @if(Post::canUploadFile())
        <!-- dropzone -->
        <script src="{{ asset('js/dropzone/dropzone.min.js')}}"></script>
        <script src="{{ asset('js/bootstrap-pagination/pagination.min.js')}}"></script>
    @endif
@endsection

@section('script')
    <script>
        let Toast = "";
        @if(session()->has('errorProfile'))
            Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
                });

            Toast.fire({
                icon: 'error',
                title: '&nbsp;&nbsp;&nbsp;User not created!'
            })
        @endif

        @if(session()->has('saveProfile'))
            Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;User created!'
            })
        @endif

        $('#summernote').summernote({
            height: 700,
            focus: true,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'video']],
                ['view', ['fullscreen', 'help']],
            ],
        });

        @if(Post::canUploadFile())
            Dropzone.options.uploadZone = {
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
                url: "{{ action('PostController@uploadFile') }}",
                init: function() {
                    this.on("addedfile", function(file) {
                        if(!file.type.includes("image")){
                            if(!file.type.includes("audio")){
                                $( file.previewElement ).find( "img" ).attr("src","{{ asset('img/fileicon.png') }}");
                            }
                            else{
                                $( file.previewElement ).find( "img" ).attr("src","{{ asset('img/audioicon.png') }}");
                            }
                        }

                        file.previewElement.addEventListener("click", function(e) {
                            let media = (JSON.parse(file.xhr.response));

                            addElementSummernote(media.path, media.name, media.type);
                        });
                    });
                }
            };
        @endif

        function addElementSummernote(path, name, type){
            let origin = "{{ asset('') }}";

            let node = "";

            if(type.includes("image")){
                node = document.createElement("img");
                node.setAttribute('src', origin + path);
            }
            else{
                node = document.createElement("a");
                node.setAttribute('href', origin + path);
                node.setAttribute('title', "Download " + name);
                var link = document.createTextNode("Download " + name);
                node.appendChild(link);
            }

            $('#summernote').summernote('insertNode', node);
        }

        $('#paginationMedia').bootpag({
            total: {{$totMedia}},
            page: 1,
            maxVisible: 5,
            leaps: true,
            firstLastUse: true,
            first: '←',
            last: '→',
            wrapClass: 'pagination',
            activeClass: 'active',
            disabledClass: 'disabled',
            nextClass: 'next',
            prevClass: 'prev',
            lastClass: 'last',
            firstClass: 'first'
        }).on("page", function(event, num){
            $.post("{{ action('PostController@listFile') }}",
            {
                page: num,
                _token: "{{csrf_token()}}"
            },
            function(data){
                let media = data;
                let html = "";
                let origin = "{{ asset('') }}";

                for (let i = 0; i < media.length; i++) {
                    html = html +'<div class="col-6 col-md-2 mb-5" ' +
                    'onclick="addElementSummernote(\''+media[i].path+'\', \''+media[i].name+'\', \''+media[i].type+'\')"> ';
                        if(media[i].type.includes('image')){
                            html = html + '<img src="'+origin + media[i].path +'" class="img-thumbnail">';
                        }
                        else{
                            if(media[i].type.includes('audio')){
                                html = html + '<img src="'+origin + 'img/audioicon.png" class="img-thumbnail">';
                            }
                            else{
                                html = html + '<img src="'+origin + 'img/fileicon.png" class="img-thumbnail">';
                            }
                        }
                    html = html + '</div>';
                }

                $("#contentMedia").html(html);
            });
        });

        $('#paginationCover').bootpag({
            total: {{$totMedia}},
            page: 1,
            maxVisible: 5,
            leaps: true,
            firstLastUse: true,
            first: '←',
            last: '→',
            wrapClass: 'pagination',
            activeClass: 'active',
            disabledClass: 'disabled',
            nextClass: 'next',
            prevClass: 'prev',
            lastClass: 'last',
            firstClass: 'first'
        }).on("page", function(event, num){
            $.post("{{ action('PostController@listFile') }}",
            {
                page: num,
                _token: "{{csrf_token()}}"
            },
            function(data){
                let media = data;
                let html = "";
                let origin = "{{ asset('') }}";

                for (let i = 0; i < media.length; i++) {
                    html = html +'<div class="col-6 col-md-2 mb-5" ' +
                    'onclick="addCover(\''+origin + media[i].path+'\', \''+media[i].id+'\')"> ';
                        if(media[i].type.includes('image')){
                            html = html + '<img src="'+origin + media[i].path +'" class="img-thumbnail">';
                        }
                    html = html + '</div>';
                }

                $("#contentMedia").html(html);
            });
        });

        function addCover(path, id){
            $('#coverImg').attr('src', path);
            $('#coverImgHidden').val(id);
        }

        // summernote.focus
        $('#summernote').on('summernote.focus', function() {
            console.log('Editable area is focused');
        })

        $("#title").change(function() {
            if($("#slug").val() == ""){
                $("#slug").val($("#title").val());
                $("#slug").change();
            }
        });

        $("#slug").change(function() {
            $.post("{{ action('PostController@checkTitlePost') }}",
            {
                title: $("#slug").val(),
                _token: "{{csrf_token()}}"
            },function(data){
                    if(data > 0){
                        $("#slug").addClass('is-invalid');
                        Toast = Swal.mixin({
                            toast: true,
                            position: 'center',
                            showConfirmButton: false,
                            timer: 3000
                            });

                        Toast.fire({
                            icon: 'error',
                            title: '&nbsp;&nbsp;&nbsp;URL already used!'
                        });
                    }
                    else{
                        $("#slug").removeClass('is-invalid');
                    }
                }
            );
        });

        $('#passwordChoose').change(function() {
            if($(this).prop('checked')){
                $("#password").removeAttr("disabled");
                $("#password").prop("required", true);
                $("#password").focus();
            } else {
                $("#password").attr("disabled", "disabled");
                $("#password").prop("required", false);
            }
        });
    </script>
@endsection
