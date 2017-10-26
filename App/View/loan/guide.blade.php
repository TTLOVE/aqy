<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>借款引导页</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css" >
</head>
<body>
	<div class="guide">
		<div class="banner-container">
			<img src="{{HOST}}/static/weixin/common/images/loanBanner.jpg">
		</div>
		<div class="application-container container">
			<h4>申请流程</h4>
			<ul class="process item-flex">
				<li>
					<p>1</p>
					<h5>补充资料</h5>
				</li>
				<li>
					<p>2</p>
					<h5>确认申请</h5>
				</li>
				<li>
					<p>3</p>
					<h5>借款审核</h5>
				</li>
				<li>
					<p>4</p>
					<h5>通过放款</h5>
				</li>
			</ul>
		</div>
		<div class="product-container container">
			<h4>产品特点</h4>
			<ul>
				<li class="item-box">
					<div class="icons-time icons"></div>
					<dl>
						<dt>借款期限</dt>
						<dd>0-45天，1月-12月灵活选择</dd>
					</dl>
				</li>
				<li class="item-box">
					<div class="icons-rate icons"></div>
					<dl>
						<dt>借款利率</dt>
						<dd>日利率低至0.04%，月利率低至0.83%</dd>
					</dl>
				</li>
				<li class="item-box">
					<div class="icons-repayment icons"></div>
					<dl>
						<dt>还款方式</dt>
						<dd>按日等额本息还款，按月等额本息还款</dd>
					</dl>
				</li>
			</ul>
		</div>
		<div class="tips">
			<p>
				温馨提示：申请借款过程中请务必本人操作并提供真实信息！获得
				借款后需按约定准时偿还，否则将影响你的信用记录并由你承担一
				切法律责任！
			</p>
			<p>
				点击“现在去借款”即代表你同意<a href="javascript:;">《个人资料授权协议》</a>
			</p>
		</div>
		<div class="operating">
			<a href="javascript:;" class="btn-normal go-loan">现在去借款</a>
		</div>
        <div class="model-box" id="model-box">
            <div class="mask"></div>
            <div class="pop-box">
                <h3>提示</h3>
                <a class="closed c-close" id="closed">×</a>
                <p id="notice-msg">需要开店成为认证商户才能借款哦</p>
                <!--<a href="javascript:;" class="btn-normal c-close" id="go-open-store">马上去开店</a>-->
                <a href="javascript:;" class="btn-normal" id="go-open-store">马上去开店</a>
            </div>
        </div>
        <input type="hidden" id="storeId" value="{{$storeId}}" >
	</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script>
$(function(){
    $(".go-loan").click(function(){
        var storeId = $('#storeId').val();
        if ( storeId==0 ) {
            $("#notice-msg").html("需要开店成为认证商户才能借款哦");
            document.getElementById('model-box').style.display = "block";
            $("#go-open-store").attr('href', '{{$goOpenStoreUrl}}');
        } else {
            var storeStatus = {{$storeInfo['state']}};
            var storeType = {{$appStoreInfo['user_type']}};
            // 商家状态,-100为没有对应商家信息，0待审核，1审核通过，2已关闭，3注册成功但未提交店铺资料，4审核不通过
            if ( storeStatus==0 ) {
                $("#notice-msg").html("您的店铺正在审核，审核通过才能借款哦");
                document.getElementById('go-open-store').style.display = "none";
                document.getElementById('model-box').style.display = "block";
            } else if ( storeStatus==2 ) {
                $("#notice-msg").html("您的店铺已被关闭，无法使用借款功能");
                document.getElementById('go-open-store').style.display = "none";
                document.getElementById('model-box').style.display = "block";
            } else if ( storeStatus==3 ) {
                $("#notice-msg").html("需要开店成为认证商户才能借款哦");
                document.getElementById('model-box').style.display = "block";
                $("#go-open-store").attr('href', '{{$goOpenStoreUrl}}');
            } else if ( storeStatus==4 ) {
                $("#notice-msg").html("您的店铺审核不通过，无法使用借款功能");
                document.getElementById('go-open-store').style.display = "none";
                document.getElementById('model-box').style.display = "block";
            } else if ( storeStatus==-100 ) {
                $("#notice-msg").html("您无法申请借款，只有店长才能借款");
                document.getElementById('go-open-store').style.display = "none";
                document.getElementById('model-box').style.display = "block";
            } else {
                window.location.href="/weixin/product/getProductList"
            }
        }
    })

    $('.c-close').click(function(){
        document.getElementById('model-box').style.display = "none";
    })
})
</script>
</html>
