# Changelog

All notable changes to `laravel-filament-flexible-content-block-pages` will be documented in this file.

## 3.0.2 - 2026-02-19

- Add conditional cache tag support: uses native `Cache::tags()` when the driver supports it (Redis, Memcached), falls back to manual key-index tracking otherwise.
- Route all `Page` model cache calls through `TaggableCache` for driver-agnostic caching.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/3.0.1...3.0.2

## 4.0.2 - 2026-02-19

- Add conditional cache tag support: uses native `Cache::tags()` when the driver supports it (Redis, Memcached), falls back to manual key-index tracking otherwise.
- Route all `Page` model cache calls through `TaggableCache` for driver-agnostic caching.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/4.0.1...4.0.2

## 4.0.1 - 2026-02-18

### What's Changed

* Use new getters from blocks package to include the parameters replacer for title, intro and seo fields of a page.
* Improve menu resource by @lukasdewijn in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/13
* Fix lazy loading violation on page tree and add canonical URL redirects by @lukasdewijn in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/12

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/4.0.0...4.0.1

## v3.0.1 - 2026-02-18

- Use new getters from blocks package to include the parameters replacer in title, intro & seo fields of the page

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/3.0.0...3.0.1

## v4.0.0 - 2026-02-12

- Filament v4 & v5 compatibility

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/3.0.0...4.0.0

## v3.0.0 - 2026-02-12

- Bump semver to match Filament versions

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.21...3.0.0

## 0.2.22 - 2026-02-09

- Add page caching for urls and models based on page code.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.21...0.2.22

## v0.2.20 - 2026-02-05

### What's Changed

* View page action on edit page by @lukasdewijn in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/10
* Code filter and code columns added to page table

### New Contributors

* @lukasdewijn made their first contribution in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/10

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.19...0.2.20

## v0.2.19 - 2026-02-04

* Fix menu seeder to use the correct morph class from the configured page model for menu items with page link

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.18...0.2.19

## v0.2.18 - 2026-02-03

* Fix bug in configurable page and tag models with the controllers. The route binding now maps to the configured model.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.17...0.2.18

## v0.2.17 - 2026-01-22

- Fix undeletable pages in bulk delete
- Fix SEO description so that when it is not available, the intro is shown in meta tags.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.16...0.2.17

## v0.2.16 - 2026-01-22

- Add initial support for video url embed in hero + make it configurable. View template is not final yet.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.15...0.2.16

## v0.2.15 - 2026-01-21

- Improve a11y aria labels in menu title.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.14...0.2.15

## v0.2.14 - 2026-01-21

### What's Changed

* Fix bug where SEO title of home page does not have a suffix.
* Bump dependabot/fetch-metadata from 2.4.0 to 2.5.0 by @dependabot[bot] in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/9

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.13...0.2.14

## v0.2.13 - 2026-01-04

### What's Changed

* Fix null pointer error in tag resource.
* Bump actions/checkout from 5 to 6 by @dependabot[bot] in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/8

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.12...0.2.13

## v0.2.12 - 2025-11-20

- Fix page resource table: make it searchable again

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.11...0.2.12

## v0.2.11 - 2025-11-20

Fix bug with translatable urls of menu items.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.10...0.2.11

## v0.2.10 - 2025-11-20

- fix menu seeder bug.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.9...0.2.10

## v0.2.9 - 2025-11-17

- add code column to page table, default hidden
- add code to table search

## v0.2.8 - 2025-11-17

### What's Changed

* Add title to menu to display above the menu on the website. by @sten in https://github.com/statikbe/laravel-filament-flexible-content-block-pages/pull/7
* Make url of menu item translatable

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.7...0.2.8

## v0.2.7 - 2025-10-21

- Fix phpstan errors
- Fix default data seeder options bug

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/compare/0.2.6...0.2.7

## v0.2.6 - 2025-10-21

- You can now disable the home page route in config, to implement a custom home page.

## v0.2.5 - 2025-10-20

- Add options to default seeder command.

## v0.2.4 - 2025-10-17

- Add configurable gate for previewing unpublished pages

## v0.2.3 - 2025-10-16

- Fix bug in parent pages
- Fix version numbering of flexible blocks package

## v0.2.2 - 2025-10-14

Fix bug with disabled page hierarchy on page tree action

## v0.2.1 - 2025-10-02

Fix lazy loading exception in menu item observer to clear menu cache.

## v0.2.0 - 2025-10-02

Add menu caching
Fix language switch component to make the home page work

## v0.1.1 - 2025-09-30

Fix eager loading of menu items with linkable model: the parent relationships need to be eager loaded to get the url.

## v0.1.0 - 2025-09-29

Initial version.

**Full Changelog**: https://github.com/statikbe/laravel-filament-flexible-content-block-pages/commits/0.1.0
