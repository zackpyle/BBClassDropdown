/**
 * adding this outside the document ready, 
 * otherwise we would have a harder time unhook/unbind our custom 
 * callback from
 *
 **/ 

const BBClassDropdown = {

    addSingletonAttributes : function () {
        // return early if there are no groups
        if ( typeof BBClassOptions.options.groups !== 'object' ) return;
        const classDropdown = parent.window.document.querySelector( '[data-target="class"]' );
        for(const group in BBClassOptions.options.groups) {
            let groupData = BBClassOptions.options.groups[group];
            classDropdown.querySelector( `optgroup[label="${groupData.name}"]` ).setAttribute( 'groupname' , groupData.name );
            // check if typeof because if it isn't it doesn't exist!
            if ( typeof groupData.singleton == 'string' ) {
                classDropdown.querySelector( `optgroup[label="${groupData.name}"]` ).setAttribute( 'singleton' , 1 );
                // now change the label so we can see we can only click once!
                classDropdown.querySelector( `optgroup[label="${groupData.name}"]` ).label = `${groupData.name} (*)`;
            }
        }
    },

    handleDropdownSelection: function () {
        // define $ as jQuery since we're using it
        const $ = jQuery;
        // return if selected index 0 (placeholder)
        if ( this.selectedIndex == 0 ) return;

        var dropdown     = $( this ),
            textField    = $( 'input[name="' + dropdown.data( 'target' ) + '"]', window.parent.document ),
            currentValue = textField.val(),
            addingValue  = dropdown.val(),
            newValue     = '',
            currentClasses = $('.fl-builder-settings:visible input[name=class]').val().split(' ');


        // get the selectedoption
        const selectedOption = this.options[ this.selectedIndex ];
        // get the closest optgroup
        const optGroup = selectedOption.closest( 'optgroup' );
        // get all optgroup options
        const optGroupOptions = optGroup.querySelectorAll( 'option' );
        
        
        let classes = [];
        // build an array of classes for this optgroup
        optGroupOptions.forEach( function(e) {
            classes = [ ...classes , e.value ];
        })

        // get what currentClasses currently intersect with our optgroup classes
        // if it has results, it means we need to replace the occurance
        // if it doesn't, we can simply add it
        let intersection = currentClasses.filter( v => classes.includes(v));

        /*
         * figure out what to do:
         *      append: 1. classname is in group with 'checkbox' but none of the others is in the array
         *              2. classname is in group that doesn't have checkbox
         * 
         *      replace: 1. classname is in group with 'checkbox', there's also a 
         *                      classname already in there that is in same group
         * 
         *      do nothing: when classname is not in group with checkbox and already in array
         */

        if ( optGroup.attributes?.singleton && intersection.length ) {

            // get the value of the intersection (it should be just one)
            intersectValue = intersection[0];
            // figure out which element we need to replace
            classIndex = currentClasses.findIndex((e)=> e == intersectValue);
            // substitute the intersection classname with our addingValue
            currentClasses[ classIndex ] = addingValue;
            // reconstruct our classes string
            newValue = currentClasses.join( ' ' );

            textField
                .val( newValue )
                .trigger( 'change' )
                .trigger( 'keyup' );

        } else {

            // Adding selected value to target text field only once
            
            // compare our currentClasses to our clicked class, not just the indexof;
            // this leads to errors when there's already a classname called 'uk-flex-center'
            // and you're trying to add in a classname called 'uk-flex'. indexof 'uk-flex' will have
            // a hit and you wouldn't be able to add it
            if ( !currentClasses.filter( v => [ addingValue ].includes(v)).length ) {
    
                newValue = ( currentValue.trim() + ' ' + addingValue.trim() ).trim();
    
                textField
                    .val( newValue )
                    .trigger( 'change' )
                    .trigger( 'keyup' );
            }

        }

        // Resetting the selector
        dropdown.val( '' );

    },

};

jQuery(document).ready(function($) {

    FLBuilder.addHook('settings-form-init', BBClassDropdown.addSingletonAttributes );
    
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
        disableUsedClasses();
    });
    
    // Listen for changes on the class select field
    $('body', window.parent.document).on( 'change', '.fl-builder-settings:visible .fl-text-field-add-value', function() {
        disableUsedClasses();
    });

    // remove the default FLBuilder eventhandler that clears the value after it's done
    $('body', window.parent.document).off( 'change', '.fl-text-field-add-value', FLBuilder._textFieldAddValueSelectChange);

    // add our own callback
    $( 'body', window.parent.document).on( 'change', '.fl-text-field-add-value', BBClassDropdown.handleDropdownSelection );

});
