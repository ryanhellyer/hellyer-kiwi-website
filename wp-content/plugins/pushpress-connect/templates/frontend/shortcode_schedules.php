<?php
date_default_timezone_set("UTC");
$linkTo = str_replace('{subdomain}', $this->subdomain, PUSHPRESS_CLIENT) . 'schedule/index/';
?>
<div class="wp-pushpress">
    <div class="schedule-date">
        <button name="btnPrev" class="btnPrev" type="button">Prev Week</button>
        <input name="txtDate" class="txtDate" type="text" value="" size="10" />
        <button name="btnNext" class="btnNext" type="button">Next Week</button>
    </div>
    <?php
    if (!empty($schedules) && ( count($schedules) > 0 )) {
        foreach ($schedules as $key => $item) {
            ?>
            <ul class="wp-pushpress-list">
                <li class="item-first">
                    <span><h3><?php echo date('l, F jS', $key); ?></h3></span>
                    <span class="schedule-button">
                        <button data-href="<?php echo $linkTo . date('z', $key); ?>" data-target="_blank">More info & Reserve</button>
                    </span>
                    <div class="clear"></div>
                </li>
                <li class="item-header" style="font-weight:bold;">
                    <span class="schedule-name">Class Time</span>
                    <span class="schedule-hour">Duration</span>
                    <span class="schedule-title">Class</span>
                    <span class="schedule-type">
                        Coach
                    </span>
                    <span class="schedule-spots">
                        Spaces Avail.
                    </span>
                    <div class="clear"></div>
                </li>
                <?php
                foreach ($item as $k => $value) {
                    ?>

                    <li class="item-other">
                        <span class="schedule-name"><?php echo date('g:i a', $value['start_timestamp']); ?></span>
                        <span class="schedule-hour"><?php echo round(( ($value['end_timestamp'] - $value['start_timestamp']) / 3600 ), 2) . " hr"; ?></span>
                        <span class="schedule-title"><?php echo $value['title']; ?></span>
                        <span class="schedule-type">
                            <?php if (strlen(trim($value['fullname']))) { 
                                echo $value['fullname']; 
                            }
                            else { 
                                echo 'Coach TBD';
                            }
                            ?>
                        </span>
                        <span class="schedule-spots">
                            <?php 
                                if ($value['status']['spots_available'] == -1) { 
                                    echo "Yes";
                                }
                                else if ($value['status']['spots_available'] == 0) { 
                                    echo "No";
                                }
                                else {
                                    echo "Yes";
                                }                                
                            ?>                            
                        </span>
                        <div class="clear"></div>
                    </li>
                    <?php
                }
                ?>

            </ul>
            <?php
        }
    }
    ?>

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
            //$( textdate ).datepicker( "option", "dateFormat", 'mm-dd-yy' );
            $(textdate).on('change', function () {
                var dateval = $(textdate).val();

                set_dateparams(dateval);
            });

            $('.btnPrev').on('click', function () {
                var dateval = $(textdate).val();
                var firstDay = new Date(dateval);
                var prevWeek = new Date(firstDay.getTime() - 7 * 24 * 60 * 60 * 1000);
                var dateval = (prevWeek.getMonth() + 1) + "/" + prevWeek.getDate() + "/" + prevWeek.getFullYear();
                set_dateparams(dateval);
            });

            $('.btnNext').on('click', function () {
                var dateval = $(textdate).val();

                var firstDay = new Date(dateval);
                var nextWeek = new Date(firstDay.getTime() + 7 * 24 * 60 * 60 * 1000);
                var dateval = (nextWeek.getMonth() + 1) + "/" + nextWeek.getDate() + "/" + nextWeek.getFullYear();
                set_dateparams(dateval);
            });

            $(textdate).val('<?php echo empty($date) ? date('m/d/Y') : date('m/d/Y', $date); ?>');
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