jQuery(document).ready(function($) {
    
	var group_template = '' +
		'<tr class="group">' +
		'   <td class="group-handle" valign="top">' +
		'       <!-- Drag-and-drop handle -->' +
		'       <div class="drag-handle"></div>' +
		'       <!-- Hidden input for ordering -->' +
		'       <input class="group-order" type="hidden" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][order]" value="__GROUP_INDEX__" />' +
		'   </td>' +
		'   <td valign="top" class="group-name-col"><input class="group-name" type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][name]" placeholder="Group Name" value="" />' +
		'   	<div class="group-options">' +
		'   		<button type="button" class="button beaver-builder-class-dropdown-remove-group">' +
		'   			<span class="sr-only">Delete Group</span><svg aria-hidden="true" width="13" height="13"><use xlink:href="#trash" /></svg>' +
		'   		</button>' +
		'   		<!-- Option to have group be only a single class at a time - useful for something like a background color -->' +
		'   		<div class="single-select-wrapper">' +
		'   			<input type="checkbox" name="beaver_builder_class_dropdown_options[groups][singleton]" value="1" />' +
		'   			<label for="beaver_builder_class_dropdown_options[groups][singleton]">Single Class Group</label>' +
		'   		</div>' +
		'   	</div>' +
		'   </td>' +
		'   <td valign="top" class="class-col">' +
		'       <table class="beaver-builder-class-dropdown-classes">' +
		'           <tbody>' +
   		'               __CLASS_TEMPLATE__' +
		'           </tbody>' +
		'       </table>' +
		'       <button type="button" class="button beaver-builder-class-dropdown-add-class"><svg aria-hidden="true" width="12" height="12"><use xlink:href="#plus" /></svg></button>' +
		'   </td>' +
		'</tr>';
  
    var class_template = '' +
        '<tr class="class-row" valign="top">' +
		'   <td class="class-handle" valign="top">' +
		'       <!-- Drag-and-drop handle -->' +
		'       <div class="drag-handle"></div>' +
		'   </td>' +
        '   <td class="class-value-col"><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][id]" placeholder="foo-bar" value="" /></td>' +
        '   <td class="class-label-col"><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][name]" placeholder="Foo Bar" value="" /></td>' +
        '   <td class="class-btn-col"><button type="button" class="button beaver-builder-class-dropdown-remove-class"><svg aria-hidden="true" width="15" height="15"><use xlink:href="#trash" /></svg></button></td>' +
        '</tr>';
	
	// Use class_template inside of group_template while maintaining the necessary dynamic index replacements for both groups and classes
	group_template = group_template.replace('__CLASS_TEMPLATE__', class_template);
	
	// Function to update hidden input values for group order
	function updateHiddenInputGroupOrder() {
        $('.beaver-builder-class-dropdown-groups > tbody > tr.group').each(function(index) {
            $(this).find('.group-order').val(index);
        });
		console.log('Hidden input group order updated.');
    }
	
	// Function to update hidden input values for class order within a group
	function updateHiddenInputClassOrder($classes_table) {
		$classes_table.find('tr.class-row').each(function(index) {
			$(this).find('.class-order').val(index);
		});
		console.log('Hidden input class order updated.');
	}
	
	// Bind group reorder event
	function bindGroupReorderEvent() {
		$('.beaver-builder-class-dropdown-groups > tbody').sortable({
            handle: '.group-handle',
            update: function(event, ui) {
                updateHiddenInputGroupOrder();
            }
        });
	}

	// Bind class reorder event within a group
	function bindClassReorderEvent() {
        $('.beaver-builder-class-dropdown-classes tbody').sortable({
            handle: '.class-handle',
            update: function(event, ui) {
                var groupName = $(ui.item).closest('tr.group').find('.group-name').val();
                var $classes_table = $('.beaver-builder-class-dropdown-classes tbody').filter(function() {
                    return $(this).closest('tr.group').find('.group-name').val() === groupName;
                });
                updateHiddenInputClassOrder($classes_table);
            }
        });
    }
	
	
	// Add classes reordering and delete buttons to a new group
	function addClassesReorderingAndDeleteButtons($groupRow) {
		var $classes_table = $groupRow.find('.beaver-builder-class-dropdown-classes tbody');
		bindClassReorderEvent($classes_table);
		updateHiddenInputClassOrder($classes_table);
	}

	// Bind remove group event
    function bindRemoveGroupEvent() {
        $(document).off('click', '.beaver-builder-class-dropdown-remove-group');
        $(document).on('click', '.beaver-builder-class-dropdown-remove-group', function() {
            if (window.confirm("Are you sure you want to delete this group?")) {
				$(this).closest('tr.group').remove();
				updateHiddenInputGroupOrder();
			}
        });
    }
	
	// Initialize events for group reordering
    bindRemoveGroupEvent();
    bindGroupReorderEvent();
	bindClassReorderEvent();
	
	
	// Add new group + dynamically add 'delete group' button
	$('.beaver-builder-class-dropdown-add-group').on('click', function() {
		var group_index = $('.beaver-builder-class-dropdown-groups > tbody > tr').length;
        var group_html = group_template.replace(/__GROUP_INDEX__/g, group_index);
		var $newGroupRow = $(group_html); // Capture the newly added group row
        $('.beaver-builder-class-dropdown-groups > tbody').append(group_html);
		bindRemoveGroupEvent();
		bindGroupReorderEvent();
        updateHiddenInputGroupOrder();
		addClassesReorderingAndDeleteButtons($newGroupRow);
	});
	
	
	// Add new class within a group
    $(document).on('click', '.beaver-builder-class-dropdown-add-class', function() {
        var $groupRow = $(this).closest('.group'); // Find the parent group row
    	var $classes_table = $groupRow.find('.beaver-builder-class-dropdown-classes tbody'); // Find the specific classes table
        var group_index = $(this).closest('tr').index();
        var class_index = $classes_table.find('tr').length;
        var class_html = class_template
            .replace(/__GROUP_INDEX__/g, group_index)
            .replace(/__CLASS_INDEX__/g, class_index);
        $classes_table.append(class_html);
		bindClassReorderEvent($classes_table);
    	updateHiddenInputClassOrder($classes_table);
    });
  
    // Remove class within a group
    $(document).on('click', '.beaver-builder-class-dropdown-remove-class', function() {
        var $groupRow = $(this).closest('.group');
    	var $classes_table = $groupRow.find('.beaver-builder-class-dropdown-classes tbody');
    	$(this).closest('tr').remove();
    	updateHiddenInputClassOrder($classes_table);
    });
});
