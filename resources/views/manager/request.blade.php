@extends('layouts.main')

@section('title')
Digital TSEL - Request
@endsection

@section('content')
        <div class="tm-main-content" id="top">
             <header style="background-color: #FFFFFF">
                <div class="container">
                    <div class="row">
                    <img src="img/TSEL.png" height="70px" width="200px" style="padding: 7px">
                    <div class="col-sm-2"></div>
                        <div style="padding-top: 20px">
                            <button class="btn btn-primary tm-btn-search" type="button" style="background-color: #FFD700; width:200px; height:40px"><a href="{{url('/upload')}}">upload</a></button>
                            <button class="btn btn-primary tm-btn-search" type="button" style="background-color: #FFD700; width:200px; height:40px"><a href="{{url('/request')}}">request</a></button>
                            <button class="btn btn-primary tm-btn-search" type="button" style="background-color: #FFD700; width:200px; height:40px"><a href="{{url('/target')}}">target</a></button>
                        </div>
                    </div>
                </div>                
            </header>
            <div class="tm-section tm-bg-img" id="tm-section-1">
                <div class="ie-container-width-auto-2" style="background-color: #E8E8E8">
                        <div class="row">
                                <form action="{{url('/request')}}" method="post" class="tm-search-form tm-section-pad-2" enctype="multipart/form-data">
                                    <div class="form-row clearfix pl-2 pr-2 tm-fx-col-xs">
                                        <p class="tm-margin-b-0">REPORT SYSTEM</p>
                                        {{csrf_field()}}
                                    </div>
                                    <div class="form-row tm-search-form-row">
                                       
                                        <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'regional', 'all')" name="INPUTAREA" class="form-control tm-select" id="area" disabled>
                                                <option value="1">Area 3</option>
                                                
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'branch','all', 'regional')" name="INPUTREGIONAL" class="form-control tm-select" id="regional">
                                                <option value="all">All Regional</option>
                                                <?php foreach($Regional as $r)
                                                { 
                                                    echo '<option value="'.$r->ID.'">'.$r->NAMA.'</option>'; 
                                                }
                                                ?>
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'cluster','all', 'branch')" name="INPUTBRANCH" class="form-control tm-select" id="branch">
                                                <option value="all">All Branch</option>
                                                <?php foreach($Branch as $b)
                                                { 
                                                    echo '<option value="'.$b->ID.'">'.$b->NAMA.'</option>'; 
                                                }
                                                ?>
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'service','all', 'cluster')" name="INPUTCLUSTER" class="form-control tm-select" id="cluster">
                                                <option value="all">All Cluster</option>
                                                <?php foreach($Cluster as $c)
                                                { 
                                                    echo '<option value="'.$c->ID.'">'.$c->NAMA.'</option>'; 
                                                }
                                                ?>
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div>

                                         <div id="datepickerDiv" class="form-group tm-form-element tm-form-element-100">
                                            <i class="fa fa-calendar fa-2x tm-form-element-icon"></i>
                                            <input name="REQUESTDATE" type="text" class="form-control" id="UPLOADDATE" placeholder="Request Date" required>
                                        </div>

                                        <button type="submit" class="btn btn-primary tm-btn-search" id="L1Button" name="output" value="L1" disabled>GET L1</button>

                                        <button type="submit" class="btn btn-primary tm-btn-search" id="L3Button" name="output" value="L3">GET L3</button>

                                        <button type="submit" class="btn btn-primary tm-btn-search" id="TOP5Button" name="output" value="TOP5">SHOW ME TOP V</button>
                                    </div>
                                    </div>
                                </form>                  
                        </div>      
                </div>                  
            </div>
            @endsection
            @section('js')
                <script type="text/javascript">
                    $(document).ready(function(){
                        findType('all', 'branch', 'all');
                        const picker = datepicker('#UPLOADDATE');
                        cekDate('regional', 'all');
                    });
                    function cekDate(type, target)
                    {
                        $.ajax({
                                    url: "{{url('/cekdate')}}?type="+type+"&target="+target,
                                    dataType: 'json'
                            }).done(function(res){
                                console.log(res);
                                if(res.lastdate)
                                {
                                    var dates = res.lastdate.split('-');
                                    dates[1]--;
                                    var min_year = new Date().getFullYear();
                                    min_year -= 2;
                                    $("#UPLOADDATE").remove();
                                    $("#datepickerDiv").append(`<input name="REQUESTDATE" type="text" class="form-control" id="UPLOADDATE" placeholder="Request Date" required>`);
                                    const picker = datepicker('#UPLOADDATE', {
                                        // maxDate: res.lastdate,
                                        maxDate: new Date(dates[0], dates[1], dates[2]),
                                        minDate: new Date(min_year, 11, 1)
                                    });
                                }
                                // $("#datepicker").datepicker();
                            });
                    }
                function findType(ID,nexttarget,type, target){
                    if(ID == 'all' || ID == '')
                    {

                        if(nexttarget == 'regional')
                        {
                            console.log(type);
                            $("#regional").prop('disabled', true);
                            $("#branch").prop('disabled', true);
                            $("#cluster").prop('disabled', true);
                            $("#service").prop('disabled', true);
                            $("#L1Button").prop('disabled', false);
                        }
                        else if(nexttarget == 'branch')
                        {
                            $("#branch").prop('disabled', true);
                            $("#cluster").prop('disabled', true);
                            $("#service").prop('disabled', true);
                            $("#L1Button").prop('disabled', false);
                        }
                        else if(nexttarget == 'cluster')
                        {
                            $("#cluster").prop('disabled', true);
                            $("#service").prop('disabled', true);
                            $("#L1Button").prop('disabled', false);
                        }
                        else if(nexttarget == 'service')
                        {
                            $("#L1Button").prop('disabled', false);
                            $("#service").prop('disabled', true);

                            //$("#L3Button").prop('disabled', true);
                            //$("#TOP5Button").prop('disabled', true);
                        }

                    }
                    else
                    {
                        if(nexttarget == 'service')
                        {
                            $("#L1Button").prop('disabled', true);
                            //$("#L3Button").prop('disabled', false);
                            //$("#TOP5Button").prop('disabled', false);
                        }
                        else
                        {
                            $.ajax({
                              url: "{{url('/type')}}?nexttarget="+nexttarget+"&id="+ID,
                              dataType:'json'
                            })
                              .done(function(wida) {
                                if(type == 'all')
                                    var options = '<option value="all">All '+nexttarget+'</option>';
                                else
                                    var options = '<option value="">Choose a '+nexttarget+'</option>';

                                wida.forEach(function(e){
                                    options += '<option value="'+e.ID+'">'+e.NAMA+'</option>';
                                });
                                // console.log("masuk");
                                // console.log(options);
                                console.log(document.getElementById(nexttarget).innerHTML);
                                document.getElementById(nexttarget).innerHTML = options; 
                                $('#'+nexttarget).prop('disabled',false);
                            });
                        }
                    }
                    cekDate(target, ID);
                }
                </script>
            @endsection