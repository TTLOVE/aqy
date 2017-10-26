@include('header')
<section id="content">
    <section class="vbox">
        <section class="scrollable wrapper">
            <div class="col-lg-12">
                <!-- .breadcrumb -->
                <ul class="breadcrumb">
                    <li><a href="/admin/product/show"><i class="fa fa-home"></i> 产品管理</a></li>
                    <li class="active"><a href="/admin/product/show"><i class="fa fa-list-ul"></i> 产品列表</a></li>
                    <button type="button" id="forReturnInfo" class="btn btn-sm btn-info">添加产品</button>
                </ul>
                <!-- / .breadcrumb -->
            </div>
            <div class="col-lg-12">
                <section class="panel panel-default">
                    <div class="panel-body">
                        <form class="form-horizontal" method="" id="productForm">
                            <input type="hidden" name="operator_id" value="{{$operatorID}}" />
                            <div class="form-group">
                                <label class="col-sm-2 control-label">产品名称</label>
                                <div class="col-sm-4">
                                    <input type="text" name="usury_name" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">产品标题</label>
                                <div class="col-sm-4">
                                    <input type="text" name="usury_title" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">产品简介</label>
                                <div class="col-sm-4">
                                    <input type="text" name="usury_desc" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">所需认证材料</label>
                                <div class="col-sm-4">
                                    <label><input name="certification[]" type="checkbox" value="1" />个人身份认证 </label>
                                    <label><input name="certification[]" type="checkbox" value="2" />商户交易流水 </label>
                                    <label><input name="certification[]" type="checkbox" value="4" />运营商认证 </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">申请条件</label>
                                <div class="col-sm-4">
                                    <textarea name="apply_condition" placeholder class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">额度范围</label>
                                <div class="col-sm-4">
                                    <label><input name="credit_type" type="radio" value="1" />整百 </label>
                                    <label><input name="credit_type" type="radio" value="2" />整千 </label>
                                    <br/>
                                    <input type="text" name="credit_min" />至<input type="text" name="credit_max" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">期限范围</label>
                                <div class="col-sm-4">
                                    <select name="usury_time_type" id="my-select">
                                    <option value="0">请选择期限范围</option>
                                    <option value="1" />日度 </option>
                                    <option value="2" />月度 </option>
                                    </select>
                                </div>
                            </div>

                            <div id="big-box">
                            </div>
<!--
                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4">
                                    <input type="text" name="credit_min" /> +
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">利率范围</label>
                                <div class="col-sm-4">
                                    <input type="text" name="credit_min" />%每月
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">考拉服务费率</label>
                                <div class="col-sm-4">
                                    <input type="text" name="credit_min" />%
                                </div>
                            </div>
-->
                            <div class="form-group">
                                <label class="col-sm-2 control-label">还款途径</label>
                                <div class="col-sm-4">
                                    <label><input name="repayment_type[]" type="checkbox" value="1" />主动还款 </label>
                                    <label><input name="repayment_type[]" type="checkbox" value="2" />余额自动划扣 </label>
                                    <label><input name="repayment_type[]" type="checkbox" value="4" />银行卡自动划扣 </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">提前还款</label>
                                <div class="col-sm-4">
                                    <label><input name="repayment_advance" type="radio" value="1" />不支持提前还款 </label>
                                    <label><input name="repayment_advance" type="radio" value="2" />提前还款全部，费用不减免 </label>
                                    <label><input name="repayment_advance" type="radio" value="3" />提前还款当期，费用不减免 </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">逾期违约金</label>
                                <div class="col-sm-4">
                                    <input type="text" name="overdue_rate" />% 费率*当期应还金额
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">逾期罚息</label>
                                <div class="col-sm-4">
                                    <input type="text" name="overdue_punish_data" />%每日
                                </div>
                            </div>
                        </form>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-default" id="submit-btn">保存</button>
                                </div>
                            </div>
                    </div>
                </section>
            </div>
        </section>
    </section>
