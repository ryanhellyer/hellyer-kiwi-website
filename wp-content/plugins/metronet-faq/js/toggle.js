jQuery(document).ready(function($) {
	$('.the-answer').before(' <a class="read-more">'+faq_read_more.more+'</a>'); // Add read more link
	$('.the-answer').css("display","none"); // Hide answer by default

	$("#faq-section li").click(function(){
		var $this = jQuery(this);

		if ($this.find('.the-answer').css("display") == "none") {
			$this.find('.read-more').html(faq_read_more.answer);
		} else {
			$this.find('.read-more').html(faq_read_more.more);
		}
		$this.find('.the-answer').toggle('fast', function() {});
		$(this).toggleClass('highlight');
	});

});
