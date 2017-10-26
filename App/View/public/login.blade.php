<!DOCTYPE html>
<html lang="en" class="app">
<head>  
  <meta charset="utf-8" />
  <title>考拉商城管理后台</title>
  <meta name="description" content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <link rel="stylesheet" href="{{HOST}}/static/admin/css/bootstrap.css" type="text/css" />
  <link rel="stylesheet" href="{{HOST}}/static/admin/css/animate.css" type="text/css" />
  <link rel="stylesheet" href="{{HOST}}/static/admin/css/app.css" type="text/css" />
  <!--<link rel="stylesheet" href="static/admin/css/style.css">-->
    <!--[if lt IE 9]>
    <script src="{{HOST}}/static/admin/js/ie/html5shiv.js"></script>
    <script src="{{HOST}}/static/admin/js/ie/respond.min.js"></script>
    <script src="{{HOST}}/static/admin/js/ie/excanvas.js"></script>
  <![endif]-->
</head>
<body class="bg-info dker">
  <section id="content" class="m-t-lg wrapper-md animated fadeInUp">    
    <div class="container aside-xl">
      <section class="m-b-lg">
        <header class="wrapper text-center">
          <strong>考拉后台</strong>
        </header>
        <form action="/admin/public/login" data-validate="parsley" method='post'>
          <div class="form-group">
            <input type="text" placeholder="登陆账号" data-required="true" name='username' class="form-control rounded input-lg text-center no-border">
          </div>
          <div class="form-group">
            <input type="password" placeholder="登陆密码" data-required="true" name='password' class="form-control rounded input-lg text-center no-border">
          </div>
          <button type="submit" class="btn btn-lg btn-warning lt b-white b-2x btn-block btn-rounded"><i class="icon-arrow-right pull-right"></i><span class="">登录</span></button>
        </form>
      </section>
      @if(!empty($errMsg))
      <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <i class="fa fa-ban-circle"></i><strong>{{$errMsg}}</strong>
      </div>
      @endif
    </div>
  </section>
  <!-- footer -->
  <!--<footer id="footer">-->
  <!--  <div class="text-center padder">-->
  <!--    <p>-->
  <!--      <small>小冬树的夏天<br>&copy; 2016</small>-->
  <!--    </p>-->
  <!--  </div>-->
  <!--</footer>-->
  <!-- / footer -->
  <script src="{{HOST}}/static/admin/js/jquery.min.js"></script>
  <script src="{{HOST}}/static/admin/js/main.js"></script> <!-- Resource jQuery -->
  <!-- Bootstrap -->
  <script src="{{HOST}}/static/admin/js/bootstrap.js"></script>
  <!-- App -->
  <script src="{{HOST}}/static/admin/js/app.js"></script>
  <script src="{{HOST}}/static/admin/js/app.plugin.js"></script>
</body>
</html>