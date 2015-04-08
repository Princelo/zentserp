<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>product/listpage' ><div>产品列表 </div></a>
                </li>
                <li>
                    <a href='<?=base_url()?>trial_product/listpage' ><div>试用品列表 </div></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->

    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">



            <div class="toolbar type-button">
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
                <div class="c50l">
                    <h3>产品详情</h3>
                </div>
            </div>



            <fieldset>
                <legend>产品详情 </legend>

                <table>
                    <col width="150">

                    <tr>
                        <th><label for="text">产品名称 <span>*</span></label></th>
                        <td><input disabled type="text" name="title" data-validate="required" value="<?=$v->title?>"/>
                        </td>
                    </tr>
                    <tr>
                        <th><label>所属分类</label></th>
                        <td><?=getCategoryName($v->category)?></td>
                    </tr>
                    <tr>
                        <th><label>总重量</label></th>
                        <td><input type="text" value="<?=$v->weight?>g" /></td>
                    </tr>
                    <tr>
                        <th><label for="properties">规格 </label></th>
                        <td><textarea disabled  name="properties" cols="50" rows="6" id="remarks" size="20" ><?=$v->properties?></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="feature">产品功效 </label></th>
                        <td><textarea disabled  name="feature" cols="50" rows="6" id="remarks" size="20" ><?=$v->feature?></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="usage_method">使用方法 </label></th>
                        <td><textarea disabled  name="usage_method" cols="50" rows="6" id="remarks" size="20" ><?=$v->usage_method?></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="ingredient">所含成份 </label></th>
                        <td><textarea disabled  name="ingredient" cols="50" rows="6" id="remarks" size="20" ><?=$v->ingredient?></textarea><br />
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price"><?=getLevelName(1)?>价</label></th>
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
                        <th>产品下订</th>
                        <td><a href="<?=base_url()?>trial_order/add/<?=$v->id?>">立即下订</a></td>
                    </tr>
                </table>

            </fieldset>





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
