# Changelog

All notable changes to this project are documented here. Version numbers follow semantic versioning (MAJOR.MINOR.PATCH).

## 0.15.0
- Changed Model B selection from weekday checkboxes to number of days selector (1-4 days per week)
- Model A still uses weekday checkboxes for selecting specific days
- Fixed button height inconsistency in step 2 navigation (buttons now have equal heights)
- Fixed JavaScript error with duplicate const model declaration that prevented form from opening
- Fixed small css issues and sped up transitions

## 0.13.0
- Added "Nur kontaktanfrage senden" link in Step 1 to skip pricing configuration and go directly to contact form
- Made weekday selection optional when skipping to contact form
- Default values are used for pricing fields when skipped (shows "Keine Auswahl" in recap)

## 0.12.0
- Added sortable columns to Submitted Forms table (click column headers to sort)
- Refactored table to group submissions by customer email
- Added visual group headers showing customer name, email, phone, and submission count
- Improved table styling with better visual hierarchy for grouped items
- Sort indicators (↑ ↓ ⇅) show current sort column and direction

## 0.11.0
- Renamed "User Configurations" admin page to "Submitted Forms"
- Added status management for form submissions with dropdown selector per row
- Status options: "Neu" (default), "Kontaktiert", "nicht erreicht", "Nicht interessiert", "Vertrag geschlossen", "Gekündigt"
- Added bulk delete functionality with checkboxes and bulk actions dropdown
- Added status column to database table with automatic migration for existing installations
- Status updates and bulk deletions handled via AJAX with proper security checks

## 0.10.1
- Fixed Model B weekday selection: users can now select any of the 5 weekdays, but limited to 4 days total (instead of disabling Friday specifically)
- When 4 days are selected in Model B, remaining unchecked weekdays are disabled until one is unchecked

## 0.10.0
- Added configurable consent checkbox that must be checked before form submission
- Checkbox label supports HTML (e.g., links to terms and conditions)
- Submit button is disabled until consent checkbox is checked (when enabled)
- Added validation to ensure consent checkbox is checked before submission

## 0.9.9
- Added backend option to round prices up to nearest whole number
- Added backend option to switch between 4 and 4.33 weeks multiplier for monthly calculation
- Model B now restricts selection to maximum 4 days (Friday checkbox is disabled when Model B is selected)
- Updated pricing calculation to use configurable multiplier and rounding settings

## 0.9.8
- Modernized admin configuration page UI with improved styling, spacing, and visual hierarchy
- Enhanced form inputs with better focus states and borders
- Improved pricing table layout and readability
- Updated help section with cleaner card-based design

## 0.9.7
- Updated monthly price calculation from 4 weeks to 4.33 weeks for more accurate monthly pricing

## 0.9.6
- Standardized all button styling for consistency (padding, border-radius, font-size)
- Updated step navigation button labels to "Weiter" and "Zurück"

## 0.9.5
- Fixed email sending functionality

## 0.9.4
- Fixed the frontend phone number validation pattern so modern browsers no longer throw an invalid regular expression error.

## 0.9.3
- Added backend option to split the calculator into Step A (pricing) and Step B (user data).
- Updated frontend markup, styles, and scripts to support the new two-step experience with summaries.

## 0.9.2
- Simplified outbound emails to plain text only for universal compatibility.

## 0.9.1
- Improved Gmail compatibility by switching to table-based HTML layout in notifications.

## 0.9.0
- Added inline validation, optional reCAPTCHA, enhanced emails, and Gutenberg block support.

## 0.8.0
- Fixed monthly calculation (×4 weeks) and introduced accordion toggle for the form.

## 0.7.0
- Added phone number and notes fields, Figtree font, and configurable colors.

## 0.6.0
- Enabled configurable subscription model names and variable pricing (1-5 days/week).

## 0.5.0
- Implemented reCAPTCHA v2 spam protection and email notifications.

## 0.4.0
- Added weekday selector (Mon-Fri) and German translations.

## 0.3.0
- Added number-of-days selection (1-30).

## 0.2.0
- Added subscription models A and B with half/full day options.

## 0.1.0
- Initial release with basic pricing configuration.

