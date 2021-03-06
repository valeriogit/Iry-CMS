<!DOCTYPE html>
<html>
<head>

    {!! CookieConsent::printCookie() !!}

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ $config->nameSite }} | Registration</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('css/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('css/adminlte/adminlte.min.css') }}">
  <!-- Sweet Alert -->
  <link rel="stylesheet" href="{{ asset('css/sweetalert2/bootstrap-4.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Important for backend & frontend -->
  <link rel="stylesheet" href="{{ asset('global/css/global.css') }}">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="{{ url('/') }}"><img src="{{ asset('img').$config->logoSite}}" class="img-fluid"></a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Register a new membership</p>

      <form action="{{ url('register') }}" method="post">
        {{ csrf_field() }}
        <div class="input-group mb-3">
          <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
          <div class="invalid-feedback">
            Username taken
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          <div class="invalid-feedback">
            Email already used
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

        <div class="input-group mb-3">
          <input type="password" id="password2" name="password2" onkeyup="CheckPassword()" class="form-control @error('password2') is-invalid @enderror" placeholder="Retype password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
          <div class="invalid-feedback">
            The password are different
          </div>
        </div>

        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="terms" name="terms" value="1" class="form-control @error('terms') is-invalid @enderror" required>
              <label for="terms">
               I agree to the <a href="#">terms</a>
              </label>
            </div>
            <div class="invalid-feedback">
              Accept out terms
            </div>
          </div>
          {!! ReCaptcha::printField() !!}
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" id="btnsubmit" class="btn btn-primary btn-block">Register</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center">
        <p>- OR -</p>
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i>
          Sign up using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i>
          Sign up using Google+
        </a>
      </div>

      <a href="{{ url('login') }}" class="text-center">I already have a membership</a>
    </div>
    <!-- /.form-box -->
  </div><!-- /.card -->
</div>

<script type="text/javascript">
  function CheckPassword(){
    let psw1 = $("#password");
    let psw2 = $("#password2");
    let btn = $('#btnsubmit');
    if(psw1.val() != psw2.val())
    {
      psw2.addClass("is-invalid");
      btn.prop("disabled",true);
    }
    else {
      psw2.removeClass("is-invalid");
      btn.prop("disabled",false)
    }
  }
</script>
<!-- /.register-box -->

<!-- jQuery -->
<script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('js/bootstrap/bootstrap.bundle.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('js/adminlte/adminlte.min.js') }}"></script>
<!-- Sweet Alert -->
<script src="{{ asset('js/sweetalert2/sweetalert2.min.js') }}"></script>

@if($mailSent != "")
  <script type="text/javascript">
  const Toast = Swal.mixin({
      toast: true,
      position: 'center',
      showConfirmButton: false,
      timer: 3000
    });

  Toast.fire({
        icon: 'success',
        title: '&nbsp;&nbsp;&nbsp;{!! $mailSent !!}'
      })
      </script>
@endif

{!! ReCaptcha::printJS() !!}

<!-- Important for backend & frontend -->
<link rel="stylesheet" href="{{ asset('global/js/global.js') }}">
</body>
</html>
