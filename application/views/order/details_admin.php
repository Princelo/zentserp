<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">



        <div class="toolbar type-button">
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
            <div class="c50l">
                <h3>订单详情 订单号(<?=$v->id;?>) </h3>
            </div>
        </div>


        <form action="<?=base_url()?>order/details_admin/<?=$v->id?>" method="post">

            <fieldset>
                <legend>订单详情 </legend>

                <table>
                    <col width="150">

                    <tr>
                        <th><label for="order_id">ID </label></th>
                        <td>
                            <input type="text" name="" value="<?=$v->id;?>" disabled="disabled"  />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="">产品名称(ID) </label></th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->title;?>(<?=$v->pid?>)" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="type">代理姓名(ID) </label></th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->username;?>(<?=$v->uid?>)" />
                        </td>
                    </tr>
                    <tr>
                        <th>订单数目</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->quantity?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>产品单价</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=cny($v->unit_price)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>价格类別</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=getLevelName($v->purchase_level)?>价" />
                        </td>
                    </tr>
                    <tr>
                        <th>订单产品总价</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="￥<?=bcmul(money($v->unit_price), $v->quantity, 4)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>运费</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=cny($v->post_fee)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>取货方式</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->is_post=='t'?"邮寄":"自取"?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>收货地址</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value=" <?=getStrProvinceName($v->province_id)?>&nbsp;<?=getStrCityName($v->city_id)?>&nbsp;<?=$v->address_info?> " />
                        </td>
                    </tr>
                    <tr>
                        <th>联系人</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->linkman?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>联系电话</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->mobile?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>订单备注</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->remark?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>订单总价</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="￥<?=bcadd(bcmul(money($v->unit_price), $v->quantity, 4), money($v->post_fee), 4)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>是否已付款</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->is_pay=='t'?"√":"×"?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>付款金额</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=cny($v->pay_amt)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>付款方式</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->is_pay_online=='t'?'线上付款':'线下付款';?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>付款时间</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=substr($v->pay_time, 0, 19)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>付款数目是否正确</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->is_correct=='t'?"√":"×"?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>订单取消情況</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->is_cancelled=='t'?"已取消":"正常"?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>订单提交时间</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=substr($v->stock_time, 0, 19)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>是否完成订单</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=$v->is_correct=='t'&&$v->is_pay=='t'?"√":"×"?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>完成订单时间</th>
                        <td>
                            <input type="text" name="" disabled="disabled" value="<?=substr($v->finish_time, 0, 19)?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><span class="red">确认订单完成</span> </th>
                        <td>
                            <input type="radio" name="finish" value="finish_with_pay" /><span class="red">完成并插入付款纪录(用于线下付款订单)<br />
                            <input type="radio" name="finish" value="finish_without_pay" /><span class="red">完成但不插入付款纪录(用于线上付款订单)<br />
                            <!--<input type="radio" name="finish" value="unfinish_rollback" disabled="disabled">未完成并取消付款纪录(用于线下付款订单)<br />
                            <input type="radio" name="finish" value="unfinish" disabled="disabled">未完成但不取消付款纪录(用于线上付款订单)<br />-->
                            <input type="button" onclick="finishConfirm()" name="btnSubmit" value="提交 "  />			</div>
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


        </form>

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
