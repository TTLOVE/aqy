<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>运营商认证</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/mine.css" >
</head>
<body>
<div class="certification">
	<div class="infos-container">
		<a href="{{$modifyUrl}}" class="item-direction item-flex"><h4>手机<i>@if(!empty($info)){{$info['mobile']}}@endif</i></h4><span>更改</span></a>
	</div>
</div>
</body>
</html>