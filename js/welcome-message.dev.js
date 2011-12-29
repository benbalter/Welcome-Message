jQuery( document ).ready( function( $ ) {
	
	//Welcome Message
	//If this is the user's first time on the site, make a json call
	//and insert a welcome message above the QA menu
	if ( $.cookie( welcome_message.cookie ) == null ) {

		$( welcome_message.prepend ).prepend( '<div id="' + welcome_message.div + '">' + welcome_message.message + '</div>' );
		$( '#' + welcome_message.div ).fadeIn('slow');
		$.cookie( welcome_message.cookie, '1', { expires: parseInt( welcome_message.expiration ), path: '/', domain: welcome_message.domain } );
		
	}

});