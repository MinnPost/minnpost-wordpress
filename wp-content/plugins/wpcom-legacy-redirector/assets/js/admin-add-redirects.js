/**
 * Provides helpful preivew of what redirect URLs will look like.
 */
var fromURL = document.getElementById('from_url');
var redirectURL = document.getElementById('redirect_to');

function redirect_top_label( labelID, inputValue ) {
    labelID.onkeyup = function() {
        var prefix = '';
        var siteURL = WPURLS.siteurl;
        if ( labelID.value.match(/^\d+$/) && labelID === redirectURL) {
            var prefix = '?p=';
        }
        if ( labelID.value.match(/^\http.+/) && labelID === redirectURL) {
            var prefix, siteURL = '';
        }
        document.getElementById(inputValue).innerHTML = siteURL + prefix + labelID.value;
    }
}
redirect_top_label( fromURL, 'from_url_value' );
redirect_top_label( redirectURL, 'redirect_to_value' );
