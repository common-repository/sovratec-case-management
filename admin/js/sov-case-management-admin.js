(function ( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$( document ).ready(
		function () {

			tinymce.init(
				{
					selector: '#scm_case_description',
					width: 580,
					height: 300,
					plugins: [
					'code fullscreen',
					'table emoticons template paste help'
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify',
					menubar: 'file edit view',
				}
			);
			tinymce.init(
				{
					selector: '#scm_case_description_admin',
					width: 950,
					height: 300,
					plugins: [
					'code fullscreen',
					'table emoticons template paste help',
					'noneditable',
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify',
					menubar: 'file edit view',
				}
			);
			tinymce.init(
				{
					selector: '#caseManagementNewCaseCreated',
					width: 950,
					height: 300,
					plugins: [
					'advlist autolink link image lists charmap print preview hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
					'table emoticons template paste help'
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
					'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
					'forecolor backcolor emoticons | help',
					menu: {
						favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons'}
					},
					menubar: 'favs file edit view insert format tools table help',
				}
			);
			tinymce.init(
				{
					selector: '#caseManagementNewCasePending',
					width: 950,
					height: 300,
					plugins: [
					'advlist autolink link image lists charmap print preview hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
					'table emoticons template paste help'
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
					'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
					'forecolor backcolor emoticons | help',
					menu: {
						favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons'}
					},
					menubar: 'favs file edit view insert format tools table help',
				}
			);
			tinymce.init(
				{
					selector: '#caseManagementNewCasePendingAssignee',
					width: 950,
					height: 300,
					plugins: [
					'advlist autolink link image lists charmap print preview hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
					'table emoticons template paste help'
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
					'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
					'forecolor backcolor emoticons | help',
					menu: {
						favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons'}
					},
					menubar: 'favs file edit view insert format tools table help',
				}
			);
			tinymce.init(
				{
					selector: '#caseManagementNewCaseComplete',
					width: 950,
					height: 300,
					plugins: [
					'advlist autolink link image lists charmap print preview hr anchor pagebreak',
					'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
					'table emoticons template paste help'
					],
					toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
					'bullist numlist outdent indent | link image | print preview media fullscreen | ' +
					'forecolor backcolor emoticons | help',
					menu: {
						favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | emoticons'}
					},
					menubar: 'favs file edit view insert format tools table help',
				}
			);
			window.setTimeout(
				function () {
					$( ".alert, .alert-success" ).fadeTo( 1000, 0 ).slideUp(
						1000,
						function () {
							$( this ).remove();
						}
					);
				},
				5000
			);
		}
	);
})( jQuery );
