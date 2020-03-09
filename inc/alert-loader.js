var alertHtml = '';

var ts = Math.round((new Date()).getTime() / 1000);
ts = (ts-(ts%1000))/1000;

$.ajax({
    url: ( data_object.site_url + '/wp-content/uploads/pw-front-page-alert/data.json?ts=' + ts ),
    dataType: 'json',
    cache: true,
})
.done(function( data ) {
	if ( data.alert_options_status == 'enabled' ) {

		// Build alert content
		alertHtml += '<div id="pw-front-alert">';
		if ( data.alert_content_headline === '' ) {
			// Nothing to do
		} else {
			alertHtml += '<h4>' + data.alert_content_headline + '</h4>';
		}
		alertHtml += data.alert_content_content;
		if ( data.alert_content_link_label === '' ) {
			// Nothing to do
		} else {
			alertHtml += '<a href="' + data.alert_content_link_url  + '" class="alert-link">' + data.alert_content_link_label + '</h4>';
		}
		alertHtml += '</div>';

		// Add alert
		$( "body" ).addClass( 'pw-front-page-alert-visible' );
		if ( data.alert_options_target === '' ) {
			$( "body" ).prepend( $( alertHtml ) );
		} else {
			$( data.alert_options_target ).prepend( $( alertHtml ) );
		}
	}
})