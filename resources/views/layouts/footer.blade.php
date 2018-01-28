        <footer style="background-color: #FFFFFF">
                <div class="container">
                    <div class="row">
                        <p class="col-sm-12 text-center tm-color-black p-4 tm-margin-b-0">
                        Copyright &copy; <span class="tm-current-year">2018</span> Your Company
                        
                        - Designed by <a href="http://www.tooplate.com" class="tm-color-primary tm-font-normal" target="_parent">Tooplate</a></p>        
                    </div>
                </div>                
        </footer>

        <!-- load JS files -->
        <script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
        <script type="text/javascript" src="js/popper.min.js"></script>                    <!-- https://popper.js.org/ -->       
        <script src="js/bootstrap.min.js" type="text/javascript"></script>                 <!-- https://getbootstrap.com/ -->
        <script src="js/datepicker.min.js" type="text/javascript"></script>                <!--https://github.com/qodesmith/datepicker-->
        <script type="text/javascript" src="js/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/jquery.singlePageNav.min.js"></script> 
        <script type="text/javascript" src="js/chartist.min.js"></script>
              Single Page Nav (https://github.com/ChrisWojcik/single-page-nav)
        <script src="js/slick/slick.min.js"></script>                  <!-- http://kenwheeler.github.io/slick/-->
        @yield('js')
        <script>

            // Google map
            var map = '';
            var center;

            $(document).ready(function(){
                $(window).on("scroll", function() {
                    if($(window).scrollTop() > 100) {
                        $(".tm-top-bar").addClass("active");
                    } else {
                        //remove the background property so it comes transparent again (defined in your css)
                       $(".tm-top-bar").removeClass("active");
                    }
                });      

                // Google Map
                // loadGoogleMap();  

                // Date Picker
                // const upload = datepicker('#UPLOADDATE');
                
                // Slick carousel
                // setCarousel();
                // setPageNav();

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
                console.log("cek");
            });

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

            // function setCarousel() {
                
            //     if ($('.tm-article-carousel').hasClass('slick-initialized')) {
            //         $('.tm-article-carousel').slick('destroy');
            //     } 

            //     if($(window).width() < 438){
            //         // Slick carousel
            //         $('.tm-article-carousel').slick({
            //             infinite: false,
            //             dots: true,
            //             slidesToShow: 1,
            //             slidesToScroll: 1
            //         });
            //     }
            //     else {
            //      $('.tm-article-carousel').slick({
            //             infinite: false,
            //             dots: true,
            //             slidesToShow: 2,
            //             slidesToScroll: 1
            //         });   
            //     }
            // }

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