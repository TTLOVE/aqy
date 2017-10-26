         @include('header')

        <section id="content">
            <section class="vbox">
                <section class="scrollable wrapper">
                    <div class="col-lg-12">
                        <!-- .breadcrumb -->
                        <ul class="breadcrumb">
                            <li><a href="#"><i class="fa fa-home"></i> 产品管理</a></li>
                            <li class="active"><a href="#"><i class="fa fa-list-ul"></i> 产品列表</a></li>
                        </ul>
                        <!-- / .breadcrumb -->
                    </div>
                    <div class="col-lg-12">
                        <section class="panel panel-default">
                            <div class="panel-body" style="">
                                <form role="form" class="form-inline" method="post" name="query-form">
                                    <div class="form-group" style="width: 80px;">
                                        <input type="text" class="form-control" name='usury_name' id='store_id'
                                               value="{{$search['usury_name']}}"
                                               placeholder="请输入关键字" style="width: 110px;">
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" id="query" style="margin-left: 50px" class="btn btn-sm btn-default">查询</button>
                                    </div>
                                    <!--<button type="button" class="btn btn-sm btn-info pull-right"
                                            id="new-productlist">添加产品
                                    </button>-->
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped b-t b-light" id="store-list">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>产品名</th>
                                            <th>额度范围（元）</th>
                                            <th>期限范围</th>
                                            <th>利率范围</th>
                                            <th>借款次数</th>
                                            <th>借款总金额（元）</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($listData as $data)
                                        <tr>
                                            <td>{{$data['id']}}</td>
                                            <td>{{$data['usury_name']}}</td>
                                            <td>{{$data['credit_min']}}-{{$data['credit_max']}}</td>

                                            @php
                                                   $arr = explode('-',$data['usury_time_data']);
                                                   $count = count($arr);
                                                   $date_limit_for_str = '';
                                                    if ($data['usury_time_type']==\Model\Product::USURY_TIME_TYPE_MONTH) {

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
                                                        $date_limit_for_str = $data['usury_time_data'].'天';
                                                    }
                                            @endphp

                                            <td>{{$date_limit_for_str}}</td>

                                            @php
                                                $arr_rate = explode('-',$data['usury_interest_rate']);
                                                $count_rate = count($arr);
                                                $usury_interest_rate_str = '';
                                                 if ($data['usury_time_type']==\Model\Product::USURY_TIME_TYPE_MONTH) {

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
                                                     $usury_interest_rate_str = $data['usury_interest_rate'].'%每日';
                                                 }
                                            @endphp

                                            <td>{{$usury_interest_rate_str}}</td>
                                            <td>{{$data['total']}}</td>
                                            <td>{{$data['total_borrow_money']}}</td>
                                            <td><a style="color: rgb(66, 139, 202);" href="/admin/product/detail?id={{$data['id']}}">查看详情</a>
                                                <a style="color: rgb(66, 139, 202);" data-type="{{$data['status']}}" data-id="{{$data['id']}}" data-toggle="modal" data-target="#exampleModal" class="op" href="javascript:">
                                                    @if($data['status']==\Model\Product::PRODUCT_STATUS_OFF)
                                                        启用
                                                    @elseif($data['status']==\Model\Product::PRODUCT_STATUS_ON)
                                                        停用
                                                    @endif
                                                </a>
                                            </td>
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

         <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
             <div class="modal-dialog" role="document" style='width:1000px;'>
                 <div class="modal-content">
                     <div class="modal-header">
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                     aria-hidden="true">×</span></button>
                         <h4 class="modal-title" id="exampleModalLabel">操作确认</h4>
                     </div>
                     <div class="modal-body">

                     </div>
                     <div class="modal-footer">
                         <input type='hidden' value='' id='check_id'>
                         <button type="button" data-status='' class="btn btn-default" id="cancel">否</button>
                         <button type="button" data-status='' class="btn btn-primary" id="confirm">是</button>
                     </div>
                 </div>
             </div>
         </div>

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

      $('#new-productlist').on('click', function () {
          window.location.href = '/admin/product/add';
      });

      $('#cancel').on('click',function () {
          $('#exampleModal').modal('hide');
      })

      $('#confirm').on('click',function(){
          var status = $(this).data('status');
          var id = $('#check_id').val();
          $.ajax({
              url : '/admin/product/operator?id='+id+'&status='+status,
              type : 'get',
              dataType : 'json',
              success : function(res){
                  if(res.status){
                      alert('操作成功');
                      $('#exampleModal').modal('hide');
                  }else{
                      alert('操作失败');
                      $('#exampleModal').modal('hide');
                  }
                  window.location.reload();
              }
          });
      });

      $('.op').on('click',function () {
          var status = $(this).attr('data-type');
          var id = $(this).attr('data-id');
          if (status==1) {
              $('#exampleModal .modal-body').text('确定启用选中产品?');
              $('#exampleModal #confirm').attr('data-status',1);
              $('#exampleModal #check_id').val(id);
          } else if (status==2){
              $('#exampleModal #confirm').attr('data-status',2);
              $('#exampleModal .modal-body').text('确定停用选中产品?');
              $('#exampleModal #check_id').val(id);
          } else{
              alert('请刷新后重新操作');
          }
      });

  });
  </script>
</body>
</html>