</section>
</section>
</section>
<script src="{{HOST}}/static/admin/js/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="{{HOST}}/static/admin/js/bootstrap.js"></script>
<script src="{{HOST}}/static/admin/js/app.js"></script>
<script>
    $(document).ready(function () {
        $('input[maxlength]').on('keyup', function () {
            var maxlength = $(this).attr('maxlength');
            var name = $(this).attr('name');
            if ($(this).val().length == maxlength) {
                $('#' + name + '_maxlength').text(maxlength);
                $('#' + name + '_too_log').removeClass('hide').css({'color': 'red'});
            } else {
                $('#' + name + '_too_log').addClass('hide')
            }
        });
        $('form').on('submit',function () {
//           if($.trim($('textarea[name=target_ids]').val()) == '' || $.trim($('input[name=comment]').val()) == '' ){
//               alert('请填写完整!');
//               return false;
//           }
        });

        var str = str_1 = str_2 = str_3 = '';

        $('#my-select').on('change',function () {
            var opValue = $(this).children('option:selected').val();
            if (opValue==1) {

                 str = '<div class="form-group">'+
                    '<label class="col-sm-2 control-label"></label>'+
                    '<div class="col-sm-4">'+
                    '<input type="text" name="usury_time_min" />至<input type="text" name="usury_time_max" />'+
                    '</div>'+
                    '</div>'+
                    '<div class="form-group">'+
                    '<label class="col-sm-2 control-label">利率范围</label>'+
                    '<div class="col-sm-4">'+
                    '<input type="text" name="usury_interest_rate" />%每日'+
                    '</div>'+
                    '</div>'+
                    '<div class="form-group">'+
                    '<label class="col-sm-2 control-label">考拉服务费率</label>'+
                    '<div class="col-sm-4">'+
                    '<input type="text" name="koala_service_rate" />%'+
                    '</div>'+
                    '</div>';
                 $('#big-box').html(str);

            }
            else if (opValue==2) {

                 str_3 = '<div class="form-group">'+
                     '<label class="col-sm-2 control-label"></label>'+
                    '<div class="col-sm-4">'+
                   '<input type="text" name="usury_time_data[]" /> <span class="spanAdd" style="cursor:pointer;font-size: 18px;margin-left: 10px"> + </span>'+
                    '</div>'+
                    '</div>';
                $('#big-box').html(str_3);
            }
            else {
                $('#big-box').html('');
            }
        });

        $('#big-box').on('click', ".spanAdd" , function () {

            var month_num = $(this).prev().val();
            if (!month_num) {
                alert('请填写期限范围');
                return;
            }
            var month_str = '借款'+month_num+'个月';
            str_1 = '<div class="form-group">'+
             '<label class="col-sm-2 control-label">利率范围</label>'+
             '<div class="col-sm-4">'+
             '<input type="text" name="usury_interest_rate[]" class="usury_interest_rate" />%每月'+'  '+month_str+
             '</div>'+
             '</div>';

            str_2 = '<div class="form-group">'+
             '<label class="col-sm-2 control-label">考拉服务费率</label>'+
             '<div class="col-sm-4">'+
             '<input type="text" name="koala_service_rate[]" class="koala_service_rate" />% '+month_str+
             '</div>'+
             '</div>';

            str_3 = '<div class="form-group">'+
                '<label class="col-sm-2 control-label"></label>'+
                '<div class="col-sm-4">'+
                '<input type="text" name="usury_time_data[]" /> <span class="spanAdd" style="cursor:pointer;font-size: 18px;margin-left: 10px"> + </span>'+
                '</div>'+
                '</div>';

            $('#big-box .spanAdd').text('');

            $('#big-box').append(str_1);
            $('#big-box').append(str_2);
            $('#big-box').append(str_3);
        });

        $('#submit-btn').on('click', function () {

            $.ajax({
                url:'/admin/product/add',
                type:'POST',
                async: false,
                data:$("#productForm").serialize(),
                dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text

                success:function(data,textStatus,jqXHR){
                    if (!data.status){

                        alert(data.msg);
                    } else {
                        alert('添加成功');
                        location.href = '/admin/product/show';
                    }
                },

            })
        });

    });

</script>
</body>
</html>