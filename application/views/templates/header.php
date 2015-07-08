<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?=base_url();?>assets/navigator_memu.css" type="text/css" media="screen, projection" >
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>UNVWEB Management System 远维网络</title>

    <meta name="language" content="en" />

    <!--[if IE]>
    <script src="<?=base_url();?>assets/js/html5.js"></script>
    <![endif]-->

    <!-- ********** jQuery ********** -->

    <!--<script type="text/javascript" src="<?=base_url();?>assets/js/jquery.1.8.0.min.js"></script>-->
    <script type="text/javascript" src="<?=base_url();?>assets/js/jquery.js"></script>


    <!-- ********** PHPJS ********** -->
    <!--<script type="text/javascript" src="<?=base_url();?>assets/js/php.default.namespaced.min.js"></script>


    <!-- ********** Custom JS ********** -->
    <script type="text/javascript" src="<?=base_url();?>assets/js/general.js"></script>




    <!-- Css -->
    <!--
    -->
    <link rel="stylesheet" href="<?=base_url();?>assets/general.css" type="text/css">
    <link rel="stylesheet" href="<?=base_url();?>assets/layout.css" type="text/css">




    <!-- ********** JSCal2 ********** -->
    <!--<link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/jscal2.css" />
    <link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/border-radius.css" />
    <link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/reduce-spacing.css" />
    <!--<script type="text/javascript" src="<?=base_url();?>assets/js/jscal2.js"></script>
    <script type="text/javascript" src="<?=base_url();?>assets/js/en.js"></script>-->
    <!--<script type="text/javascript" src="<?=base_url();?>assets/js/jquery.timepicker.js"></script>
    -->
    <script src="<?=base_url()?>assets/js/jquery-ui.js"></script>
    <link rel="stylesheet" href="<?=base_url()?>assets/css/jquery-ui.css"/>

    <!-- Clock Picker -->
    <!--<script type="text/javascript" src="<?=base_url();?>assets/jquery.clockpick.1.2.7.js"></script>
    <link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/js/jquery.clockpick.1.2.7.css"/>

    <!-- ********** :: Animated jQuery Menu Style 08  ********** -->
    <!--
    <script type="text/javascript" src="<?=base_url();?>assets/js/menu.js"></script>-->
    <link type="text/css" rel="stylesheet" href="<?=base_url();?>assets/menu.css" />

    <!-- ********** :: colorbox-master  ********** -->
    <!--<link rel="stylesheet" href="<?=base_url();?>assets/colorbox.css" />
    <script type="text/javascript" src="<?=base_url();?>assets/js/jquery.colorbox.js"></script>

    <!-- Freeze Header  ********** -->
    <!--<script type="text/javascript" src="<?=base_url();?>assets/js/jquery.freezeheader.js"></script>

    <!-- Mobile Detector  ********** -->
    <!--<script type="text/javascript" src="<?=base_url();?>assets/js/detectmobilebrowser.js"></script>-->
    <script src="<?=base_url();?>assets/js/verify.notify.js"></script>
    <script>
        $(document).ready(

        );
    </script>
    <style>
        .li-menu {position: relative;}
        .li-menu:hover .dropdown-menu{display:block;}
        .dropdown-menu{
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            display: none;
            float: left;
            min-width: 160px;
            padding: 5px 0;
            margin: 2px 0 0;
            list-style: none;
            background-color: #ffffff;
            border: 1px solid #ccc;
            border: 1px solid rgba(0, 0, 0, 0.2);
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding;
            background-clip: padding-box;
            width:140px;
            padding-left:3px;
        }
        .dropdown-menu li{
            line-height: 20px;
            display: list-item;
            text-align: -webkit-match-parent;
            padding:0;
        }
        .dropdown-menu li a{color:#000; text-decoration: none; display: block; width:143px; padding-left:14px;
            line-height: 50px;}
        .dropdown-menu li a:hover{color:#fff; background:#007dbc;}
    </style>
</head>

<body>


<div id="header">
    <div id="logo">远维网络管理系统 </div>


    <div id='head_info'>

									<span style="margin-right:40px">
					</span>
        <!--
        <a href=""  class="image_button"  title="Tutor Buffer Table"><img src='/cgi-bin/common/images/page_white_gear.png'></a>
        <a href=""  class="image_button"  title="Tutor Buffer Table"><img src='/cgi-bin/common/images/chart_organisation.png'></a>
        <a href=""  class="image_button"  title="Tutor Buffer Table"><img src='/cgi-bin/common/images/status_online.png'></a>
        <a href=""  class="image_button"  title="Tutor Buffer Table"><img src='/cgi-bin/common/images/door_in.png'></a>
-->
    </div>
</div><!-- header -->


<div id="mainmenu">
    <style>
        #mainmenu #menu .menu li {position: relative;}
        #mainmenu #menu .menu li div {position: absolute; left: 0; top:43px; z-index: 999;}
    </style>
    <!-- begin: main navigation #nav -->
    <style>
        #mainmenu #menu .menu li {position: relative;}
        #mainmenu #menu .menu li div {position: absolute; left: 0; top:43px; z-index: 999;}
    </style>
    <script>
        function showmenu(id)
        {
            $('other-menu').each(
                function(){
                    $(this).hide();
                }
            );
            $('#'+id).show();
        }
    </script>
    <div id="menu">
        <ul class="menu">
            <li class="li-menu">
                <a href="<?=base_url()?>forecast/index" class="parent" ><span>首页 </span></a>
            </li>
            <li class="li-menu">
                <a href="javascript:;" onclick="showmenu('menu-product');" class="parent"><span>产品</span></a>
                <ul class="dropdown-menu other-menu" id="menu-product">
                    <li>
                        <a href="<?=base_url()?>product/listpage_admin" class="" style="background: none;"><span>产品列表(上架) </span></a>
                    </li>
                    <li>
                        <a href="<?=base_url()?>product/listpage_admin_invalid" class="" style="background: none;"><span>产品列表(下架) </span></a>
                    </li>
                    <li>
                        <a href="<?=base_url()?>product/listpage_admin?is_trial=true" class="" style="background: none;"><span>试用品列表(上架) </span></a>
                    </li>
                    <li>
                        <a href="<?=base_url()?>product/listpage_admin_invalid?is_trial=true" class="" style="background: none;"><span>试用品列表(下架) </span></a>
                    </li>
                    <li>
                        <a href="<?=base_url()?>product/listpage_admin?is_trial=true&trial_type=<?=get_trial_type('event products')?>'" class="" style="background: none;"><span>活动产品列表(上架) </span></a>
                    </li>
                    <li>
                        <a href="<?=base_url()?>product/listpage_admin_invalid?is_trial=true&trial_type=<?=get_trial_type('event products')?>'" class="" style="background: none;"><span>活动产品列表(下架) </span></a>
                    </li>
                    <li><a href="<?=base_url()?>product/add" class="" style="background: none;"><span>新增产品 </span></a></li>
                    <li><a href="<?=base_url()?>product/trial_add" class="" style="background: none;"><span>新增试用品 </span></a></li>
                    <li><a href="<?=base_url()?>product/trial_add?trial_type=<?=get_trial_type('event products');?>" class="" style="background: none;"><span>新增活动产品 </span></a></li>
                </ul>
            </li>
            <li class="li-menu">
                <a href="javascript:;" onclick="showmenu('menu-user');" class="parent" ><span>代理 </span></a>
                <ul class="dropdown-menu other-menu" id="menu-user">
                    <li><a href="<?=base_url()?>user/listpage_admin" class="" style="background: none;"><span>代理列表 </span></a>
                    </li>
                    <li><a href="<?=base_url()?>user/addRootUser" class="" style="background: none;"><span>新增代理 </span></a>
                    </li>
                </ul>

            </li>
            <li class="li-menu">
                <a href="javascript:;" onclick="showmenu('menu-order')" class="parent" ><span>订单系统 </span></a>
                <ul class="dropdown-menu other-menu" id="menu-order">
                    <li><a href="<?=base_url()?>order/listpage_admin" class="" style="background: none;"><span>订单列表 </span></a>
                    </li>
                </ul>

            </li>
            <li class="li-menu">
                <a href="javascript:;" onclick="showmenu('menu-report')" class="parent" ><span>报表系统 </span></a>
                <ul class="dropdown-menu other-menu" id="menu-report">
                    <li><a href="<?=base_url()?>report/index_admin" class="" style="background: none;"><span>代理报表查询 </span></a>
                    </li>
                    <li><a href="<?=base_url()?>report/index_zents" class="" style="background: none;"><span>Zents总报表查询 </span></a>
                    </li>
                </ul>
            </li>
            <li class="li-menu">
                <a href="<?=base_url()?>post_setting"><span>运费设置</span></a>
            </li>
            <li class="li-menu"><a href="<?=base_url();?>user/password" class=""><span>修改密碼 </span></a></li>
            <li class="li-menu"><a href="<?=base_url();?>logout" class=""><span>登出 </span></a></li>
        </ul>
    </div>

    <!-- end: main navigation -->

</div><!-- mainmenu -->
<script>
    function isValidDate( input ) {
        var date = new Date( input );
        input = input.split( '-' );
        return date.getFullYear()  === +input[0] &&
            date.getMonth() + 1 === +input[1] &&
            date.getDate() === +input[2];
    }
    function isValidTime( input ){
        if(input.indexOf(':')==-1)
            return false;
        input = input.split( ':' );
        if(input.length!=2)
            return false;
        if(!isNumber(input[0])||!isNumber(input[1]))
            return false;
        if(+input[0]>24||+input[0]<0)
            return false;
        if(+input[1]>60||+input[1]<0)
            return false;
        if(isNumber(input[0])&&isNumber(input[1]))
            return true;
        return false;
    }
    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    function checkIdcard(idcard){
        var Errors=new Array(
            "SUCCESS",
            "身份证号码位数不对!",
            "身份证号码出生日期超出范围或含有非法字符!",
            "身份证号码校验错误!",
            "身份证地区非法!"
        );
        var area={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"}
        var retflag=false;
        var idcard,Y,JYM;
        var S,M;
        var idcard_array = new Array();
        idcard_array = idcard.split("");
        switch(idcard.length){
            case 8:
                ereg=/^[a-zA-Z][0-9]*$/;
                if(ereg.test(idcard))
                    return Errors[0];
                else
                    return Errors[3];
                break;
            case 15:
                if ( (parseInt(idcard.substr(6,2))+1900) % 4 == 0 || ((parseInt(idcard.substr(6,2))+1900) %
                    100 == 0 && (parseInt(idcard.substr(6,2))+1900) % 4 == 0 )){
                    ereg=/^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}$/;//测试出生日期的合法性
                }
                else {
                    ereg=/^[1-9][0-9]{5}[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}$/;//测试出生日期的合法性
                }
                if(ereg.test(idcard))
                    return Errors[0];
                else
                {
                    return Errors[2];
                }
                break;
            case 18:
                if ( parseInt(idcard.substr(6,4)) % 4 == 0 || (parseInt(idcard.substr(6,4)) % 100 == 0 && parseInt(idcard.substr(6,4))%4 == 0 ))
                {
                    ereg=/^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;//闰年出生日期的合法性正则表达式
                }
                else
                {
                    ereg=/^[1-9][0-9]{5}19[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;//平年出生日期的合法性正则表达式
                }
                if(ereg.test(idcard)){//测试出生日期的合法性
                    S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7
                        + (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9
                        + (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10
                        + (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5
                        + (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8
                        + (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4
                        + (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2
                        + parseInt(idcard_array[7]) * 1
                        + parseInt(idcard_array[8]) * 6
                        + parseInt(idcard_array[9]) * 3 ;
                    Y = S % 11;
                    M = "F";
                    JYM = "10X98765432";
                    M = JYM.substr(Y,1);//判断校验位
                    if(M == idcard_array[17]) return Errors[0]; //检测ID的校验位
                    else return Errors[3];
                }
                else return Errors[2];
                break;
            default:
                return Errors[1];
                break;
        }
    }

    $(document).ready(function(){
        $(".timepicker").keydown(false);
        $.verify.addRules({
            uniqueemail: function(r) {
                var ajax = $.ajax({url:"http://test.huayu160.com/ajax/check_unique_email/" + r.val().trim(), async:false});
                var data = ajax.responseText;
                if(data == 'false'){
                    return '此帐号已被占用';
                }else if(data == 'true'){
                    return true;
                }
            },
            mydate: function(r){
                if(!isValidDate(r.val()))
                    return '无效日期';
                else
                    return true;
            },
            mytime: function(r){
                if(r.val()!="")
                    if(!isValidTime(r.val()))
                        return '无效时间';
                var f = $(".cast_time_from").val();
                var t = $(".cast_time_to").val();
                if(f!=""||t!=""){
                    if(f=="")
                        return '起始结束时间必须完整';
                    if(t=="")
                        return '起始结束时间必须完整';
                }
                var tomorrow = $(".cast_time_to_tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return '起始时间必须早于终止时间';
                    }
                }
                return true;
            },
            mytime1: function(r){
                if(r.val()!="")
                    if(!isValidTime(r.val()))
                        return '无效时间';
                var f = $(".busy_time_from_1").val();
                var t = $(".busy_time_to_1").val();
                if(f!=""||t!=""){
                    if(f=="")
                        return '起始结束时间必须完整';
                    if(t=="")
                        return '起始结束时间必须完整';
                }
                var tomorrow = $(".busy_time_to_1_tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return '起始时间必须早于终止时间';
                    }
                }
                return true;
            },
            mytime2: function(r){
                if(r.val()!="")
                    if(!isValidTime(r.val()))
                        return '无效时间';
                var f = $(".busy_time_from_2").val();
                var t = $(".busy_time_to_2").val();
                if(f!=""||t!=""){
                    if(f=="")
                        return '起始结束时间必须完整';
                    if(t=="")
                        return '起始结束时间必须完整';
                }
                var tomorrow = $(".busy_time_to_2_tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return '起始时间必须早于终止时间';
                    }
                }
                return true;
            },
            mytime3: function(r){
                if(r.val()!="")
                    if(!isValidTime(r.val()))
                        return '无效时间';
                var f = $(".busy_time_from_3").val();
                var t = $(".busy_time_to_3").val();
                if(f!=""||t!=""){
                    if(f=="")
                        return '起始结束时间必须完整';
                    if(t=="")
                        return '起始结束时间必须完整';
                }
                var tomorrow = $(".busy_time_to_3_tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return '起始时间必须早于终止时间';
                    }
                }
                return true;
            },
            mytime4: function(r){
                if(r.val()!="")
                    if(!isValidTime(r.val()))
                        return '无效时间';
                var f = $(".busy_time_from_4").val();
                var t = $(".busy_time_to_4").val();
                if(f!=""||t!=""){
                    if(f=="")
                        return '起始结束时间必须完整';
                    if(t=="")
                        return '起始结束时间必须完整';
                }
                var tomorrow = $(".busy_time_to_4_tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return '起始时间必须早于终止时间';
                    }
                }
                return true;
            },
            mytime5: function(r){
                if(r.val()!="")
                    if(!isValidTime(r.val()))
                        return '无效时间';
                var f = $(".busy_time_from_5").val();
                var t = $(".busy_time_to_5").val();
                if(f!=""||t!=""){
                    if(f=="")
                        return '起始结束时间必须完整';
                    if(t=="")
                        return '起始结束时间必须完整';
                }

                var tomorrow = $(".busy_time_to_5_tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return '起始时间必须早于终止时间';
                    }
                }
                return true;
            },
            mypassword: function(r){
                if(r.val().length<8)
                    return "密码长度不得小于8个字符";
                if($('input[name="password"]').val()!=""&&$('input[name="password2"]').val()!="")
                    if($('input[name="password"]').val() != $('input[name="password2"]').val())
                        return "两次输入密码不相同";
                return true;
            },
            myconfirm: function(r){
                if($('input[name="password"]').val()!=""&&$('input[name="password2"]').val()!="")
                    if($('input[name="password"]').val() != $('input[name="password2"]').val())
                        return "两次输入密码不相同";
                return true;
            },
            chinese_idcard: function(r){
                var idcard_error = checkIdcard(r);
                if(idcard_error != 'SUCCESS')
                {
                    return idcard_error;
                }else{
                    return true;
                }
            },
        });
        $.verify.addGroupRules({
            timefromto: function(r) {
                var f = r.field("from").val();
                var t = r.field("to").val();
                if(f!="")
                    if(!isValidTime(f))
                        return {from:'无效时间'};
                if(t!="")
                    if(!isValidTime(t))
                        return {to:'无效时间'};
                var tomorrow = r.field("tomorrow").is(":checked");
                f = f.split(':');
                t = t.split(':');
                if(f[0]){
                    console.log(tomorrow);
                    if( (+f[0]*60+f[1])>(+t[0]*60+t[1]) && tomorrow != true){
                        return {to:'起始时间必须早于终止时间'};
                    }
                }
                return true;
            }
        });
        $("form").verify();
        $('form').each(function()
            {
                $(this).attr('novalidate', 'novalidate');
            }
        );
        $('input[required]').each(function(){
            console.log($(this));
            $(this).removeAttr('required');
        });
        $( ".datepicker" ).datepicker({
            'dateFormat': 'yy-m-d',
            'changeYear' : true
        });
    });
</script>

