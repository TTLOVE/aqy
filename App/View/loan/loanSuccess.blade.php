<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>借款成功</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css?v=01">
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css">
</head>
<body>
	<div class="loanSuccess">
		<i class="loan-review-icon"></i>
		<h4>已申请，借款审核中</h4>
        <button class="large-btn">完 成</button>
	</div>
    <div class="loan-suc-list">
        <ul>
            <li class="item-flex"><h4>借款金额</h4><p>{{$borrowInfo['apply_amt']/100}}元</p></li>
            <li class="item-flex"><h4>实际到账</h4><p>{{$borrowInfo['apply_amt']/100}}元</p></li>
            <!--<li class="item-flex"><h4>每月还款</h4><p>122元</p></li>-->
            <li class="item-flex"><h4>借款期限</h4><p>{{$borrowInfo['loan_period']}}@if($borrowInfo['loan_type']==3)月@else日@endif</p></li>
            <li class="item-flex"><h4>借款利率</h4><p>{{$borrowInfo['loan_rate']}}%</p></li>
            <li class="item-flex"><h4>服务费率</h4><p>{{$borrowInfo['mng_rate']}}%</p></li>
            <li class="item-flex"><h4>到账途径</h4><p>银行卡</p></li>
        </ul>
    </div>
    <div class="tips">
        预计1-2小时通过审核，30分钟内入账，请耐心等待。
    </div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
@if (isset($borrowInfo['log_id'])) 
<script>
    $('.large-btn').on('click',function () {
        location.href = '/weixin/storeBorrow/reviewOn?log_id={{$borrowInfo['log_id']}}';
    })
</script>
@else
<script>
    $('.large-btn').on('click',function () {
        location.href = '/weixin/product/getProductList';
    })
</script>
@endif
</html>
