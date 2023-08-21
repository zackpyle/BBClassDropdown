# Beaver Builder Class Dropdown
Adds user defined CSS classes to dropdown below the Beaver Builder class input in the Advanced tab

The settings for this plugin are located inside of Beaver Builder's settings, under the new Predefined Classes tab

![admin-panel](https://github.com/zackpyle/BBClassDropdown/assets/19413506/fa298823-8066-4215-ae22-f79b9aaac75b)


The groups and classes you define in the settings panel will then be displayed inside the builder in a dropdown below the class input in the Advanced tab

The little = icon next to the groups and classes let you drag/drop to reorder your groups and classes for your convenience. 

Below the predefined classes in the admin panel, there is a section to Import/Export Settings. It is useful when you have a default list of classes you like to use on every site (or as a starting point) - so you can create them once and import them into sites as you need.

The last and what I think is the **best** feature is you also have the ability to select if a group is a single class group - meaning, you only select one out of the group at a time. If you check this for the group, then in the dropdown, it will give you radio button looking symbols to let you know which class has been added aready. If you select another class, it will swap it for you. This is useful for things like background colors where you wouldn't have more than one of those classes at a time.

![frontend-dropdown](https://github.com/zackpyle/BBClassDropdown/assets/19413506/82d05c71-f675-4480-be2d-64e42924a1e4)


## Changelog

*v1.0.2 - 8/21/23*
- fix error when opening other settings panels
- moved all functions to BBClassDropdown

*v1.0.1 - 8/20/23*
- WP Updater connected so you can get the latest version pushed from Github straight to your WP install

*v0.7.0-beta - 8/19/23*
- Previously you could only clear existing classes/settings manually via a url query param. Now there is a new Reset button in the settings section at the bottom of the page
- After reset (and on initial activation), a new blank group is added so you don't have to click Add Group to get started

*v0.6.1-beta - 8/19/23*
- Fixed - v0.5.2 patch forgot to include rows and columns in the 'secondary modal' fix

*v0.6.0-beta - 8/19/23*
- New Import/Export Settings feature

*v0.5.2-beta - 8/18/23*
- Opening a secondary BB modal (like Post Module -> Custom Layout) would throw an error as the second modal assumed it was a module settings modal. Added more strict logic to look for the module settings modal

*v0.5.1-beta - 8/18/23*
- JS refactoring by badabingbreda

*v0.5.0-beta - 8/12/23*
- Drag and Drop reorder functionality for both Groups and Classes inside of Groups
- New table layout / styles
- Made the code more DRY
- First class or group can now be deleted if not only-child
- Don't need to confirm to delete an empty group
- Bug fixes

*v0.4.0-beta - 8/10/23*
- Feature - On groups with single selection substitute previous selected classname from optgroup with selection
- Feature - Fix for insertion of classes that are prefix of other classes. eg. if `uk-flex-center` already exists, you couln't add `uk-flex` later with callback handled by BB in (current) version 2.7.1.1
- Some code cleanup
- Renamed singleton checkbox from `checkbox` to `singleton` in all code.
- Select2 is enabled by default

*v0.3.3.0-beta - 8/7/23*
- Bug - SVG that holds symbols is taking up space on other tabs
- Bug - Typo when linking to Select2

*v0.3.2-beta - 8/7/23*
- Get rid of Remove Group on first group since you will never delete the first group

*v0.3.1-beta - 8/7/23*
- Bug fixes with disabled options
- Move Delete Group to same column as Delete Class

*v0.3.0-beta - 8/7/23*
- Gray out (disable) already used classes in the dropdown
- Add Select2 option

*v0.2.0-beta - 8/7/23*
- Settings page moved to Beaver Builder settings tab

*v0.1.1-beta - 8/6/23*
- Settings page saves to WP options

*v0.1.0-beta - 8/6/23*
- Initial commit
