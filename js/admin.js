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

	TONJOO_PWA_ADMIN_SCRIPTS = function() {
		var self = this;

		self.init = function() {
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

	var TONJOO_PWA_ADMIN_JS;

	$(document).ready(function() {
		if( TONJOO_PWA_ADMIN_JS == null ) {
			TONJOO_PWA_ADMIN_JS = new TONJOO_PWA_ADMIN_SCRIPTS();
			TONJOO_PWA_ADMIN_JS.init();
		}
	});

}(jQuery));
