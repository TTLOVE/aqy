<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>申请富友账号</title>
</head>
<body>
    <form id="fuyou_submit" name="form" action="{{$requestData['url']}}" method="post" style="display:none;">
        <textarea name="reqStr">{{$requestData['reqStr']}}</textarea>
        <input type="text" name="sign" value="{{$requestData['sign']}}"/>
        <input type="submit" class="sub-btn" name="submit"/>
    </form>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script type="text/javascript">
    $(function(){
        $('.sub-btn').click()
    });
</script>
</html>
