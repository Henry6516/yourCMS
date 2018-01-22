<?php if (!defined('THINK_PATH')) exit();?><html>
<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/3.7.0/echarts.js"></script>
<body>
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="main" style="width:1800px;height:800px;"></div>
<script>
    $(function () {
        var titles = new Set();
        $.ajax({
            url:"/salestrend/trend/sales",
            success:function (data) {
                init_chart('main',data);
            }
        });
    })
</script>
<script type="text/javascript">
    function init_chart(id,row_data) {
        // 基于准备好的dom，初始化echarts实例

        var myChart = echarts.init(document.getElementById(id));
        var data = eval("("+row_data+")");
        var title = data.title;
        var ordertime = data.ordertime;
        var value = data.value;
        var line = new Array();
        for(var index in value){
            var single_line=  {
                name:title[index],
                type:'line',
                stack: '总量',
                itemStyle: {normal: {areaStyle: {type: 'default'}}},
                data:value[index]
            };
            line.push(single_line);
        }
        option = {
            title : {
                text: '销售额走势$'
            },
            tooltip : {
                trigger: 'axis'
            },
            grid: {
                left: 220
            },
            legend: {
                    x: 'left',
                    data: title,

                    inactiveColor: '#999',
//                    selectedMode: 'single',
                    orient: 'vertical',
                    width: 150,
                    top: 50,
                    borderWidth: 1,
                    borderColor: 'blue',
                    textStyle: {
                    color: '#000'
                }
            },

            toolbox: {
                show : true,
                feature : {
                    mark : {show: true},
                    dataView : {show: true, readOnly: false},
                    magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
                    restore : {show: true},
                    saveAsImage : {show: true}
                }
            },
            calculable : true,
            xAxis : [
                {
                    type : 'category',
                    boundaryGap : false,
                    data :ordertime
                }
            ],
            yAxis : [
                {
                    axisLabel:{
                        formatter: function (value, index) {
                            value = value + "$"
                            return value;
                        }
                    },
                    type : 'value'
                }
            ],
            series :line
        };
        myChart.setOption(option);

    }
</script>
</body>
</html>