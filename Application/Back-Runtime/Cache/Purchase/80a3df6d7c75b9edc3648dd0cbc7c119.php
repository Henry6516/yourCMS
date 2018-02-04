<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>采购毛利报表</title>
    <link rel="stylesheet" href="/Public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Public/assets/bootstrap-table/src/bootstrap-table.css">
    <link rel="stylesheet" href="//rawgit.com/vitalets/x-editable/master/dist/bootstrap3-editable/css/bootstrap-editable.css">

    <!--<link rel="stylesheet" href="/Public/assets/examples.css">-->
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <script src="http://cdn.bootcss.com/bootstrap-table/1.9.1/bootstrap-table.min.js"></script>


</head>
<body>
<div class="header navbar " style="margin-bottom: -38px; width:40%">

    <div class="header-inner">
        <a class="navbar-brand"  href="#" onClick="javascript :history.back(-1);">返回条件选择页
        </a>
        <a href="javascript:;" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a  class="navbar-brand" href="<?php echo U('Purchase/Purchase/export');?>" >导出数据
        </a>
    </div>

</div>
<div  class="container-fluid">

    <div >
        <table id="table">
        </table>
    </div>




</div>


<script>


    function initTable(tableName) {

        tableName.bootstrapTable({

            footerStyle:footerStyle,
            showFooter:true,
//            showColumns:true,
            search:true,
            url:"/Purchase/Purchase/echo_purchase",
            columns: [
                [

                   {
                    field: 'purchaser',
                    title: '采购员',
                    sortable: true,
                    visible:true,
                    align: 'center'
                    ,footerFormatter:'<strong style="color: red;">汇总</strong>',

                },{
                    field: 'salemoneyrmbus',
                    title: '成交价$',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                },{
                    field: 'salemoneyrmbzn',
                    title: '成交价￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,


                }, {
                    field: 'ppebayus',
                    title: '交易费汇总$',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,


                }, {
                    field: 'ppebayzn',
                    title: '交易费汇总￥',
                    visible:true,
                    sortable: true,
                    align: 'center',
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'costmoneyrmb',
                    title: '商品成本￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'expressfarermb',
                    title: '运费成本￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,


                }, {
                    field: 'inpackagefeermb',
                    title: '包装成本￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'devofflinefee',
                    title: '死库处理￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'devopefee',
                    title: '运营杂费￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'netprofit',
                    title: '毛利￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'netrate',
                    title: '毛利率%',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'totalamount',
                    title: '采购差额￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,
                }

                ]
            ]
        });
    }


</script>
<script>
    $(function () {
        var $table = $('#table');
        initTable($table);
    })


    function totalPriceFormatter(data) {
        var total = 0;
        field = this.field;
        if(this.field !=='netrate'){
            return '<strong style="color: red;width: 10px;" >'+ parseFloat(data.reduce(function(sum, row) {

                    return  sum + (+row[field]);

                }, 0)).toFixed(2)+'<strong>';
        }else{

            var colnetprofit = data.reduce(function(sum, row) {
                return  sum + (+row.netprofit);

            }, 0);
            var salemoneyrmbzn = data.reduce(function(sum, row) {
                return  sum + (+row.salemoneyrmbzn);

            }, 0);
            return  '<strong style="color: red;width: 10px;">'+parseFloat(100*colnetprofit/salemoneyrmbzn).toFixed(2)+'<strong>';
        }


    }



    function colformatter(value, row, index){

        return  parseFloat(value).toFixed(2)  ;

    }


    function footerStyle(value, row, index) {
        console.log(123);
        return {
            css: {
                "font-weight": "bold" ,
                "font-color":"blue",

            }
        };
    }
    function  widthFormatter(value,row,index) {
        return '<strong style=" width:2%">' + value +"</strong>" ;
    }

</script>
</body>
</html>