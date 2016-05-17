/**
 * 
 * @author Crystal Barton (atrus1701)
 */

( function( $ ) {
			
	$.fn.CheckboxSearchSelector = function( options ) {

		//
		// 
		//
		options = $.extend( {
			item_class : 'item',
			search_class : 'search'
		}, options);
		
		options.item_class = '.' + options.item_class;
		options.search_class = '.' + options.search_class;
		
		var container = this;
		
		
		//
		// 
		//
		function refresh_item_choices()
		{
			var value = $( container )
				.find( 'input[type="textbox"]:first-child' )
				.attr( 'value' ).trim().toLowerCase();
				
			$( container ).find( '.search-results '+options.item_class ).each(function(){
				if( $( this ).children('input').is(':checked') ) {
					$( this ).show();
					return;
				}
				var search_text = $( this ).children( options.search_class ).text().toLowerCase();
				if( ( "" === value ) || ( -1 === search_text.indexOf(value) ) ) {
					$( this ).hide();
				} else {
					$( this ).show();
				}
			});
		}
		
		
		//
		// 
		//
		function setup_selector( container )
		{	
			// Add search results div around items.
			$( container )
				.addClass('jquery-checkbox-search-selector')
				.wrapInner('<div class="search-results"></div>');
			
			// Add entry textbox.
			$( container )
				.prepend('<div class="entry"><input type="textbox" /></div>');
			
			// When entry gets focus it is diverted to the textbox.
			$( container ).find('div.entry')
				.click(function() {
					$(this).find('input[type="textbox"]').focus();
				});
			
			// Whenever something new is entered, then update the choices.
			$( container ).find('input')
				.keyup(function() {
					refresh_item_choices();
				}); // [container input].keyup
			
			// Refresh items based on search.
			refresh_item_choices();
		}
		
		
		return this.each( function() { setup_selector(this); } );
		
	}
	
})( jQuery )
