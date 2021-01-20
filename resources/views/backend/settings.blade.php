@extends('backend.app')

@section('title')
  <title>{{ $config->nameSite }} - Settings Manager</title>
@endsection

@section('css')
    <!-- bootstrap-switch -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap-toggle/bootstrap-toggle.min.css') }}">
@endsection

@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>SETTINGS MANAGER</h1>
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
                <!-- /.card-header -->
                <div class="card-header">
                    <h3 class="card-title">Info settings (leave the image fields blank if you don't want to change them)</h3>
                </div>
                <!-- form start -->
                <form action="{{ action('SettingsController@saveInfoSettings') }}" method="POST" enctype="multipart/form-data" id="saveInfoSettings">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-2 col-xs-12">
                                <label for="name">Name Site</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter name site" required value="{{ $config->nameSite }}">
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="logo">Logo Site</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" id="logo" name="logo" accept=".png,.jpg,.jpeg">
                                        <label class="custom-file-label" for="logo">Choose logo site</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="icon">Icon Site (550*550 or square)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('icon') is-invalid @enderror" id="icon" name="icon" accept=".png,.jpg,.jpeg">
                                        <label class="custom-file-label" for="icon">Choose icon site</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-3 col-xs-12">
                                <label for="favicon">Favicon Site (48*48 .ico)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input @error('favicon') is-invalid @enderror" id="favicon" name="favicon" accept=".ico">
                                        <label class="custom-file-label" for="favicon">Choose favicon site</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-1 col-xs-12">
                                <label for="validationEmail">Email Validation</label>
                                <br>
                                <input type="checkbox" name="validationEmail" id="validationEmail" @if($config->emailValidation == 1) checked @endif data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-2 col-xs-12">
                                <label for="cookieBanner">Cookie Banner</label>
                                <br>
                                <input type="checkbox" name="cookieBanner" id="cookieBanner" @if($config->cookieBanner == 1) checked @endif data-toggle="toggle" data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        {!! ReCaptcha::printField() !!}
                        <button type="submit" class="btn btn-success col-1">SAVE</button>
                    </div>
                </form>
            </div>

            <div class="card card-primary">
                <!-- /.card-header -->
                <div class="card-header">
                    <h3 class="card-title">Recaptcha settings (to use reCaptcha register on google here <a href="https://www.google.com/recaptcha/admin"><b style="color:black">reCaptcha</b></a>)</h3>
                </div>
                <!-- form start -->
                <form action="{{ action('SettingsController@saveRecaptchaSettings') }}" method="POST" enctype="multipart/form-data" id="saveRecaptchaSettings">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-2 col-xs-12">
                                <label for="reCaptchaValue">reCaptcha Activated</label>
                                <br>
                                <input id="reCaptchaValue" name="reCaptchaValue" type="checkbox" data-toggle="toggle" @if($config->recaptcha == 1) checked @endif data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label for="reCaptchaPublic">reCaptcha Site</label>
                                <input type="text" class="form-control @error('reCaptchaPublic') is-invalid @enderror" id="reCaptchaPublic" name="reCaptchaPublic" placeholder="Enter reCapatcha site" @if($config->recaptchaSite == "" || $config->recaptcha==0 ) disabled @endif value="{{ $config->recaptchaSite }}">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label for="reCaptchaPrivate">reCaptcha Private</label>
                                <input type="text" class="form-control @error('reCaptchaPrivate') is-invalid @enderror" id="reCaptchaPrivate" name="reCaptchaPrivate" placeholder="Enter reCapatcha site" @if($config->recaptchaSecret == "" || $config->recaptcha==0) disabled @endif value="{{ $config->recaptchaSecret }}">
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        {!! ReCaptcha::printField() !!}
                        <button type="submit" class="btn btn-success col-1">SAVE</button>
                    </div>
                </form>
            </div>

            <div class="card card-primary">
                <!-- /.card-header -->
                <div class="card-header">
                    <h3 class="card-title">Google Analytics (GA4) settings</h3>
                </div>
                <!-- form start -->
                <form action="{{ action('SettingsController@saveAnalyticsSettings') }}" method="POST" enctype="multipart/form-data" id="saveAnalyticsSettings">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-2 col-xs-12">
                                <label for="analytics">GA4 Activated</label>
                                <br>
                                <input id="analytics" name="analytics" type="checkbox" data-toggle="toggle" @if($config->analytics == 1) checked @endif data-on="ON" data-off="OFF" data-onstyle="success" data-offstyle="danger">
                            </div>
                            <div class="form-group col-md-4 col-xs-12">
                                <label for="analyticsCode">GA4 Code</label>
                                <textarea id="analyticsCode" name="analyticsCode" class="form-control @error('analyticsCode') is-invalid @enderror" placeholder="Enter Google Analytics code" @if($config->analyticsCode == "" || $config->analytics==0 ) disabled @endif rows="4">{{ $config->analyticsCode }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        {!! ReCaptcha::printField() !!}
                        <button type="submit" class="btn btn-success col-1">SAVE</button>
                    </div>
                </form>
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
    <!-- bootstrap-switch -->
    <script src="{{ asset('js/bootstrap-toggle/bootstrap-toggle.min.js') }}"></script>
@endsection

@section('script')
    <script>
        @if(session()->has('savedSettings'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'success',
                title: '&nbsp;&nbsp;&nbsp;Settings saved'
            })
        @endif

        @if(session()->has('errorSettings'))
            const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000
            });

            Toast.fire({
                icon: 'error',
                title: '&nbsp;&nbsp;&nbsp;Settings not saved!'
            })
        @endif

        $(function () {
            bsCustomFileInput.init();
        });

        $(function() {
            $('#reCaptchaValue').change(function() {
                if($(this).prop('checked')){
                    $("#reCaptchaPublic").removeAttr("disabled");
                    $("#reCaptchaPublic").prop("required", true);
                    $("#reCaptchaPublic").focus();

                    $("#reCaptchaPrivate").removeAttr("disabled");
                    $("#reCaptchaPrivate").prop("required", true);
                } else {
                    $("#reCaptchaPublic").attr("disabled", "disabled");
                    $("#reCaptchaPrivate").attr("disabled", "disabled");

                    $("#reCaptchaPublic").prop("required", false);
                    $("#reCaptchaPrivate").prop("required", false);
                }
            })

            $('#analytics').change(function() {
                if($(this).prop('checked')){
                    $("#analyticsCode").removeAttr("disabled");
                    $("#analyticsCode").prop("required", true);
                    $("#analyticsCode").focus();
                } else {
                    $("#analyticsCode").attr("disabled", "disabled");
                    $("#analyticsCode").prop("required", false);
                }
            })
        })
    </script>
@endsection
