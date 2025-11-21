# Modular Pricing Calculator

WordPress plugin that lets dog daycare businesses provide an interactive calculator so prospects can configure weekly care subscriptions (model, half/full day, days per week) and submit a request that is saved in WordPress and emailed to the business.

## Features
- Configurable pricing tables for two subscription models (half/full day, 1‑5 days per week)
- Inline-validated frontend form with accordion or always-open layout
- Optional two-step flow that separates price selection (Step A) from contact capture (Step B)
- Optional reCAPTCHA v2 spam protection
- Saves submissions to `wp_pricing_configurations` and sends plain-text notification email
- Gutenberg block and shortcode support for easy embedding

## Installation
1. Upload the `modular-pricing-calculator` folder to `wp-content/plugins/`.
2. Activate **Modular Pricing Calculator** in the WordPress admin.
3. Visit **Modular Pricing → Modular Pricing** to configure pricing, colors, email target, and optional reCAPTCHA.

## Usage

### Shortcode
Add the calculator into any post or page:

```
[pricing_calculator]
```

### Gutenberg Block
Insert the block **Modular Pricing Calculator** from the Widgets category to render the same form.

### Admin Pages
- `Modular Pricing → Modular Pricing`: plugin settings (pricing, colors, display mode, notification email, reCAPTCHA keys).
- `Modular Pricing → User Configurations`: latest submissions stored in the custom table.

## Email Notifications
Each valid submission sends a plain-text email containing contact details, care preferences, selected weekdays, monthly price, notes, and timestamp to the configured notification address.

## Database
On activation the plugin creates `wp_pricing_configurations` (table name respects the site prefix) with columns for name, contact info, subscription selection, price, notes, and submission timestamp.

