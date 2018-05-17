<?php
date_default_timezone_set('UTC');
?>	
<style>
    .pp-icons-plans { 
        cursor:pointer;
        margin:0 20px;
    }
</style>
<div class="wp-pushpress">
    <div class="workout-date">
        <button name="btnToday" class="btnToday" type="button">Today</button>
        <button name="btnTomorrow" class="btnTomorrow" type="button">Tomorrow</button>
        <input name="txtDate" class="txtDate" type="button" size="10" value="">
    </div>

    <div class="line-date">
        <span><h2><?php echo empty($date) ? date('l, F jS') : date('l, F jS', $date); ?></h2></span> 
    </div>
    <?php foreach ($workouts as $key => $workout): ?>
        <h2><?php echo $workout['track_name']; ?></h2>
        
    
        <div class="wp-pushpress-list">
            <?php if (count($workout['data']) > 0): ?>

                <?php foreach ($workout['data'] as $key => $item): ?>							
                    <div class="item-other">
                        <h3><?php echo $item['type']; ?>
                        </h3>
                        <?php
                        if ($item['name']) {
                            echo '<p class="workout-title"><strong>' . $item['name'] . '</strong></p>';
                        }
                        ?>                                                
                        <p><?php echo $item['description']; ?></p>

                        <p class="public_notes">
                            <strong>Notes:</strong>
                            <br/>
                            <?php echo $item['public_notes'] ?>
                        </p>
                        <div class="clear"></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>

                <div class="item-other">No Workout scheduled.</div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

</div>
<script type="text/javascript">
    jQuery(function ($) {       

        window.pushpressLoaded = function () {
            'use strict';

            function set_dateparams(dateval) {
                var url = window.location.href;
                if (url.indexOf('?')) {
                    var end = url.indexOf('?');
                    window.location.href = url.substr(0, end) + "?datefilter=" + dateval;
                } else {
                    window.location.href = url + "?datefilter=" + dateval;
                }
            }
            var textdate = ".txtDate";
            $(textdate).datepicker( );
            $(textdate).on('change', function () {
                var dateval = $(textdate).val();

                set_dateparams(dateval);
            });

            $(textdate).on('click', function () {
                $(this).datepicker('show');
            });

            $('.btnToday').on('click', function () {
                var currDate = new Date();
                var dateval = (currDate.getMonth() + 1) + "/" + currDate.getDate() + "/" + currDate.getFullYear();
                set_dateparams(dateval);
            });

            $('.btnTomorrow').on('click', function () {
                var currDate = new Date();
                var tomorrow = new Date(currDate.getTime() + 1 * 24 * 60 * 60 * 1000);
                var dateval = (tomorrow.getMonth() + 1) + "/" + tomorrow.getDate() + "/" + tomorrow.getFullYear();
                set_dateparams(dateval);
            });

            $(textdate).val('<?php echo empty($date) ? date('m/d/Y') : date('m/d/Y', $date); ?>');

            makeDisabled();
            function makeDisabled() {
                var dateval = new Date($(".txtDate").val());
                var currDate = new Date();
                var tomorrow = new Date(currDate.getTime() + 1 * 24 * 60 * 60 * 1000);
                currDate.setHours(0, 0, 0, 0);//reset hours is zero
                dateval.setHours(0, 0, 0, 0);//reset hours is zero
                tomorrow.setHours(0, 0, 0, 0);//reset hours is zero
                if (currDate.valueOf() === dateval.valueOf()) {
                    $('.btnToday').attr('disabled', 'disabled');
                }
                if (tomorrow.valueOf() === dateval.valueOf()) {
                    $('.btnTomorrow').attr('disabled', 'disabled');
                }
            }
        };
        
        //case for theme not support load js incorrect standard
        if (!$.fn.datepicker) {
            var get_site_url = '<?php echo get_site_url(); ?>';
            var plugins_url = '<?php echo plugins_url(); ?>';
            loadJS(get_site_url + '/wp-includes/js/jquery/ui/core.min.js?ver=1.11.4');
            loadJS(get_site_url + '/wp-includes/js/jquery/ui/datepicker.min.js?ver=1.11.4');
            loadJS(plugins_url + '/wp-pushpress/js/script.js?ver=1.0.0');
        } else {
            window.pushpressLoaded();
        }

        function loadJS(file) {
            // DOM: Create the script element
            var jsElm = document.createElement("script");
            // set the type attribute
            jsElm.type = "application/javascript";
            // make the script element load file
            jsElm.src = file;
            jsElm.async = false;
            // finally insert the element to the body element in order to load the script
            document.body.appendChild(jsElm);
        }

    });
</script>