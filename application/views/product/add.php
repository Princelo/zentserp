<?php $trial_type = 0;if(isset($_GET['trial_type']) && intval($_GET['trial_type']) > 0 ) { $trial_type = intval($_GET['trial_type']); }?>
<?php
switch(intval($trial_type))
{
    case 0:
        $type_name = '试用品';
        break;
    case 1:
        $type_name = '活动产品';
        break;
    default:
        break;
}
?>
<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>product/listpage_admin' ><div>产品列表(上架) </div></a>
                </li>
                <li><a href='<?=base_url();?>product/listpage_admin_invalid' ><div>产品列表(下架) </div></a></li>
                <li>
                    <a href='<?=base_url()?>product/listpage_admin?is_trial=true' ><div>试用品(上架) </div></a>
                </li>
                <li><a href='<?=base_url();?>product/listpage_admin_invalid?is_trial=true' ><div>试用品(下架) </div></a></li>
                <li><a href='<?=base_url()?>product/listpage_admin?is_trial=true&trial_type=<?=get_trial_type('event products')?>' ><div>活动产品(上架) </div></a>
                </li>
                <li><a href='<?=base_url();?>product/listpage_admin_invalid?is_trial=true&trial_type=<?=get_trial_type('event products')?>' ><div>活动产品(下架) </div></a></li>
                <li><a href='<?=base_url();?>product/add' ><div>新增产品 </div></a></li>
                <li><a href='<?=base_url();?>product/trial_add' ><div>新增试用品 </div></a></li>
                <li><a href='<?=base_url();?>product/trial_add?trial_type=<?=get_trial_type('event products')?>' ><div>新增活动产品 </div></a></li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->

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
                <h3><?=($error!="")?"<span style=\"color:red\">".$error."</span>":"添加产品";?> </h3>
            </div>
        </div>


        <?=form_open_multipart('product/add');?>

            <fieldset>
                <legend>添加产品 </legend>

                <table>
                    <col width="150">

                    <tr>
                        <th><label for="text">产品名称 <span>*</span></label></th>
                        <td><input type="text" name="title" data-validate="required" value="<?=set_value('title')?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><label>所属分类</label></th>
                        <td>
                            <select name="category">
                                <option value="0"><?=getCategoryName(0)?></option>
                                <option value="1"><?=getCategoryName(1)?></option>
                                <option value="2"><?=getCategoryName(2)?></option>
                                <option value="3"><?=getCategoryName(3)?></option>
                                <option value="4"><?=getCategoryName(4)?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><label>总重量</label></th>
                        <td><input type="text" name="weight" data-validate="required,number" />(单位: 克)</td>
                    </tr>
                    <tr>
                        <th><label for="properties">规格 </label></th>
                        <td><textarea name="properties" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="feature">产品功效 </label></th>
                        <td><textarea name="feature" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="usage_method">使用方法 </label></th>
                        <td><textarea name="usage_method" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="ingredient">所含成份 </label></th>
                        <td><textarea name="ingredient" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_special"><?=getLevelName(1)?>价 <span>*</span></label></th>
                        <td>
                            <input name="price_special" data-validate="required,decimal" value="<?=set_value('price_special')?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_last_2"><?=getLevelName(2)?>价 <span>*</span></label></th>
                        <td>
                            <input name="price_last_2" data-validate="required,decimal" value="<?=set_value('price_last_2')?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_last_3"><?=getLevelName(3)?>价 <span>*</span></label></th>
                        <td>
                            <input name="price_last_3" data-validate="required,decimal" value="<?=set_value('price_last_3')?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_normal"><?=getLevelName(0)?>价 <span>*</span></label></th>
                        <td>
                            <input name="price_normal" data-validate="required,decimal" value="<?=set_value('price_normal')?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th>产品图片</th>
                        <td><input name="img" value="上传" onclick="//alert('upload')" type="file" class="" data-validate="" /></td>
                    </tr>
                    <tr>
                        <th><label for="time_slot">是否上架 <span>*</span></label></th>
                        <td><select name="is_valid" id='time_slot'>
                                <option value="1" <?//=($v->is_valid==1)?"selected=\"selected\"":"";?>>上架</option>
                                <option value="0" <?//=($v->is_valid==0)?"selected=\"selected\"":"";?>>下架</option>
                            </select></td>
                    </tr>
                    <input type="hidden" value="<?=$trial_type?>" name="trial_type" />
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
