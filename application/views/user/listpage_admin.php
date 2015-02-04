<div id="container">



    <!-- begin: #col1 - first float column -->
    <div id="col1" role="complementary" style="display: block;">
        <div id="col1_content" class="clearfix">

            <ul id="left_menu">
                <li>
                    <a href='<?=base_url()?>user/listpage_admin' ><div>代理列表 </div></a>
                </li>
                <li><a href='<?=base_url();?>user/addRootUser' ><div>新增代理 </div></a></li>
            </ul>
        </div>
    </div>
    <!-- end: #col1 -->



    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">


            <div class="info view_form">
                <h2>代理列表</h2>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
                <div>
                    <form action="<?=base_url()?>user/listpage_admin" method="get">
                        <table>
                            <tr>
                                <th>搜索</th>
                                <th>
                                    姓名:<input type="text" name="search" value="<?=set_value('search')?>"  />
                                </th>
                                <th style="display: ;">
                                    等级:
                                    <select name="level">
                                        <option ></option>
                                        <option value="1">特約代理</option>
                                        <option value="2">一級代理</option>
                                        <option value="3">二級代理</option>
                                        <option value="0">零售商</option>
                                    </select>
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
                        <th>代理ID</th>
                        <th>用戶名</th>
                        <th>姓名</th>
                        <th>代理级別</th>
                        <th>身份证</th>
                        <th>电话</th>
                        <th>微信</th>
                        <th>QQ</th>
                        <th>他的业绩</th>
                        <th>他的收益</th>
                        <th>是否生效</th>
                        <th></th>
                    </tr>
                    <? $n = 0; ?>
                    <? if(!empty($users)) {?>
                    <? foreach($users as $k => $v){ ?>
                        <? $n ++; ?>
                        <tr class="<?=$n%2==0?"even":"odd";?>">
                            <td><?=$v->id?></td>
                            <td><?=$v->username;?></td>
                            <td><?=$v->name;?></td>
                            <td><?=getLevelName($v->level)?></td>
                            <td><?=$v->citizen_id;?></td>
                            <td><?=$v->mobile_no?></td>
                            <td><?=$v->wechat_id?></td>
                            <td><?=$v->qq_no?></td>
                            <td><?=cny($v->turnover)?></td>
                            <td><?=cny($v->profit)?></td>
                            <td><?=$v->is_valid=='t'?'是':'否'?></td>
                            <td><a href="<?=base_url()?>user/details_admin/<?=$v->id?>">编辑</a></td>
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