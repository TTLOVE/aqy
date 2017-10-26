<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>审核详情--已放款</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/mine.css?v=01" >
</head>
<body>
<div class="reviewComplete">
	<div class="conditions">
		<h4 class="pro-name"><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$logData['product_name']}}</h4>
		<div class="chart-img item-flex">
            <div id="doughnutChart" class="chart"></div>
            <ul class="chart-list">
                <li class="item-flex"><h5>借款</h5><i>{{$logData['apply_amt']/100}}元</i></li>
                <li class="item-flex"><h5>借款期限 </h5><i>{{$logData['loan_period']}}@if($logData['loan_type']==3)月@else日@endif</i></li>
                <li class="item-flex"><h5>@if($logData['loan_type']==3)月@else日@endif利率 </h5><i>{{$logData['loan_rate']}}%</i></li>
            </ul>
        </div>
        <div class="repayment-statement">
    		<ul class="statement-tips">
                <p>还款方式</p>
                <li>{{$repaymentTypeData[ $logData['loan_type'] ]}}</li>
                <p>提前还款</p>
                <li>费率{{$product['loanTypes'][0]['confPreRate']}}%*剩余本金</li>
                <p>逾期说明</p>
                <li>逾期罚息=当期应还金额*{{$product['loanTypes'][0]['confLateRate']}}%*逾期天数</li>
    		</ul>
        </div>
        <a href="javascript:;" class="toggle-btn"></a>
	</div>
    <div class="repayment-list">
        @foreach($returnList as $key => $return)
            <div class="repayment-box">
                <div class="title item-flex">
                    <h4 class="pro-name"><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$logData['product_name']}}({{$return['period']}}期)</h4>
                    <!--<p>已开启余额自动还款</p>-->
                </div>
                <div class="repayment-infos item-flex">
                    <!-- 逾期 -->
                    @if($return['repayment_status'] == 3)
                    <div class="repayment-infos-left">
                        <h5><i>¥</i>{{$return['money']}}</h5>
                        <p>+¥{{$return['return_fee']}}</p>
                    </div>
                    <div class="repayment-infos-right item-flex">
                       <div class="expired">
                            <h5 class="time-out">已逾期{{$return['days']}}天</h5>
                            <p>{{date("Y-m-d", $return['repayment_time'])}}还款</p>
                        </div>
                        <a href="/weixin/repayment/way?repayment_id={{$return['log_id']}}&repay_tag=01" class="go-repayment">结清欠款</a>
                    </div>
                    @elseif($return['repayment_status'] == 2)
                    <!-- 已还款 -->
                    <div class="repayment-infos-left">
                        <h5><i>¥</i>{{($return['money']+$return['return_fee'])}}</h5>
                        <p>每@if($logData['loan_type']==3)月@else日@endif还款</p>
                    </div>
                    <div class="repayment-infos-right item-flex">
                       <div class="expired">
                            <h5>{{date("Y-m-d", $return['real_pay_time'])}}还款</h5>
                            <p>已正常还款</p>
                        </div>
                        <a href="javascript:;" class="go-repayment">正常还款</a>
                    </div>
                    @elseif($return['repayment_status'] == 1)
                    <!-- 未逾期 -->
                    <div class="repayment-infos-left">
                        <h5><i>¥</i>{{($return['money']+$return['return_fee'])}}</h5>
                        <p>每@if($logData['loan_type']==3)月@else日@endif还款</p>
                    </div>
                    <div class="repayment-infos-right item-flex">
                       <div class="expired">
                            <h5>{{date("Y-m-d", $return['repayment_time'])}}还款</h5>
                            @if ($return['days']==0)
                            <p>今天是还款日</p>
                            @else
                            <p>距离还款日还有{{$return['days']}}天</p>
                            @endif
                        </div>
                        <a href="/weixin/repayment/way?repayment_id={{$return['log_id']}}&repay_tag=01" class="go-repayment">结清欠款</a>
                    </div>
                    @endif
                </div>
                <!--<h3 class="notice">您有还款单已逾期，每日罚息0.5%，请尽快还款！</h3>-->
            </div>
        @endforeach
    </div>  
</div>
</body>
<script src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/jquery.drawDoughnutChart.js"></script>
<script>
$(function(){
    $("#doughnutChart").drawDoughnutChart([
        { title: "借款 ",value: {{$logData['apply_amt']/100}},  color: "#FEAD38" },
        { title: "借款期限",value: {{intval($logData['loan_period'])}},color: "#FF835A" },
        { title: "{{$logData['loan_type']==3 ? '月' : '日'}}利率",value: {{$product['loanTypes'][0]['confRate']}},color: "#F15B5C" },
    ]);

    // toggle
    $('.toggle-btn').click(function() {
      $('.repayment-statement').slideToggle();
      $(this).toggleClass('toggle-on')
    })
});
</script>
</html>
