<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>产品详情</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/select.css" >
</head>
<body>
	<div class="productDetail">
		<input type='hidden' id='loan_id' name='id' value='{{$id}}'>
		<input type='hidden' id='minMoney' value='{{$detail["prodMinAmt"]}}'>
		<input type='hidden' id='maxMoney' value='{{$detail["prodMaxAmt"]}}'>
		<input type='hidden' id='store_id' value='{{$store_id}}'>


		<div class="range-info">
			<h4 class="pro-name"><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$detail['prodName']}}</h4>
			<ul class="item-flex fill-info">
				<li>
					<div class="input-item item-flex">
						<label>金额</label><input type="number" class="loan-amount" name="borrow_money" placeholder="{{$plan['apply_amt']}}" value="{{$plan['apply_amt']}}"><i>元</i>
					</div>
					<p>额度范围&nbsp;:&nbsp;{{$detail['prodMinAmt']}}～{{$detail['prodMaxAmt']}}</p>
				</li>
				<li class="limit">
					<div class="input-item item-flex">
						<label>期限</label><input type="text" readonly="readonly" class="loan-time" name="borrow_time" data-value="{{$plan['loan_period']}}" value="{{$plan['loan_period']}}{{$loanType}}"><em></em>
					</div>
					<p>限期范围&nbsp;:&nbsp;{{$detail['loanTypes']['confMinPeriod']}}～{{$detail['loanTypes']['confMaxPeriod']}}{{$loanType}}</p>
				</li>
			</ul>			
			<div class="chart-img item-flex">
            <div id="doughnutChart" class="chart"></div>
	            <ul class="chart-list">
	                <li class="item-flex"><h5>借款</h5><i class="bo-money">{{$plan['apply_amt']}}元</i></li>
	                <li class="item-flex"><h5>借款期限 </h5><i class="bo-time">{{$plan['loan_period']}}{{$loanType}}</i></li>
	                <li class="item-flex"><h5>总利息 </h5><i class="bo-interest">{{$repaymentInterest}}元</i></li>
	                <li class="item-flex"><h5>{{$loanType}}利率 </h5><i class="bo-rate">{{$detail['loanTypes']['confRate']}}%</i></li>
	            </ul>
	        </div>
            <!--
			<div class="tips">
			 费率说明，3月、6月、12月对应服务费率为1.7%、1.4%、1.2%，对应月利率为：1.2%、1%、0.8%。
			</div>
            -->
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
		<div class="item-container">
			<h4 class="pro-name">所需资料</h4>
			<ul class="way-list">
				@if(!empty($isAuth))
				<li><a href="javascript:;" class="item-flex"><h4>个人身份认证</h4><i class="item-selected"></i></a></li>
				@else
                <li><a href="/weixin/storeExtra/storeProve" class="item-flex"><h4>个人身份认证</h4> 完善资料&nbsp;&nbsp;&nbsp;> </a></li>
                @endif
                <li>
                    <a href="javascript:;" class="item-flex"><h4>商户60天交易流水</h4>
                    @if(!empty($isAuth) && $isAuth['open_stream'] == 1)
                    <i class="item-selected"></i>
                    @endif
                    </a>
                </li>
                <li><a href="javascript:;" class="item-flex"><h4>运营商认证</h4><i class="item-selected"></i></a></li>
			</ul>
		</div>
		<div class="item-container conditions">
			<h4 class="pro-name">申请条件</h4>
			<ul>
				<li>1、大陆居民身份证</li>
				<li>2、年龄范围为18-55周岁</li>
			</ul>
		</div>
		<div class="item-container conditions">
			<h4  class="pro-name">还款说明</h4>
			<ul class="statement-tips">
				<p>还款方式</p>
				<li>{{$detail['loanTypes']['confReNm']}}，支持账户余额划扣还款</li>
				<p>提前还款</p>
				<li>费率{{$detail['loanTypes']['confPreRate']}}%*剩余本金</li>
				<p>逾期说明</p>
				<li>逾期罚息=当期应还金额*{{$detail['loanTypes']['confLateRate']}}%*逾期天数</li>
			</ul>
		</div>
		<div class="operating item-flex">
			<input type="checkbox" class="agreement-input">
			<h5>我已阅读并同意<a href="javascript:;">《考拉借款条约》</a></br><a href="javascript:;">《个人资料授权协议》</a></h5>
			<button class="loan-btn">申请借款</button>
		</div>
	</div>

    <form action="" method="post" id='fuyou_submit'>
    	<textarea name="reqStr"></textarea>
    	<input type="text" name="sign" id='sign' value="">
    </form>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/echarts.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/transform.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.select.js"></script>
