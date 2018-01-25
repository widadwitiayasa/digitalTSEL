        <!-- load JS files -->
        <script src="js/jquery-1.11.3.min.js"></script>             <!-- jQuery (https://jquery.com/download/) -->
        <script src="js/popper.min.js"></script>                    <!-- https://popper.js.org/ -->       
        <script src="js/bootstrap.min.js"></script>                 <!-- https://getbootstrap.com/ -->
        <script src="js/datepicker.min.js"></script>                <!-- https://github.com/qodesmith/datepicker -->
        <script src="js/jquery.singlePageNav.min.js"></script>
        <script src="js/chartist.min.js"</script>
              <!-- Single Page Nav (https://github.com/ChrisWojcik/single-page-nav) -->
        <script src="slick/slick.min.js"></script>                  <!-- http://kenwheeler.github.io/slick/ -->
        <script>

            /* Google map
            ------------------------------------------------*/
            var map = '';
            var center;

            $(document).ready(function(){
                findType('all','branch', 'all');
                $(window).on("scroll", function() {
                    if($(window).scrollTop() > 100) {
                        $(".tm-top-bar").addClass("active");
                    } else {
                        //remove the background property so it comes transparent again (defined in your css)
                       $(".tm-top-bar").removeClass("active");
                    }
                });      

                // Google Map
                loadGoogleMap();  

                // Date Picker
                const upload = datepicker('#UPLOADDATE');
                const aa = datepicker('#FINISHDATE');
                
                
                // Slick carousel
                setCarousel();
                setPageNav();

                $(window).resize(function() {
                  setCarousel();
                  setPageNav();
                });

                // Close navbar after clicked
                $('.nav-link').click(function(){
                    $('#mainNav').removeClass('show');
                });

                // Control video
                $('.tm-btn-play').click(function() {
                    togglePlayPause();                                      
                });

                $('.tm-btn-pause').click(function() {
                    togglePlayPause();                                      
                });

                // Update the current year in copyright
                $('.tm-current-year').text(new Date().getFullYear());

            });



            function findType(ID,nexttarget,type){
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
                        $("#L3Button").prop('disabled', true);
                        $("#TOP5Button").prop('disabled', true);
                    }
                    return;
                }
                if(nexttarget == 'service')
                {
                    $("#L1Button").prop('disabled', true);
                    $("#L3Button").prop('disabled', false);
                    $("#TOP5Button").prop('disabled', false);
                }
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

            function initialize() {
                var mapOptions = {
                    zoom: 16,
                    center: new google.maps.LatLng(13.7567928,100.5653741),
                    scrollwheel: false
                };

                map = new google.maps.Map(document.getElementById('google-map'),  mapOptions);

                google.maps.event.addDomListener(map, 'idle', function() {
                  calculateCenter();
              });

                google.maps.event.addDomListener(window, 'resize', function() {
                  map.setCenter(center);
              });
            }

            function calculateCenter() {
                center = map.getCenter();
            }

            function loadGoogleMap(){
                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDVWt4rJfibfsEDvcuaChUaZRS5NXey1Cs&v=3.exp&sensor=false&' + 'callback=initialize';
                document.body.appendChild(script);
            } 

            function setCarousel() {
                
                if ($('.tm-article-carousel').hasClass('slick-initialized')) {
                    $('.tm-article-carousel').slick('destroy');
                } 

                if($(window).width() < 438){
                    // Slick carousel
                    $('.tm-article-carousel').slick({
                        infinite: false,
                        dots: true,
                        slidesToShow: 1,
                        slidesToScroll: 1
                    });
                }
                else {
                 $('.tm-article-carousel').slick({
                        infinite: false,
                        dots: true,
                        slidesToShow: 2,
                        slidesToScroll: 1
                    });   
                }
            }

            function setPageNav(){
                if($(window).width() > 991) {
                    $('#tm-top-bar').singlePageNav({
                        currentClass:'active',
                        offset: 79
                    });   
                }
                else {
                    $('#tm-top-bar').singlePageNav({
                        currentClass:'active',
                        offset: 65
                    });   
                }
            }

            function togglePlayPause() {
                vid = $('.tmVideo').get(0);

                if(vid.paused) {
                    vid.play();
                    $('.tm-btn-play').hide();
                    $('.tm-btn-pause').show();
                }
                else {
                    vid.pause();
                    $('.tm-btn-play').show();
                    $('.tm-btn-pause').hide();   
                }  
            }
       

        </script>             

</body>
</html>
