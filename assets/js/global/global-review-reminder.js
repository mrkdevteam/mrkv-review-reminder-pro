jQuery(window).on('load', function() 
{
	jQuery('body').on('click', '.mrkv_review_reminder__order__send_manually', function()
	{
		let order_id = jQuery(this).attr('data-order-id');
		let current_btn = jQuery(this);

		let data_ajax = {
            action: 'mrkv_review_reminder_sent',
            order_id: order_id,
            nonce: mrkv_review_rem_helper.nonce
        };

        jQuery.ajax({
			url: mrkv_review_rem_helper.ajax_url,
			type: 'POST',
			data: data_ajax,
			beforeSend: function( xhr ) 
			{
				jQuery(this).addClass('active');
			},
			success: function( data ) 
			{
				jQuery(current_btn).removeClass('active');

				if(jQuery(current_btn).closest('.mrkv_review_reminder__orders').length > 0)
				{
					jQuery(current_btn).closest('a').html(data);
				}
				else
				{
					jQuery('#mrkv_review_reminder_data_box .inside').html(mrkv_review_rem_helper.already_sent);
					location.reload();
				}
			}
		});
	});

	jQuery('body').on('click', '.mrkv_review_reminder__order__delete_manually', function()
	{
		let order_id = jQuery(this).attr('data-order-id');
		let current_btn = jQuery(this);

		let data_ajax = {
            action: 'mrkv_review_reminder_remove',
            order_id: order_id,
            nonce: mrkv_review_rem_helper.nonce
        };

        jQuery.ajax({
			url: mrkv_review_rem_helper.ajax_url,
			type: 'POST',
			data: data_ajax,
			beforeSend: function( xhr ) 
			{
				jQuery(this).addClass('active');
			},
			success: function( data ) 
			{
				jQuery(current_btn).removeClass('active');

				if(jQuery(current_btn).closest('.mrkv_review_reminder__orders').length > 0)
				{
					jQuery(current_btn).closest('a').html(data);
				}
				else
				{
					jQuery('#mrkv_review_reminder_data_box .inside').html(data);
				}
			}
		});
	});
});