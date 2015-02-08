<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>product/listpage' ><div>产品列表 </div></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- begin: #col3 static column -->
    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">



            <div class="toolbar type-button">
                <h4><?php echo validation_errors(); ?></h4>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
                <div class="c50l">
                    <h3><?=($error!="")?"<span style=\"color:red\">".$error."</span>":"添加订单";?> </h3>
                </div>
            </div>


            <?=form_open_multipart('order/add_non_member/'.$product_id);?>

            <fieldset>
                <legend>添加订单 </legend>

                <table>
                    <col width="150">

                    <tr>
                        <th><label for="text">产品名称 </label></th>
                        <td><input type="text" value="<?=$product_name;?>" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="count">进货数量 <span>*</span></label></th>
                        <td>
                            <input name="count" data-validate="required,number" value="<?=set_value('count')?>"/>件
                        </td>
                    </tr>
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
                        <th><label for="is_post">收货方式</label></th>
                        <td>
                            <select name="is_post">
                                <option value="0" <?//=$is_post==false?"selected=\"selected\"":"";?>>自取</option>
                                <!--<option value="1" <?//=$is_post==true?"selected=\"selected\"":"";?>>快递</option>-->
                            </select>
                        </td>
                    </tr>
                    <tr style="ddisplay: none;">
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
                </table>

            </fieldset>


            <div class="toolbar type-button">
                <div class="c50l">
                    <input type="submit" name="btnSubmit" value="加入购物车 "  />			</div>
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
                            <th>订单总价(不含运费)</th>
                            <th>运费</th>
                            <th>移除</th>
                        </tr>
                        <? $total = 0;?>
                        <? $total_post_fee = 0;?>
                        <? $n = 0; ?>
                        <?foreach($cart as $k => $v){?>
                            <? $n ++; ?>
                            <?$total = bcadd($total, bcmul(money($v->amount), $v->count, 2), 2)?>
                            <?$total_post_fee = bcadd($total_post_fee, $v->post_fee)?>
                        <tr class="<?=$n%2==0?"even":"odd";?>">
                            <td><?=$v->title?></td>
                            <td><?=$v->count?></td>
                            <td><?=cny($v->amount)?></td>
                            <td><?=bcmul(money($v->amount), $v->count, 2)?></td>
                            <td><?=$v->post_fee?></td>
                            <td><a href="<?=base_url()?>order/delete/<?=$v->order_id?>/<?=$product_id?>">移出购物车</a></td>
                        </tr>
                        <?}?>

                        <tr>
                            <th>购物车总价</th>
                            <th>￥<?=$total?></th>
                            <th>运费：￥<?=$total_post_fee?></th>
                            <th>目标金额：￥<?=$target?></th>
                        </tr>
                    </table>
                <?
                if($total >= $target){
                    $enable = true;
                }else{
                    $enable = false;
                }
                ?>
                <div class="toolbar type-button">
                    <div class="c50l">
                        <a href="<?=$enable?base_url()."order/enableCart":"javascript:;"?>" class="btn">结算</a>
                        <?if(!$enable){?>
                        <span>你当前购物车总额为￥<?=$total?>，需满￥<?=$target?>才可结算购物车。</span>
                        <?}?>
                    </div>
                    <div class="c50r right">
                    </div>
                </div>
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
