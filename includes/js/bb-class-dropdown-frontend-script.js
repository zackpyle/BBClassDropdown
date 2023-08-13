/**
 * adding this outside the document ready, 
 * otherwise we would have a harder time unhook/unbind our custom 
 * callback from
 *
 **/ 

const BBClassDropdown = {

	on  : '●',		// symbol for the singleton ON status
	off : '○',		// symbol for the singleton OFF status

    addSingletonAttributes : function () {
		// return early if there are no groups
		if ( typeof BBClassOptions.options.groups !== 'object' ) return;
		const classDropdown = parent.window.document.querySelector( '[data-target="class"]' );
		for(const group in BBClassOptions.options.groups) {
			let groupData = BBClassOptions.options.groups[group];
			let optGroupElement = classDropdown.querySelector( `optgroup[label="${groupData.name}"]` );
			optGroupElement.setAttribute( 'groupname' , groupData.name );
            		// check if typeof because if it isn't it doesn't exist!
			if ( typeof groupData.singleton == 'string' ) {
				optGroupElement.setAttribute( 'singleton' , 1 );
				optGroupElement.label = `${groupData.name}`;
                // prepend symbol to each option
				/* add data-label to each option so we can juggle with the text */
			}
		}
		// add data-label top all options so we can use it should we need it
		classDropdown.querySelectorAll('option').forEach(option => {
			option.dataset.label = option.text;
		});
		// add the singleton symbols to start this off
		BBClassDropdown.updateSingletonOptions();
	},

	updateSingletonOptions : function() {
		
		// get current classes
		const currentClasses = jQuery('.fl-builder-settings:visible input[name=class]').val().split(' ');
		const classDropdown = parent.window.document.querySelector( '[data-target="class"]' );
		// loop over the opgroup that are singleton and add 'label' prefix;
		classDropdown.querySelectorAll( 'optgroup[singleton] option' ).forEach(el => {
			// use the data-label with the on/off symbol to mark it as selected
			el.text = 	(
							currentClasses.includes(el.value) 
								? BBClassDropdown.on 
								: BBClassDropdown.off 
						) + 
						' ' + 
						el.dataset.label; 
		});

		console.log('updating options');
		// Update Select2 if enabled
		if (classDropdown.classList.contains('select2-hidden-accessible')) {
			// switch to jQuery to re-init the select2
			jQuery(classDropdown).select2( 'destroy' );
			jQuery(classDropdown).select2();
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

		// get the selected option
		const selectedOption = this.options[ this.selectedIndex ];
		// get the closest optgroup
		const optGroup = selectedOption.closest( 'optgroup' );
		// get all optgroup options
		const optGroupOptions = optGroup.querySelectorAll( 'option' );
		
		// if optgroup is not singleton, add class to BB class input as normal
		if (optGroup && optGroup.getAttribute('singleton') !== '1') {
			// Original behavior for non-singleton optgroups
			newValue = ( currentValue.trim() + ' ' + addingValue.trim() ).trim();
			//textField.val( newValue ).trigger( 'change' ).trigger( 'keyup' );

		}

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
		
		dropdown.val(''); // Resetting the selector
		console.log( 'method handleDropdownSelection ran' );
	},
	
};

jQuery(document).ready(function($) {

	let timeout = null;

    FLBuilder.addHook('settings-form-init', BBClassDropdown.addSingletonAttributes );
    
    // Function to disable already used classes in the dropdown
    function disableUsedClasses() {
		console.log('method disableUsedClasses started')

		// get the current classes in the input field
        var currentClasses = $('.fl-builder-settings:visible input[name=class]').val().split(' ');

        // reset all the disabled attributes
        $('.fl-text-field-add-value option').prop('disabled', false);
		
        // loop through each class in the input field
        $.each(currentClasses, function(index, classname) {
			// find the option with that value and set its disabled attribute to true
            $('.fl-text-field-add-value option[value="' + classname + '"]').prop('disabled', true);
        });
		console.log('disabled existing classes')
		
		// now update the singleton options
		BBClassDropdown.updateSingletonOptions();
		console.log('method disableUsedClasses ran')
    }

	// remove the default FLBuilder event handler that clears the value after it's done
	$('body', window.parent.document).off( 'change', '.fl-text-field-add-value', FLBuilder._textFieldAddValueSelectChange);
	
	// add our own callback to the dropdown
	$('body', window.parent.document).on( 'change', '.fl-text-field-add-value', BBClassDropdown.handleDropdownSelection );

	// listen for changes on the text-input class field
	$('body', window.parent.document).on( 'change keyup', '.fl-builder-settings:visible input[name=class]', function() {
		// use debounce to prevent too many changes when using the keyboard
		clearTimeout( timeout );
		timeout = setTimeout( function() {disableUsedClasses();} , 250 );
	});

	// listen for changes on the class select field
	$('body', window.parent.document).on( 'change', '.fl-builder-settings:visible .fl-text-field-add-value', function() {
	    disableUsedClasses();
	});
	
});
