<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>还款管理</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css?v=01" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/repayment.css?v=01">
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/select.css?v=01" >
</head>
<body>
	<div class="repayment">
		<div class="details-container">
			<ul class="item-flex">
				<li>
					<a href="javascript:;">
						<h5>还应还款总额</h5>
						<p>¥{{$repaymentMoney}}</p>
					</a>
				</li>
				<li>
					<a href="javascript:;">
						<h5>账户余额</h5>
						<p>¥{{$balance}}</p>
					</a>
				</li>
			</ul>
		</div>
        <div id="selectCtn"></div>
        @if (empty($repaymentList))
		    <div class="repayment-list">
		    	<div class="item-direction payway">
		    		<a href="/weixin/repayment/history"><h4>还款历史</h4></a>
		    	</div>
                <div class="time-select">
                    <div class=" item-flex"><span>{{$dateInfo['year']}}年{{$dateInfo['month']}}月</span><i></i></div>
                </div>
		        <p class="no-data">暂无还款~</p>
		    </div>	
        @else
		    <div class="repayment-list">
		    	<div class="item-direction payway">
		    		<a href="/weixin/repayment/history"><h4>还款历史</h4></a>
		    	</div>
		        <div class="time-select">
		        	<div class=" item-flex"><span>{{$dateInfo['year']}}年{{$dateInfo['month']}}月</span><i></i></div>
		        </div>
                @foreach ($repaymentList as $repayment)
		    	    <div class="repayment-box">
		    	    	<div class="title item-flex">
		    	    		<h4><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$repayment['product_name']}}（{{$repayment['period']}}期）</h4>
		    	    		<p><a href="/weixin/storeBorrow/reviewOn?log_id={{$repayment['borrow_money_log_id']}}">还款计划&nbsp;></a></p>
		    	    	</div>
		    	    	<div class="repayment-infos item-flex">
		    	    		<div class="repayment-infos-left">
		    	    			<h5><i>¥</i>{{$repayment['money']}}</h5>
                                @if ($repayment['return_fee']>0.00 && $repayment['repayment_status']==3)
                                    <p>+¥{{$repayment['return_fee']}}</p>
                                @else 
                                    <p>每@if($borrowInfo['loan_type']==3)月@else日@endif还款</p>
                                @endif
		    	    		</div>
		    	    		<div class="repayment-infos-right item-flex">
		    	    		   <div class="expired">
                               @if ($repayment['repayment_status']==3)
                                    <h5 class="time-out">已逾期{{$repayment['days']}}天</h5>
		    	    				<p>{{date("Y.m.d", $repayment['repayment_time'])}}还款</p>
                               @else
		    					    <h5>{{date("Y.m.d", $repayment['repayment_time'])}}还款</h5>
                                    @if ($repayment['days']==0)
		    					        <p>今天是还款日</p>
                                    @else
		    					        <p>距离还款日还有{{$repayment['days']}}天</p>
                                    @endif
                               @endif
		    	    			</div>
                                @if ($repayment['repayment_status']==3 || ($repayment['repayment_status']==1 && $repayment['days']==0))
                                <div class="btn-group">
		    	    			    <a href="/weixin/repayment/way?repayment_id={{$repayment['log_id']}}&repay_tag=01" class="repayment-btn current-btn">还款当期</a>
		    	    			    <a href="/weixin/repayment/way?repayment_id={{$repayment['log_id']}}&repay_tag=02" class="repayment-btn settle-btn">结清欠款</a>
                                </div>
                                @elseif ($repayment['repayment_status']==2)
                                <div class="btn-group">
		    	    			    <a href="/weixin/repayment/way?repayment_id={{$repayment['log_id']}}" class="repayment-btn settle-btn">还款中</a>
                                </div>
                                @else
                                <div class="btn-group">
		    	    			    <a href="/weixin/repayment/way?repayment_id={{$repayment['log_id']}}&repay_tag=02" class="repayment-btn settle-btn">结清欠款</a>
                                </div>
                                @endif
		    	    		</div>
		    	    	</div>
		    	    </div>
				    <!--<h4 class="notice">您有还款单已逾期，每日罚息0.5%，请尽快还款！</h4>-->
                @endforeach
		    </div>	
        @endif
		</div>	
	</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/transform.js?v=01"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.js?v=01"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.select.multiple.js?v=01"></script>
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
                window.location.href="/weixin/repayment/home?year="+text1.name+"&month="+text2.name;
	        }
	    })

	$('.time-select').click(function(){
		time.show();
	})
})
</script>
</html>
