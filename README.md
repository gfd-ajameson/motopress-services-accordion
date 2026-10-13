# MotoPress Services Accordion (Themify Ultra child theme)

A WordPress child theme built for a nail-salon client. Their booking runs on the **MotoPress Appointment** plugin, whose default services list is a grid of cards. This theme turns that list into an accessible accordion: the service name stays visible, and the price, duration, full description and a "See More" link expand underneath.

## What it does

- **Overrides MotoPress's list-item template** (`motopress/appointment/service/single-item.php`). It keeps every action hook the plugin fires, so MotoPress still renders the title, image, price and attributes itself. The theme only rearranges where they sit: a header row that acts as the toggle, and a collapsible panel.
- **Overrides the excerpt template** (`motopress/appointment/service/excerpt.php`) and filters `get_the_excerpt` for the `mpa_service` post type, so the panel shows the full, filtered description instead of a trimmed excerpt.
- **Shortcodes for page-builder layouts**: `[tpp_services_meta]` prints price and duration for the service in the current loop, and `[custom_service_details id="123"]` prints the meta bar plus full description for any service.
- **Accordion behaviour** (`assets/js/tpp-service-accordion.js`): keyboard support (Enter / Space), `aria-expanded` on the header, and a `max-height` transition that measures the panel's real height so it animates both ways.

## Separating logic from presentation

- `get_custom_service_details_html()` does the data work: it validates the ID and post type, reads MotoPress's `_mpa_price` / `_mpa_duration` meta, falls back gracefully if the plugin's currency helper is missing, and escapes every value. Templates and shortcodes only call it.
- Its output passes through a `custom_service_details_html` filter, so the markup can be adjusted elsewhere without editing the function.
- The template override contains no queries or meta lookups. It decides layout only and leaves rendering to MotoPress's own hooks.
- The script is enqueued with a `filemtime()` version, so browsers pick up edits without a manual version bump, and it is skipped if the file is missing.

## What I wrote

The template overrides, `functions.php`, the accordion script and the custom CSS in `style.css`. The parent theme (Themify Ultra) and MotoPress Appointment are commercial products and aren't included here. You need both installed to run this.

## Trade-offs

- **Template overrides vs. hooks only.** Overriding `single-item.php` gives full control of the markup, but it has to be checked against each MotoPress update. The override keeps the plugin's structure and hooks one-to-one so that diff stays small.
- **`extract()` in the template** mirrors how MotoPress's own templates receive their arguments. It isn't a pattern I'd choose in new code, but matching the parent template makes upgrades easier to compare.
- **The two price/duration renderers overlap.** `get_custom_service_details_html()` is the more general of the two, and `[tpp_services_meta]` could be reduced to a call to it.
- **No no-JS fallback.** The CSS collapses every panel with `max-height: 0`, so if the script fails, the descriptions and "See More" links stay hidden. Collapsing only once the script adds a class to the list would fix that.
- **jQuery for the accordion.** WordPress and Themify already load it on every page, so it added no weight. A standalone version would use plain DOM APIs.
- **Unprefixed public function name.** `get_custom_service_details_html()` should carry the theme's `tpp_` prefix to rule out collisions. It keeps its original name here to match the deployed site.

## Changes for this public copy

- Removed two `require_once` lines for checkout-field add-ons (birthday capture on the booking form). Those files aren't part of this sample.
- Replaced a placeholder text domain with the theme's own, made the hard-coded "Price / Duration" labels translatable, and tidied comments.
