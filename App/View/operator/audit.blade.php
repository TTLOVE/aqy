         @include('header')
         
        <section id="content">
            <section class="vbox">
                <section class="scrollable wrapper">
                    <div class="col-lg-12">
                        <!-- .breadcrumb -->
                        <ul class="breadcrumb">
                            <li><a href="#"><i class="fa fa-home"></i> 运营商</a></li>
                            <li><a href="#"><i class="fa fa-list-ul"></i> 运营商入驻</a></li>
                            <li class="active">运营商审核</li>
                        </ul>
                        <!-- / .breadcrumb -->
                    </div>
                    <div class="col-lg-12">
                        <section class="panel panel-default">
                            <div class="table-responsive">
                            
                                <table class="table table-striped b-t b-light">
                                    <tbody>
                                    @if(!empty($info['company_name']))
                                        <tr>
                                            <td>ID：{{$info['operator_id']}}</td>
                                            <td>运营商：{{$info['operator_name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>商户额度：{{$info['money_limit']}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>U融汇账户：U融汇账户</td>
                                            <td>账户余额：账户余额</td>
                                        </tr>
                                        <tr>
                                            <td>企业名称：{{$info['company_name']}}</td>
                                            <td>法人姓名：{{$info['corporation_name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>法人身份证：{{$info['corporation_id_card_sn']}}</td>
                                            <td>手机号码：{{$info['phone']}}</td>
                                        </tr>
                                        <tr>
                                            <td>企业账户：{{$info['company_account']}}</td>
                                            <td>开户行：{{$info['company_account_name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>支行信息：{{$info['bank_branch_info']}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>法人身份证照：<img alt="" src="{{$info['corporation_id_card_img_front']}}!260x260">&nbsp;&nbsp;&nbsp;&nbsp;<img alt="" src="{{$info['corporation_id_card_img_opposite']}}!260x260"></td>
                                            <td>营业执照：<img alt="" src="{{$info['company_business_licence']}}!260x260"></td>
                                        </tr>
                                        <tr>
                                            <td>上传对公开户许可证：<img alt="" src="{{$info['company_public_licence']}}!260x260"></td>
                                            <td></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>ID：{{$info['operator_id']}}</td>
                                            <td>运营商：{{$info['operator_name']}}</td>
                                        </tr>
                                        <tr>
                                            <td>商户额度：{{$info['money_limit']}}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td>U融汇账户：U融汇账户</td>
                                            <td>账户余额：账户余额</td>
                                        </tr>
                                        <tr>
                                            <td>姓名：{{$info['corporation_name']}}</td>
                                            <td>身份证：{{$info['corporation_id_card_sn']}}</td>
                                        </tr>
                                        <tr>
                                            <td>手机号码：{{$info['phone']}}</td>
                                            <td>银行卡号：{{$info['company_account']}}</td>
                                        </tr>
                                        <tr>

                                            <td>开户行：{{$info['company_account_name']}}</td>
                                            <td>支行信息：{{$info['bank_branch_info']}}</td>
                                        </tr>
                                        <tr>
                                            <td>银行卡正面照：<img alt="" src="{{$info['bank_card_img']}}!260x260"></td>
                                            <td></td>
                                        </tr>
                                    @endif
                                        <tr align="center"><td colspan="2"><button class='check' data-status='2'>通过</button>&nbsp;&nbsp;<button class='check' data-status='3'>不通过</button></td></tr>
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

      $('.check').on('click',function(){
          var status = $(this).data('status');
          var id = '{{$info["operator_id"]}}';
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
                  window.location.href = '/admin/operator/operatorAuditList';
              }
          });
      });

         
  });
  </script>
</body>
</html>