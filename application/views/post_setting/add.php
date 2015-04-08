<div id="container">
    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">



            <div class="toolbar type-button">
                <div class="c50l">
                    <h3 style="color:#f00">运费规则设置</h3>
                </div>
            </div>
<?=form_open_multipart('post_setting/add');?>

<fieldset>
    <legend>添加规则 </legend>

    <table>
        <col width="150">

        <tr>
            <th><label>省份</label></th>
            <td>
                <? $provinces = getArrProvinces() ?>
                <select name="province_id" class="provinceSelect">
                    <option value="0">任意</option>
                    <?foreach($provinces as $k => $v){?>
                        <option value="<?=$k?>"><?=$v?></option>
                    <?}?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>城市</label></th>
            <td>
                <select name="city_id" class="citySelect">
                    <option value="0">任意</option>
                </select>
            </td>
        </tr>
        <tr>
            <th><label>首重重量</label></th>
            <td><input type="text" name="first_weight" data-validate="required,number" />(单位: 克)</td>
        </tr>
        <tr>
            <th><label>续重单位重量</label></th>
            <td><input type="text" name="additional_weight" data-validate="required,number" />(单位: 克)</td>
        </tr>
        <tr>
            <th><label for="first_pay">起步价</label></th>
            <td>
                <input type="text" name="first_pay" data-validate="required,decimal" value="<?=set_value('first_pay')?>"/>(单位: 元)
            </td>
        </tr>
        <tr>
            <th><label for="additional_pay">续重价</label></th>
            <td>
                <input type="text" name="additional_pay" data-validate="required,decimal" value="<?=set_value('additional_pay')?>"/>(单位: 元)
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
            <script>
                $(".citySelect").html('<option value="0">任意</option>');
                var city = <?=getJsonCity();?>;
                city = city.provinces;
                var optionhtml = '<option value="0">任意</option>';
                $(".provinceSelect").change(function(){
                    var optionhtml = '<option value="0">任意</option>';
                    for(var key in city){
                        if(city[key].id == $('.provinceSelect').val()){
                            for(var ikey in city[key].cities)
                                for(var iikey in city[key].cities[ikey])
                                    optionhtml += "<option value=\""+iikey+"\">"+city[key].cities[ikey][iikey]+"</option>";
                        }
                    }
                    $(".citySelect").html(optionhtml);
                    if($('.provinceSelect').val() == '0')
                    {
                        $(".citySelect").html('<option value="0">任意</option>');
                    }
                });
            </script>

<?=form_close();?>

</div>
