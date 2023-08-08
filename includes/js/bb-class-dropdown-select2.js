jQuery(document).ready(function($) {  
    
    // Turn <select> into select 2
    FLBuilder.addHook('settings-form-init', function(){
        $('.fl-builder-settings:visible .fl-text-field-add-value').select2({
            placeholder: "- Choose from predefined classes -",
            width: '50%'
        });
    });

});