<script src="{{HOST}}/static/weixin/common/js/jquery.drawDoughnutChart.js"></script>
<script type="text/javascript">
$(function(){
    // 显示数据
    var returnDataStr = '{{$returnData}}'.replace(/&quot;/g,'"');
    var returnData  = JSON.parse(returnDataStr);

    var confMinPeriod = {{$detail['loanTypes']['confMinPeriod']}};//最小期限
    var confMaxPeriod = {{$detail['loanTypes']['confMaxPeriod']}};//最大期限
    var loanType = '{{$loanType}}';//类型:月,日
    var options = [];//日期选项
    for(var i = confMinPeriod ;i <= confMaxPeriod ;i++ ){
        options.push({value: i+loanType,text: i+loanType});
    }

	// 期限
	var record = new AlloyTouch.Select({  
		options:options,
		selectedIndex: 0,
		change: function(item, index) {
		},
		complete: function(item, index) {
            $('.limit').find('input').val(item.value)
		    record.hide()
            ajaxForPretendBorrowMoney()
		}
	})

	$('.limit').click(function(){
		record.show();
	})

    reloadChart()

    function ajaxForPretendBorrowMoney() {
    	var loan_amount = parseInt($('.loan-amount').val());//借款金额
        var loan_id = $('#loan_id').val();//产品id
        var loan_time = parseInt($('.loan-time').val());
        var store_id = $('#store_id').val();
        var loan_type = "{{$detail['loanTypes']['confReMethod']}}";
        var loan_rate = {{$detail['loanTypes']['confRate']}};
        var mng_rate = {{$detail['loanTypes']['confMngRate']}};
        
        $.ajax({
    			url : '/weixin/storeBorrow/borrowMoney',
    			type : 'post',
    			data : {store_id:store_id,id:loan_id,borrow_money:loan_amount,borrow_time:loan_time,loan_type:loan_type,loan_rate:loan_rate,mng_rate:mng_rate},
    			dataType : 'json',
    			success : function(result){
    				if(result.status == 0){
                        var timeType = "{{$loanType}}";
                        $('.bo-money').html(result.data.borrow_money + '元')
                        $('.bo-time').html(result.data.borrow_time + timeType)
                        $('.bo-interest').html(result.data.interestMoney + '元')
                        reloadChart()
    				}else{
    					alert(result.msg);
    				}
    			}
            });
        return false;
    }

	// 数据表渲染
    function reloadChart() {
        $("#doughnutChart").html('');
	    $("#doughnutChart").drawDoughnutChart([
	    	{ title: "借款",value: 75,  color: "#FEAD38" },
	    	{ title: "借款期限",value: 10,color: "#FF835A" },
	    	{ title: "总利息",value: 9,color: "#5A596A" },
	    	{ title: "利率",value: 6,color: "#F15B5C" }
	    ]);
    }

	$('.iselect-toolbar-ok').on('click', function(){
        console.log('重新渲染列表')
	});
	
	$(".loan-amount").on('keypress',function(e) {  
	    var keycode = e.keyCode;                 
	    if(keycode=='13') {
	    	console.log('重新渲染列表')
	        e.preventDefault();  
	    }  
	}); 

    // 申请借款按钮点击效果
    $('.loan-btn').on('click',function () {
    	var loan_amount = parseInt($('.loan-amount').val());//借款金额
        var loan_id = $('#loan_id').val();//产品id
        var loan_time = parseInt($('.loan-time').val());
        var store_id = $('#store_id').val();

        if($('.agreement-input').is(':checked')) {
            $.ajax({
    			url : '/weixin/storeBorrow/apply',
    			type : 'post',
    			data : {store_id:store_id,id:loan_id,loanAmount:loan_amount,loanTime:loan_time},
    			dataType : 'json',
    			success : function(result){
    				if(result.status == true){
    					$('#fuyou_submit').attr('action',result.data.url);
    					$('#sign').val(result.data.sign);
    					$('#fuyou_submit').find('textarea').html(result.data.reqStr);
    					$('#fuyou_submit').submit();
    					return false;
    				}else{
    					alert(result.msg);
    				}
    			}
            });
        } else {
            alert("请先确认已读取对应协议信息")
        }
        return false;
    })
});


</script>
</html>
