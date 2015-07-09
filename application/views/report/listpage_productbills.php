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
                <h2>产品明细报表(<?=$bills[0]->date_from?> - <?=$bills[0]->date_to?>)</h2>
                <script>
                    if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                        alert("<?=$this->session->flashdata('flashdata', 'value');?>");
                </script>
                <table width="100%">
                    <!--<col width="50%">
                    <col width="50%">-->
                    <tr>
                        <th>产品ID</th>
                        <th>产品名称</th>
                        <th>总出货量</th>
                        <th>零售出货量</th>
                        <th>经销出货量</th>
                        <th>市代出货量</th>
                        <th>总代出货量</th>
                        <th>试用品出货量</th>
                        <th>涉总金额</th>
                        <th>涉零售金额</th>
                        <th>涉经销金额</th>
                        <th>涉市代金额</th>
                        <th>涉总代金额</th>
                        <th>涉试用品或活动产品金额</th>
                    </tr>
                    <? $n = 0; ?>
                    <? foreach($bills as $k => $v){ ?>
                        <? $n ++; ?>
                        <tr class="<?=$n%2==0?"even":"odd";?>">
                            <td><?=$v->product_id?></td>
                            <td><a href="<?=base_url()?>product/details_admin/<?=$v->product_id?>?is_trial=<?=$v->is_trial?>"><?=$v->title?></a></td>
                            <td><?=$v->total_quantity?></td>
                            <td><?=$v->quantity_0?></td>
                            <td><?=$v->quantity_3?></td>
                            <td><?=$v->quantity_2?></td>
                            <td><?=$v->quantity_1?></td>
                            <td><?=$v->quantity_t?></td>
                            <td><?=cny($v->amount);?></td>
                            <td><?=cny($v->amount_0);?></td>
                            <td><?=cny($v->amount_3);?></td>
                            <td><?=cny($v->amount_2);?></td>
                            <td><?=cny($v->amount_1);?></td>
                            <td><?=cny($v->amount_t);?></td>
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