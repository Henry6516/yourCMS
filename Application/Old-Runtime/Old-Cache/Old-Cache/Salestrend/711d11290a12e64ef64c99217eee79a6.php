<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.0.2
Version: 1.5.4
Author: KeenThemes
Website: http://www.keenthemes.com/
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8" />
    <title>毛利润报表</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <meta name="MobileOptimized" content="320">
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="/Public/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="/Public/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
    <link href="/Public/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="/Public/assets/css/style-metronic.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/css/pages/tasks.css" rel="stylesheet" type="text/css"/>
    <link href="/Public/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="/Public/assets/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->

    <link rel="stylesheet" type="text/css" href="/Public/css/bootstrap-select.min.css">


</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="page-header-fixed">
<!-- BEGIN HEADER -->
<div class="header navbar navbar-inverse navbar-fixed-top">
    <!-- BEGIN TOP NAVIGATION BAR -->
    <div class="header-inner">
        <!-- BEGIN LOGO -->
        <a class="navbar-brand" href="index.html">
            <img src="/Public/assets/img/logo.png" alt="logo" class="img-responsive" />
        </a>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <img src="/Public/assets/img/menu-toggler.png" alt="" />
        </a>

        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <ul class="nav navbar-nav pull-right">
            <!-- END TODO DROPDOWN -->
            <!-- BEGIN USER LOGIN DROPDOWN -->
            <li class="dropdown user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <img alt="" src="/Public/assets/img/avatar1_small.jpg"/>
                    <span class="username"><?php echo ($username); ?></span>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo U('/Home/Users/logout');?>"><i class="fa fa-key"></i>退出登录</a></li>
                </ul>
            </li>
            <!-- END USER LOGIN DROPDOWN -->
        </ul>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END TOP NAVIGATION BAR -->
</div>
<!-- END HEADER -->


<!-- BEGIN CONTAINER -->
<div class="page-container">s

    <!-- BEGIN SIDEBAR -->
<div class="page-sidebar navbar-collapse collapse">
    <!-- BEGIN SIDEBAR MENU -->
    <ul class="page-sidebar-menu">
        <li>
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <div class="sidebar-toggler hidden-phone"></div>
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
        </li>
        <li>
            <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
            <form class="sidebar-search" action="extra_search.html" method="POST">
                <div class="form-container">
                    <div class="input-box">
                        <a href="javascript:;" class="remove"></a>
                        <input type="text" placeholder="Search..."/>
                        <input type="button" class="submit" value=" "/>
                    </div>
                </div>
            </form>
            <!-- END RESPONSIVE QUICK SEARCH FORM -->
        </li>
        <li class="start active ">
            <a href="#">
                <i class="fa fa-home"></i>
                <span class="title">Dashboard</span>
                <span class="selected"></span>
            </a>
        </li>

        <?php if(is_array($result)): $i = 0; $__LIST__ = $result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="">
                <a href="<?php echo ($vo["menuurl"]); ?>">
                    <i class="fa fa-briefcase"></i>
                    <span class="title"><?php echo ($vo["menuname"]); ?></span>
                </a>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>

    </ul>
    <!-- END SIDEBAR MENU -->
</div>
<!-- END SIDEBAR -->
    <!-- BEGIN PAGE -->
    <div class="page-content">
        <!-- BEGIN PAGE HEADER-->
        <div class="row page">
            <div class="col-md-12">
                <form class="form-horizontal myclass"  action="<?php echo U('Salestrend/Trend/roleBase');?> " method="post">
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><i class="fa fa-globe"></i>销售额走势</div>
                            <div class="tools">
                                <!--<a  href="<?php echo U('Home/Product/showUploadForm');?>">退款费用</a>|-->
                                <!--<a  href="<?php echo U('Home/Product/diebasefee');?>">死库费用</a>-->
                                <a href="javascript:;" class="reload"></a>
                                <a href="javascript:;" class="remove"></a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="form-group" >
                                <div class="col-sm-2">
                                    <select name="Flag" class="col-sm-1 form-control">
                                        <option value="0">按天</option>
                                        <option value="2">按月</option>

                                    </select>
                                </div>
                                <label for="department" class="col-sm-1 control-label" >部门</label>
                                <div class="col-sm-2 " >
                                    <select  name="department[]" class="form-control selectpicker" multiple data-actions-box="true">
                                        <!--id="department"-->
                                            <?php if(is_array($department)): $i = 0; $__LIST__ = $department;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><!--<option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option>-->
                                                <option value="<?php echo ($vo); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>

                                    </select>

                                </div>
                                <label for="pingtai" class="col-sm-1 control-label"  >平台</label>
                                <div class="col-sm-2 " >
                                    <select  name="pingtai[]" class="form-control selectpicker" multiple data-actions-box="true">

                                        <?php if(is_array($pingtai)): $i = 0; $__LIST__ = $pingtai;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>

                                </div>
                                <label for="saler" class="col-sm-1 control-label" >销售员</label>
                                <div class="col-sm-2" >
                                    <select name="saler[]"  class="form-control selectpicker" id="department-user-select" multiple data-actions-box="true">
                                        <?php if(is_array($saler)): $i = 0; $__LIST__ = $saler;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <option  value="">测试</option>

                                    </select>
                                </div>

                            </div>
                            <div class="col-sm-2">
                                <select name="DateFlag" class="col-sm-2 form-control">
                                    <option value="0">交易时间</option>
                                    <option value="1">发货时间</option>

                                </select>
                            </div>
                            <div class="col-sm-2">
                                <input type="date" name="BeginDate" class="form-control" required/>

                            </div>
                            <div class="col-sm-2">
                                <input type="date" name="EndDate" class="form-control" required/>

                            </div>
                            <div class="form-group">
                                <label  class="col-sm-1 control-label">账号</label>
                                <div class="col-sm-2 " >
                                    <select  name="suffix[]" class="form-control selectpicker"  multiple data-actions-box="true">
                                            <?php if(is_array($suffix)): $i = 0; $__LIST__ = $suffix;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;?><option  value="<?php echo ($s); ?>"><?php echo ($s); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <option  value="">测试</option>


                                    </select>


                                </div>

                                <div class="col-sm-1" >
                                    <input  name="" type="submit" class="btn btn-default btn-primary btn-lg btn-block"  id="form-sale-btn" value="查询">
                                </div>


                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div><!--销售-->

    </div>
    <!-- END PAGE -->

