var initAce;

(function ($) {

	/**
	 * Ace editor
	 */
	initAce = function(el)
	{
		var textarea = $(el);
		var mode = textarea.data('editor');
		var editDiv = $('<div>', {
			position: 'absolute',
			height: 500,
			'class': textarea.attr('class')
		}).insertBefore(textarea);

		textarea.css('display','none');
		var editor = ace.edit(editDiv[0]);
		editor.getSession().setValue(textarea.val());		    
		editor.getSession().setMode("ace/mode/css");
		editor.getSession().setMode("ace/mode/html");
		editor.setTheme("ace/theme/monokai");

		// copy back to textarea on form submit...
		textarea.closest('form').submit(function() {
			textarea.val(editor.getSession().getValue());
		});

		return editor;
	}

	$(document).ready(function(){
		$('.pwa-editor-text').each(function(){
			initAce(this);
		});
	});

	// $(".bootstrap-switch").bootstrapSwitch({ 
	// 	size: 'mini' 
	// });

	TONJOO_PWA_SETTINGS = function() {
		var self = this;

		self.init = function() {
			// Initiate Color Picker
            $('.wp-color-picker-field').wpColorPicker();

            // Media Gallery
            $('.wpsa-browse').on('click', function (event) {
                event.preventDefault();
                var self = $(this);
                // Create the media frame.
                var file_frame = wp.media.frames.file_frame = wp.media({
                    title: self.data('uploader_title'),
                    button: {
                        text: self.data('uploader_button_text'),
                    },
                    multiple: false
                });
                file_frame.on('select', function () {
                    attachment = file_frame.state().get('selection').first().toJSON();
                    self.prev('.wpsa-url').val(attachment.url);
                });
                // Finally, open the modal
                file_frame.open();
            });

            // Switches option sections
			$('.group').hide();

			var activetab = '';

			if (typeof(localStorage) != 'undefined' ) {
				activetab = localStorage.getItem("activetab");
			}

			console.log( activetab );

			if (activetab != '' && $(activetab).length ) {
				$(activetab).fadeIn();
			} else {
				$('.group:first').fadeIn();
			}

			$('.group .collapsed').each(function(){
				$(this).find('input:checked').parent().parent().parent().nextAll().each(
				function(){
					if ($(this).hasClass('last')) {
						$(this).removeClass('hidden');
						return false;
					}
					$(this).filter('.hidden').removeClass('hidden');
				});
			});

			if (activetab != '' && $(activetab + '-tab').length ) {
				$(activetab + '-tab').addClass('nav-tab-active');
			} 
			else {
				$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
			}

			$('.nav-tab-wrapper a').click(function(evt) {
				$('.nav-tab-wrapper a').removeClass('nav-tab-active');
				$(this).addClass('nav-tab-active').blur();
				var clicked_group = $(this).attr('href');
				if (typeof(localStorage) != 'undefined' ) {
					localStorage.setItem("activetab", $(this).attr('href'));
				}
				$('.group').hide();
				$(clicked_group).fadeIn();
				evt.preventDefault();
			});

			$(document).on('click', '.btn-pwa-change-permission', self.changePermission);
		};

		self.changePermission = function(e){
			e.preventDefault();

			var el = $(this), 
				filename = el.data('filename');

			// console.log(filename);

			var dataForm = { 
				filename: filename 
			};

			var dataPost = { 
				action: 'tonjoo_pwa_change_file_or_dir_permission', 
				dataForm: dataForm, 
				nonce: TONJOO_PWA.nonce 
			};

			$.ajax({
				url: TONJOO_PWA.ajaxurl, 
				type: 'POST', 
				data: dataPost, 
				dataType: "json", 
				success: function(response){
					// console.log(response); 

					el.closest('tr').removeClass('is-readable').addClass('is-writeable').find('.current-permission').text('755');
					el.closest('td').text('No Action Required');
				}, 
				complete: function(jqXHR, status){
					// console.log(status);

					// if ( 'success' == status ) {
					// 	var json_res = jqXHR.responseJSON;

					// 	// console.log(json_res);
					// } else {}
				}
			});
		};
	}

	var PWA_SETTINGS;

	$(document).ready(function() {
		if( PWA_SETTINGS == null ) {
			PWA_SETTINGS = new TONJOO_PWA_SETTINGS();
			PWA_SETTINGS.init();
		}
	});

}(jQuery));
