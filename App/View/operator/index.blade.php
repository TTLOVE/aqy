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
                                        <th>商户额度(元)</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($list as $row)
                                    <tr>
                                        <td>{{$row['operator_id']}}</td>
                                        <td>{{$row['operator_name']}}</td>
                                        <td>{{$row['money_limit']}}</td>
<!--                                         data-toggle="modal" data-target="#myModal" -->
                                        <td class='audit'> <a href='/admin/operator/operatorAudit?id={{$row["operator_id"]}}'>审核</a></td>
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
<!-- <!-- Modal --> -->
<!-- <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"> -->
<!--   <div class="modal-dialog" role="document"> -->
<!--     <div class="modal-content"> -->
<!--       <div class="modal-header"> -->
<!--         <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
<!--         <h4 class="modal-title" id="myModalLabel">入驻审核</h4> -->
<!--       </div> -->
<!--       <div class="modal-body"> -->
<!--         ... -->
<!--       </div> -->
<!--       <div class="modal-footer"> -->
<!--         <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
<!--         <button type="button" class="btn btn-primary">Save changes</button> -->
<!--       </div> -->
<!--     </div> -->
<!--   </div> -->
<!-- </div> -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">  
    <div class="modal-dialog" role="document" style='width:1000px;'>  
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span  
                        aria-hidden="true">×</span></button>  
                <h4 class="modal-title" id="exampleModalLabel">入驻审核</h4>  
            </div>  
            <div class="modal-body">  
                <form>  
                    <div class="form-group">
                    <div><a href='javascript:void(0);' class='btn_base'>基本信息</a>|<a href='javascript:void(0);' class='btn_u_financial'>U融汇账户</a></div>
                    <div id='base' >
                    <!-- 基本信息  -->
                        <br/>
                        <table>
                            <tr><th>id : </th> <td><span id='operator_id'></span></td></tr>
                            <tr><th>运营商 : </th> <td><span class='company_name'></span></td></tr>
                            <tr><th>商户额度 : </th> <td><span id='money_limit'></span></td></tr>
                        </table>
                    </div>
                    <div id='u_financial' style='display: none;'>
                    <table>
                    <br/>  
                        <tr><td>U融汇账户:&nbsp;&nbsp;U融汇账户</td> <td>账户余额:&nbsp;&nbsp;<span id=''>10000</span></td></tr>
                        <tr><td>企业名称:&nbsp;&nbsp;<span class='company_name'></span></td> <td>法人姓名:&nbsp;&nbsp;<span id='corporation_name'></span>1000</td></tr>
                        <tr><td>法人身份证:&nbsp;&nbsp;<span id='corporation_id_card_sn'></span></td> <td>手机号码:&nbsp;&nbsp;<span id='phone'></span></td></tr>
                        <tr><td>企业账户:&nbsp;&nbsp;<span id='company_account'></span></td> <td>开户行:&nbsp;&nbsp;<span id='company_account_name'></span></td></tr>
                        <tr><td>支行信息:&nbsp;&nbsp;0001</td> <td></td></tr>
                        <tr><td>法人身份证照:<div id='id_card'></div></td> <td>营业执照:<div id='company_business_licence'></div></td></tr>
                        <tr><td>&nbsp;&nbsp;</td><td>&nbsp;&nbsp;</td></tr>
                        <tr><td>上传对公开户许可证:<div id='company_public_licence'></div></td> <td></td></tr>
                    </table>
                    </div>
                    </div>  
                </form>  
            </div>  
            <div class="modal-footer">  
                <input type='hidden' value='' id='check_id'>
                <button type="button" data-status='2' class="btn btn-default check">通过</button>  
                <button type="button" data-status='3' class="btn btn-primary check">不通过</button>  
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
              url : '/admin/operator/checkPassOperator?id='+id+'&status='+status,
              type : 'get',
              dataType : 'json',
              success : function(res){
                  if(res.status == true){
               	      alert('审核成功');
                  	  $('#exampleModal').modal('hide');
                  }else{
                      alert('审核失败');
                      $('#exampleModal').modal('hide');
                  }
                  window.location.reload();
              }
          });
      });

      $('#exampleModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // 触发事件的按钮
           var operator_id = button.data('id'); //运营商id
           $('#check_id').val(operator_id);
            $.ajax({
                url : '/admin/operator/ajaxGetOperatorInfoByid?id='+operator_id,
                type : 'get',
                dataType : 'json',
                async: false,
                success : function(res){
                    if(res.status == true){
                        $('#operator_id').html(res.data.operator_id);
                        $('#money_limit').html(res.data.money_limit);
                        $('#company_account').html(res.data.company_account);
                        $('.company_name').html(res.data.company_name);
                        $('#corporation_id_card_sn').html(res.data.corporation_id_card_sn);
                        $('#company_account_name').html(res.data.company_account_name);
                        $('#phone').html(res.data.phone);
                        $('#corporation_name').html(res.data.corporation_name);
                        $('#id_card').html('<img src="'+res.data.corporation_id_card_img_front+'!260x260">&nbsp;&nbsp;<img src="'+res.data.corporation_id_card_img_opposite+'!260x260">');
                        $('#company_business_licence').html('&nbsp;&nbsp;&nbsp;&nbsp;<img src="'+res.data.company_business_licence+'!260x260">');
                        $('#company_public_licence').html('<img src="'+res.data.company_public_licence+'!260x260">');
                    }
                }
            });
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