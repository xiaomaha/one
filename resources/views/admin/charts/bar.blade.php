<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/2
 * Time: 19:47
 */
?>
<!-- Box1 -->
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">班级</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
            <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">
        <canvas id="myChart"></canvas>
        <script>
            $(function () {
                $.get('getDurationDataByClass',function (data, status) {
                    var ctx = document.getElementById("myChart").getContext("2d");
                    var my_chart = new Chart(ctx,{
                        type: 'pie',
                        data: {
                            labels: [
                                "大一班",
                                "小一班",
                                "小二班",
                                "中三班"
                            ],
                            datasets: [{
                                data: data,
                                label: 'Votes',
                                backgroundColor: [
                                    window.chartColors.red,
                                    window.chartColors.orange,
                                    window.chartColors.purple,
                                    window.chartColors.green
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero:true
                                    }
                                }]
                            }
                        }
                    });
                });

                window.chartColors = {
                    red: 'rgb(255, 99, 132)',
                    orange: 'rgb(255, 159, 64)',
                    yellow: 'rgb(255, 205, 86)',
                    green: 'rgb(75, 192, 192)',
                    blue: 'rgb(54, 162, 235)',
                    purple: 'rgb(153, 102, 255)',
                    grey: 'rgb(201, 203, 207)'
                };
            });
        </script>

    </div>
    <div class="box-footer">
        <form action='#'>

        </form>
    </div>
</div>

