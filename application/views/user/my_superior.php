<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>user/listpage' ><div>我的下级代理 </div></a>
                </li>
                <li><a href='<?=base_url();?>user/add' ><div>新增代理 </div></a></li>
                <li>
                    <a href='<?=base_url()?>user/my_superior' ><div>我的上级代理信息 </div></a>
                </li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->



    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">


            <div class="info view_form">
                <h2>我的上级代理信息</h2>
                <script>
                    if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                        alert("<?=$this->session->flashdata('flashdata', 'value');?>");
                </script>
                <table width="100%">
                    <!--<col width="50%">
                    <col width="50%">-->
                    <tr>
                        <th>姓名</th>
                        <th>电话</th>
                        <th>QQ</th>
                    </tr>
                    <? $n = 1; ?>
                    <? if(!empty($v)) {?>
                        <tr class="<?=$n%2==0?"even":"odd";?>">
                            <td><?=$v->name;?></td>
                            <td><?=$v->mobile_no?></td>
                            <td><?=$v->qq_no?></td>
                        </tr>
                    <? } ?>
                </table>
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