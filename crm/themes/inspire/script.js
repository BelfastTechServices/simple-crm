
// Create object containing GET variables
var httpGet = {};
(function () {
    var q = window.location.search.substring(1);
    var assignments = q.split('&');
    var parts;
    var i;
    for (i = 0; i < assignments.length; i++) {
        parts = assignments[i].split('=');
        httpGet[parts[0]] = parts[1];
    }
})();

// Add datepicker to necessary fields
$(document).ready(function () {
    
    // Enable date picker
    $('input.date').datepicker({"dateFormat" : "yy-mm-dd"});
    
    // Set up tabbing
    showTab();
    $('ul.page-nav li a').click(function () {
        showTab($(this).attr('href'));
        return false;
    });
    
    // Set up autocomplete forms
    initAutocomplete();
    
    // Setup input clear
    initInputClear();
    
    // Enable focusing
    $('.focus').focus();
});

var showTab = function (hash) {
    $('fieldset.tab').hide();
    $('ul.page-nav li a').removeClass('active');
    if (hash == null) {
        hash = window.location.hash;
    }
    if (hash != '') {
        // Display tab specified in hash
        $('fieldset' + hash).show();
        $('ul.page-nav li a[href="' + hash + '"]').addClass('active');
    } else if (httpGet.hasOwnProperty('tab')) {
        // Display tab specified in query string
        $('fieldset#tab-' + httpGet.tab).show();
        $('ul.page-nav li a[href="#tab-' + httpGet.tab + '"]').addClass('active');
    } else {
        // Display view tab
        $('fieldset#tab-view').show();
        $('ul.page-nav li a[href="#tab-view"]').addClass('active');
    }
};

// Add autocomplete functionality to input fields
var initAutocomplete = function () {
    $('input.autocomplete').each(function () {
        var command = $(this).parent().children('span.autocomplete').html();
        $(this).autocomplete({
            'source': 'autocomplete.php?command=' + command
            , 'focus': function (event, ui) {
                $(this).val(ui.item.label);
                return false;
            }
            , 'select': function (event, ui) {
                $(this).parent().children('input.autocomplete-value').val(ui.item.value);
                $(this).val(ui.item.label);
                return false;
            }
        });
    });
};

var initInputClear = function() {
    $('.defaultClear').focus(function () {
    if ($(this).val() == $(this).attr("title")) {
        $(this).val("");
    }}).blur(
    function () {
        if ($(this).val() == ""){
            $(this).val($(this).attr("title"));
        }
    });
};