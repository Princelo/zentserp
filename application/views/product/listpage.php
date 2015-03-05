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
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">


        <div class="info view_form">
            <h2>产品列表 您当前等级为 <span class="red"><?=getLevelName($level)?></span>, 有效价格为 <span class="red"><?=getLevelName($level)?>价</span></h2>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
            <div>
                <form action="<?=base_url()?>product/listpage" method="get">
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
                    <th>产品名称</th>
                    <th>所属分类</th>
                    <th>规格</th>
                    <th>产品功效</th>
                    <th>产品图片</th>
                    <th><span <?=$level==1?"class=\"red\"":"";?>><?=getLevelName(1)?>价</span></th>
                    <th><span <?=$level==2?"class=\"red\"":"";?>><?=getLevelName(2)?>价</span></th>
                    <th><span <?=$level==3?"class=\"red\"":"";?>><?=getLevelName(3)?>价</span></th>
                    <th><span <?=$level==0?"class=\"red\"":"";?>><?=getLevelName(0)?>价</span></th>
                    <th></th>
                    <th></th>
                </tr>
                <? $n = 0; ?>
                <? if(!empty($products)) {?>
                <? foreach($products as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
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
                        <td><img src="<?=base_url().'uploads/'.thumb($v->img)?>" /></td>
                        <td><?=cny($v->price_special)?></td>
                        <td><?=cny($v->price_last_2)?></td>
                        <td><?=cny($v->price_last_3)?></td>
                        <td><?=cny($v->price_normal)?></td>
                        <td><a href="<?=base_url()?>product/details/<?=$v->id;?>">查看详情</a></td>
                        <td><a href="<?=base_url()?>order/add/<?=$v->id;?>">产品下订</a></td>
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
    Copyright &copy; <?=date('Y');?> by UNVWEB<br/>
    All Rights Reserved.<br/>
</div><!-- footer -->
</body>
</html>