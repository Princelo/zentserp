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
                    <a href='<?=base_url()?>trial_product/listpage_admin' ><div>试用品(上架) </div></a>
                </li>
                <li><a href='<?=base_url();?>trial_product/listpage_admin_invalid' ><div>试用品(下架) </div></a></li>
                <li><a href='<?=base_url();?>product/add' ><div>新增产品 </div></a></li>
                <li><a href='<?=base_url();?>trial_product/add' ><div>新增试用品 </div></a></li>
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
                <h3>产品详情</h3>
            </div>
        </div>


        <?=form_open_multipart('trial_product/details_admin/'.$v->id);?>

        <fieldset>
            <legend>产品详情 </legend>

            <table>
                <col width="150">

                <tr>
                    <th><label for="text">产品名称 <span>*</span></label></th>
                    <td><input type="text" name="title" data-validate="required" value="<?=$v->title?>"/>
                    </td>
                </tr>
                <tr>
                    <th><label>所属分类</label></th>
                    <td>
                        <select name="category">
                            <option value="0" <?=$v->category==0?"selected=\"selected\"":""?>><?=getCategoryName(0)?></option>
                            <option value="1" <?=$v->category==1?"selected=\"selected\"":""?>><?=getCategoryName(1)?></option>
                            <option value="2" <?=$v->category==2?"selected=\"selected\"":""?>><?=getCategoryName(2)?></option>
                            <option value="3" <?=$v->category==3?"selected=\"selected\"":""?>><?=getCategoryName(3)?></option>
                            <option value="4" <?=$v->category==4?"selected=\"selected\"":""?>><?=getCategoryName(4)?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>总重量</label></th>
                    <td>
                        <input type="text" name="weight" data-validate="required,number" value="<?=$v->weight?>" />(单位: 克)
                    </td>
                </tr>
                <tr>
                    <th><label for="properties">规格 </label></th>
                    <td><textarea name="properties" cols="50" rows="6" id="remarks" size="20" ><?=$v->properties?></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="feature">产品功效 </label></th>
                    <td><textarea name="feature" cols="50" rows="6" id="remarks" size="20" ><?=$v->feature?></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="usage_method">使用方法 </label></th>
                    <td><textarea name="usage_method" cols="50" rows="6" id="remarks" size="20" ><?=$v->usage_method?></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="ingredient">所含成份 </label></th>
                    <td><textarea name="ingredient" cols="50" rows="6" id="remarks" size="20" ><?=$v->ingredient?></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="price">单价 <span>*</span></label></th>
                    <td>
                        <input disabled  name="price" data-validate="required,decimal" value="<?=cny($v->price)?>"/>(单位: 元)
                    </td>
                </tr>
                <tr>
                    <th>产品图片</th>
                    <td>
                        <img src="<?=base_url().'uploads/'.$v->img;?>" />
                    </td>
                </tr>
                <tr>
                    <th><label for="is_valid">是否上架 <span>*</span></label></th>
                    <td><select name="is_valid" id='time_slot'>
                            <option value="1" <?=($v->is_valid=='t')?"selected=\"selected\"":"";?>>上架</option>
                            <option value="0" <?=($v->is_valid=='f')?"selected=\"selected\"":"";?>>下架</option>
                        </select></td>
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
