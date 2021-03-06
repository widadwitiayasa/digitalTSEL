@include('css')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>

<div class="tm-main-content" id="top">
        <header style="background-color: #FFFFFF">
            <div class="container">
                <div class="row">
                    <img src="img/TSEL.png" height="80px" width="200px" style="padding: 7px">
                    <div class="col-sm-3"></div>
                        <div style="padding-top: 20px">
                            <button class="btn btn-primary tm-btn-search" type="button" style="background-color: #FFD700; width:200px; height:40px"><a href="{{url('/upload')}}">upload</a></button>
                            <button class="btn btn-primary tm-btn-search" type="button" style="background-color: #FFD700; width:200px; height:40px"><a href="{{url('/request')}}">request</a></button>
                            <button class="btn btn-primary tm-btn-search" type="button" style="background-color: #FFD700; width:200px; height:40px"><a href="{{url('/target')}}">target</a></button>
                        </div>
                    </div>
                </div>
            </div>                
            </header>
        </div>
        <div class="tm-section tm-bg-img row" id="tm-section-1">
                <div class="col-lg-10 text-center" style="padding-left: 0px;">

        <div id="mychart" style="min-width: 310px; max-width: 800px; height: 400px; margin: 0 auto; padding: 20px"></div>
                    <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8">
                            <thead>
                                <tr>
                                    <th class="tengah">NAMA</th>
                                @if($tipe=='L1')
                                    <th class="tengah">{{date("d-M", strtotime($result[0]->{'0'}->now_bulanlalu))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0]->{'0'}->now))}}</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Ytd</th>
                                    <th class="tengah">YoY</th>
                                @elseif($tipe=='L2')
                                    <th class="tengah">{{date("d-M", strtotime($result[0]->{'0'}->now_bulanlalu))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0]->{'0'}->now))}}</th>
                                    <th class="tengah">Target</th>
                                    <th class="tengah">GAP</th>
                                    <th class="tengah">Achievement</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Ytd</th>
                                    <th class="tengah">YoY</th>
                                @else
                                    <th class="tengah">{{date("d-M", strtotime($result[0]->{'0'}->now_bulanlalu))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0]->{'0'}->now))}}</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Absolut</th>
                                @endif
                                    <!-- <th class="tengah">Jumlah</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!is_array($result)) $result = (array)$result; ?>
                                @foreach($result[1] as $r)
                                    <tr>
                                        <td>{{$r->name}}</td>
                                @if($tipe=='L1')
                                        <td>{{ number_format($r->actual_bulanlalu, 2, ".", ".") }}</td>
                                        <td>{{ number_format($r->actual, 2, ".", ".") }}</td>
                                        <td>{{$r->mom}}{{ '%' }}</td>
                                        <td>{{$r->ytd}}{{ '%' }}</td>
                                        <td>{{$r->yoy}}{{ '%' }}</td>
                                @elseif($tipe=='L2')
                                        <td>{{ number_format($r->actual_bulanlalu, 2, ".", ".") }}</td>
                                        <td>{{ number_format($r->actual, 2, ".", ".") }}</td>
                                        <td>{{ number_format($r->target, 2, ".", ".") }}</td>
                                        <td>{{ number_format($r->GAP, 2, ".", ".") }}</td>
                                        <td>{{$r->achievement}}{{ '%' }}</td>
                                        <td>{{$r->mom}}{{ '%' }}</td>
                                        <td>{{$r->ytd}}{{ '%' }}</td>
                                        <td>{{$r->yoy}}{{ '%' }}</td>
                                @else
                                        <td>{{ number_format($r->actual_bulanlalu, 2, ".", ".") }}</td>
                                        <td>{{ number_format($r->actual, 2, ".", ".") }}</td>
                                        <td>{{$r->mom}}{{ '%' }}</td>
                                        <td>{{ number_format($r->absolut, 2, ".", ".")  }}</td>
                                @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
        </div>

<script>

Highcharts.chart('mychart', {
    chart: {
        type: 'bar'
    },
    title: {
        text: 'TOP 5 SERVICE'
    },
    // subtitle: {
    //     text: 'Source: <a href="https://en.wikipedia.org/wiki/World_population">Wikipedia.org</a>'
    // },
    xAxis: {
        categories: [@foreach($result[1] as $r) '{{$r->name}}', @endforeach],
        title: {
            text: null
        }
    },
    yAxis: {
        min: 0,
        title: {
            text: 'Actual (Milyard)',
            align: 'high'
        },
        labels: {
            overflow: 'justify'
        }
    },
    tooltip: {
        valueSuffix: ' Milyard'
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
        name: '{{$result[0]->{'0'}->now}}',
        data: [@foreach($result[1] as $r) {{ number_format($r->actual, 2, ".", ".") }}, @endforeach]
    }, 
    {
        name: '{{$result[0]->{'0'}->now_bulanlalu}}',
        data: [@foreach($result[1] as $r) {{ number_format($r->actual_bulanlalu, 2, ".", ".") }}, @endforeach]
    }]
    });

</script>

<footer style="background-color: #FFFFFF">
            <div class="container">
                <div class="row">
                    <p class="col-sm-12 text-center tm-color-black p-4 tm-margin-b-0">
                    Copyright &copy; <span class="tm-current-year">2018</span> Your Company- Designed by <a href="http://www.tooplate.com" class="tm-color-primary tm-font-normal" target="_parent">Tooplate</a> 
                </div>
            </div>                
        </footer>
@include('js')