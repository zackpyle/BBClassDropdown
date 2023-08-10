jQuery(document).ready(function($) {
    
    var group_template = '' +
        '<tr class="group">' +
        '   <td valign="top"><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][name]" placeholder="Group Name" value="" />' +
		'       <div class="single-select-wrapper">' +
        '           <input type="checkbox" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][checkbox]" value="1" />' +
        '           <label for="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][checkbox]">Single Select Classes</label>' +
        '       </div>' +
		'	</td>' +
        '   <td>' +
        '       <table class="beaver-builder-class-dropdown-classes">' +
        '           <tbody>' +
        '               <tr>' +
        '                   <td><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][classes][0][id]" placeholder="foo-bar" value="" /></td>' +
        '                   <td><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][classes][0][name]" placeholder="Foo Bar" value="" /></td>' +
        '                   <td><button type="button" class="button beaver-builder-class-dropdown-remove-class"><svg aria-hidden="true" width="15" height="15"><use xlink:href="#trash" /></svg></button></td>' +
        '               </tr>' +
        '           </tbody>' +
        '       </table>' +
        '       <button type="button" class="button beaver-builder-class-dropdown-add-class">+</button>' +
        '   </td>' +
        '</tr>';
  
    var class_template = '' +
        '<tr>' +
        '   <td><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][id]" placeholder="foo-bar" value="" /></td>' +
        '   <td><input type="text" name="beaver_builder_class_dropdown_options[groups][__GROUP_INDEX__][classes][__CLASS_INDEX__][name]" placeholder="Foo Bar" value="" /></td>' +
        '   <td><button type="button" class="button beaver-builder-class-dropdown-remove-class"><svg aria-hidden="true" width="15" height="15"><use xlink:href="#trash" /></svg></button></td>' +
        '</tr>';

	
	function addDeleteGroupButton() {
		// Remove any existing delete group buttons to avoid duplication
		$('.beaver-builder-class-dropdown-remove-group').remove();
		// Add the delete group button to the first row of each group
		$('.beaver-builder-class-dropdown-classes > tbody > tr:first-child').each(function() {
			var deleteGroupButton = '<button type="button" class="button beaver-builder-class-dropdown-remove-group">Delete Group</button>';
			$(this).find('td').last().append(deleteGroupButton);
		});
	}

    function bindRemoveGroupEvent() {
        $(document).off('click', '.beaver-builder-class-dropdown-remove-group');
        $(document).on('click', '.beaver-builder-class-dropdown-remove-group', function() {
            if (window.confirm("Are you sure you want to delete this group?")) {
				$(this).closest('tr.group').remove();
			}
        });
    }
	
	// Add group + dynamically add 'delete group' button
	addDeleteGroupButton();
	$('.beaver-builder-class-dropdown-add-group').on('click', function() {
		var group_index = $('.beaver-builder-class-dropdown-groups > tbody > tr').length;
        var group_html = group_template.replace(/__GROUP_INDEX__/g, group_index);
        $('.beaver-builder-class-dropdown-groups > tbody').append(group_html);
		addDeleteGroupButton();
		bindRemoveGroupEvent(); // Rebind the remove group event
	});
  
    // Delete Group
    bindRemoveGroupEvent();
  
    // Add class
    $(document).on('click', '.beaver-builder-class-dropdown-add-class', function() {
        var $classes_table = $(this).prev('.beaver-builder-class-dropdown-classes').find('tbody');
        var group_index = $(this).closest('tr').index();
        var class_index = $classes_table.find('tr').length;
        var class_html = class_template
            .replace(/__GROUP_INDEX__/g, group_index)
            .replace(/__CLASS_INDEX__/g, class_index);
        $classes_table.append(class_html);
    });
  
    // Remove class
    $(document).on('click', '.beaver-builder-class-dropdown-remove-class', function() {
        $(this).closest('tr').remove();
    });
  });

  