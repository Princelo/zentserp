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


        <div class="info view_form">
            <h2><?=$type_name?>列表</h2>
            <script>
                if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                    alert("<?=$this->session->flashdata('flashdata', 'value');?>");
            </script>
            <div>
                <form action="<?=base_url()?>product/listpage_admin?is_trial=true" method="get">
                    <table>
                        <tr>
                            <th>搜索</th>
                            <th>所属分类&nbsp;&nbsp;
                                <select name="category">
                                    <option value="">所有</option>
                                    <option value="0"><?=getCategoryName(0)?></option>
                                    <option value="1"><?=getCategoryName(1)?></option>
                                    <option value="2"><?=getCategoryName(2)?></option>
                                    <option value="3"><?=getCategoryName(3)?></option>
                                    <option value="4"><?=getCategoryName(4)?></option>
                                </select>
                            </th>
                            <th>
                                产品名称、功效:<input type="text" name="search" value="<?=set_value('search')?>"  />
                            </th>
                            <th style="display: none;">
                               价格区间(低) <input type="text" name="price_low" value="<?=set_value('price_low')?>" />
                            </th>
                            <th style="display: none;">
                                价格区间(高) <input type="text" name="price_high" value="<?=set_value('price_high')?>" />
                            </th>
                            <input type="hidden" name="trial_type" value="<?=$trial_type?>" />
                            <input type="hidden" name="is_trial" value="true" />
                            <th>
                                <input type="submit" />
                            </th>

                        </tr>
                        </table>
                </form>
            </div>
            <table width="100%">
                <!--<col width="50%">
                <col width="50%">-->
                <tr>
                    <th>ID</th>
                    <th>产品名称</th>
                    <td>所属分类</td>
                    <th>规格</th>
                    <th>产品功效</th>
                    <th>所含成分</th>
                    <th>产品图片</th>
                    <th>单价</th>
                    <th></th>
                </tr>
                <? $n = 0; ?>
                <? if(!empty($products)) { ?>
                <? foreach($products as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><?=$v->id?></td>
                        <td><?=$v->title?></td>
                        <td><?=getCategoryName($v->category)?></td>
                        <td class="max-width">
                            <div class="max-width">
                                <?=$v->properties;?>
                            </div>
                        </td>
                        <td class="max-width">
                            <div class="max-width">
                                <?=$v->feature?>
                            </div>
                        </td>
                        <td><?//=$v->ingredient;?></td>
                        <td><img src="<?=base_url().'uploads/'.thumb($v->img)?>" /></td>
                        <td><?=cny($v->price)?></td>
                        <td><a href="<?=base_url()?>product/details_admin/<?=$v->id;?>?is_trial=true">编辑</a></td>
                    </tr>
                <? } ?>
                <? } ?>
            </table>
            <div class="page"><?=$page;?></div>
            <script>
                /*function myconfirm(id){
                 if (confirm("are you sure?")){
                 window.location.href = "<?=base_url()?>index.php/unvadmin/singerdelete/"+id;
                 } else {

                 }
                 }*/
            </script>


        </div>



        <div class="">
            <h2></h2>


        </div>



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
<!-- end: #col4 -->	</div>

<div id="footer">
    Copyright &copy; <?=date('Y');?> by ZENTS<br/>
    All Rights Reserved.<br/>
</div><!-- footer -->
</body>
</html>