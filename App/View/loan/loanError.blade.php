<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>出错了哦~</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css">
</head>
<body>
	<div class="loanSuccess">
		<h5>错误消息</h5>
        <p>{{$ErrorMsg}}</p>
        <button class="large-btn">返回首页</button>
	</div>

</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script>
    $('.large-btn').on('click',function () {
        location.href = '/weixin/product/getProductList';
    });
</script>
</html>
