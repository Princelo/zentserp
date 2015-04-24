
<div id="container">
    <!-- begin: #col3 static column -->
    <div id="col3" role="main" class="one_column">
        <div id="col3_content" class="clearfix">



            <div class="toolbar type-button">
                <script>
                    if("<?=$this->session->flashdata('flashdata', 'value');?>"!="")
                        alert("<?=$this->session->flashdata('flashdata', 'value');?>");
                </script>
                <div class="c50l">
                    <h3 style="color:#f00"><?=($error=="")?"修改密码":$error;?></h3>
                </div>
            </div>


            <form action="<?=base_url()?>user/passwordupdate" method="post">

                <fieldset>
                    <legend>修改密码 </legend>

                    <table>
                        <col width="150">

                        <tr>
                            <th><label for="type">原密码</label></th>
                            <td>
                                <input name="password-original" value="" type="password" data-validate="required" />
                            </td>
                        </tr>

                        <tr>
                            <th><label for="type">新密码</label></th>
                            <td>
                                <input name="password" value="" type="password" data-validate="required,mypassword,max(30)"/>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="type">再次确认</label></th>
                            <td>
                                <input name="password2" value="" type="password" data-validate="required,myconfirm,max(30)"/>
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

