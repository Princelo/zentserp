<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>report/index_admin' ><div>代理报表查询 </div></a>
                </li>
                <li><a href='<?=base_url();?>report/index_zents' ><div>Zents总报表查询 </div></a></li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->
<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">


        <div class="info view_form">
            <h2>月报表(Zents)</h2>
            <script>
                if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                    alert("<?=$this->session->flashdata('flashdata', 'value');?>");
            </script>
            <table width="100%">
                <!--<col width="50%">
                <col width="50%">-->
                <tr>
                    <th>日期</th>
                    <th>总金额(含运费)</th>
                    <th>产品总金额</th>
                    <th>运费总金额</th>
                    <th>成本金额</th>
                    <th>回扣总量(含推荐)</th>
                    <th>回扣总量(不含推荐)</th>
                    <th>回扣(经总差价)</th>
                    <th>回扣(经市差价)</th>
                    <th>回扣(市总差价)</th>
                    <th>推荐回扣</th>
                    <th>订单数</th>
                </tr>
                <? $n = 0; ?>
                <? foreach($bills as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><?=$v->date?></td>
                        <td><?=cny($v->total_volume);?></td>
                        <td><?=cny($v->products_volume);?></td>
                        <td><?=cny($v->post_fee);?></td>
                        <td><?=cny($v->products_cost);?></td>
                        <td><?=cny($v->return_profit_volume);?></td>
                        <td><?=cny($v->normal_return_profit_volume);?></td>
                        <td><?=cny($v->return_profit_3_1);?></td>
                        <td><?=cny($v->return_profit_3_2);?></td>
                        <td><?=cny($v->return_profit_2_1);?></td>
                        <td><?=cny($v->extra_return_profit_volume);?></td>
                        <td><?=$v->order_quantity?></td>
                    </tr>
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



        <div class="toolbar type-button">
            <form action="<?=base_url()?>report/download_xls" method="post">
                <input name="report_type" value="<?=$report_type?>" type="hidden" />
                <input name="date_from" value="<?=$date_from?>" type="hidden" />
                <input name="date_to" value="<?=$date_to?>" type="hidden" />
                <input value="下载本报表" type="submit" />
            </form>


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