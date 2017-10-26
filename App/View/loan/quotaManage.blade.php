<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>额度管理</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/loan.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/select.css" >
</head>
<body>
	<div class="quota-manage">
		<div class="quota-container">
			<div class="quota">
				<h5>可用额度</h5>
				<h3>¥
				@if($isOverdue)
				    0
				@else
				    {{$list['lmtAble']}}
				@endif</h3>
				<h5>总额度  
				@if($isOverdue)
				    0
				@else
				    {{$list['lmtTotal']}}
				@endif元</h5>
			</div>
		</div>
		<div class="infos-container">
			<ul>
				<li class="item-direction">
    				<a href="/weixin/storeExtra/storeProve" class="item-flex">
    				    <h4>个人身份认证</h4>
    				@if(empty($storeExtraInfo))
    				    <span>完善资料</span>
    				@else
    				    <span>已认证</span>
    				@endif </a>
				</li>
				<li class="item-direction"><a href="{{$bankConfigUrl}}"  class="item-flex"><h4>绑定银行卡</h4><span>已绑定</span></a></li>
				<li class="item-direction"><a href="/weixin/storeExtra/certification"  class="item-flex"><h4>运营商认证</h4><span>已认证</span></a></li>
			</ul>
		</div>
        @if (!empty($storeExtraInfo))
		<div class="infos-container">
			<ul>
				<li class="transaction-record item-direction">
				<a href="javascript:;"  class="item-flex">
					<h4>商户交易流水</h4>
					<span  class="add-status">
					@if(!empty($storeExtraInfo) && $storeExtraInfo['open_stream'] == 1)已开放@else未开放@endif</span>
				</a>
				</li>
			</ul>
		</div>
        @endif
	</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script src="{{HOST}}/static/weixin/common/js/transform.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.select.js"></script>
<script src="{{HOST}}/static/weixin/common/js/alloy_touch.button.js"></script>
<script>
$(function(){
	var record = new AlloyTouch.Select({  
		options:[
		    { value: "已开放", text: "开放交易流水",h_value : 1 },
		    { value: "未开放", text: "暂不开放" ,h_value : 0}
		],
		selectedIndex: 0,
		change: function(item, index) {
			console.log(item);
		},
		complete: function(item, index) {
			var h_value = item.h_value;
            var store_id = {{$storeId}};
			$.ajax({
			    url : '/weixin/storeExtra/updateOpenStream',
    			data : {store_id:store_id,setStatus:h_value},
			    dataType : 'json',
			    type : 'post',
			    success : function(msg){
				    
				}
			});
		    $('.add-status').text(item.value);
		    record.hide();
		}
	})

	new AlloyTouch.Button(".transaction-record", function () {
	    record.show();
	});
});
</script>
</html>
