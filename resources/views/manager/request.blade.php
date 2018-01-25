@include('css')
        <div class="tm-main-content" id="top">
             <header style="background-color: #FFFFFF">
                <div class="container">
                    <div class="row">
                    <img src="img/TSEL.png" height="70px" width="200px" style="padding: 7px">
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
                                            <i class="fa fa-calendar fa-2x tm-form-element-icon"></i>
                                            <input name="REQUESTDATE" type="text" class="form-control" id="UPLOADDATE" placeholder="Request Date" required>
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'regional', 'all')" name="INPUTAREA" class="form-control tm-select" id="area" disabled>
                                                <option value="1">Area 3</option>
                                                
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div>
                                        <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'branch','all')" name="INPUTREGIONAL" class="form-control tm-select" id="regional">
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
                                            <select onchange="findType(this.value,'cluster','all')" name="INPUTBRANCH" class="form-control tm-select" id="branch">
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
                                            <select onchange="findType(this.value,'service','all')" name="INPUTCLUSTER" class="form-control tm-select" id="cluster">
                                                <option value="all">All Cluster</option>
                                                <?php foreach($Cluster as $c)
                                                { 
                                                    echo '<option value="'.$c->ID.'">'.$c->NAMA.'</option>'; 
                                                }
                                                ?>
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div>
                                        <!-- <div class="form-group tm-form-element tm-form-element-100">
                                            <select onchange="findType(this.value,'service')" name="INPUTSERVICE" class="form-control tm-select" id="service">
                                                <option value="all">All Service</option>
                                                <?php foreach($Service as $s)
                                                { 
                                                    echo '<option value="'.$s->ID.'">'.$s->NAMA.'</option>'; 
                                                }
                                                ?>
                                            </select>
                                            <i class="fa fa-map-marker fa-fw fa-2x tm-form-element-icon"></i>
                                        </div> -->
                                        <button type="submit" class="btn btn-primary tm-btn-search" id="L1Button" name="output" value="L1" disabled>GET L1</button>

                                        <button type="submit" class="btn btn-primary tm-btn-search" id="L3Button" name="output" value="L3" disabled>GET L3</button>

                                        <button type="submit" class="btn btn-primary tm-btn-search" id="TOP5Button" name="output" value="TOP5" disabled>SHOW ME TOP V</button>
                                    </div>
                                    </div>
                                </form>                  
                        </div>      
                </div>                  
            </div>
            <footer style="background-color: #FFFFFF">
                <div class="container">
                    <div class="row">
                        <p class="col-sm-12 text-center tm-color-black p-4 tm-margin-b-0">
                        Copyright &copy; <span class="tm-current-year">2018</span> Your Company
                        
                        - Designed by <a href="http://www.tooplate.com" class="tm-color-primary tm-font-normal" target="_parent">Tooplate</a></p>        
                    </div>
                </div>                
            </footer>
@include('js')


