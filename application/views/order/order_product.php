<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>product/listpage' ><div>产品列表 </div></a>
                </li>
                <li><a href="<?=base_url()?>order/index_sub" class="" style="background: none;"><div>下级代理订单查询 </div></a>
                </li>
                <li><a href="<?=base_url()?>order/cart" class="" ><div>我的购物车 </div></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">


            <div class="info view_form">
                <script>
                    if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                        alert("<?=$this->session->flashdata('flashdata', 'value');?>");
                </script>
                <table width="100%">
                    <!--<col width="50%">
                    <col width="50%">-->
                    <tr>
                        <th>产品编号</th>
                        <th>产品名称</th>
                        <th>是否为试用品</th>
                        <th>购买价格</th>
                        <th>入货数量</th>
                    </tr>
                    <? $n = 0; ?>
                    <? if(!empty($products)) {?>
                        <? foreach($products as $k => $v){ ?>
                            <? $n ++; ?>
                            <tr class="<?=$n%2==0?"even":"odd";?>">
                                <td><?=$v->id?></td>
                                <td><a href="<?=base_url();?>product/details/<?=$v->id?>"><?=$v->title?></a></td>
                                <td>
                                    <?=$v->is_trial == 't'?'是':'否'?>
                                </td>
                                <td>
                                    <?=$v->amount?>
                                </td>
                                <td><?=$v->quantity?></td>
                            </tr>
                        <? } ?>
                    <? } ?>
                </table>


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