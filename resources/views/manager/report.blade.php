@include('css')
        <div class="tm-main-content" id="top">
        <header style="background-color: #FFFFFF">
            <div class="container">
                <div class="row">
                    <img src="img/TSEL.png" height="80px" width="200px" style="padding: 7px">
                </div>
            </div>                
            </header>
        </div>
        <div class="tm-section tm-bg-img" id="tm-section-1">
                    <div class="row">

                    @if($detail['regional'] != 'all')
                    <div class="col-lg-12"><br>
                        <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8">
                            <thead>
                                <tr>
                                    <th class="tengah">NAMA</th>
                                @if($tipe=='L1')
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now_bulanlalu']))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now']))}}</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Ytd</th>
                                    <th class="tengah">YoY</th>
                                @elseif($tipe=='L2')
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now_bulanlalu']))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now']))}}</th>
                                    <th class="tengah">Target</th>
                                    <th class="tengah">GAP</th>
                                    <th class="tengah">Achievement</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Ytd</th>
                                    <th class="tengah">YoY</th>
                                @else
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now_bulanlalu']))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now']))}}</th>
                                    <th class="tengah">MOM</th>
                                    <th class="tengah">Absolut</th>
                                @endif
                                    <!-- <th class="tengah">Jumlah</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result[0] as $r)
                                    <tr>
                                        <td>{{$r['name']}}</td>
                                @if($tipe=='L1')
                                        <td>{{ number_format($r['actual_bulanlalu'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['actual'], 0, ".", ".") }}</td>
                                        @if($r['mom']<0) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                        @else <td>{{$r['mom']}}{{ '%' }}</td>
                                        @endif
                                        <td>{{$r['ytd']}}{{ '%' }}</td>
                                        <td>{{$r['yoy']}}{{ '%' }}</td>
                                @elseif($tipe=='L2')
                                        <td>{{ 2 }}</td>
                                        <td>{{ number_format($r['actual'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['target'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['GAP'], 0, ".", ".") }}</td>
                                        <td>{{ $r['achievement'] }}{{ '%' }}</td>
                                        @if($r['mom']<0) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                        @else <td>{{$r['mom']}}{{ '%' }}</td>
                                        @endif
                                        <td>{{$r['ytd']}}{{ '%' }}</td>
                                        <td>{{$r['yoy']}}{{ '%' }}</td>
                                @else
                                        <td>{{ number_format($r['actual_bulanlalu'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['actual'], 0, ".", ".") }}</td>
                                        <td>{{$r['mom']}}{{ '%' }}</td>
                                        @if($r['mom']<0) <td style="background-color: red">{{$r['absolut']}}{{ '%' }}</td>
                                        @else <td>{{$r['absolut']}}{{ '%' }}</td>
                                        @endif
                                @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>


                @else
                <!-- <button onclick="location.href='{{ url('download') }}'" type="button" class="btn btn-primary tm-btn-search" style="background-color: #000000">Download as CSV</button> -->
                <!-- <a href="/all-tweets-csv" class="btn btn-primary">Export as CSV</a> -->
                <?php for($i = 0; $i<=3; $i++){
                        if($i == 1) continue;
                        $judul[0] = 'Regional';
                        $judul[2] = 'Branch';
                        $judul[3] = 'Cluster';
                    ?>
                <center>
                <div class="col-lg-12" style="padding-left: 250px"><br>
                        <h4>{{$judul[$i]}}</h4>
                        <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8;">
                            <thead>
                                <tr>
                                    <th class="tengah">NAMA</th>
                                @if($tipe=='L1')
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now_bulanlalu']))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[0][0]['now']))}}</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Ytd</th>
                                    <th class="tengah">YoY</th>
                                @elseif($tipe=='L2')
                                    <th class="tengah">{{date("d-M", strtotime($result[$i][0]['now_bulanlalu']))}}</th>
                                    <th class="tengah">{{date("d-M", strtotime($result[$i][0]['now']))}}</th>
                                    <th class="tengah">Target</th>
                                    <th class="tengah">GAP</th>
                                    <th class="tengah">Achievement</th>
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Ytd</th>
                                    <th class="tengah">YoY</th>
                                @else
                                    <th class="tengah">MoM</th>
                                    <th class="tengah">Absolut</th>
                                @endif
                                    <!-- <th class="tengah">Jumlah</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result[$i] as $r)
                                    <tr>
                                        <td>{{$r['name']}}</td>
                                @if($tipe=='L1')
                                        <td>{{ number_format($r['actual_bulanlalu'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['actual'], 0, ".", ".") }}</td>
                                        @if($r['mom']) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                        @else <td>{{$r['mom']}}{{ '%' }}</td>
                                        @endif
                                        <td>{{$r['ytd']}}{{ '%' }}</td>
                                        <td>{{$r['yoy']}}{{ '%' }}</td>
                                @elseif($tipe=='L2')
                                        <td>{{ number_format($r['actual_bulanlalu'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['actual'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['target'], 0, ".", ".") }}</td>
                                        <td>{{ number_format($r['GAP'], 0, ".", ".") }}</td>
                                        <td>{{ $r['achievement'] }}{{ '%' }}</td>
                                        @if($r['mom']<0) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                        @else <td>{{$r['mom']}}{{ '%' }}</td>
                                        @endif
                                        <td>{{$r['ytd']}}{{ '%' }}</td>
                                        <td>{{$r['yoy']}}{{ '%' }}</td>
                                @else
                                        <td>{{$r['mom']}}</td>
                                        @if($r['mom']<0) <td style="background-color: red">{{$r['absolut']}}{{ '%' }}</td>
                                        @else <td>{{$r['absolut']}}{{ '%' }}</td>
                                        @endif
                                @endif
                                    </tr>
                                    @endforeach
                            </tbody>
                        </table>
                </div>
                </center>
                <?php } ?>
                @endif
            </div>
        </div>
        <footer style="background-color: #FFFFFF">
            <div class="container">
                <div class="row">
                    <p class="col-sm-12 text-center tm-color-black p-4 tm-margin-b-0">
                    Copyright &copy; <span class="tm-current-year">2018</span> Your Company- Designed by <a href="http://www.tooplate.com" class="tm-color-primary tm-font-normal" target="_parent">Tooplate</a> 
                </div>
            </div>                
        </footer>
@include('js')

