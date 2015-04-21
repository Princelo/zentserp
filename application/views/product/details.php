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
                    <a href='<?=base_url()?>trial_product/listpage' ><div>试用品列表 </div></a>
                </li>
                <?}?>
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
                        <th><label for="price_special"><?=getLevelName(1)?>价</label></th>
                        <td>
                            <input disabled  name="price_special" data-validate="required,decimal" value="<?=cny($v->price_special)?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_last_2"><?=getLevelName(2)?>价</label></th>
                        <td>
                            <input disabled  name="price_last_2" data-validate="required,decimal" value="<?=cny($v->price_last_2)?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_last_3"><?=getLevelName(3)?>价</label></th>
                        <td>
                            <input disabled  name="price_last_3" data-validate="required,decimal" value="<?=cny($v->price_last_3)?>"/>(单位: 元)
                        </td>
                    </tr>
                    <tr>
                        <th><label for="price_normal"><?=getLevelName(0)?>价</label></th>
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
                        <th>产品下订</th>
                        <td><a href="<?=base_url()?>order/add/<?=$v->id?>">立即下订</a></td>
                    </tr>
                    <tr>
                        <th>加入购物车</th>
                        <td>
                            <div class="buy-quantity">
                                <input value="1" name="product<?=$v->id?>" id="quantity-<?=$v->id?>"/>
                                <a href="javascript:void(0)" class="increase" onclick="increase(<?=$v->id?>)">+</a>
                                <a href="javascript:void(0)" class="decrease" onclick="decrease(<?=$v->id?>)">-</a>
                            </div>
                            <a href="javascript:void(0);" onclick="addtocart(<?=$v->id?>)">加入购物车</a>
                        </td>
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
    <script>
        var addtocart = function(id){
            if(parseInt($("input[name=\"product"+id+"\"]").val()) <= 0)
            {
                alert('入货数量必须大于零');
                return false;
            }
            $.post("<?=base_url()?>order/addtocart", { "product_id": id + "", "is_trial": "true", "quantity": $("input[name=\"product"+id+"\"]").val()  },
                function(data){
                    alert(data.info);
                }, "json");
        }
        var increase = function(id)
        {
            $("#quantity-"+id).val(parseInt($("#quantity-" + id).val()) + 1);
        }
        var decrease = function(id)
        {
            if($("#quantity-" + id).val() > 1)
                $("#quantity-"+id).val(parseInt($("#quantity-" + id).val()) - 1);
        }
    </script>
