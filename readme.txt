=== Velký mlýn ===

Tags: custom-background, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready
Requires at least: 6.7
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.5.0
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

The site-specific WordPress theme for the Velký mlýn cultural centre and library in Prague-Libeň.

Website: https://velkymlyn.cz/
Project contact: pk@velkymlyn.cz

== Description ==

Velký mlýn is a custom, standalone theme based on Underscores. It provides the
public website layout, a curated block-editable homepage, event listings, event-detail
templates, navigation, partner information, and integration points for the
site's companion plugins.

The Events Calendar and Advanced Custom Fields are required for the event
features. Mlýn Configurable Footer manages the shared contacts and partner
logos. The homepage uses native blocks supplied by Mlýn Flexible Slider,
Mlýn Social Feed Curator, MailPoet, and the theme's calendar integration.

== Installation ==

1. Upload the theme ZIP under Appearance > Themes > Add New > Upload Theme, or copy the `velkymlyn` directory to `wp-content/themes`.
2. Install and activate The Events Calendar and Advanced Custom Fields.
3. Activate the Velký mlýn theme.
4. Assign the primary navigation menu and choose a static front page.
5. Insert the **Velký mlýn homepage** pattern on that page when migrating an existing installation.

== Frequently Asked Questions ==

= Is this a general-purpose theme? =

No. It is tailored to the content structure, calendar, branding, and companion
plugins of the Velký mlýn website.

= Why are README.md and readme.txt both included? =

README.md documents the source repository for developers. This file follows
the conventional WordPress distribution format and contains release metadata,
installation notes, credits, and the changelog. The theme metadata displayed
by WordPress comes from the header in style.css.

== Changelog ==

= 1.5.0 - 2026-08-29 =

* Moved homepage text, images, and section order into the native block editor.
* Added a curated, content-only homepage pattern with protected internal layouts.
* Added a server-rendered upcoming-events block and editor-specific styling.
* Added theme.json design constraints and a homepage-specific block allowlist.

= 1.4.1 - 2026-08-28 =

* Fixed List View event cards to show explicit start and end dates and times for multi-day events.
* Replaced empty all-day time ranges with a clear all-day label.
* Reused the corrected schedule formatting in homepage event cards.

= 1.4.0 - 2026-08-28 =

* Added per-page controls for replacing the image hero with a Mlýn Flexible Slider.
* Added automatic fallback from an unavailable slider to the page Featured Image, configured Custom Header, or built-in global theme image.
* Preserved visible page titles and the existing homepage slider behaviour.

= 1.3.0 - 2026-08-28 =

* Restored The Events Calendar search bar and event display-mode switcher.
* Replaced the event-tag links with an expandable multi-select checkbox filter.
* Added an all-tags control and preserved selected tags across calendar views, navigation, and searches.

= 1.2.0 - 2026-08-24 =

* Integrated Mlýn Configurable Footer for administrator-managed contact columns and partner logos.
* Retained the legacy footer markup as an emergency fallback when the plugin is inactive.
* Removed the duplicate `basic-info` ID from the homepage footer fallback.

= 1.1.0 - 2026-08-12 =

* Replaced obsolete Underscores placeholder documentation and package metadata.
* Documented actual requirements, integrations, development commands, licensing, and release workflow.
* Documented the shortcode-powered homepage hero.
* Removed historical asset-recovery reports and tooling from the distributable theme source.

= 1.0.0 =

* Initial custom Velký mlýn theme created by Honza Kopta based on Underscores

== Credits ==

* Based on Underscores, https://underscores.me/, copyright 2012-2020 Automattic, Inc., GPLv2 or later.
* normalize.css, https://necolas.github.io/normalize.css/, copyright Nicolas Gallagher and Jonathan Neal, MIT.
* Slick, https://kenwheeler.github.io/slick/, copyright Ken Wheeler, MIT.

Photographs, logos, partner marks, and other organisation-specific media are
not granted for reuse by the GPL license applied to the theme code.
