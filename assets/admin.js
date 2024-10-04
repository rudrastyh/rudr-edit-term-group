jQuery( function( $ ) {
	$( '.editinline' ).on( 'click', function() {
		const termId = $( this ).parents( 'tr' ).attr( 'id' );
		const termGroup  = parseInt( $( 'td.term_group', '#' + termId ).text() );
		$( ':input[name="term_group"]', '.inline-edit-row' ).val( termGroup );
	} );
} );
