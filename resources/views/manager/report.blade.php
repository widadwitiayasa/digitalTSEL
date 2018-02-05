@include('css')
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
        <div class="tm-section tm-bg-img" id="tm-section-1">
            <div class="row">
            @php $count = 0 @endphp
                @if($detail['button']=='L1')
                    @foreach($tables as $t)
                    <center>
                    <div class="col-lg-12" style="padding-left: 250px"><br>
                            <h4>{{$t}}</h4>
                            <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8;">
                                <thead>
                                    <tr>
                                        <th class="tengah">NAMA</th>
                                        @if(is_array($result[0]))
                                            <th class="tengah">{{date("d-M", strtotime($result[0]['now_bulanlalu']))}}</th>
                                            <th class="tengah">{{date("d-M", strtotime($result[0]['now']))}}</th>
                                        @else
                                            <th class="tengah">{{date("d-M", strtotime($result[0]->now_bulanlalu))}}</th>
                                            <th class="tengah">{{date("d-M", strtotime($result[0]->now))}}</th>
                                        @endif
                                    @if($t != 'area')
                                        <th class="tengah">Target</th>
                                        <th class="tengah">Gap</th>
                                        <th class="tengah">Achievement</th>
                                    @endif
                                        <th class="tengah">MoM</th>
                                        <th class="tengah">Ytd</th>
                                        <th class="tengah">YoY</th>
                                        <!-- <th class="tengah">Jumlah</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($count == 0)
                                        @if(is_array($result[0]))
                                            <tr>
                                            <td>{{$result[0]['name']}}</td>
                                            <td>{{ number_format($result[0]['actual_bulanlalu'], 4, ".", ".") }}</td>
                                            <td>{{ number_format($result[0]['actual'], 4, ".", ".") }}</td>                      
                                            @if($t != 'area')
                                                <td>{{ number_format($result[0]['target'], 4, ".", ".") }}</td>
                                                <td>{{ number_format($result[0]['GAP'], 4, ".", ".") }}</td>
                                                <td>{{ $result[0]['achievement'] }}{{'%'}}</td>
                                            @endif
                                                 @if($result[0]['mom']<0) <td style="background-color: red">{{$result[0]['mom']}}{{ '%' }}</td>
                                                @else <td>{{$result[0]['mom']}}{{ '%' }}</td>
                                                @endif
                                                <td>{{$result[0]['ytd']}}{{ '%' }}</td>
                                                <td>{{$result[0]['yoy']}}{{ '%' }}</td>
                                                </tr>
                                        @else
                                            <tr>
                                            <td>{{$result[0]->name}}</td>
                                            <td>{{ number_format($result[0]->actual_bulanlalu, 4, ".", ".") }}</td>
                                            <td>{{ number_format($result[0]->actual, 4, ".", ".") }}</td>                      
                                            @if($t != 'area')
                                                <td>{{ number_format($result[0]->target, 4, ".", ".") }}</td>
                                                <td>{{ number_format($result[0]->GAP, 4, ".", ".") }}</td>
                                                <td>{{ $result[0]->achievement }}{{'%'}}</td>
                                            @endif
                                                 @if($result[0]->mom<0) <td style="background-color: red">{{$result[0]->mom}}{{ '%' }}</td>
                                                @else <td>{{$result[0]->mom}}{{ '%' }}</td>
                                                @endif
                                                <td>{{$result[0]->ytd}}{{ '%' }}</td>
                                                <td>{{$result[0]->yoy}}{{ '%' }}</td>
                                                </tr>
                                        @endif
                                    @else
                                        @foreach($result[$count] as $r)
                                            @if(is_array($r))
                                                <tr>
                                                <td>{{$r['name']}}</td>
                                                <td>{{ number_format($r['actual_bulanlalu'], 4, ".", ".") }}</td>
                                                <td>{{ number_format($r['actual'], 4, ".", ".") }}</td>                      
                                            @if($t != 'area')
                                                <td>{{ number_format($r['target'], 4, ".", ".") }}</td>
                                                <td>{{ number_format($r['GAP'], 4, ".", ".") }}</td>
                                            <td>{{ $r['achievement'] }}{{'%'}}</td>
                                            @endif
                                                 @if($r['mom']<0) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                                @else <td>{{$r['mom']}}{{ '%' }}</td>
                                                @endif
                                                <td>{{$r['ytd']}}{{ '%' }}</td>
                                                <td>{{$r['yoy']}}{{ '%' }}</td>
                                                </tr>
                                            @else
                                                    <tr>
                                                    <td>{{$r->name}}</td>
                                                    <td>{{ number_format($r->actual_bulanlalu, 4, ".", ".") }}</td>
                                                    <td>{{ number_format($r->actual, 4, ".", ".") }}</td>                      
                                                @if($t != 'area')
                                                    <td>{{ number_format($r->target, 4, ".", ".") }}</td>
                                                    <td>{{ number_format($r->GAP, 4, ".", ".") }}</td>
                                                <td>{{ $r->achievement }}{{'%'}}</td>
                                                @endif
                                                     @if($r->mom<0) <td style="background-color: red">{{$r->mom}}{{ '%' }}</td>
                                                    @else <td>{{$r->mom}}{{ '%' }}</td>
                                                    @endif
                                                    <td>{{$r->ytd}}{{ '%' }}</td>
                                                    <td>{{$r->yoy}}{{ '%' }}</td>
                                                    </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                    </div>
                    </center>
                    @php $count++ @endphp
                @endforeach


                @else
                    <div class="col-lg-12"><br>
                        <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8">
                            <thead>
                                <tr>
                                @if(is_array($result))
                                        <th class="tengah">NAMA</th>
                                    @if($tipe=='L1')
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]['now_bulanlalu']))}}</th>
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]['now']))}}</th>
                                        <th class="tengah">MoM</th>
                                        <th class="teangah">Ytd</th>
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
                                @else
                                    <th class="tengah">NAMA</th>
                                    @if($tipe=='L1')
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]->now_bulanlalu))}}</th>
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]->now))}}</th>
                                        <th class="tengah">MoM</th>
                                        <th class="tengah">Ytd</th>
                                        <th class="tengah">YoY</th>
                                    @elseif($tipe=='L2')
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]->now_bulanlalu))}}</th>
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]->now))}}</th>
                                        <th class="tengah">Target</th>
                                        <th class="tengah">GAP</th>
                                        <th class="tengah">Achievement</th>
                                        <th class="tengah">MoM</th>
                                        <th class="tengah">Ytd</th>
                                        <th class="tengah">YoY</th>
                                    @else
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]->now_bulanlalu))}}</th>
                                        <th class="tengah">{{date("d-M", strtotime($result[0][0]->now))}}</th>
                                        <th class="tengah">MOM</th>
                                        <th class="tengah">Absolut</th>
                                    @endif
                                @endif
                                    <!-- <th class="tengah">Jumlah</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!is_array($result)) $result = (array)$result; ?>
                                @foreach($result[0] as $r)
                                    @if(is_array($r))
                                        <tr>
                                        <td>{{$r['name']}}</td>
                                        
                                        @if($tipe=='L1')
                                            <td>{{ number_format($r['actual_bulanlalu'], 4, ".", ".") }}</td>
                                            <td>{{ number_format($r['actual'], 4, ".", ".") }}</td>
                                            @if($r['mom']<0) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                            @else <td>{{$r['mom']}}{{ '%' }}</td>
                                            @endif
                                            <td>{{$r['ytd']}}{{ '%' }}</td>
                                            <td>{{$r['yoy']}}{{ '%' }}</td>
                                        @elseif($tipe=='L2')
                                            <td>{{ number_format($r['actual_bulanlalu'], 4, ".", ".") }}</td>
                                            <td>{{ number_format($r['actual'], 4, ".", ".") }}</td>
                                            <td>{{ number_format($r['target'], 4, ".", ".") }}</td>
                                            <td>{{ number_format($r['GAP'], 4, ".", ".") }}</td>
                                            <td>{{ $r['achievement'] }}{{ '%' }}</td>
                                            @if($r['mom']<0) <td style="background-color: red">{{$r['mom']}}{{ '%' }}</td>
                                            @else <td>{{$r['mom']}}{{ '%' }}</td>
                                            @endif
                                            <td>{{$r['ytd']}}{{ '%' }}</td>
                                            <td>{{$r['yoy']}}{{ '%' }}</td>
                                        @else
                                            <td>{{ number_format($r['actual_bulanlalu'], 4, ".", ".") }}</td>
                                            <td>{{ number_format($r['actual'], 4, ".", ".") }}</td>
                                            <td>{{$r['mom']}}{{ '%' }}</td>
                                            @if($r['mom']<0) <td style="background-color: red">{{ number_format($r['absolut'], 4, ".", ".") }}</td>
                                            @else <td>{{ number_format($r['absolut'], 4, ".", ".") }}</td>
                                            @endif
                                        @endif
                                        </tr>
                                    @else
                                        <tr>
                                        <td>{{$rname}}</td>
                                        
                                        @if($tipe=='L1')
                                            <td>{{ number_format($r->actual_bulanlalu, 4, ".", ".") }}</td>
                                            <td>{{ number_format($r->actual, 4, ".", ".") }}</td>
                                            @if($r->mom<0) <td style="background-color: red">{{$r->mom}}{{ '%' }}</td>
                                            @else <td>{{$r->mom}}{{ '%' }}</td>
                                            @endif
                                            <td>{{$r->ytd}}{{ '%' }}</td>
                                            <td>{{$r->yoy}}{{ '%' }}</td>
                                        @elseif($tipe=='L2')
                                            <td>{{ number_format($r->actual_bulanlalu, 4, ".", ".") }}</td>
                                            <td>{{ number_format($r->actual, 4, ".", ".") }}</td>
                                            <td>{{ number_format($r->target, 4, ".", ".") }}</td>
                                            <td>{{ number_format($r->GAP, 4, ".", ".") }}</td>
                                            <td>{{ $r->achievement }}{{ '%' }}</td>
                                            @if($r->mom<0) <td style="background-color: red">{{$r->mom}}{{ '%' }}</td>
                                            @else <td>{{$r->mom}}{{ '%' }}</td>
                                            @endif
                                            <td>{{$r->ytd}}{{ '%' }}</td>
                                            <td>{{$r->yoy}}{{ '%' }}</td>
                                        @else
                                            <td>{{ number_format($r->actual_bulanlalu, 4, ".", ".") }}</td>
                                            <td>{{ number_format($r->actual, 4, ".", ".") }}</td>
                                            <td>{{$r->mom}}{{ '%' }}</td>
                                            @if($r->mom<0) <td style="background-color: red">{{ number_format($r->absolut, 4, ".", ".") }}</td>
                                            @else <td>{{ number_format($r->absolut, 4, ".", ".") }}</td>
                                            @endif
                                        @endif
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                </div>

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
        <script type="text/javascript">
            $(document).ready(function(){
                const aa = datepicker('#FINISHDATE');
                
            })
        </script>
@include('js')

