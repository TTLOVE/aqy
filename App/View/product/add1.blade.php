<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title></title>

</head>

<body>

<ul class="fuckme">

    这是添加产品页面ｏ

</ul>

<form method="post" action="/admin/product/add">
    产品名称:<input type="text" name="usury_name" /><br/>
    产品标题:<input type="text" name="usury_title" /><br/>
    产品简介:<input type="text" name="usury_desc" /><br/>
    所需认证材料:<label><input name="certification[]" type="checkbox" value="1" />个人身份认证 </label>
    <label><input name="certification[]" type="checkbox" value="2" />商户交易流水 </label>
    <label><input name="certification[]" type="checkbox" value="3" />运营商认证 </label><br/>
    申请条件:<textarea rows="3" cols="20" name="apply_condition"></textarea><br/>
    额度范围:<label><input name="credit_type" type="radio" value="1" />整百 </label>
    <label><input name="credit_type" type="radio" value="2" />整千 </label><br/>
    范围区间:<input type="text" name="credit_min" />-<input type="text" name="credit_max" /><br/>
    期限范围:<label><input name="usury_time_type" type="radio" value="1" />日度 </label>
    <label><input name="usury_time_type" type="radio" value="2" />月度 </label><br/>
    范围区间:<input type="text" name="usury_time_min" />-<input type="text" name="usury_time_max" /><br/>
    利率范围:<input type="text" name="usury_interest_rate" /><br/>
    考拉服务费率:<input type="text" name="koala_service_rate" /><br/>
    还款途径:<label><input name="repayment_type[]" type="checkbox" value="1" />余额自动划扣 </label>
    <label><input name="repayment_type[]" type="checkbox" value="2" />银行卡自动划扣 </label>
    <label><input name="repayment_type[]" type="checkbox" value="3" />主动还款 </label><br/>
    提前还款:<label><input name="repayment_advance" type="radio" value="1" />不支持 </label>
    <label><input name="repayment_advance" type="radio" value="2" />提前还款全部,费用不减免 </label>
    <label><input name="repayment_advance" type="radio" value="2" />提前还款当期,费用不减免 </label><br/>
    逾期违约金:<input type="text" name="overdue_rate" /><br/>
    每日计算罚息利率:<input type="text" name="overdue_punish_data" /><br/>
    <input type="submit" value="添加产品" />
</form>

</body>

</html>
