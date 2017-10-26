<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>我</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/mine.css?v=01" >
</head>
<body>
<div class="mine">
	<div class="mine-top">
		<div class="avatar"><img src="{{$info['store_logo']}}"></div>
		<dl>
			<dt>{{$info['owner_name']}}</dt>
			<dd>{{$info['tel']}}</dd>
		</dl>
	</div>
	<div class="infos-container spec-con">
			<ul>
				<a href="/weixin/storeExtra/quotaManage"><li class="item-direction item-flex"><h4>可借款额度</h4><span>{{$productList['lmtAble']}}元</span></li></a>
			</ul>
		</div>
	<div class="infos-container">
		<ul>
			<li class="item-direction"><a class='item-flex' href="{{B_URL . 'Mall/Bill/myDraw'}}"><h4>账户余额</h4><span>{{$balance}}元</span></a></li>
			<li class="item-direction repayment-home"><a class='item-flex' href="/weixin/repayment/home"><h4>还款管理</h4><span>待还总金额: {{$repaymentMoney}}元</span></a></li>
			<li class="item-direction"><a class='item-flex' href="/weixin/storeBorrow/loanLog"><h4>借款记录</h4><span></span></a></li>
		</ul>
	</div>
	<div class="infos-container">
		<ul>
			<li class="item-direction"><a href="/weixin/account/guidance" class="item-flex"><h4>借款攻略</h4><span></span></a></li>
			<li class="item-direction"><a href="/weixin/feedback/add" class="item-flex"><h4>反馈意见</h4><span></span></a></li>
		</ul>
	</div>
	<div class="tabbar operating">
		<ul class="item-flex">
			<li>
				<a href="/weixin/product/getProductList">
					<i class="loan-icon"></i>
					<h5>借款</h5>
				</a>
			</li>
			<li class="active">
				<a href="javascript:;">
					<i class="mine-icon"></i>
					<h5>我</h5>
				</a>
			</li>
		</ul>
	</div>
</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script>
	$('.repayment-home').click(function () {
		location.href = '/weixin/repayment/home';
    })
</script>
</html>
