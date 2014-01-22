jQuery( document ).ready( function( $ ) {
  setInterval(function() {
    var data = {
      action: 'query_dex_pm_counter',
      security: wp_ajax.ajaxnonce
    };
    $.post( 
      wp_ajax.ajaxurl, 
      data,                   
      function( response ){
        // ERROR HANDLING
        if( !response.success ){
          // No data came back, maybe a security error
          if( !response.data )
            $( '#message_count' ).html( 'AJAX ERROR: no response' );
          else
            $( '#message_count' ).html( response.data.error );
        } else
          $( '#message_count' ).html( response.data );
        }
      ); 
    }, 5000); //5 seconds
  }
);
