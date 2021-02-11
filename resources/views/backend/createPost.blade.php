@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Create Post</title>
@endsection

@section('css')
    <!-- summernote -->
    <link href="{{ asset('css/summernote/summernote-bs4.min.css')}}" rel="stylesheet">

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
                        @if(Post::canUploadFile())
                            <div class="row mb-3">
                                <div class="col-12">
                                    <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#exampleModal">
                                    <i class="fas fa-photo-video mr-2"></i> Manage media
                                    </button>
                                </div>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Manage Media</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <p>You can upload a file or choose one! The clicked one will automatically be inserted into the document.</p>
                                            <div id="uploadZone" class="dropzone"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="form-group col-12 col-md-10">
                                <div id="summernote" name="summernote"></div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer mb-2">
                        {!! ReCaptcha::printField() !!}
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            <div class="col-1 mb-4 mt-4">
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

    @if(Post::canUploadFile())
        <!-- dropzone -->
        <script src="{{ asset('js/dropzone/dropzone.min.js')}}"></script>
    @endif
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
                title: '&nbsp;&nbsp;&nbsp;User not created!'
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
                ['view', ['fullscreen', 'codeview','help']],
            ],
        });

        @if(Post::canUploadFile())
            Dropzone.options.uploadZone = {
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content') },
                url: "{{ action('PostController@uploadFile') }}",
                init: function() {
                    this.on("addedfile", function(file) {
                        file.previewElement.addEventListener("click", function(e) {
                            console.log(file.xhr.response);

                           /* let imgNode = document.createElement("img");
                            imgNode.setAttribute('src', file.xhr.response)*/
                            let imgNode = document.createElement("a");
                            imgNode.setAttribute('href', "file:///"+file.xhr.response);
                            imgNode.setAttribute('title', "file.xhr.response");
                            imgNode.setAttribute('download', true);
                            var link = document.createTextNode("This is link");
                            imgNode.appendChild(link);
                            $('#summernote').summernote('insertNode', imgNode);
                        });
                    });
                }
            };
        @endif
    </script>
@endsection
