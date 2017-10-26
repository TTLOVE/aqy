         @include('header')
        <section id="content">
            <section class="vbox">
                <section class="scrollable wrapper">
                    <div class="col-lg-12">
                        <!-- .breadcrumb -->
                        <ul class="breadcrumb">
                            <li><a href="#"><i class="fa fa-home"></i> 借款管理</a></li>
                            <li class="active"><a href="#"><i class="fa fa-list-ul"></i> 还款商户</a></li>
                        </ul>
                        <!-- / .breadcrumb -->
                    </div>
                    <div class="col-lg-12">
                        <section class="panel panel-default">
                            <div class="panel-body" style="display:none;">
                                <form role="form" class="form-inline" method="get" name="query-form">
                                    <div class="form-group" style="width: 80px;">
                                        <input type="text" class="form-control" name='store_id' id='store_id'
                                               value=""
                                               placeholder="请输入关键字" style="width: 80px;">
                                    </div>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name='store_name' id='store_name'
                                               value=""
                                               placeholder="店铺名称" style="width: 80px;">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" id="query" class="btn btn-sm btn-default">查询</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped b-t b-light" id="store-list">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>商户名</th>
                                            <th>姓名</th>
                                            <th>借款产品</th>
                                            <th>借款金额（元）</th>
                                            <th>借款期限</th>
                                            <th>费率</th>
                                            <th>还款状态</th>
                                            <th>放款时间</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($listData as $data)
                                        <tr>
                                            <td>{{$data['log_id']}}</td>
                                            <td>{{$data['store_name']}}</td>
                                            <td>{{$data['store_real_name']}}</td>
                                            <td>{{$data['usury_name']}}</td>
                                            <td>{{$data['borrow_money']}}</td>
                                            <td>{{$data['borrow_time']}}</td>
                                            <td>{{$data['borrow_fee']}}</td>
                                            <td>{{$configStatus[ $data['status'] ]}}</td>
                                            <td>{{date("Y-m-d H:i:s", $data['apply_time'])}}</td>
                                            <td><a style="color: rgb(66, 139, 202);" href="/admin/return/detail?id={{$data['log_id']}}">查看详情</a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                    @include('pager')
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



  });
  </script>
</body>
</html>
