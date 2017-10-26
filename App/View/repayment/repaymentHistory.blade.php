<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>还款历史</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/repayment.css">
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/select.css" >
</head>
<body>
<div class="repaymentHistory">
	<div class="time-select" id="selectCtn">
		<div class=" item-flex"><span>{{$dateInfo['year']}}年{{$dateInfo['month']}}月</span><i></i></div>
	</div>
	<div class="repayment-list">
		<ul>
        @if (!empty($repaymentList))
            @foreach ($repaymentList as $repayment)
			    <li class="item-flex">
			    	<div class="list-left">
			    		<h5>{{date("Y.m.d", $repayment['real_pay_time'])}}还款</h5>
			    		<p>{{$repayment['product_name']}}（{{$repayment['period']}}期）{{$configMethod[ $repayment['conf_re_method'] ]}}</p>
			    	</div>
			    	<div class="list-right">
			    		<h5>¥{{($repayment['realpay_capital']+$repayment['realpay_interest']+$repayment['realpay_penalty']+$repayment['realpay_ahead_fee']+$repayment['realpay_mng_fee'])/100}}</h5>
			    		<p>{{$configStatus[ $repayment['repayment_status'] ]}}</p>
			    	</div>
			    </li>
            @endforeach
        @endif
		</ul>
	</div>
</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/transform.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.select.multiple.js"></script>
<script>
$(function(){
	// 1-12月
	var monthArr=[];
	for (var i = 1; i <= 12; i++) {
	    var fee = {name:i};
	    monthArr.push(fee);
	}

	var time = new AlloyTouch.MultipleSelect({
	        options: [
	            {name: '2017', list: monthArr}
	        ],
	        level: 2,
	        renderTo: "#selectCtn",
	        selectedIndex: [1, 2],
	        change: function (selectedIndex, text1, text2) {          
	        },
	        complete: function (selectedIndex, text1, text2) {
                window.location.href="/weixin/repayment/history?year="+text1.name+"&month="+text2.name;
	        }
	    })

	$('.time-select').click(function(){
		time.show();
	})
})
</script>
</html>
