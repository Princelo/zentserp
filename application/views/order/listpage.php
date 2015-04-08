<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>order/listpage' ><div>订单列表 </div></a>
                </li>
                <? if($this->session->userdata('level') != 0) {?>
                <li>
                    <a href='<?=base_url()?>trial_order/listpage' ><div>试用品订单列表 </div></a>
                </li>
                <?}?>
                <li>
                    <a href='<?=base_url()?>order/index_sub' ><div>下级代理订单查询 </div></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->
<!-- begin: #col3 static column -->
<?
?>
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">


        <div class="info view_form">
            <h2>订单列表</h2>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
            <div>
                <form action="<?=base_url()?>order/listpage" method="get">
                    <table>
                        <tr>
                            <th>搜索</th>
                            <th>
                                <select name="is_finish">
                                    <option></option>
                                    <option value="1">已完成</option>
                                    <option value="0">未完成</option>
                                </select>
                            </th>
                            <th>
                                <input type="submit" value="提交"/>
                            </th>
                        </tr>
                    </table>
                </form>
            </div>
            <table width="100%">
                <!--<col width="50%">
                <col width="50%">-->
                <tr>
                    <th>订单号</th>
                    <th>产品名称(ID)</th>
                    <th>订单数量</th>
                    <th>订单总价</th>
                    <th>是否已付款</th>
                    <th>是否完成</th>
                    <th>交易完成时间</th>
                    <th>取货方式</th>
                    <th>订单联系人</th>
                    <th>联系人电话</th>
                    <th>订单备注</th>
                    <th>订单提交时间</th>
                    <th></th>
                </tr>
                <? $n = 0; ?>
                <? if(!empty($orders)) {?>
                <? foreach($orders as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><?=$v->id?></td>
                        <td><a href="<?=base_url()?>product/details/<?=$v->pid?>"><?=$v->title;?>(<?=$v->pid?>)</a></td>
                        <td><?=$v->quantity;?></td>
                        <td><?="￥".bcmul(money($v->unit_price), $v->quantity, 2)?></td>
                        <td><span class="<?=$v->is_pay=='t'?"accept":"cross";?>"></span></td>
                        <td><span class="<?=$v->is_pay=='t'&&$v->is_correct=='t'?"accept":"cross";?>"></span></td>
                        <td><?=$v->finish_time?></td>
                        <td><?=$v->is_post=='t'?"邮寄":"自取"?></td>
                        <td><?=$v->linkman?></td>
                        <td><?=$v->mobile?></td>
                        <td><?=$v->remark?></td>
                        <td><?=substr($v->stock_time, 0, 19);?></td>
                        <td><a href="<?=base_url()?>order/details/<?=$v->id;?>">查看详情</a></td>
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