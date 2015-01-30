<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>ZENTS ERP System 臻芝订单管理系統</title>
    <!--<link href="includes/css/style_login.css" rel="stylesheet" type="text/css"/>-->
    <link rel="stylesheet" href="<?=base_url();?>assets/login.css">

    <script type="text/javascript" src="<?=base_url()?>assets/js/jquery.1.8.0.min.js"></script>
    <!--
    <script type="text/javascript" src="http://202.155.230.91:4280/includes/js/jquery-ui-1.10.2/ui/jquery-ui.js"></script>
    <link type="text/css" rel="stylesheet" href="http://202.155.230.91:4280/includes/js/jquery-ui-1.10.2/themes/base/jquery.ui.all.css" />

    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script language="javascript">google.load("jquery", "1.7.1"); </script>
    -->
</head>
<body>

<script type="text/javascript" charset="utf-8">
</script>
<div class="page_margins">
    <div class="page">


        <!-- begin: main content area #main -->
        <div id="main">

            <!-- begin: #col5 static column -->
            <div id="col5" role="main" class="one_column">
                <div id="col5_content" class="clearfix"  align="center">


                    <!--<div style="background:url('includes/images/login_interface.jpg'); width:500px; height:300px;border:6px solid #fff">-->
                    <div>
                        <p class="title">ZENTS ERP System 臻芝订单管理系統</p>
                        <p stype="width:100%; text-align:center; color:#f00;"><?=$error;?></p>
                        <div class="choose-block" style="display:none;">
                            <div class="choose">
                                <div id="drpro" onClick="$('#bal').removeClass();$('#onespine').removeClass();this.className='selected'"><span>Dr Pro</span></div>
                                <div id="bal" onClick="$('#drpro').removeClass();$('#onespine').removeClass();this.className='selected'"><span>Bealady</span></div>
                                <div id="onespine" onClick="$('#bal').removeClass();$('#drpro').removeClass();this.className='selected'"><span>Onespine</span></div>
                            </div>
                        </div>


                        <!-- begin: Login Form -->
                        <!--<div class="center" style="width:400px;padding-top:80px;">-->
                        <div class="center">



                            <div align="left">
                                <form action="<?=base_url()?>login/check" method="post" class="yform columnar" id="frm">
                                    <fieldset>
                                        <legend>登入系统 </legend>

                                        <table>
                                            <tr>
                                                <td class="display_field"></td><td class="input_field"><input type="text" name="login_id" value="" id="login_id" class="textfield" placeholder="登入编号 "  /></td>
                                            </tr>
                                            <tr>
                                                <td class="display_field"></td><td class="input_field"><input type="password" name="password" value="" id="password" class="textfield" placeholder="密码 "  /></td>
                                            </tr>
                                            <tr>
                                                <td class="display_field">
                                                </td>
                                                <td class="input_field">
                                                    <input type="text" name="captcha" value="" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <?=$captcha;?>
                                                </td>
                                            </tr>
                                        </table>


                                    </fieldset>
                                    <div class="info_msg">
                                    </div>

                                    <div class="type-button" align="right">

                                        <input type="submit" name="btnSubmit" value="登入 "  />										<input type="reset" value="重设 " class="reset" id="btnReset" name="btnReset" />
                                        <input type="hidden" value="" id="system" name="system" />
                                    </div>

                                </form>								</div>
                            <div align="" style="color:red;font-weight:bold">
                            </div>
                        </div>
                        <!-- end: Login Form -->



                    </div>


                </div>
                <!-- IE Column Clearing -->
                <div id="ie_clearing">&nbsp;</div>
            </div>
            <!-- end: #col5 -->

        </div>
        <!-- end: #main -->
    </div>
</div>
</body>
</html>
