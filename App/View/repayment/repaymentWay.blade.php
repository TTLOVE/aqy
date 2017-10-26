<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>还款方式</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/dialog.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/repayment.css?v=01">
</head>
<body>
	<div class="repaymentWay">
		<div class="title">
			<h4 ><i><img src="{{HOST}}/static/weixin/common/images/pro-logo.png"></i>{{$returnDetail['product_name']}}（{{$repaymentInfo['period']}}期）</h4>
		</div>
		<div class="quota">
			<h5>还款额</h5>
            <h3>¥{{$repayment['totalAmt']/100}}</h3>
		</div>
		<div class="quota-description">
			<div class="item-flex">
				<p class="item-flex">当期应还 <em>¥{{$repayment['waitingAmt']/100}}</em></p>
				<p class="item-flex">逾期罚息  <em>@if(isset($repayment['overAmt']) && $repayment['overAmt']>0)¥{{($repayment['overAmt']/100)}}@else¥0.00 @endif</em></p>
			</div>			
            @if (isset($repayment['aheadAmt']) && $repayment['aheadAmt']>0)
			<p class="prepayment">提前还款费用  <em>¥{{($repayment['aheadAmt']+$repayment['aheadAmtFee'])/100}}</em> <i>{{$returnDetail['conf_pre_rate']}}%*提前还款本金</i></p>
            @endif
		</div>
		<ul class="way-list">
			<li>
			 	<a href="javascript:;"  class="item-flex">
				 <h4>账户余额划扣</h4><input type="checkbox" class="item-selected" name="payType" value="01">
				</a>
			</li>		
            <!--
			<li>
			 	<a href="javascript:;"  class="item-flex">
				 <h4>银行卡代扣</h4><input type="checkbox" name="payType" value="02">
				</a>
			</li>
            -->
		</ul>
	</div>
	<button class="large-btn">还 款</button>
	<div id="dialogs">
    <div class="js_dialog" id="iosDialog1" style="display:none;">
        <div class="weui-mask"></div>
        <div class="weui-dialog">
            <div class="weui-dialog__hd"><strong class="weui-dialog__title">代扣还款</strong></div>
            <div class="weui-dialog__bd">您发起了一笔银行代扣还款</div>
            <div class="weui-dialog__ft">
                <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a>
                <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
            </div>
        </div>
    </div>
    <form action="" method="post" id='fuyou_submit' style="display:none;">
        <textarea name="reqStr"></textarea>
        <input type="text" name="sign" id='sign' value="">
    </form>
</div>
</body>
<script type="text/javascript" src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
<script>
$(function(){
	// 还款方式选择
	$('.way-list li').click(function(){
		$(this).find('input').addClass('item-selected')
		$(this).siblings('li').find('input').removeClass('item-selected')
	})

	// 还款弹窗 
	$('.large-btn').on('click',function(){
		var thisWay = $('.item-selected').parent().find('h4').text()
		if(thisWay == '银行卡代扣'){
			$('.weui-dialog__title').text('代扣还款')
			$('.weui-dialog__bd').text('您发起了一笔银行代扣还款')
		}else{
			$('.weui-dialog__title').text('余额还款')
			$('.weui-dialog__bd').text('您发起了一笔账户余额划扣还款')
		}
		$('.js_dialog').show()
	})

	$('.weui-dialog__btn').click(function(){
       $('.js_dialog').hide()
    })

    $(".weui-dialog__btn_primary").click(function(){
        var repayType = $('.item-selected').val();
        if ( typeof(repayType)=="undefined" ) {
            alert("请选择还款方式");
        } else {
            var returnMoney = {{$repayment['totalAmt']}};
            var loanId = "{{$returnDetail['loan_id']}}";
            var repayTag = '{{$repayTag}}';
            var storeId = {{$storeId}};
            var borrowId = {{$repaymentInfo['borrow_money_log_id']}};
            var period = {{$repaymentInfo['period']}};
			$.ajax({
			    url : '/weixin/repayment/returnMoney',
                data : {store_id: storeId,money: returnMoney,repay_type: repayType,loan_id: loanId,repay_tag: repayTag,borrowId:borrowId,period:period},
			    dataType : 'json',
                type : 'post',
			    success : function(result){
				    if(result.status == 0){
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
        }
    })
})
</script>
</html>
