@include('css')

<head>
    <div class="tm-main-content" id="top">
        <header style="background-color: #FFFFFF">
            <div class="container">
                <div class="row">
                    <img src="img/TSEL.png" height="80px" width="200px" style="padding: 7px">
                </div>
            </div>                
            </header>
        </div>
        <div class="tm-section tm-bg-img row" id="tm-section-1">
                <div class="col-lg-10 text-center" style="padding-left: 0px;">
</head>

<body>
    <div id="container" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto">

    </div>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script>
        Highcharts.chart('container', {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'TOP 5 SERVICES'
            },
            xAxis: {
                categories: ['Domestik MMS MO Eksternal', 'USSD Charging', 'AS(Transfer Pulsa)', 'VAS Content', 'IVR Music'],
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Population (millions)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ' millions'
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -40,
                y: 80,
                floating: true,
                borderWidth: 1,
                backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
                shadow: true
            },
            credits: {
                enabled: false
            },
            series: [{
                name: '20 Dec',
                data: [0.06, -0.18, -0.01, -0.04, 0]
            }, {
                name: '20 Jan',
                data: [7.49, 2.43, 1.18, 0.43, 0]
            }]
        });
    </script>
</body>

@include('js')