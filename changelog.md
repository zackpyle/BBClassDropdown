*1.6.1 - 8/19/23*
- Fixed - v1.5.2 patch forgot to include rows and columns in the secondary modal fix

*1.6 - 8/19/23*
- New Import/Export Settings feature

*1.5.2 - 8/18/23*
- Opening a secondary BB modal (like Post Module -> Custom Layout) would throw an error as the second modal assumed it was a module settings modal. Added more strict logic to look for the module settings modal

*1.5.1 - 8/18/23*
- JS refactoring by badabingbreda

*1.5 - 8/12/23*
- Drag and Drop reorder functionality for both Groups and Classes inside of Groups
- New table layout / styles
- Made the code more DRY
- First class or group can now be deleted if not only-child
- Don't need to confirm to delete an empty group
- Bug fixes

*1.4.0 - 8/10/23*
- Feature - On groups with single selection substitute previous selected classname from optgroup with selection
- Feature - Fix for insertion of classes that are prefix of other classes. eg. if `uk-flex-center` already exists, you couln't add `uk-flex` later with callback handled by BB in (current) version 2.7.1.1
- Some code cleanup
- Renamed singleton checkbox from `checkbox` to `singleton` in all code.
- Select2 is enabled by default

*1.3.3 - 8/7/23*
- Bug - SVG that holds symbols is taking up space on other tabs
- Bug - Typo when linking to Select2

*1.3.2 - 8/7/23*
- Get rid of Remove Group on first group since you will never delete the first group

*1.3.1 - 8/7/23*
- Bug fixes with disabled options
- Move Delete Group to same column as Delete Class

*1.3 - 8/7/23*
- Gray out (disable) already used classes in the dropdown
- Add Select2 option

*1.2 - 8/7/23*
- Settings page moved to Beaver Builder settings tab

*1.1 - 8/6/23*
- Settings page saves to WP options

*1.0 - 8/6/23*
- Initial commit
