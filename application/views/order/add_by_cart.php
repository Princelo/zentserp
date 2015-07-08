<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>product/listpage' ><div>产品列表 </div></a>
                </li>
                <? if($this->session->userdata('level') != 0) {?>
                    <li>
                        <a href='<?=base_url()?>product/listpage?is_trial=true' ><div>试用品列表 </div></a>
                    </li>
                    <li>
                        <a href='<?=base_url()?>product/listpage?is_trial=true&trial_type=<?=get_trial_type('event products');?>' ><div>活动产品列表 </div></a>
                    </li>
                <?}?>
            </ul>
        </div>
    </div>
    <!-- begin: #col3 static column -->
    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">



            <div class="toolbar type-button">
                <h4><?php echo validation_errors(); ?></h4>
                <script>
                    if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                        alert("<?=$this->session->flashdata('flashdata', 'value');?>");
                </script>
                <div class="c50l">
                    <h3>添加订单 </h3>
                </div>
            </div>


            <?=form_open_multipart('order/add');?>
            <input name="token" value="<?=$token?>" type="hidden"/>

            <fieldset>
                <legend>添加订单 </legend>

                <table>
                    <col width="150">

                    <tr>
                        <th><label for="contact">订单联系人 <span>*</span></label></th>
                        <td>
                            <input name="contact" data-validate="required" value="<?=set_value('contact')?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="mobile">联系电话 <span>*</span></label></th>
                        <td>
                            <input name="mobile" data-validate="required" value="<?=set_value('mobile')?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="pay_method">付款方式</label></th>
                        <td>
                            <select name="pay_method">
                                <option value="alipay">线上付款</option>
                                <option value="offline">线下付款</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="remark">备注</label></th>
                        <td>
                            <input name="remark" data-validate="max(100)" value="<?=set_value('remark')?>" maxlength="100" size="30" />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="is_post">收货方式</label></th>
                        <td>
                            <select name="is_post" id="is_post">
                                <option value="0" <?//=$is_post==false?"selected=\"selected\"":"";?>>自取</option>
                                <option value="1" <?//=$is_post==true?"selected=\"selected\"":"";?>>快递</option>
                            </select>
                        </td>
                    </tr>
                    <script>
                        $("#is_post").change(function(){
                            if($(this).val() == "1"){
                                $("#address").show();
                            }else{
                                $("#address").hide();
                            }
                        });
                    </script>
                    <tr style="display: none;" id="address">
                        <th><label>地址</label> <span>*</span></th>
                        <?
                        $provinces = getArrCity()->provinces;
                        ?>
                        <td>
                            <select name="province_id" class="provinceSelect">
                                <?foreach($provinces as $k => $v){?>
                                    <option value="<?=$v->id?>"><?=$v->name?></option>
                                <?}?>
                            </select>
                            <select name="city_id" class="citySelect">
                                <option value="?">北京市</option>
                            </select>
                            <input name="address_info" data-validate="required" value="<?=set_value('address_info')?>"/>
                        </td>
                    </tr>
                    <input value="<?=$str?>" name="cart_info" type="hidden" />
                </table>

            </fieldset>


            <div class="toolbar type-button">
                <div class="c50l">
                    <input type="submit" name="btnSubmit" value="提交 "  />			</div>
                <div class="c50r right">
                </div>
            </div>

            <fieldset>
                <legend>我的购物车</legend>
                <table>
                    <tr>
                        <th>产品名称</th>
                        <th>入货数量</th>
                        <th>产品单价</th>
                        <th>单项总价(不含运费)</th>
                    </tr>
                    <? $total = 0;?>
                    <? $n = 0; ?>
                    <?foreach($products as $k => $v){?>
                        <? if(array_key_exists($v->pid, $products_quantity)):?>
                        <? $n ++; ?>
                        <?$total = bcadd($total, bcmul(money($v->unit_price), $products_quantity[$v->pid], 2), 2)?>
                        <tr class="<?=$n%2==0?"even":"odd";?>">
                            <td><?=$v->title?></td>
                            <td><?=$products_quantity[$v->pid]?></td>
                            <td><?=cny($v->unit_price)?></td>
                            <td>￥<?=bcmul(money($v->unit_price), $products_quantity[$v->pid], 2)?></td>
                        </tr>
                        <? endif ?>
                    <?}?>

                    <tr>
                        <th>购物车总价(不含运费)</th>
                        <th>￥<?=$total?></th>
                    </tr>
                </table>
            </fieldset>


            <?=form_close();?>

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
    <script>
        $(".citySelect").html('<option value="2">北京市</option>');
        var city = <?=getJsonCity();?>;
        city = city.provinces;
        var optionhtml = "";
        $(".provinceSelect").change(function(){
            optionhtml = "";
            for(var key in city){
                if(city[key].id == $('.provinceSelect').val()){
                    for(var ikey in city[key].cities)
                        for(var iikey in city[key].cities[ikey])
                            optionhtml += "<option value=\""+iikey+"\">"+city[key].cities[ikey][iikey]+"</option>";
                }
            }
            $(".citySelect").html(optionhtml);
        });
    </script>
