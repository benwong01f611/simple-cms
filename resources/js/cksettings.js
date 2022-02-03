
ClassicEditor
	.create( document.querySelector( '.editor' ), {
		
	toolbar: {
		items: [
			'heading',
			'|',
			'bold',
			'italic',
			'underline',
			'strikethrough',
			'subscript',
			'superscript',
			'bulletedList',
			'numberedList',
			'|',
			'findAndReplace',
			'outdent',
			'indent',
			'|',
			'ckfinder',
			'imageUpload',
			'blockQuote',
			'insertTable',
			'mediaEmbed',
			'undo',
			'redo',
			'sourceEditing'
		],
		plugins: [CKFinder],
		ckfinder: {
			uploadUrl: '/resources/ckfinder3/core/connector/php/connector.php?command=QuickUpload&type=Images&responseType=json',
		}
	},
	language: 'en',
	image: {
		toolbar: [
			'imageTextAlternative',
			'imageStyle:inline',
			'imageStyle:block',
			'imageStyle:side'
		]
	},
	allowedContent: true,
	table: {
		contentToolbar: [
			'tableColumn',
			'tableRow',
			'mergeTableCells'
		]
	},
	licenseKey: '',
	
	} )
	.then( editor => {
		window.editor = editor;
	} )
	.catch( error => {
		console.error( 'Oops, something went wrong!' );
		console.error( 'Please, report the following error on https://github.com/ckeditor/ckeditor5/issues with the build id and the error stack trace:' );
		console.warn( 'Build id: hylou3cf57wg-runnrbtfktew' );
		console.error( error );
	} );