</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="footer">
    <div class="footer-inner">
        2013 &copy; Metronic by keenthemes.
    </div>
    <div class="footer-tools">
			<span class="go-top">
			<i class="fa fa-angle-up"></i>
			</span>
    </div>
</div>
<!-- END FOOTER -->


<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->

<script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="/Public/js/jquery.min.js"></script>
<script src="http://libs.baidu.com/jquery/1.10.0/jquery.min.js"></script>
<script src="//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="/Public/js/bootstrap-select.min.js"></script>
<script src="/Public/js/moment.min.js"></script>
<script src="/Public/js/zh-cn.js"></script>
<script src="/Public/js/sidebar.js"></script>
<script src="/Public/js/jquery_chained.js"></script>


<script type="text/javascript">
    $(function(){
        $("#series").chained("#mark");
    });
</script>

<script>
    $(document).ready(function(){
        //根据部门 拿到对应的销售员
        $('#department').change(function(){

            var opration = $(this).children('option:selected').val();
            $.ajax({
                url:"<?php echo U('home/Department/fetch_userde');?>",
                type:'GET',
                data:{'opration':opration},
                success:function(data){
                    var haha = eval('(' + data + ')'); //字符串转 对象
//                 var haha = JSON.parse(data);
                    $('#department-user-select').empty();
                    $('#department-user-select').append("<option value='"+''+"'>"+''+"</option>");
                    $.each(haha,function(index,n){
                        $('#department-user-select').append("<option value='"+n.username+"'>"+n.username+"</option>"); //为Select追加一个Option(下拉项)
                    });
                    $('#department-user-select').attr("class:'form-control selectpicker'");
                }

            })
        })


        //显示汇率管理页面
        $('#exchangeRate-btn').click(function(){
            $('.page').hide();
            $('.devcondition-page').hide();
            $.ajax({
                url:"<?php echo U('home/ExchangeRateM/as_rate');?>",
                success:function(data){
                    var da =JSON.parse(data);
                    $('#salerrate').attr('placeholder',da[0].salerrate);
                    $('#devrate').attr('placeholder',da[0].devrate);
                    $('#salerrate').attr('value',da[0].salerrate);
                    $('#devrate').attr('value',da[0].devrate);
                    //data可能是xmlDoc、jsonObj、html、text等等
                    //this;  //调用本次ajax请求时传递的options参数
                }
            })
            $('.exchange-rate').show();


        });

        //更新销售汇率
        $('#save-salerrate').click(function(){
            var darate =$("input[name='SalerRate']").val();
            $.ajax({
                type:'POST',
                url:"<?php echo U('home/ExchangeRateM/update_salerrate');?>",
                data:{'salerrate':darate},
                success:function(msg){
                    alert(msg);
                }

            })
        });

        //更新开发汇率
        $('#save-devrate').click(function(){
            var devrate =$("input[name='DevRate']").val();
            $.ajax({
                type:'post',
                url:"<?php echo U('home/ExchangeRateM/update_devrate');?>",
                data:{'devrate':devrate},
                success:function(msg){
                    alert(msg);
                }

            })
        });


        //nav切换
        $('#saler-netprofit-btn').click(function(){

            $('.page').show();
            $('.exchange-rate').hide();
            $('.devcondition-page').hide();

        })

        //开发毛利表
        $('#dev-netprofit-btn').click(function(){

            $('.page').hide();
            $('.exchange-rate').hide();
            $('.devcondition-page').show();
            $('#dev-salername-select').empty();
        })

        //开发模块加上部门 根据部门 加上 对应的开发

        $('#dev-department').change(function(){

            var opration = $(this).children('option:selected').val();
            $('#dev-salername-select').empty();
            $.ajax({
                url:"<?php echo U('home/Department/dev_department');?>",
                type:'GET',
                data:{'opration':opration},
                success:function(data){
                    var haha = eval('(' + data + ')'); //字符串转 对象
//                 var haha = JSON.parse(data);
                    $('#dev-salername-select').append("<option value='"+''+"'>"+''+"</option>");
                    $.each(haha,function(index,n){

                        $('#dev-salername-select').append("<option value='"+n.username+"'>"+n.username+"</option>"); //为Select追加一个Option(下拉项)
                    });
                }

            })
        })




    });
</script>