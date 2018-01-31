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
                    <div class="col-lg-12"><br>
                        <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8">
                            <thead>
                                <tr>
                                    <th class="tengah">NAMA</th>
                                    <th class="tengah">TARGET</th>
                                    <th class="tengah"> </th>
                                    <!-- <th class="tengah">Jumlah</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($result as $r)
                                    <tr>
                                        <form action="{{url('/target/'.$r->ID)}}" method="post" class="tm-search-form tm-section-pad-2">
                                        <input type="hidden" value="{{$cluster}}" name="INPUTCLUSTER"></input>
                                        <input type="hidden" value="{{$branch}}" name="INPUTBRANCH"></input>
                                        <input type="hidden" value="{{$regional}}" name="INPUTREGIONAL"></input>
                                        <input type="hidden" value="{{$area}}" name="INPUTAREA"></input>
                                        {{csrf_field()}}
                                        <td>{{$r->cluster->NAMA}}</td>
                                        <td><input name="target" value="{{$r->TARGET}}" type="number" class="form-control"></input></td>
                                        <td>
                                            <button type="submit">edit</button>
                                        </td>
                                        </form>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                </div>
                <table class="table table-hover table-bordered table-striped tm-position-relative" style="background-color: #E8E8E8">
                            <thead>
                                <tr>
                                    <th class="tengah">NAMA</th>
                                    <th class="tengah">TARGET</th>
                                    <!-- <th class="tengah">Jumlah</th> -->
                                </tr>
                            </thead>

                            <tbody>
                                    <tr>
                                        <td>{{$target['namaarea']}}</td>
                                        <td>{{ number_format($target['totaltargetarea'], 0, ".", ".") }}</td>
                                    </tr><tr>
                                        <td>Regional {{$target['namaregional']}}</td>
                                        <td>{{ number_format($target['totaltargetregional'], 0, ".", ".") }}</td>
                                    </tr><tr>
                                        <td>Branch {{$target['namabranch']}}</td>
                                        <td>{{ number_format($target['totaltargetbranch'], 0, ".", ".") }}</td>
                                    </tr>
                            </tbody>
                        </table>
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

