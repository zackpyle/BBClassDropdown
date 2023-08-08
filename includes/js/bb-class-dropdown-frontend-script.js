jQuery(document).ready(function($) {
    
    // Function to disable already used classes in the dropdown
    function disableUsedClasses() {
        // get the current classes in the input field
        var currentClasses = $('.fl-builder-settings:visible input[name=class]').val().split(' ');

        // reset all the disabled attributes
        $('.fl-text-field-add-value option').prop('disabled', false);

        // loop through each class in the input field
        $.each(currentClasses, function(index, classInField) {
            // find the option with that value and set its disabled attribute to true
            $('.fl-text-field-add-value option[value="' + classInField + '"]').prop('disabled', true);
        });
    }

    // listen for changes on the class input field
    $('body', window.parent.document).on( 'change keyup', '.fl-builder-settings:visible input[name=class]', function() {
        console.log('Class input changed');
        disableUsedClasses();
    });
    
    // Listen for changes on the class select field
    $('body', window.parent.document).on( 'change', '.fl-builder-settings:visible .fl-text-field-add-value', function() {
        console.log('Class selected');
        disableUsedClasses();
    });

});
