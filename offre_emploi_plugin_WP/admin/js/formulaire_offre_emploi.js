jQuery(document).ready(function($) {

    jQuery('#commune').select2();

    jQuery('#date_debut').datepicker();
    jQuery('#date_fin').datepicker();

	$('body').on( 'click', '.custom-upload-button', function(e){
		e.preventDefault();
		var upload_button = $(this),
		custom_media_uploader = wp.media({
			title: 'Insertion d\'image',
			library : {
				type : 'image'
			},
			button: {
				text: 'Utiliser cette image'
			},
			multiple: false
		}).on('select', function() {
			var attachment = custom_media_uploader.state().get('selection').first().toJSON();
			upload_button.html('<img decoding="async" src="' + attachment.url + '" width="20%">');
			upload_button.next().show().next().val(attachment.url);
		}).open();
	
	});

	// remove function
	$('body').on('click', '.custom-upload-remove', function(e){
		e.preventDefault();
		var upload_button = $(this);
		upload_button.next().val('');
		upload_button.hide().prev().html('Envoyer une image');
	});
});