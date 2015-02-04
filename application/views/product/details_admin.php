<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>product/listpage_admin' ><div>产品列表(上架) </div></a>
                </li>
                <li><a href='<?=base_url();?>product/listpage_admin_invalid' ><div>产品列表(下架) </div></a></li>
                <li><a href='<?=base_url();?>product/add' ><div>新增产品 </div></a></li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->

<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">



        <div class="toolbar type-button">
            <h4><?php echo validation_errors(); ?></h4>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
            <div class="c50l">
                <h3>编辑产品</h3>
            </div>
        </div>


        <?=form_open_multipart('product/details_admin/'.$v->id);?>

        <fieldset>
            <legend>添加产品 </legend>

            <table>
                <col width="150">

                <tr>
                    <th><label for="text">产品名称 <span>*</span></label></th>
                    <td><input disabled type="text" name="title" data-validate="required" value="<?=$v->title?>"/>
                    </td>
                </tr>
                <tr>
                    <th><label for="properties">规格 </label></th>
                    <td><textarea disabled  name="properties" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="feature">产品功效 </label></th>
                    <td><textarea disabled  name="feature" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="usage_method">使用方法 </label></th>
                    <td><textarea disabled  name="usage_method" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="ingredient">所含成份 </label></th>
                    <td><textarea disabled  name="ingredient" cols="50" rows="6" id="remarks" size="20" ></textarea><br />
                    </td>
                </tr>
                <tr>
                    <th><label for="price_special">特约代理价 <span>*</span></label></th>
                    <td>
                        <input disabled  name="price_special" data-validate="required,decimal" value="<?=cny($v->price_special)?>"/>(单位: 元)
                    </td>
                </tr>
                <tr>
                    <th><label for="price_last_2">一级代理价 <span>*</span></label></th>
                    <td>
                        <input disabled  name="price_last_2" data-validate="required,decimal" value="<?=cny($v->price_last_2)?>"/>(单位: 元)
                    </td>
                </tr>
                <tr>
                    <th><label for="price_last_3">二级代理价 <span>*</span></label></th>
                    <td>
                        <input disabled  name="price_last_3" data-validate="required,decimal" value="<?=cny($v->price_last_3)?>"/>(单位: 元)
                    </td>
                </tr>
                <tr>
                    <th><label for="price_normal">零售价 <span>*</span></label></th>
                    <td>
                        <input disabled  name="price_normal" data-validate="required,decimal" value="<?=cny($v->price_normal)?>"/>(单位: 元)
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
