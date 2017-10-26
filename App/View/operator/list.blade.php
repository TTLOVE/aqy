         @include('header')
         
        <section id="content">
            <section class="vbox">
                <section class="scrollable wrapper">
                    <div class="col-lg-12">
                        <!-- .breadcrumb -->
                        <ul class="breadcrumb">
                            <li><a href="#"><i class="fa fa-home"></i> 运营商</a></li>
                            <li class="active"><a href="#"><i class="fa fa-list-ul"></i> 运营商入驻</a></li>
                        </ul>
                        <!-- / .breadcrumb -->
                    </div>
                    <div class="col-lg-12">
                        <section class="panel panel-default">
                            <div class="panel-body">
                                <form role="form" class="form-inline" method="get" id="query-form" name="query-form">
                                    <div class="form-group" style='display: none;'>
                                        <button type="submit" id="query" class="btn btn-sm btn-default">查询</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped b-t b-light" id="store-list">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>运营商</th>
                                        <th>接入产品数</th>
                                        <th>商户额度（元）</th>
                                        <th>放贷次数</th>
                                        <th>放贷总金额（元）</th>
                                        <th>待回款金额（元）</th>
                                        <th>逾期金额（元）</th>
                                        <th>U融汇余额（元）</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($list as $row)
                                    <tr>
                                        <td>{{$row['operator_id']}}</td>
                                        <td>{{$row['operator_name']}}</td>
                                        <td>{{$row['product_count']}}</td>
                                        <td>
                                        @if(!empty($row['tmp_money_limit']))
                                        <a href='javascript:void(0)' data-company_name='{{$row["company_name"]}}' data-tmoneylimit='{{$row["tmp_money_limit"]}}' data-moneylimit='{{$row["money_limit"]}}' data-id='{{$row["operator_id"]}}' data-toggle="modal" data-target="#exampleModal" >申请变更</a>
                                        @else
                                        {{$row['money_limit']}}
                                        @endif
                                        </td>
                                        <td>{{$row['lend_count']}}</td>
                                        <td>{{$row['lend_money_count']}}</td>
                                        <td>{{$row['return_money']+$row['return_fee']}}</td>
                                        <td>{{$row['return_fee']}}</td>
                                        <td>U融汇余额（元）</td>
                                        <td class='audit' data-id="{{$row['operator_id']}}"  ><a href='/admin/product/add?operator_id={{$row['operator_id']}}'>配置产品</a></td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @include('pager')
                        </section>
                    </div>
                </section>
            </section>
        </section>
      </section>
    </section>
  </section>
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">  
    <div class="modal-dialog" role="document" style='width:500px;'>  
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span  
                        aria-hidden="true">×</span></button>  
                <h4 class="modal-title" id="exampleModalLabel">商户额度变更申请</h4>  
            </div>  
            <div class="modal-body">  
                <form>  
                    <div class="form-group">
                    <table>
                        <tr><td>运营商:</td> <td id='check_operator'>环太阳运营商</td></tr>
                        <tr><td>变更前商户额度:</td> <td id='check_money_limit'>1000</td></tr>
                        <tr><td>申请变更商户额度:</td> <td id='check_t_money_limit'>10000</td></tr>
                    </table>
                    </div>  
                </form>  
            </div>  
            <div class="modal-footer">  
                <input type='hidden' value='' id='check_id'>
                <button type="button" data-status='1' class="btn btn-default check">通过</button>  
                <button type="button" data-status='0' class="btn btn-primary check">不通过</button>  
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

      $('.check').on('click',function(){
          var status = $(this).data('status');
          var id = $('#check_id').val();

          $.ajax({
              url : '/admin/operator/updateMoneyLimit',
              type : 'post',
              data : {operator_id: id,status: status},
              dataType : 'json',
              success : function(res){
                  if(res.status == true){
                  	  $('#exampleModal').modal('hide');
                  	  window.location.reload();
                  }else{
                      alert('更新失败');
                  }
              }
          });
      });

      $('#exampleModal').on('show.bs.modal', function (event) {
           var button = $(event.relatedTarget); // 触发事件的按钮
          var operator_id = button.data('id'); //运营商id
          var tmp_money_limit = button.data('tmoneylimit');//临时额度
          var money_limit = button.data('moneylimit');//现在额度
           $('#check_id').val(operator_id);
           $('#check_operator').html(button.data('company_name'));
           $('#check_money_limit').html(money_limit);
           $('#check_t_money_limit').html(tmp_money_limit);
           var modal = $(this);
      });

      $('.btn_base').on('click',function(){
    	  $('#base').show();   
    	  $('#u_financial').hide();   
      });
      $('.btn_u_financial').on('click',function(){
    	  $('#u_financial').show();   
    	  $('#base').hide();   
      });
  });
  </script>
</body>
</html>