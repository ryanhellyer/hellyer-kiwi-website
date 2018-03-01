<?php 
$ajax_security = "eMu.J9aFhfi#R[:oWA@xp_~P@&BGQHD{c|64b2Wu@kvO1 L4Sh=x;J0<UeB1].`Y";      
if ( ! isset( $atts['id'] ) ) { 
	$atts['id'] = "pushpress-calendar";
}

?>




<div id="eventContent" title="Event Details" style="display:none;">
	<p>
		<span id="pushpress-schedule-modal-date"></span>
		<big><strong><span id="pushpress-schedule-modal-time"></span></strong></big>
	</p>
	<p>
		<span id="pushpress-schedule-modal-class_type"></span>
	</p>
	<p>
		<span id="pushpress-schedule-modal-coach"></span>
	</p>
	<p id="pushpress-schedule-modal-description">
</div>



<div class="pushpress-container" id="pushpress-schedule-<?php echo esc_attr( $atts['id'] );?>-container">
	<div class="clearfix">
		<div class="spinner"></div>
	</div>
	<div id="<?php echo esc_attr( $atts['id'] );?>" class="pushpress-calendar calendar"></div>	
</div>

<style>
.fc tr td,
.fc thead th { 
	padding: 0 !important;
}

.fc th { 
	border-right: 0 !important;
}

.fc-head table { 
	margin: 0 !important;
}

.fc .fc-today  { 
	background: #cdecfc !important;
}

.fc-time-grid .fc-slats tr { 
	height: 50px;
}

.fc-time-grid .fc-slats td { 
	/*border:0;*/
}
</style>

<script>
jQuery('#<?php echo esc_attr( $atts['id'] );?>').fullCalendar({
	// put your options and callbacks here
	header: {
		left: 'prev,next today',
		center: 'title',
		right: 'month,agendaWeek,agendaDay'
	},
	loading: function( isLoading, view ) {
		if(isLoading) {// isLoading gives boolean value
			//show your loader here 
			jQuery('.spinner').show();
		} else {
			//hide your loader here
			jQuery('.spinner').hide();
		}
	},
	allDaySlot:false,
	year: <?php echo esc_html( date( 'Y' ) );?>,
	month: <?php echo esc_html( date( 'm' ) ) - 1;?>,
	date: <?php echo esc_html( date( 'd' ) );?>,
	contentHeight: 850,
	defaultView: 'agendaWeek',
	slotDuration: '00:60:00',
	slotLabelInterval: '00:60:00',
	minTime: '4:00:00',
	maxTime: '23:00:00',
	dayClick: function(date, allDay, jsEvent, view) {
		/*
		console.log("DAYCLICK");
		console.log(date);
		var utc_date = date.toUTCString(); 
		var date = new Date(utc_date);
		var is_dst = date.dst();

		console.log("UTC To String");
		console.log(utc_date);
		console.log("dst? " + is_dst);
		console.log(date.getTimezoneOffset());
		var start_time = date.getTime()/1000;
		if (is_dst) { 
		start_time += 3600;
		}

		var d = {
		starttimestamp : (start_time),
		allday: allDay
		}
		d = JSON.stringify(d);
		d = encodeURIComponent(d);
		console.log(d);
		window.location = '/calendar/create/' + d;
		*/
	},

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php

	events: {
		url: '<?php 

		$url = admin_url( 'admin-ajax.php' ) . '?callback=?';
		echo esc_url_raw( $url );

		?>',
		cache: true,
		type: 'POST',
		data: {
			action: 'get_schedule',
			class_type: '<?php

				if ( isset( $atts['class_type'] ) ) {
					echo esc_html( $atts['class_type'] );
				} else {
					//
				}

			?>',
			calendar_type: '<?php

				if ( isset( $atts['calendar_type'] ) ) {
					echo esc_html( $atts['calendar_type'] );
				} else {
					//
				}

			?>',
			post_code: '<?php

				if ( isset( $atts['post_code'] ) ) {
					echo esc_html( $atts['post_code'] );
				} else {
					//
				}

			?>',
			coach: '<?php

				if ( isset( $atts['coach'] ) ) {
					echo esc_html( $atts['coach'] );
				} else {
					//
				}

			?>',
			month: '<?php

				if ( isset( $atts['month'] ) ) {
					echo esc_html( $atts['month'] );
				} else {
					//
				}

			?>',
			week: '<?php

				if ( isset( $atts['week'] ) ) {
					echo esc_html( $atts['week'] );
				} else {
					//
				}

			?>',
			day: '<?php

				if ( isset( $atts['day'] ) ) {
					echo esc_html( $atts['day'] );
				} else {
					//
				}

			?>',
			security : '<?php echo wp_create_nonce($ajax_security);?>'
		},
		error: function(e) {
			alert('there was an error while fetching events!');
			console.log(e);
		},
		// borderColor: '#1a90d8',
		// backgroundColor: '#1a90d8',
		className: 'pp-event',
		// textColor: 'white' // a non-ajax option
	},
	eventRender: function(event, element) {


		/* jQuery UI Dialog */
		element.attr('href', 'javascript:void(0);');
		element.click(function(element) {
			console.log(event);

			jQuery("#pushpress-schedule-modal-date").html(event.date+'<br />');
			jQuery("#pushpress-schedule-modal-time").html(event.time);
			if ( '' !== event.class_type ) {
				jQuery("#pushpress-schedule-modal-class_type").html('<?php esc_html_e( 'Class type', 'pushpress-schedule ' ); ?><strong><br />'+event.class_type+'</strong>');
			}
			if ( '' !== event.coach ) {
				jQuery("#pushpress-schedule-modal-coach").html('<?php esc_html_e( 'Coach', 'pushpress-schedule ' ); ?><strong><br />'+event.coach+'</strong>');
			}
			if ( '' !== event.description ) {
				jQuery("#pushpress-schedule-modal-description").html('<?php esc_html_e( 'Class type', 'pushpress-schedule ' ); ?><strong>'+event.description+'</strong>');
			}

			jQuery("#eventContent").dialog({ modal: true, title: event.title, width:350});
		});

/*
		console.log("-- rendering event --");
		console.log(event);
		console.log(element[0]);
		element = element[0];
		var event_inner = jQuery(element).children('.fc-content');
		event_inner = event_inner[0];
		console.log("EVENT INNER");
		console.log(event_inner);
		var time = jQuery(event_inner).children(".fc-time");
		time = time[0];
		console.log("time:");
		console.log(time);
		jQuery(time).css('display','none');

		var start = jQuery(time).html(); 
		console.log(event.Coach);

		jQuery(event_inner).append("<div class='fc-event-coach'>" + event.Coach + "</div>");
*/
	},

	eventAfterAllRender: function(event, element) {
		console.log("-------------------------------");
		console.log("EventAfterAllRender");

		var bg = false;
		jQuery('.fc-slats tr').find('span').each( function(){
			var timeslot = jQuery(this).text();

			removeWords = 'am|pm';
			re = new RegExp(removeWords, 'gi');
			timeslot = timeslot.replace(re, '');
			timeslot = parseInt(timeslot);
			console.log(timeslot);

			if (timeslot % 2 == 0) { 
				console.log(timeslot % 2);
				bg = true;
			}
			else { 
				bg = false;
			}
			// if(timeSlot> 13 && timeSlot < 18)    //Change 13 and 18 according to what you need

			if (bg) { 
				//jQuery(this).closest('tr').css('background-color', '#e5e5e5');
			}
		});



	}
});

</script>