<p align="center">
  <a href="https://velkymlyn.cz/">
    <img src="image/logo_298.png" alt="Velký mlýn" width="520">
  </a>
</p>

# Velký mlýn WordPress theme

The custom WordPress theme used by [Velký mlýn](https://velkymlyn.cz/), an
independent cultural centre and library in Prague-Libeň.

Project contact: [pk@velkymlyn.cz](mailto:pk@velkymlyn.cz)

The theme began with [Underscores (`_s`)](https://underscores.me/) and now
contains site-specific templates for the homepage, event listings, individual
calendar events, navigation, contact information, partners, and integrations
used by the Velký mlýn website. It is a standalone theme, not a child theme or
a reusable starter theme.

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer
- [The Events Calendar](https://wordpress.org/plugins/the-events-calendar/)
- [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/)

The Events Calendar is required by the event templates and the homepage event
list. Advanced Custom Fields supplies the `barva_stitku` colour assigned to
event tags.

The production website also uses these companion features:

- `mlyn-flexible-slider` for the homepage hero via
  `[mlyn_slider id="homepage-hero"]`
- Social Feed Gallery for `[insta-gallery id="0"]`
- MailPoet for `[mailpoet_form id="1"]`

If an optional shortcode provider is unavailable, its corresponding homepage
section may be empty. The homepage hero is explicitly hidden when the Mlýn
Flexible Slider shortcode is unavailable or has no active slides.

## Installation

1. Copy or clone this repository to `wp-content/themes/velkymlyn`.
2. Install and activate the required plugins.
3. Activate **Velký mlýn** under **Appearance → Themes**.
4. Assign the primary navigation menu and configure the homepage as needed.
5. Ensure the companion shortcodes listed above match the IDs configured on
   the target WordPress site.

This repository contains theme source only. WordPress content, uploads,
plugin data, secrets, and environment configuration are deliberately excluded.

## Development

PHP syntax checks are available through Composer:

```sh
composer install
composer lint:php
```

With WP-CLI installed, regenerate the translation template with:

```sh
wp i18n make-pot . languages/velkymlyn.pot
```

JavaScript linting, RTL stylesheet generation, and creation of an installable
ZIP are available through npm:

```sh
npm install
npm run lint:js
npm run compile:rtl
npm run bundle
```

Generated dependencies and archives are excluded from Git. The checked-in
`style.css` and `style-rtl.css` are runtime assets and must remain in the theme.

## Repository structure

- `front-page.php` — homepage layout and companion shortcodes
- `functions.php` — theme setup, assets, event list, and event filters
- `tribe-events/` and `tribe/` — The Events Calendar template overrides
- `template-parts/` — reusable WordPress content templates
- `inc/` — theme setup helpers and optional Jetpack compatibility
- `image/`, `js/`, and `slick/` — bundled presentation assets
- `readme.txt` — WordPress-style release metadata and changelog

## Publishing notes

`README.md` is the developer-facing GitHub document. `readme.txt` is retained
separately because it follows the conventional WordPress distribution format;
WordPress itself reads the authoritative theme header from `style.css`.

Before publishing a release, update the version consistently in `style.css`,
`style-rtl.css`, `functions.php`, `package.json`, and `readme.txt`, then run the
syntax checks and build the ZIP.

## Credits and licensing

Theme PHP, CSS, and JavaScript are licensed under the GNU General Public
License v2 or later; see [LICENSE](LICENSE).

- Based on [Underscores](https://underscores.me/), © 2012–2020 Automattic,
  Inc., GPL-2.0-or-later
- Includes [normalize.css](https://necolas.github.io/normalize.css/), © Nicolas
  Gallagher and Jonathan Neal, MIT
- Includes [Slick](https://kenwheeler.github.io/slick/), © Ken Wheeler, MIT

Photographs, logos, partner marks, and other organisation-specific media in
`image/` are not granted for reuse by the GPL license applied to the theme
code. Confirm the relevant rights before redistributing those assets.
