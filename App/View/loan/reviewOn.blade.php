<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>审核详情--审核中</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/mine.css?v=01" >
</head>
<body>
<div class="reviewOn">
	<h3 class="notice">@if($logData['status']==4)借款审核未通过！@else借款审核中，请您耐心等待！@endif</h3>
	<div class="conditions" style="position: relative;">
		<h4 class="pro-name"><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$logData['product_name']}}</h4>
		<div class="chart-img item-flex">
            <div id="doughnutChart" class="chart"></div>
            <ul class="chart-list">
                <li class="item-flex"><h5>借款</h5><i>{{$logData['apply_amt']/100}}元</i></li>
                <li class="item-flex"><h5>借款期限 </h5><i>{{$logData['loan_period']}}@if($logData['loan_type']==3)月@else日@endif</i></li>
                <li class="item-flex"><h5>@if($logData['loan_type']==3)月@else日@endif利率 </h5><i>{{$logData['loan_rate']}}%</i></li>
            </ul>
        </div>
	</div>
	<div class="item-container conditions">
		<h4 class="pro-name">还款说明</h4>
		<ul class="statement-tips">
			<p>还款方式</p>
			<li>{{$repaymentTypeData[ $logData['loan_type'] ]}}</li>
			<p>提前还款</p>
			<li>费率{{$product['loanTypes'][0]['confPreRate']}}%*剩余本金</li>
			<p>逾期说明</p>
			<li>逾期罚息=当期应还金额*{{$product['loanTypes'][0]['confLateRate']}}%*逾期天数</li>
		</ul>
	</div>
</div>
   
<script src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/jquery.drawDoughnutChart.js"></script>
<script>
$(function(){
  $("#doughnutChart").drawDoughnutChart([
    { title: "借款 ",value: {{$logData['apply_amt']/100}},  color: "#FEAD38" },
    { title: "借款期限",value: {{intval($logData['loan_period'])}},color: "#FF835A" },
    { title: "{{$logData['loan_type']==3 ? '月' : '日'}}利率",value: {{$product['loanTypes'][0]['confRate']}},color: "#F15B5C" },
  ]);
});
</script>
 </body>
</html>
