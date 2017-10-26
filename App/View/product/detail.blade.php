         @include('header')
        <section id="content">
            <section class="vbox">
                <section class="scrollable wrapper">
                    <div class="col-lg-12">
                        <!-- .breadcrumb -->
                        <ul class="breadcrumb">
                            <li><a href="/admin/product/show"><i class="fa fa-home"></i> 产品管理</a></li>
                            <li class="active"><a href="/admin/product/show"><i class="fa fa-list-ul"></i> 产品列表</a></li>
                            <button type="button" id="forReturnInfo" class="btn btn-sm btn-info">产品详情</button>
                        </ul>
                        <!-- / .breadcrumb -->
                    </div>
                    <div class="col-lg-12">
                        <section class="panel panel-default">
                            <div class="table-responsive" id="return_info">
                                <table class="table table-striped b-t b-light">
                                    <tbody>
                                        <tr>
                                            <td>ID：{{$Detail['id']}}</td>
                                            <td>运营商ID：{{$Detail['operator_id']}}</td>
                                        </tr>
                                        <tr>
                                            <td>产品名称：{{$Detail['usury_name']}}</td>
                                            <td>产品标题：{{$Detail['usury_title']}}</td>
                                        </tr>
                                        <tr>

                                            @php
                                                $certification_str='';
                                                if ($Detail['certification'] & \Model\Product::CERTIFICATION_ONE) {
                                                    $certification_str .= '个人身份证 ';
                                                }
                                                if ($Detail['certification'] & \Model\Product::CERTIFICATION_TWO) {
                                                    $certification_str .= '商户交易流水 ';
                                                }
                                                if ($Detail['certification'] & \Model\Product::CERTIFICATION_FOUR) {
                                                    $certification_str .= '运营商认证 ';
                                                }
                                            @endphp

                                            <td>认证所需材料：{{$certification_str}}</td>
                                            <td>申请条件：{{$Detail['apply_condition']}}</td>
                                        </tr>
                                        <tr>
                                            <td>额度范围：{{$Detail['credit_min']}}-{{$Detail['credit_max']}}</td>

                                            @php
                                                $arr = explode('-',$Detail['usury_time_data']);
                                                $count = count($arr);
                                                $date_limit_for_str = '';
                                                 if ($Detail['usury_time_type']==\Model\Product::USURY_TIME_TYPE_MONTH) {

                                                     if ($count==1) {
                                                         $date_limit_for_str = $arr[0].'个月';

                                                     } else {

                                                         for ($i=0; $i<$count; $i++) {
                                                          if ($i==$count-1) {
                                                              $date_limit_for_str .= $arr[$i].'个月';
                                                          } else {
                                                              $date_limit_for_str .= $arr[$i].'、';
                                                          }
                                                         }
                                                     }

                                                 } else {
                                                     $date_limit_for_str = $Detail['usury_time_data'].'天';
                                                 }
                                            @endphp

                                            <td>期限范围：{{$date_limit_for_str}}</td>
                                        </tr>
                                        <tr>

                                            @php
                                                $arr_rate = explode('-',$Detail['usury_interest_rate']);
                                                $count_rate = count($arr);
                                                $usury_interest_rate_str = '';
                                                 if ($Detail['usury_time_type']==\Model\Product::USURY_TIME_TYPE_MONTH) {

                                                     if ($count_rate==1) {
                                                         $usury_interest_rate_str = $arr_rate[0].'%每月';

                                                     } else {

                                                         for ($i=0; $i<$count_rate; $i++) {
                                                          if ($i==$count_rate-1) {
                                                              $usury_interest_rate_str .= $arr_rate[$i].'%每月';
                                                          } else {
                                                              $usury_interest_rate_str .= $arr_rate[$i].'%、';
                                                          }
                                                         }
                                                     }

                                                 } else {
                                                     $usury_interest_rate_str = $Detail['usury_interest_rate'].'%每日';
                                                 }
                                            @endphp

                                            <td>利率范围：{{$usury_interest_rate_str}}</td>

                                            @php
                                                $arr_kl_rate = explode('-',$Detail['koala_service_rate']);
                                                $count_kl_rate = count($arr);
                                                $usury_kl_rate_str = '';
                                                 if ($Detail['usury_time_type']==\Model\Product::USURY_TIME_TYPE_MONTH) {

                                                     if ($count_rate==1) {
                                                         $usury_kl_rate_str = $arr_kl_rate[0].'%每月';

                                                     } else {

                                                         for ($i=0; $i<$count_rate; $i++) {
                                                          if ($i==$count_rate-1) {
                                                              $usury_kl_rate_str .= $arr_kl_rate[$i].'%每月';
                                                          } else {
                                                              $usury_kl_rate_str .= $arr_kl_rate[$i].'%、';
                                                          }
                                                         }
                                                     }

                                                 } else {
                                                     $usury_kl_rate_str = $Detail['koala_service_rate'].'%每日';
                                                 }
                                            @endphp

                                            <td>服务费率范围：{{$usury_kl_rate_str}}</td>
                                        </tr>
                                        <tr>

                                            @php
                                                $repayment_type_str='';
                                                if ($Detail['repayment_type'] & \Model\Product::CERTIFICATION_ONE) {
                                                    $repayment_type_str .= '主动还款 ';
                                                }
                                                if ($Detail['repayment_type'] & \Model\Product::CERTIFICATION_TWO) {
                                                    $repayment_type_str .= '余额自动划扣 ';
                                                }
                                                if ($Detail['repayment_type'] & \Model\Product::CERTIFICATION_FOUR) {
                                                    $repayment_type_str .= '银行卡自动划扣 ';
                                                }
                                            @endphp

                                            <td>还款方式：{{$repayment_type_str}}</td>

                                            @php
                                                $repayment_advance_str='';
                                                if ($Detail['repayment_advance'] == \Model\Product::CERTIFICATION_ONE) {
                                                    $repayment_advance_str .= '不支持 ';
                                                }
                                                elseif ($Detail['repayment_advance'] == \Model\Product::CERTIFICATION_TWO) {
                                                    $repayment_advance_str .= '提前还款全部，费用不减免 ';
                                                }
                                                elseif ($Detail['repayment_advance'] == \Model\Product::CERTIFICATION_FOUR) {
                                                    $repayment_advance_str .= '提前还款当期，费用不减免 ';
                                                }
                                            @endphp

                                            <td>提前还款：{{$repayment_advance_str}}</td>
                                        </tr>
                                        <tr>
                                            <td>逾期违约金：{{$Detail['overdue_rate']}}%</td>
                                            <td>罚息利率：{{$Detail['overdue_punish_data']}}%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </section>
            </section>
        </section>
      </section>
    </section>
  </section>

  <script src="{{HOST}}/static/admin/js/jquery.min.js"></script>
  <!-- Bootstrap -->
  <script src="{{HOST}}/static/admin/js/bootstrap.js"></script>
  <!-- App -->
  <script src="{{HOST}}/static/admin/js/app.js"></script>  

  <script>
  $(document).ready( function() {
      var form = $('#query-form');
      var body = $('body');
      // 查询按钮
      $('#query').click(function () {
          form.find('input[name=page]').val(1);
          form.find('input[name=export_format]').remove();
          form.submit();
      });

      $('#exportUTF8').click(function () {
          form.find('input[name=m]').val('exportCsv');
          form.find('input[name=export_format]').remove();
          form.submit();
      });
      $('#exportGBK').click(function () {
          form.find('input[name=m]').val('exportCsv');
          form.find('input[name=export_format]').remove();
          form.append('<input type="hidden" name="export_format" value="gbk">');
          form.submit();
      });
      // 选中当前的搜索条件

      // 分页
      $('#pager').find('a').not('[data-page=""]').click(function () {
          var page = $(this).attr('data-page');
          if(page == undefined) return ;
          form.find('input[name=page]').val(page);
          form.find('input[name=export_format]').remove();
          form.submit();
      });
      $('#pager').find('a[data-page='+form.find('input[name=page]').val()+']').css({'background-color':'#428bca','border-color':'#428bca', 'color':'#fff'});

      $('#forReturnInfo').click(function(){
          $('#store_info').hide();
          $('#return_info').show();
      })
      $('#forStoreInfo').click(function(){
          $('#return_info').hide();
          $('#store_info').show();
      })
  });
  </script>
</body>
</html>
