<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <meta content="yes" name="apple-mobile-web-app-capable">
    <meta content="black" name="apple-mobile-web-app-status-bar-style">
    <meta content="telephone=no" name="format-detection">
    <meta content="email=no" name="format-detection">
    <title>借款记录</title>
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/common/css/reset.css" >
    <link type="text/css" rel="stylesheet" href="{{HOST}}/static/weixin/css/mine.css" >
</head>

<body>
    <div class="mineRecord">
        <ul class="mineRecord-nav">
            <li class="nav-item nav-examine active">审核中</li>
            <li class="nav-item nav-loan">已放款</li>
        </ul>
        <div class="mineRecord-content">
            <div class="content-container examine-content ">
            @foreach ($borrowList as $log)
            @if (in_array($log['status'],[0,1,2,3,4]))
                <div class='content-item fuyou no_pass no_check' data-logId='{{$log["log_id"]}}'>
                    <!-- no-pass类名控制不通过或者已逾期字体变红 -->
                    <!-- fuyou/xiaonuo类名控制logo -->
                    <div class="item-msg">
                        <div class="item-msg-l item-flex">
                            <div class="company-icon"></div>
                            <div class="company-name">{{$log['product_name']}}</div>
                            <div class="apply-time">{{date("Y-m-d", $log['apply_time'])}}申请</div>
                        </div>
                        <div class="item-msg-r">
                            <div class="apply-status">@if($log['status'] == 4)不通过@else审核中@endif</div>
                        </div>
                    </div>
                    <div class="item-content">
                        <div class="item-flex content-detail">
                            <span class="s1">{{$log['apply_amt']/100}}元</span>
                            <span class="s1">{{$log['loan_period']}}@if($log['loan_type']==3)月@else日@endif</span>
                            <span class="s1">{{$log['loan_rate']}}%</span>
                        </div>
                        <div class="item-flex content-title ">
                            <span class="s1">借款</span>
                            <span class="s1">借款期限</span>
                            <span class="s1">@if($log['loan_type']==3)月@else日@endif利率</span>
                        </div>
                    </div>
                </div>
            @endif
            @endforeach
            </div>
            @if ($isOverdue)
            <div class="content-container loan-content dn overdue">
                <!-- overdue控制有逾期情况需出现提示，notice的dn类名控制提示出现 -->
                <h4 class="notice">您有还款单已逾期，每日会计算罚息，请尽快还款！</h4>
            @else
            <div class="content-container loan-content dn">
            @endif

            @foreach($borrowList as $log)
            @if(in_array($log['status'],[5,6,7]))
                <div class='content-item fuyou no_pass is_check' data-logId='{{$log["log_id"]}}'>
<!--                 xiaonuo类名控制富有小诺 -->
                    <div class="item-msg">
                        <div class="item-msg-l item-flex">
                            <div class="company-icon"></div>
                            <div class="company-name">{{$log['product_name']}}</div>
                            <div class="apply-time">{{date("Y-m-d", $log['auditing_time'])}}审核通过</div>
                        </div>
                        <div class="item-msg-r">
                            <div class="apply-status">@if(isset($log['desc'])){{$log['desc']}}@else还款中@endif</div>
                        </div>
                    </div>
                    <div class="item-content">
                        <div class="item-flex content-detail">
                            <span class="s1">{{$log['apply_amt']/100}}元</span>
                            <span class="s1">每@if($log['loan_type']==3)月@else日@endif{{($repaymentList[ $log['log_id'] ]['repayment_capital']+$repaymentList[ $log['log_id'] ]['repeyment_interest']+$repaymentList[ $log['log_id'] ]['repayment_penalty']+$repaymentList[ $log['log_id'] ]['repayment_ahead_fee']+$repaymentList[ $log['log_id'] ]['repayment_mng_fee'])/100}}元</span>
                            <span class="s1">{{$log['loan_period']}}@if($log['loan_type']==3)月@else日@endif</span>
                            <span class="s1">{{$log['loan_rate']}}%</span>
                        </div>
                        <div class="item-flex content-title ">
                            <span class="s1">借款</span>
                            <span class="s1">每@if($log['loan_type']==3)月@else日@endif还款</span>
                            <span class="s1">借款期限</span>
                            <span class="s1">@if($log['loan_type']==3)月@else日@endif利率</span>
                        </div>
                    </div>
                </div>
            @endif
            @endforeach
            </div>
        </div>
    </div>
    <script src="{{HOST}}/static/weixin/common/js/jquery.min.js"></script>
    <script>
        $(function() {
            $('.nav-item').on('click', function() {
                var index = $(this).index();
                $(this).addClass('active').siblings().removeClass('active');
                $('.content-container').eq(index).removeClass('dn').siblings().addClass('dn');
            });
            $('.no_check').on('click',function(){
                var log_id = $(this).data('logid');
                window.location.href = '/weixin/storeBorrow/reviewOn?log_id='+log_id;
            });

            $('.is_check').on('click',function(){
                var log_id = $(this).data('logid');
                window.location.href = '/weixin/storeBorrow/reviewOn?log_id='+log_id;
            });
        });
    </script>
</body>

</html>
