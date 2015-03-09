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
                    <li><a href="<?=base_url()?>product/add" class="" style="background: none;"><span>新增产品 </span></a></li>
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
            <li class="li-menu"><a href="<?=base_url();?>user/password" class=""><span>修改密碼 </span></a></li>
            <li class="li-menu"><a href="<?=base_url();?>logout" class=""><span>登出 </span></a></li>
        </ul>
    </div>

    <!-- end: main navigation -->

</div><!-- mainmenu -->
<script>
    $(document).ready(function(){
        //Examples of how to assign the Colorbox event to elements
        /*$(".group1").colorbox({rel:'group1'});
        $(".group2").colorbox({rel:'group2', transition:"fade"});
        $(".group3").colorbox({rel:'group3', transition:"none", width:"75%", height:"75%"});
        $(".group4").colorbox({rel:'group4', slideshow:true});
        $(".ajax").colorbox();
        $(".youtube").colorbox({iframe:true, innerWidth:425, innerHeight:344});
        $(".vimeo").colorbox({iframe:true, innerWidth:500, innerHeight:409});
        $(".iframe").colorbox({iframe:true, width:"100%", height:"100%"});
        $(".inline").colorbox({inline:true, width:"50%"});
        $(".callbacks").colorbox({
            //onOpen:function(){ alert('onOpen: colorbox is about to open'); },
            //onLoad:function(){ alert('onLoad: colorbox has started to load the targeted content'); },
            //onComplete:function(){ alert('onComplete: colorbox has displayed the loaded content'); },
            //onCleanup:function(){ alert('onCleanup: colorbox has begun the close process'); },
            //onClosed:function(){ alert('onClosed: colorbox has completely closed'); }
        });

        $('.non-retina').colorbox({rel:'group5', transition:'none'})
        $('.retina').colorbox({rel:'group5', transition:'none', retinaImage:true, retinaUrl:true});

        //Example of preserving a JavaScript event for inline calls.
        $("#click").click(function(){
            $('#click').css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here.");
            return false;
        });
    });
</script>
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
    $(document).ready(function(){
    });
</script>
