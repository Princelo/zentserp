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



            <div class="toolbar type-button">
                <h4><?php echo validation_errors(); ?></h4>
            <span class="red">
                <?=$this->session->flashdata('flashdata', 'value');?>
            </span>
                <div class="c50l">
                    <h3>编辑代理</h3>
                </div>
            </div>


            <form action="<?=base_url()?>user/details_admin" method="post">

                <fieldset>
                    <legend>代理编辑 </legend>

                    <table>
                        <col width="150">

                        <tr>
                            <th><label>代理ID</label></th>
                            <td><input type="text" value="<?=$v->id?>" disabled="disabled"/></td>
                        </tr>
                        <tr>
                            <th><label for="username">代理账号 <span>*</span></label></th>
                            <td><input type="text" name="username" data-validate="required,size(5,20)"
                                       maxlength="20" value="<?=$v->username?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="level">代理级別 <span>*</span></label></th>
                            <td>
                                <select name="level">
                                    <option value="1" <?=$v->level=='1'?'selected="selected"':'';?>>特約代理</option>
                                    <option value="2" <?=$v->level=='2'?'selected="selected"':'';?>>一級代理</option>
                                    <option value="3" <?=$v->level=='3'?'selected="selected"':'';?>>二級代理</option>
                                    <option value="0" <?=$v->level=='0'?'selected="selected"':'';?>>零售商</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="name">姓名 <span>*</span></label></th>
                            <td>
                                <input type="text" name="name" data-validate="required,size(2,10)"
                                       maxlength="10" value="<?=$v->name?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="citizen_id">身份证号码 <span>*</span></label></th>
                            <td>
                                <input type="text" name="citizen_id" data-validate="required,citizen_id"
                                       maxlength="18" value="<?=$v->citizen_id?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="mobile_no">移动电话 <span>*</span></label></th>
                            <td>
                                <input type="text" name="mobile_no" data-validate="required,size(11,11)"
                                       maxlength="11" size="11" value="<?=$v->mobile_no?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="wechat_id">微信号 </label></th>
                            <td>
                                <input type="text" name="wechat_id" data-validate="size(2,30)"
                                       maxlength="30" value="<?=$v->wechat_id?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="qq_no">QQ号 <span>*</span></label></th>
                            <td>
                                <input type="text" name="qq_no" data-validate="required,numeric,qq"
                                       maxlength="11" value="<?=$v->qq_no?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th>他的业绩</th>
                            <td>
                                <input type="text" value="<?=cny($v->turnover)?>" disabled="disabled" />
                            </td>
                        </tr>
                        <tr>
                            <th>他的收益</th>
                            <td>
                                <input type="text" value="<?=cny($v->profit)?>" disabled="disabled" />
                            </td>
                        </tr>
                        <tr>
                            <th><label>是否生效 <span>*</span></label></th>
                            <td>
                                <select name="is_valid">
                                    <option value="1" <?=$v->is_valid=='t'?'selected="selected"':'';?>>是</option>
                                    <option value="0" <?=$v->is_valid=='t'?'selected="selected"':'';?>>否</option>
                                </select>
                            </td>
                        </tr>
                    </table>

                </fieldset>


                <div class="toolbar type-button">
                    <div class="c50l">
                        <input type="submit" name="btnSubmit" value="提交 "  />			</div>
                    <div class="c50r right">
                    </div>
                </div>


            </form>

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
