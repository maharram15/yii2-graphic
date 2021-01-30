<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Graphic!</h1>

        <div id="all">
            <div id="canvas">
            </div>
            <div class="custom-select" style="width:200px;">
                <select onchange="Test()" name="date" id="dates">
                    <?php for ($i = 0; $i < count($options); $i++) { ?>
                        <option id="select-<?= $id++ ?>" class="form-select" selected value="<?= $options[$i]['date'] ?>"><?= Yii::$app->formatter->asDate($options[$i]['date']) ?></option>
                    <?php }  ?>
                </select>
            </div>
            <canvas id="myChart" style="width: -20px;" height="-5"></canvas>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
            <div id="can">
                <script>
                    var ctx = document.getElementById('myChart').getContext("2d");
                    if (chart) chart.destroy();

                    var chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ["00:00", "01:00", "02:00", "03:00", "04:00", "05:00", "06:00", "07:00", "08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00", "20:00", "21:00", "22:00", "23:00", "00:00"],
                            datasets: [{
                                tension: 0,
                                data: [<?php
                                        foreach ($value as $k => $v) {
                                            echo $v . ",";
                                        } ?>],

                                label: "graphic 1",
                                borderColor: "#0085E1",
                                backgroundColor: "rgb(0, 116, 255,0.2)",
                                fill: true,
                                stepSize: 500,
                                pointHoverRadius: 5,
                                hoverBackgroundColor: ['#5cb85c', '#D74B4B', '#6685a4', '#f0ad4e', '#5bc0de', '#EE82EE'],

                            }, {
                                tension: 0,
                                data: [<?php
                                        foreach ($value2 as $s => $a)
                                            echo $a . ","
                                        ?>],
                                label: "graphic 2",
                                borderColor: "#D70021",
                                backgroundColor: "rgb(187, 0, 0,0.2)",
                                fill: true,
                                stepSize: 500,
                                hoverBackgroundColor: "#0085E1",

                            }, {
                                tension: 0,
                                data: [<?php
                                        foreach ($value3 as $b => $c)
                                            echo $c . ","
                                        ?>],
                                label: "graphic 3",
                                borderColor: "#26B100",
                                backgroundColor: "rgb(53, 217, 0,0.2)",

                                fill: true,
                                stepSize: 500,

                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        max: 500,
                                        min: 0,
                                        stepSize: 50
                                    }
                                }]
                            },
                            title: {
                                display: true,
                                text: 'Server Rating'
                            },
                        }
                    });
                    console.log(chart.config.data.datasets[0].data.length);

                    function Test() {
                        let id = $("#dates").find(':selected')[0].id;
                        let graphicData = $("#" + id).val();
                        $.ajax({
                                url: "http://localhost/MyTest/frontend/web/site/getgraphic",
                                data: {
                                    graphicRequest: true,
                                    graphicData: graphicData
                                },
                                type: "POST",
                                success: function(data) {
                                    if (data.html) {
                                        $('#can').html(data.html)
                                    }
                                },
                                error: function() {
                                    console.log("Error");
                                }
                            }),
                            event.preventDefault();
                    }
                </script>
            </div>
            <style>
                body {
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
                }

                .custom-select {
                    position: relative;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }

                .custom-select select {
                    font-size: 1.5em;
                    border: none;
                    font-weight: 500;
                    /*hide original SELECT element: */
                }
            </style>
        </div>
    </div>

    <div class="body-content">

        <div class="row">
        </div>

    </div>
</div>