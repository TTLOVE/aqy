<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>借款</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css?v=01" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css?v=01" >
</head>
<body>
	<div class="loan">
		@if ($isOverdue)
		<h4 class="notice">您有还款单已逾期，每日会计算罚息，请尽快还款！</h4>
		@endif
		<div class="loan-container">
			<div class="quota">
				<h5>可用贷款额度</h5>
				<p>¥{{$list['lmtAble']}}</p>
			</div>
			<ul class="item-flex">
				<li>
					<a href="/weixin/storeExtra/quotaManage">
						<h5>额度管理</h5>
						<p>总额度  {{$list['lmtTotal']}}元</p>
					</a>
				</li>
				<li>
					<a href="/weixin/repayment/home">
						<h5>还款管理</h5>
						<p>待还  {{$repaymentMoney}}元</p>
					</a>
				</li>
			</ul>
		</div>
		<div class="loan-list">
			@if (isset($list['productList']))
				@foreach ($list['productList'] as $product)
			        <div class="items-list">
			        	<div class="loan-box">
							<a href="/weixin/product/getInfo?id={{$product['productNo']}}">
			        			<h4 class="pro-name"><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$product['prodName']}}</h4>
			        			<div class="loan-infos">
			        				<div class="loan-infos-left">
										<h5>{{$product['prodMinAmt']/100}}～{{$product['prodMaxAmt']/100}}</h5>
			        					<p>额度范围（元）</p>							
			        				</div>
			        				<div class="loan-infos-mid">
			        					<h5>{{$product['loanTypes'][0]['confMinPeriod']}}-{{$product['loanTypes'][0]['confMaxPeriod']}}</h5>
			        					<p>期限（@if($product['loanTypes'][0]['confReMethod']=='03')月@else日@endif）</p>
			        				</div>
			        				<div class="loan-infos-right">
			        					<h5>{{$product['loanTypes'][0]['confRate']}}%</h5>
			        					<p>@if($product['loanTypes'][0]['confReMethod']=='03')月@else日@endif利率</p>
			        				</div>
			        			</div>
			        			<!--<p class="loan-infos-tips">提供流水提升额度，可绑定pos机还款</p>-->
			        		</a>
			        	</div>
			        </div>
				@endforeach
			@endif
			<div class="advertising-container">
				<img src="{{HOST}}/static/weixin/common/images/loanBanner.jpg">
			</div>
		</div>
		<div class="tabbar operating">
			<ul class="item-flex">
				<li class="active">
					<a href="javascript:;">
						<i class="loan-icon"></i>
						<h5>借款</h5>
					</a>
				</li>
				<li>
					<a href="/weixin/account/info">
						<i class="mine-icon"></i>
						<h5>我</h5>
					</a>
				</li>
			</ul>
		</div>
	</div>
</body>
</html>
