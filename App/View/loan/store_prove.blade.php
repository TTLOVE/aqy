<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>个人身份证</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/select.css?v=01" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/layer.css" >
</head>
<body>
	<div class="infos">
		<div class="infos-container">
			<ul>
				<li class="item-flex user-name">
					<h4>本人姓名</h4>
					<span>{{$base['store_realname']}}</span>
				</li>
				<li class="item-flex idcard">
					<h4>本人身份证号码</h4>
					<span>{{$base['idcard_sn']}}</span>
				</li>
				<li class="item-flex">
					<h4>最高月还款额度</h4>
					<input type="number" class="return_money" placeholder="1000" value="@if(!empty($info)){{$info['return_money_max']}}@endif"><i>元</i>		
				</li>
				<li class="item-flex item-direction edu-info">
					<h4>教育程度</h4>
					<span>
					@if(!empty($info))
					{{$info['educational_status']}}
					@else
					请选择教育程度
					@endif
					
					</span>
				</li>
				<li class="item-flex item-direction insurance-info">
					<h4>现单位是否缴纳社保</h4>
					<span>
					@if(!empty($info))
					   {{$info['social_security']}}
					@else
					   请选择是否缴纳社保
					@endif
					</span>
				</li>
				<li class="item-flex item-direction car-info">
					<h4>车辆情况</h4>
					<span>
					@if(!empty($info))
					   {{$info['car_infomation']}}
					@else
					   请选择车辆情况
					@endif
					</span>
				</li>
				<li class="item-flex item-direction business-info">
					<h4>经营年限</h4>
					<span>
					@if(!empty($info))
					   {{$info['operating_life']}}
					@else
					   请选择经营年限
					@endif
					</span>
				</li>
				<li class="item-flex">
					<h4>经营流水</h4>
					<input type="number" class="record-info" placeholder="1000" value="@if(!empty($info)){{$info['operating_stream']}}@endif"><i>元</i>				
				</li>
			</ul>
		</div>
		<div class="agreement"><input type="checkbox" class="agreement-input" >我已阅读并同意《个人资料授权协议》</div>
		<button class="large-btn">提  交</button>
	</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/transform.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.select.js"></script>
<script src="{{HOST}}/static/weixin/common/js/layer.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.button.js"></script>

