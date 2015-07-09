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
                <h2>年报表<?if(isset($bills[0])):?>(<?=  date('Y', strtotime($bills[0]->date_from))?> - <?=date('Y', strtotime($bills[0]->date_to))?>)<?endif?></h2>
                <script>
                    if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                        alert("<?=$this->session->flashdata('flashdata', 'value');?>");
                </script>
                <table width="100%">
                    <!--<col width="50%">
                    <col width="50%">-->
                    <tr>
                        <th>日期</th>
                        <th>自身业绩增量</th>
                        <th>下级业绩增量</th>
                        <th>实际业绩增量</th>
                        <th>收益增量(不含推荐)</th>
                        <th>推荐收益增量</th>
                        <th>总收益增量</th>
                        <th>至上级收益</th>
                        <th>至上级推荐收益</th>
                        <th>至上级总收益</th>
                        <th>上级代理</th>
                    </tr>
                    <? $n = 0; ?>
                    <? foreach($bills as $k => $v){ ?>
                        <? $n ++; ?>
                        <tr class="<?=$n%2==0?"even":"odd";?>">
                            <td><?=$v->date?></td>
                            <td><?=cny($v->self_turnover)?></td>
                            <td><?=cny($v->sub_turnover)?></td>
                            <td>￥<?=bcadd(money($v->self_turnover),money($v->sub_turnover),2)?></td>
                            <td><?=cny($v->normal_return_profit_sub2self)?></td>
                            <td><?=cny($v->extra_return_profit_sub2self)?></td>
                            <td>￥<?=bcadd(money($v->normal_return_profit_sub2self), money($v->extra_return_profit_sub2self), 2 )?></td>
                            <td><?=cny($v->normal_return_profit_self2parent);?></td>
                            <td><?=cny($v->extra_return_profit_self2parent);?></td>
                            <td>￥<?=bcadd(money($v->normal_return_profit_self2parent), money($v->extra_return_profit_self2parent), 2 )?></td>
                            <? if($v->pid == '1' || $v->pid == '') {?>
                                <td></td>
                            <?} else {?>
                                <td><a href="<?=base_url()?>user/details_admin/<?=$v->pid?>"><?=$v->pname?>(<?=$v->pusername?>/<?=$v->pid?>)</a></td>
                            <? } ?>
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