
<div id="container">



<!-- begin: #col1 - first float column -->
<div id="col1" role="complementary" style="display: block;">
    <div id="col1_content" class="clearfix">

        <ul id="left_menu">
            <li>
                <a href='<?=base_url()?>order/listpage' ><div>订单列表 </div></a>
            </li>
            <li>
                <a href='<?=base_url()?>order/index_sub' ><div>下级代理订单查询 </div></a>
            </li>
            <li>
                <a href="<?=base_url()?>order/cart"><div>我的购物车</div></a>
            </li>
        </ul>
    </div>
</div>
<!-- end: #col1 -->
<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">



        <div class="toolbar type-button">
            <script>
                if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                    alert("<?=$this->session->flashdata('flashdata', 'value');?>");
            </script>
            <div class="c50l">
                <h3>订单详情 订单号(<?=$v->id;?>) </h3>
            </div>
        </div>


            <fieldset class="my my-large">
                <legend>订单详情 </legend>

                <table class="float_left margin-left">
                    <col width="150">

                    <tr class="odd">
                        <th><label for="order_id">ID </label></th>
                        <td>
                            <span class="info"><?=$v->id?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>产品总量</th>
                        <td>
                            <span class="info"><?=$v->quantity?></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>产品种类数目</th>
                        <td>
                            <span class="info"><?=$v->diff_quantity?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><a href="<?=base_url()?>order/order_product/<?=$v->id?>">详情</a>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>价格类別</th>
                        <td>
                            <span class="info"><?=$v->purchase_level?></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>订单产品总价(不含运费)</th>
                        <td>
                            <span class="info"><?=cny($v->amount)?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>运费</th>
                        <td>
                            <span class="info"><?=cny($v->post_fee)?></span>
                        </td>
                    </tr>
                </table>
                <table class="float_left margin-left">
                    <tr class="odd">
                        <th>取货方式</th>
                        <td>
                            <span class="info"><?=$v->is_post=='t'?"邮寄":"自取"?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>收货地址</th>
                        <td>
                            <span class="info important<?=$v->is_post=='f'?" hidden":''?>"> <?=getStrProvinceName($v->province_id)?>&nbsp;<?=getStrCityName($v->city_id)?>&nbsp;<?=$v->address_info?> </span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>联系人</th>
                        <td>
                            <span class="info important"><?=$v->linkman?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>联系电话</th>
                        <td>
                            <span class="info important"><?=$v->mobile?></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>订单备注</th>
                        <td>
                            <span class="info important"><?=$v->remark?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>订单总价</th>
                        <td>
                            <span class="info important">￥<?=bcadd( money($v->amount), money($v->post_fee), 2)?></span>
                        </td>
                    </tr>
                </table>
                <table class="float_left margin-left">
                    <tr class="odd">
                        <th>是否已付款</th>
                        <td>
                            <span class="<?=$v->is_pay=='t'&&$v->is_correct=='t'?"accept":"cross";?>"></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>付款金额</th>
                        <td>
                            <span class="info important"><?=cny($v->pay_amt)?></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>付款方式</th>
                        <td>
                            <span class="info"><?=$v->pay_method=='alipay'?'线上付款':'线下付款';?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>付款时间</th>
                        <td>
                            <span class="info"><?=substr($v->pay_time, 0, 19)?></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>付款数目是否正确</th>
                        <td>
                            <span class="<?=$v->is_pay=='t'&&$v->is_correct=='t'?"accept":"cross";?>"></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>订单取消情況</th>
                        <td>
                            <span class="info"><?=$v->is_cancelled=='t'?"已取消":"正常"?></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>订单提交时间</th>
                        <td>
                            <span class="info"><?=substr($v->stock_time, 0, 19)?></span>
                        </td>
                    </tr>
                    <tr class="even">
                        <th>是否完成订单</th>
                        <td>
                            <span class="<?=$v->is_pay=='t'&&$v->is_correct=='t'?"accept":"cross";?>"></span>
                        </td>
                    </tr>
                    <tr class="odd">
                        <th>完成订单时间</th>
                        <td>
                            <span class="info"><?=substr($v->finish_time, 0, 19)?></span>
                        </td>
                    </tr>
                </table>

            </fieldset>

            <script>
                function finishConfirm()
                {
                    if (confirm("确认执行？")){
                       $("form").submit();
                    } else {

                    }
                }
            </script>


            <div class="toolbar type-button">
                <div class="c50l">
                    <!--<input type="submit" name="btnSubmit" value="提交 "  />-->			</div>
                <div class="c50r right">
                </div>
            </div>



    </div>
    <!-- IE Column Clearing -->
    <div id="ie_clearing">&nbsp;</div>
    <!--
            <script>
                $(document).ready(function(){
                    Calendar.setup({
                        weekNumbers   : true,
                        fdow		: 0,
                        inputField : 'end_time',
                        trigger    : 'end_time-trigger',
                        onSelect   : function() { this.hide() }
                    });

                });

            </script>

        : IE Column Clearing -->
</div>