<script>
$(function(){
	var edu_value =@if(!empty($info))"{{$info['educational_status']}}"@else""@endif;//教育程度
	var insurance_value =@if(!empty($info))"{{$info['social_security']}}"@else""@endif;//社保
	var car_value =@if(!empty($info))"{{$info['car_infomation']}}"@else""@endif;//车辆
	var business_value =@if(!empty($info))"{{$info['operating_life']}}"@else""@endif;//经营年限

	// 教育状况
	var edu_datas = [
		     		    { value: "硕士及以上", text: "硕士及以上" },
		    		    { value: "本科", text: "本科" },
		    		    { value: "大专", text: "大专" },
		    		    { value: "中专/高中及以下", text: "中专/高中及以下" }
		    		];
	var edu_index = insurance_index = car_index = business_index = 0;
	$(edu_datas).each(function(i,item){
		if(item.value == edu_value){
			edu_index = i;return true;
		}
		});
	var edu = new AlloyTouch.Select({  
		options:edu_datas,
		selectedIndex: edu_index,
		change: function(item, index) {
		   
		},
		complete: function(item, index) {
		    $('.edu-info').find('span').text(item.value);
		    edu.hide();
		}
	})

    new AlloyTouch.Button('.edu-info', function () {
        edu.show();
    });

	var insurance_datas = [
		     		    { value: "缴纳本地社保", text: "缴纳本地社保" },
		    		    { value: "未缴纳社保", text: "未缴纳社保" }
		    		];
	$(insurance_datas).each(function(i,item){
		if(item.value == insurance_value){
			insurance_index = i;return true;
		}
		});
	// 社保
	var insurance = new AlloyTouch.Select({  
		options:insurance_datas,
		selectedIndex: insurance_index,
		change: function(item, index) {
		   
		},
		complete: function(item, index) {
		    $('.insurance-info').find('span').text(item.value);
		    insurance.hide();
		}
	})
    new AlloyTouch.Button('.insurance-info', function () {
        insurance.show();
    });

	// 车辆
	var car_datas = [
		           		    { value: "无车", text: "无车" },
		        		    { value: "本人名下有车无贷款", text: "本人名下有车无贷款" },
		        		    { value: "本人名下有车有按揭贷款", text: "本人名下有车有按揭贷款" },
		        		    { value: "本人名下有车但已被抵押", text: "本人名下有车但已被抵押" },
		        		    { value: "其他", text: "其他" },
		        		];
	$(car_datas).each(function(i,item){
		if(item.value == car_value){
			car_index = i;return true;
		}
		});
	
	var car = new AlloyTouch.Select({  
		options:car_datas,
		selectedIndex: car_index,
		change: function(item, index) {
		   
		},
		complete: function(item, index) {
		    $('.car-info').find('span').text(item.value);
		    car.hide();
		}
	})
    new AlloyTouch.Button('.car-info', function () {
        car.show();
    });

	// 经营年限
		// 车辆
	var business_datas = [
		     		    { value: "0-6个月", text: "0-6个月" },
		    		    { value: "7-12个月", text: "7-12个月" },
		    		    { value: "1-2年", text: "1-2年" },
		    		    { value: "3-4年", text: "3-4年" },
		    		    { value: "5年以上", text: "5年以上" },
		    		];
	$(business_datas).each(function(i,item){
		if(item.value == business_value){
			business_index = i;return true;
		}
		});
	
	var business = new AlloyTouch.Select({  
		options:business_datas,
		selectedIndex: business_index,
		change: function(item, index) {
		   
		},
		complete: function(item, index) {
		    $('.business-info').find('span').text(item.value);
		    business.hide();
		}
	})
    new AlloyTouch.Button('.business-info', function () {
        business.show();
    });

	// 表单验证
	var $edu = $('.edu-info').find('span'),
		$insurance = $('.insurance-info').find('span'),
		$car = $('.car-info').find('span'),
		$business = $('.business-info').find('span'),
		$record = $('.record-info'),
		$agreement = $('.agreement-input'),
        $returnMoney = $('.return_money');
        
	$('.large-btn').click(function(){
		if($returnMoney.val() ==''){
			layer.msg('请填写最高月还款额度！')
		}else if($edu.text()=='请选择教育程度'){
			layer.msg('请选择教育程度！')
		}else if($insurance.text()=='请选择是否缴纳社保'){
			layer.msg('请选择现单位是否缴纳社保！')
		}else if($car.text()=='请选择车辆情况'){
			layer.msg('请选择车辆情况！')
		}else if($business.text()=='请选择经营年限'){
			layer.msg('请选择经营年限！')
		}else if($record.val()==''){
			layer.msg('请填写经营流水！')
		}else if(!$agreement.is(":checked")){
			layer.msg('请勾选同意协议！')
		}else{
			var data = {
					return_money_max : $returnMoney.val(),
					educational_status : $edu.text(),
					social_security: $insurance.text(),
					car_infomation: $car.text(),
					operating_life: $business.text(),
					operating_stream: $record.val(),
					store_id: {{$base['store_id']}},
						};
			$.ajax({
			    url : '/weixin/storeExtra/saveInfo',
			    dataType : 'json',
			    type : 'post',
			    data : data,
			    success : function(msg){
				    if(msg.status == 1){
				    	window.location.href = '/weixin/storeExtra/quotaManage';
					}else{
				    	layer.msg(msg.msg);
				    }
				}
			});
			
		}
	})
})
</script>
</html>
