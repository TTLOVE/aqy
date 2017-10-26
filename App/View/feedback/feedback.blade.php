<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>意见反馈</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/mine.css" >
</head>
<body>
<div class="feedback">
    <h4>意见与建议</h4>
    <form action method="post">
        <input type="hidden" name="store_id" value="{{$store_id}}">
    <div class="wrap-main" >        
        <textarea name="comment" class="feedback-input" required="required" oninvalid="setCustomValidity('请填写内容哦~')" oninput="setCustomValidity('')" placeholder="请输入您宝贵的意见～"></textarea>
    </div>
        <button type="submit" class="large-btn">提 交</button>
    </form>
</div>
</body>
</html>