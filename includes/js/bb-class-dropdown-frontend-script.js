/**
 * BBClassDropdown – Utility class dropdown enhancement for Beaver Builder
 * Adds select dropdowns below class inputs and manages singleton logic
 * Fully supports Beaver Builder Components (dynamic field combinations)
 */

const BBClassDropdown = {

    on: '●',
    off: '○',

    /**
     * Return result that we are displaying the top-most Beaver Builder modal
     * This prevents interactions with nested overlays (e.g., media library)
     */
    isTopModal: function() {
        return ('block' == jQuery('.fl-lightbox-wrap').css('display'));
    },

    /**
     * Injects the dropdown below class inputs if it does not exist yet
     * and populates it with the correct grouped options from global JS config
     */
    injectDropdowns: function() {
        if (!BBClassDropdown.isTopModal()) return;
        if (typeof BBClassOptions === 'undefined' || typeof BBClassOptions.options !== 'object') return;

        jQuery('.fl-builder-settings:visible input[name="class"], .fl-builder-settings:visible input[name$="__class"]').each(function(index) {
            const $input = jQuery(this);
            const $wrapper = $input.closest('.fl-field-control-wrapper');

            // Only inject if not already present
            if ($wrapper.find('select.fl-text-field-add-value').length) return;

            let $select = jQuery('<select class="fl-text-field-add-value"><option value="">Choose a class</option></select>');

            for (const groupKey in BBClassOptions.options.groups) {
                const group = BBClassOptions.options.groups[groupKey];
                const $optgroup = jQuery(`<optgroup label="${BBClassDropdown.convertHTML(group.name)}"></optgroup>`);
                if (group.singleton == 1) {
                    $optgroup.attr('singleton', 1);
                }
                if (Array.isArray(group.classes)) {
                    group.classes.forEach(cls => {
                        if (cls.id && cls.name) {
                            $optgroup.append(`<option value="${cls.id}" data-label="${cls.name}">${BBClassDropdown.off} ${cls.name}</option>`);
                        }
                    });
                }
                $select.append($optgroup);
            }

            $select.insertAfter($input);
        });
    },

    /**
     * Updates singleton dropdown items to reflect which class is currently selected
     * Adds or removes indicator symbols (●/○) for clarity
     */
    updateSingletonOptions: function() {
        if (!BBClassDropdown.isTopModal()) return;

        jQuery('.fl-builder-settings:visible input[name="class"], .fl-builder-settings:visible input[name$="__class"]').each(function(index) {
            const currentClasses = jQuery(this).val().split(' ');
            const dropdown = jQuery(this).closest('td').find('select.fl-text-field-add-value')[0];
            if (!dropdown) return;

            // Patch dropdown from PHP-rendered HTML if needed
            dropdown.querySelectorAll('optgroup').forEach(groupEl => {
                const groupName = groupEl.getAttribute('label');
                const configGroup = Object.values(BBClassOptions.options.groups).find(g => g.name === groupName);
                if (!configGroup) return;

                if (configGroup.singleton == 1) {
                    groupEl.setAttribute('singleton', '1');
                }

                groupEl.querySelectorAll('option').forEach(opt => {
                    if (!opt.dataset.label) {
                        opt.dataset.label = opt.textContent.trim();
                        opt.textContent = `${BBClassDropdown.off} ${opt.dataset.label}`;
                    }
                });
            });

            // Now do the update based on selected classes
            dropdown.querySelectorAll('optgroup[singleton] option').forEach(el => {
                el.text = (currentClasses.includes(el.value) ? BBClassDropdown.on : BBClassDropdown.off) + ' ' + el.dataset.label;
            });
        });
    },

    /**
     * Disables already-selected class values from all visible dropdowns
     * Ensures uniqueness of class selections across multiple component fields
     */
    disableUsedClasses: function() {
        if (!BBClassDropdown.isTopModal()) return;

        jQuery('.fl-builder-settings:visible input[name="class"], .fl-builder-settings:visible input[name$="__class"]').each(function() {
            const currentClasses = jQuery(this).val().split(' ');
            const dropdown = jQuery(this).closest('td').find('select.fl-text-field-add-value');
            dropdown.find('option').prop('disabled', false);
            currentClasses.forEach(classname => {
                dropdown.find(`option[value="${classname}"]`).prop('disabled', true);
            });
        });
        BBClassDropdown.updateSingletonOptions();
    },

    /**
     * Handles the selection of a class from the dropdown and updates the input
     * Supports singleton replacement logic for grouped class families
     */
    handleDropdownSelection: function() {
        const $ = jQuery;
        if (this.selectedIndex == 0) return;

        const dropdown = $(this);
        const textField = dropdown.closest('td').find('input[name="class"], input[name$="__class"]');
        const currentValue = textField.val();
        const addingValue = dropdown.val();
        const currentClasses = textField.val().split(' ');
        const selectedOption = this.options[this.selectedIndex];
        const optGroup = selectedOption.closest('optgroup');
        const optGroupOptions = optGroup.querySelectorAll('option');

        let newValue = '';
        let classes = Array.from(optGroupOptions).map(e => e.value);
        let intersection = currentClasses.filter(v => classes.includes(v));

        if (optGroup.hasAttribute('singleton') && intersection.length) {
            const intersectValue = intersection[0];
            const classIndex = currentClasses.findIndex(e => e == intersectValue);
            currentClasses[classIndex] = addingValue;
            newValue = currentClasses.join(' ');
            textField.val(newValue).trigger('change').trigger('keyup');
        } else {
            if (!currentClasses.includes(addingValue)) {
                newValue = (currentValue.trim() + ' ' + addingValue.trim()).trim();
                textField.val(newValue).trigger('change').trigger('keyup');
            }
        }

        dropdown.val('');
    },

    /**
     * replace some chars to prevent code from breaking in label generation
     */
    convertHTML: function(str) {
        let replacements = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&apos;",
            "<>": "&lt;&gt;"
        };
        return str.replace(/(&|<|>|"|'|<>)/gi, m => replacements[m]);
    }
};

jQuery(document).ready(function($) {
    let timeout = null;

    FLBuilder.addHook('settings-form-init', function() {
        BBClassDropdown.injectDropdowns();
        BBClassDropdown.disableUsedClasses();
    });

    $('body', window.parent.document).off('change', '.fl-text-field-add-value', FLBuilder._textFieldAddValueSelectChange);
    $('body', window.parent.document).on('change', '.fl-text-field-add-value', BBClassDropdown.handleDropdownSelection);

    $('body', window.parent.document).on('change keyup', '.fl-builder-settings:visible input[name="class"], .fl-builder-settings:visible input[name$="__class"]', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            BBClassDropdown.disableUsedClasses();
        }, 250);
    });

    $('body', window.parent.document).on('change', '.fl-builder-settings:visible .fl-text-field-add-value', function() {
        BBClassDropdown.disableUsedClasses();
    });
});