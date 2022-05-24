function setSuggest() {
    jQuery('.mp-spills-terms').suggest(
        ajaxurl + "?action=ajax-tag-search&tax=post_tag",
        {
            multiple:true, 
            multipleSep: ","
        }
    );
}

$(document).ready(function() {
    setSuggest();
});

$(document).on('widget-updated widget-added', function() {
    setSuggest(); 
});
