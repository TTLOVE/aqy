<!DOCTYPE html>
<html lang="en" class="app">
<head>
    <meta charset="utf-8"/>
    <title>高利贷后台－总部</title>
    <meta name="description"
          content="app, web app, responsive, admin dashboard, admin, flat, flat ui, ui kit, off screen nav"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="stylesheet" href="{{HOST}}/static/admin/css/bootstrap.css" type="text/css"/>
    <link rel="stylesheet" href="{{HOST}}/static/admin/css/animate.css" type="text/css"/>
    <link rel="stylesheet" href="{{HOST}}/static/admin/css/font-awesome.min.css" type="text/css"/>
    <link rel="stylesheet" href="{{HOST}}/static/admin/css/simple-line-icons.css" type="text/css"/>
    <link rel="stylesheet" href="{{HOST}}/static/admin/css/font.css" type="text/css"/>
    <link rel="stylesheet" href="{{HOST}}/static/admin/css/app.css" type="text/css"/>
    <!--[if lt IE 9]>
    <script src="{{HOST}}/static/admin/js/ie/html5shiv.js"></script>
    <script src="{{HOST}}/static/admin/js/ie/respond.min.js"></script>
    <script src="{{HOST}}/static/admin/js/ie/excanvas.js"></script>
    <![endif]-->
</head>
<body class="">
<section class="vbox">
    <header class="bg-white-only header header-md navbar navbar-fixed-top-xs">
        <div class="navbar-header aside bg-info dk">
            <a class="btn btn-link visible-xs" data-toggle="class:nav-off-screen,open" data-target="#nav,html">
                <i class="icon-list"></i>
            </a>
            <a class="navbar-brand text-lt">
                <span class="hidden-nav-xs">借款后台-总部</span>
            </a>
            <a class="btn btn-link visible-xs" data-toggle="dropdown" data-target=".user">
                <i class="icon-settings"></i>
            </a>
        </div>
        <ul class="nav navbar-nav hidden-xs">
            <li>
                <a href="#nav,.navbar-header" data-toggle="class:nav-xs,nav-xs" class="text-muted">
                    <i class="fa fa-indent text"></i>
                    <i class="fa fa-dedent text-active"></i>
                </a>
            </li>
        </ul>
    </header>
    <section>
        <section class="hbox stretch">
            <!-- .aside -->
            <aside class="bg-black dk aside hidden-print" id="nav">
                <section class="vbox">
                    <section class="w-f-md scrollable">
                        <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0"
                             data-size="10px" data-railOpacity="0.2">
                            <!-- nav -->
                            <nav class="hidden-xs">
                                <ul class="nav" data-ride="collapse">
                                    <!--商城数据-->
                                    <li>
                                        <a href="#" class="auto">
                                            <span class="pull-right text-muted">
                                              <i class="fa fa-angle-left text"></i>
                                              <i class="fa fa-angle-down text-active"></i>
                                            </span>
                                            <i class="icon-screen-desktop icon">
                                            </i>
                                            <span>运营商</span>
                                        </a>
                                        <ul class="nav dk text-sm" style="display: none">
                                                <li >
                                                    <a href="/admin/operator/operatorList" class="auto">
                                                        <i class="fa fa-angle-right text-xs"></i>

                                                        <span>运营商列表</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/admin/operator/operatorAuditList" class="auto">
                                                        <i class="fa fa-angle-right text-xs"></i>

                                                        <span>入驻审核</span>
                                                    </a>
                                                </li>
                                        </ul>
                                    </li>
                                    <!--产品管理-->
                                    <li>
                                        <a href="#" class="auto">
                                            <span class="pull-right text-muted">
                                              <i class="fa fa-angle-left text"></i>
                                              <i class="fa fa-angle-down text-active"></i>
                                            </span>
                                            <i class="icon-screen-desktop icon">
                                            </i>
                                            <span>产品管理</span>
                                        </a>
                                        <ul class="nav dk text-sm" style="display: none">
                                            <li >
                                                <a href="/admin/product/show" class="auto">
                                                    <i class="fa fa-angle-right text-xs"></i>
                                                    <span>产品列表</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <!--借款管理-->
                                    <li>
                                        <a href="#" class="auto">
                                            <span class="pull-right text-muted">
                                              <i class="fa fa-angle-left text"></i>
                                              <i class="fa fa-angle-down text-active"></i>
                                            </span>
                                            <i class="icon-screen-desktop icon">
                                            </i>
                                            <span>借款管理</span>
                                        </a>
                                        <ul class="nav dk text-sm" style="display: none">
                                                <li >
                                                    <a href="/admin/borrow/list" class="auto">
                                                        <i class="fa fa-angle-right text-xs"></i>
                                                        <span>借款审核</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/admin/return/list" class="auto">
                                                        <i class="fa fa-angle-right text-xs"></i>
                                                        <span>还款商户</span>
                                                    </a>
                                                </li>
                                        </ul>
                                    </li>
                                    <li>
                                        <a href="/admin/public/logout" class="auto">
                                            <span class="pull-right text-muted">
                                              <i class="fa fa-angle-left text"></i>
                                              <i class="fa fa-angle-down text-active"></i>
                                            </span>
                                            <i class="icon-screen-desktop icon">
                                            </i>
                                            <span>退出 ()</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                            <!-- / nav -->
                        </div>
                    </section>
                </section>
            </aside>
            <!-- /.aside -->
