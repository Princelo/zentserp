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
            <table width="100%">
                <!--<col width="50%">
                <col width="50%">-->
                <tr>
                    <th>搜索<br/>
                        产品名称/功效
                    </th>
                    <form action="<?=base_url()?>product/listpage" method="post">
                        <td colspan="13"><input type="text" name="search" value=""  /><input type="submit" />
                        </td>
                    </form>
                </tr>
                <tr>
                    <th>订单号</th>
                    <th>产品名称(ID)</th>
                    <th>用戶名(ID)</th>
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
                <? foreach($orders as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><?=$v->id?></td>
                        <td><?=$v->title;?>(<?=$v->pid?>)</td>
                        <td><?=$v->username."(".$v->uid.")"?></td>
                        <td><?=$v->quantity;?></td>
                        <td><?="￥".bcmul(money($v->unit_price), $v->quantity, 4)?></td>
                        <td><?=$v->is_pay=='t'?"√":"×";?></td>
                        <td><?=$v->is_pay=='t'&&$v->is_correct=='t'?"√":"×";?></td>
                        <td><?=$v->finish_time?></td>
                        <td><?=$v->is_post=='t'?"邮寄":"自取"?></td>
                        <td><?=$v->linkman?></td>
                        <td><?=$v->mobile?></td>
                        <td><?=$v->remark?></td>
                        <td><?=substr($v->stock_time, 0, 19);?></td>
                        <td><a href="<?=base_url()?>order/details/<?=$v->id;?>">查看详情</a></td>
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