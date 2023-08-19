jQuery(document).ready(function($) {
    
	const
		formFieldname = 'beaver_builder_class_dropdown_options',

		/* template for adding a class to a group */
		class_template = `` +
			'<tr class="class-row" valign="top">' +
			'	<td class="class-handle" valign="top">' +
			'           <!-- Drag-and-drop handle -->' +
			'           <div class="drag-handle"></div>' +
			`			<input class="class-order" type="hidden" data-name="order" />` +
			'	</td>' +
			'   <td class="class-value-col"><input type="text" data-name="id" placeholder="foo-bar" value="" /></td>' +
			'   <td class="class-label-col"><input type="text" data-name="name" placeholder="Foo Bar" value="" /></td>' +
			'   <td class="class-btn-col"><button type="button" class="button beaver-builder-class-dropdown-remove-class"><svg aria-hidden="true" width="15" height="15"><use xlink:href="#trash" /></svg></button></td>' +
			'</tr>',
		group_template = '' +
			`<tr class="group">` +
			`   <td class="group-handle" valign="top">`+ 
			`       <!-- Drag-and-drop handle -->` +
			`       <div class="drag-handle"></div>` +
			`       <!-- Hidden input for ordering -->` +
			`       <input class="group-order" type="hidden" data-name="order" value="__GROUP_INDEX__" />` +
			`	</td>` +
			`   <td valign="top" class="group-name-col">` + 
			`		<input class="group-name" type="text" data-name="name" placeholder="Group Name" value="" />` +
			`   	<div class="group-options">` +
			`   		<button type="button" class="button beaver-builder-class-dropdown-remove-group">` +
			`   			<span class="sr-only">Delete Group</span><svg aria-hidden="true" width="13" height="13"><use xlink:href="#trash" /></svg>` +
			`   		</button>` +
			`   		<!-- Option to have group be only a single class at a time - useful for something like a background color -->` +
			`   		<div class="single-select-wrapper">` +
			`   			<label><input type="checkbox" data-name="singleton" value="1" /> Single Class Group</label>` +
			`   		</div>` +
			`   	</div>` +
			`   </td>` +
			`   <td valign="top" class="class-col">` +
			`       <table class="beaver-builder-class-dropdown-classes">` +
			`           <tbody>${class_template}</tbody>` +
			`       </table>` +
			`       <button type="button" class="button beaver-builder-class-dropdown-add-class"><svg aria-hidden="true" width="12" height="12"><use xlink:href="#plus" /></svg></button>` +
			`   </td>` +
			`</tr>`;
	

	
	// Initialize events for group reordering
	bindGroupReorderEvent();
	bindClassReorderEvent();
	
	// Add new group + dynamically add 'delete group' button
	$(document).on( 'click' , '.beaver-builder-class-dropdown-add-group'		, handleAddGroup );

	$(document).on( 'click' , '.beaver-builder-class-dropdown-remove-group' 	, handleRemoveGroup );
	// Add new class within a group
	$(document).on( 'click', '.beaver-builder-class-dropdown-add-class'			, handleAddClass );
    // Remove class within a group
    $(document).on( 'click', '.beaver-builder-class-dropdown-remove-class'		, handleRemoveClass );

	$(document).on( 'submit' , '#fl-class-dropdown-form form' , handleFormSubmit );

	$(document).on( 'click' , '#export-classes' , handleExportClasses );
	
	// Bind group reorder event
	function bindGroupReorderEvent() {
		$('.beaver-builder-class-dropdown-groups > tbody').sortable({
			handle: '.group-handle',
		});
	}
	
	// Bind class reorder event within a group
	function bindClassReorderEvent() {
		$('.beaver-builder-class-dropdown-classes tbody').sortable({
			handle: '.class-handle',
		});
	}

	/**
	 * Add a group
	 */
	function handleAddGroup() {
		$('.beaver-builder-class-dropdown-groups > tbody').append(group_template);
	}

	/**
	 * Remove a group, give a warning if there are actual classes inside
	 */
	function handleRemoveGroup() {
		var $groupRow = $(this).closest('.group');
		var $groupNameInput = $groupRow.find('.group-name');
		var groupHasName = $groupNameInput.val();

		var groupHasClasses = false;
		$groupRow.find('.beaver-builder-class-dropdown-classes tbody tr').each(function() {
			var $classInputs = $(this).find('.class-value-col input, .class-label-col input');
			if ($classInputs.filter(function() { return $(this).val(); }).length > 0) {
				groupHasClasses = true;
				return false; // Exit the loop early since we found a non-empty class
			}	
		});	

		if ((!groupHasName && !groupHasClasses) || window.confirm("Are you sure you want to delete this group?")) {
			$groupRow.remove();
		}	
	}	


	/**
	 * Add a class item
	 */
	function handleAddClass() {
        var $groupRow = $(this).closest('.group'); // Find the parent group row
    	var $classes_table = $groupRow.find('.beaver-builder-class-dropdown-classes tbody'); // Find the specific classes table
        $classes_table.append(class_template);
    }

	/**
	 * remove a class item
	 */
	function handleRemoveClass() {
    	$(this).closest('tr').remove();
    }

	/**
	 * handle the names of the items prior to posting the form
	 * 
	 * @param {*} e 
	 * @returns 
	 */
	function handleFormSubmit(e) {

		// when we get an early return because a field is missing, do NOT submit the form
		if (checkEarlyReturn()) return false;


		const groups = document.querySelectorAll( '.beaver-builder-class-dropdown-groups .group' );
		// loop over the groups and assign the index to each
		groups.forEach( (el,group_index) => {
			// group name
			var groupname = el.querySelector( '.group-name' );
			var grouporder = el.querySelector( '.group-order' );
			groupname.name = `${formFieldname}[groups][__GROUP_INDEX__][${groupname.dataset.name}]`.replace(/__GROUP_INDEX__/g, group_index);
			grouporder.name = `${formFieldname}[groups][__GROUP_INDEX__][${grouporder.dataset.name}]`.replace(/__GROUP_INDEX__/g, group_index);
			// single select / singleton
			// note that we ignore the label here, not really needed
			var checkbox = el.querySelector( '.single-select-wrapper' ).querySelector( 'input[type=checkbox]' );
			checkbox.name = `${formFieldname}[groups][__GROUP_INDEX__][${checkbox.dataset.name}]`.replace(/__GROUP_INDEX__/g, group_index);

			// now get the classes and loop over those them

			var classes = el.querySelectorAll( '.class-row' );
			classes.forEach( ( el, class_index ) => {
				var order = el.querySelector( '.class-order' );
				var value = el.querySelector( '.class-value-col input' );
				var label = el.querySelector( '.class-label-col input' );
				order.name = `${formFieldname}[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][${order.dataset.name}]`.replace(/__GROUP_INDEX__/g, group_index).replace(/__CLASS_INDEX__/g, class_index);
				value.name = `${formFieldname}[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][${value.dataset.name}]`.replace(/__GROUP_INDEX__/g, group_index).replace(/__CLASS_INDEX__/g, class_index);
				label.name = `${formFieldname}[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][${label.dataset.name}]`.replace(/__GROUP_INDEX__/g, group_index).replace(/__CLASS_INDEX__/g, class_index);

			});
		});
		return true;
	}

	function checkEarlyReturn() {

		let groupsNames = document.querySelectorAll( '.beaver-builder-class-dropdown-groups .group-name' ),
			classRows = document.querySelectorAll( '.beaver-builder-class-dropdown-groups .class-row' ),
			earlyReturn = false;

		groupsNames.forEach( el => {
			if ( earlyReturn ) return;
			if ( !el.value ) {
				alert( 'One or more groupnames has no title. Please correct before submitting.' );
				earlyReturn = true;
			}
		});
		
		classRows.forEach( ( el ) => {
			if ( earlyReturn ) return;
			var value = el.querySelector( '.class-value-col input' );
			var label = el.querySelector( '.class-label-col input' );

			if ( !value.value || !label.value ) {
				alert( 'One or more of the added classes have an empty classname and/or label. Please correct before submitting.' );
				earlyReturn = true;
			}

		});

		// if any error return without submitting
		return earlyReturn;
	}

	/**
	 * Export the groups and classes to a json file
	 * 
	 * @param {} e 
	 * @returns 
	 */
	function handleExportClasses(e) {
		e.preventDefault();

		const link = document.createElement("a");
		const textarea = document.createElement( "textarea" );

		let collection = {};
		const groups = document.querySelectorAll( '.beaver-builder-class-dropdown-groups .group' );
		// loop over the groups and assign the index to each
		groups.forEach( (el,group_index) => {

			var collected_classes = [];
			const classes = el.querySelectorAll( '.class-row' );
			classes.forEach( ( el, class_index ) => {
				var order = el.querySelector( '.class-order' );
				var value = el.querySelector( '.class-value-col input' );
				var label = el.querySelector( '.class-label-col input' );
				collected_classes = [...collected_classes , ...[{ id: value.value, name: label.value, order: order.value}] ];
			});

			var groupname = el.querySelector( '.group-name' );
			var grouporder = el.querySelector( '.group-order' );
			var singleton = el.querySelector( '.single-select-wrapper input[type=checkbox]' );


			collection = { ...collection , ...{ [ groupname.value ] : { name: groupname.value , classes : collected_classes , singleton : (singleton.checked ? '1' : '0') } } };
		});


		textarea.value = JSON.stringify({ groups : collection } );
		const content = textarea.value;
		const file = new Blob([content], { type: 'text/plain' });
		link.href = URL.createObjectURL(file);
		link.download = "class-dropdown-" + date_format(new Date()) + ".json";
		link.click();
		URL.revokeObjectURL(link.href);	
		return;	
	}

	function date_format( date ) {
		return `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}-${date.getHours()}-${date.getMinutes()}-${date.getSeconds()}`;
	}

	
});
