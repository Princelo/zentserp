
<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>report/index' ><div>报表查询 </div></a>
                </li>
                <li><a href='<?=base_url();?>report/index_sub' ><div>下级代理报表查询 </div></a></li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->
<!-- begin: #col3 static column -->
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">


        <div class="info view_form">
            <h2>年收益报表</h2>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
            <table width="70%">
                <!--<col width="50%">
                <col width="50%">-->
                <tr>
                    <th>日期</th>
                    <th>收益总量</th>
                    <th>订单数</th>
                </tr>
                <? $n = 0; ?>
                <? foreach($bills as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><?=$v->date?></td>
                        <td><?=cny($v->volume);?></td>
                        <td><?=$v->count?></td>
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