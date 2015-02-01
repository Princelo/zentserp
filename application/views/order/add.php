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


        <?=form_open_multipart('order/add/'.$product_id);?>

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
                    <input type="submit" name="btnSubmit" value="提交 "  />			</div>
                <div class="c50r right">
                </div>
            </div>


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
