         @include('header')
        <section id="content">
            <section class="vbox">
                <section class="scrollable wrapper">
                    <div class="col-lg-12">
                        <!-- .breadcrumb -->
                        <ul class="breadcrumb">
                            <li><a href="/admin/borrow/list"><i class="fa fa-home"></i> 借款管理</a></li>
                            <li class="active"><a href="/admin/return/list"><i class="fa fa-list-ul"></i> 还款商户</a></li>
                            <button type="button" id="forReturnInfo" class="btn btn-sm btn-info">还款详情</button>
                            <button type="button" id="forStoreInfo" class="btn btn-sm btn-warning">身份信息</button>
                        </ul>
                        <!-- / .breadcrumb -->
                    </div>
                    <div class="col-lg-12">
                        <section class="panel panel-default">
                            <div class="table-responsive" id="return_info">
                                <table class="table table-striped b-t b-light">
                                    <tbody>
                                        <tr>
                                            <td>ID：{{$returnDetail['log_id']}}</td>
                                            <td>商户名：{{$returnDetail['store_name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>姓名：{{$returnDetail['store_real_name']}}</td>
                                            <td>借款产品：{{$returnDetail['usury_name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>借款金额：{{$returnDetail['borrow_money']}}</td>
                                            <td>借款期限：{{$returnDetail['borrow_time']}}</td>
                                        </tr>
                                        <tr>
                                            <td>利率：@if($returnDetail['usury_time_type']==1)每日@else每月@endif{{$returnDetail['usury_interest_rate']}}%</td>
                                            <td>服务费率：{{$returnDetail['koala_service_rate']}}%</td>
                                        </tr>
                                        <tr>
                                            <td>还款状态：{{$configStatus[ $returnDetail['status'] ]}}</td>
                                            <td>放款时间：{{date("Y-m-d H:i:s", $returnDetail['auditing_time'])}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-striped b-t b-light" style="margin-top:90px;">
                                    <tbody>
                                        @foreach ($returnLogList as $key => $logData)
                                            <tr>
                                                <td>
                                                    {{$logData['return_money']}}@if ($logData['return_fee']>0.00)+{{$logData['return_fee']}}@endif &nbsp;&nbsp;&nbsp;&nbsp;
                                                    {{date("Y-m-d", $logData['dead_line_time'])}}还款 
                                                    {{$returnDetail['usury_name']}}（{{$key+1}}期） 
                                                    每@if ($returnDetail['usury_time_type']==1)日@else月@endif等额本息还款 {{$configStatus[ $logData['status'] ]}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-responsive" id="store_info" style="display:none;">
                                <table class="table table-striped b-t b-light" id="store-list">
                                    <tbody>
                                        @if (empty($storeInfo))
                                            <tr>
                                                <td>商家信息不存在</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>姓名：{{$storeInfo['store_realname']}}</td>
                                                <td>身份证号：{{$storeInfo['idcard_sn']}}</td>
                                            </tr>
                                            <tr>
                                                <td>电话：{{$storeInfo['tel']}}</td>
                                                <td>商户名：{{$storeInfo['store_name']}}</td>
                                            </tr>
                                            <tr>
                                                <td>商户地址：{{$storeInfo['region_name']}}{{$storeInfo['address']}}</td>
                                            </tr>
                                        @endif
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
