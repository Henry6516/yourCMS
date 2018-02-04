<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <title>销售毛利报表</title>
    <link rel="stylesheet" href="/Public/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Public/assets/bootstrap-table/src/bootstrap-table.css">
    <link rel="stylesheet" href="//rawgit.com/vitalets/x-editable/master/dist/bootstrap3-editable/css/bootstrap-editable.css">
    <style>
        .fixed-table-body{overflow:hidden}
        .fixed-table-container{position: absolute; overflow-x:auto;}
    </style>
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
        <a  class="navbar-brand" href="<?php echo U('Home/Product/export');?>" >导出数据
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
            url:"/Home/Product/index2",
            columns: [
                [

                    {
                        field: 'pingtai',
                        title: '平台',
                        sortable: true,
                        align: 'center',
                        visible:true,
                        footerFormatter:'<strong style="color: red;">汇总</strong>',

                    },{
                    field: 'suffix',
                    title: '账号',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    footerFormatter:'<strong style="color: red;">汇总</strong>',

                },{
                    field: 'salesman',
                    title: '销售员',
                    sortable: true,
                    visible:true,
                    align: 'center'
                    ,footerFormatter:'<strong style="color: red;">汇总</strong>',

                },{
                    field: 'salemoney',
                    title: '成交价$',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                },{
                    field: 'salemoneyzn',
                    title: '成交价￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,


                }, {
                    field: 'ebayfeeebay',
                    title: 'eBay成交费$',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,


                }, {
                    field: 'ebayfeeznebay',
                    title: 'eBay成交费￥',
                    visible:true,
                    sortable: true,
                    align: 'center',
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'ppfee',
                    title: 'PP成交费$',
                    visible:true,
                    sortable: true,
                    align: 'center',
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'ppfeezn',
                    title: 'PP成交费￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'costmoney',
                    title: '商品成本￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'expressfare',
                    title: '运费成本￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,


                }, {
                    field: 'inpackagemoney',
                    title: '包装成本￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,
                }, {
                    field: 'storename',
                    title: '发货仓库',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    footerFormatter:'<strong style="color: red;">汇总</strong>',

                }, {
                    field: 'refund',
                    title: '退款金额￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                },{
                    field: 'refundrate',
                    title: '退款率%',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                },{
                    field: 'diefeezn',
                    title: '死库处理￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'insertionfee',
                    title: '店铺杂费￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'saleopefeezn',
                    title: '运营杂费￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'grossprofit',
                    title: '毛利￥',
                    sortable: true,
                    visible:true,
                    align: 'center',
                    formatter:colformatter,
                    footerFormatter:totalPriceFormatter,

                }, {
                    field: 'grossprofitrate',
                    title: '毛利率%',
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
        if(this.field !=='grossprofitrate'){
            if(this.field ==='refundrate'){  //退款率

                var  colrefund= data.reduce(function(sum, row) {
                    return  sum + (+row.refund);

                }, 0);
                var  colsalemoneyzn= data.reduce(function(sum, row) {
                    return  sum + (+row.salemoneyzn);

                }, 0);
                return  '<strong style="color: red;width: 10px;">'+parseFloat(100*colrefund/colsalemoneyzn).toFixed(2)+'<strong>';

            }else{
                return '<strong style="color: red;width: 10px;" >'+ parseFloat(data.reduce(function(sum, row) {

                        return  sum + (+row[field]);

                    }, 0)).toFixed(2)+'<strong>';
            }


        }else{

            var colgrossprofit = data.reduce(function(sum, row) {
                return  sum + (+row.grossprofit);

            }, 0);
            var colsalemoneyzn = data.reduce(function(sum, row) {
                return  sum + (+row.salemoneyzn);

            }, 0);
            return  '<strong style="color: red;width: 10px;">'+parseFloat(100*colgrossprofit/colsalemoneyzn).toFixed(2)+'<strong>';
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

    function refundRateFormatter(){

    }

    function  widthFormatter(value,row,index) {
        return '<strong style=" width:2%">' + value +"</strong>" ;
    }

</script>
</body>
</html>