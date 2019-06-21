(function (wp) {
    jQuery(document).ready(function ($) {

        wp.data.dispatch('core/notices').createNotice(
            'warning', // Can be one of: success, info, warning, error.
            'New Strattic feature: scheduled posts! Please note that scheduled posts publish your whole site at the designated time so please Save as Draft if you don’t want your post or page to be public.', // Text string to display.
            {
                isDismissible: true, // Whether the user can dismiss the notice.
                // Any actions the user can perform.
                // actions: [
                // 	{
                // 		url: '#',
                // 		label: 'View post'
                // 	}
                // ]
            }
        );
    })
})(window.wp);