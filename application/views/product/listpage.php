<!-- begin: #col3 static column -->
<?
switch($level)
{
    case 1:
        $level_name = '特约代理';
        break;
    case 2:
        $level_name = '一级代理';
        break;
    case 3:
        $level_name = '二级代理';
        break;
}
?>
<div id="col3" role="main" class="one_column">
    <div id="col3_content" class="clearfix">


        <div class="info view_form">
            <h2>产品列表 您当前等级为 <span class="red"><?=$level_name?></span>, 有效价格为 <span class="red"><?=$level_name?>价</span></h2>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
            <table width="70%">
                <!--<col width="50%">
                <col width="50%">-->
                <tr>
                    <th>搜索<br/>
                        产品名称/功效
                    </th>
                    <form action="<?=base_url()?>product/listpage" method="post">
                        <td colspan="9"><input type="text" name="search" value=""  /><input type="submit" />
                        </td>
                    </form>
                </tr>
                <tr>
                    <th>产品名称</th>
                    <th>规格</th>
                    <th>产品功效</th>
                    <th>所含成分</th>
                    <th>产品图片</th>
                    <th <?=$level==1?"class=\"red\"":"";?>>特约代理价</th>
                    <th <?=$level==2?"class=\"red\"":"";?>>一级代理价</th>
                    <th <?=$level==3?"class=\"red\"":"";?>>二级代理价</th>
                    <th <?=$level==0?"class=\"red\"":"";?>>零售价</th>
                    <th></th>
                    <th></th>
                </tr>
                <? $n = 0; ?>
                <? foreach($products as $k => $v){ ?>
                    <? $n ++; ?>
                    <tr class="<?=$n%2==0?"even":"odd";?>">
                        <td><?=$v->title?></td>
                        <td><?=$v->properties;?></td>
                        <td><?=$v->feature?></td>
                        <td><?//=$v->ingredient;?></td>
                        <td><?=$v->img?></td>
                        <td><?=cny($v->price_special)?></td>
                        <td><?=cny($v->price_last_2)?></td>
                        <td><?=cny($v->price_last_3)?></td>
                        <td><?=cny($v->price_normal)?></td>
                        <td><a href="<?=base_url()?>product/details/<?=$v->id;?>">查看详情</a></td>
                        <td><a href="<?=base_url()?>order/add/<?=$v->id;?>">产品下订</a></td>
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