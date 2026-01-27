# Filament v4 Migration Plan

## Current Status

**Branch**: `feature/filament-v4`

### Completed Work
- [x] Updated `composer.json` dependencies for Filament v4
- [x] Created Pest feature tests for all resources (Menu, MenuItem, Tag, TagType, Settings, Page, Redirect)
- [x] Created test database migrations
- [x] Created model factories
- [x] Updated TestCase for testing setup
- [x] Started manual updates to Resource pages
- [x] **Phase 1**: Ran automated Filament v4 upgrade script
- [x] **Phase 2**: Fixed PHPStan errors and all tests passing
- [x] **Phase 3**: Refactored all resources to use schema classes
- [x] Code review and cleanup

### Pending Work
- [ ] Remove local path repository from `composer.json` before merge
- [ ] Optional directory structure migration (Phase 4)

---

## Phase 1: Run Automated Upgrade Script

The Filament team provides an upgrade script that handles most breaking changes automatically.

### Steps

```bash
# 1. Install the upgrade tool
composer require filament/upgrade:"^4.0" -W --dev

# 2. Run the automated upgrade script
vendor/bin/filament-v4

# 3. Follow any output commands unique to your app

# 4. Remove the upgrade tool and finalize dependencies
composer remove filament/upgrade --dev
composer update
```

### What the Script Handles
- Namespace changes
- Method renames
- Class renames
- Basic syntax updates

---

## Phase 2: Run Tests & PHPStan

After the automated upgrade, verify the codebase works correctly.

### Steps

```bash
# Run the test suite
composer test

# Run static analysis
composer analyse

# Format code
composer format
```

### Fix Any Failures
- Document failures and their fixes here as we encounter them

---

## Phase 3: Manual Review for Filament v4 Patterns

### Key Breaking Changes to Review

#### 1. Layout Component Spanning
`Grid`, `Section`, and `Fieldset` components now consume one column by default.
- Use `columnSpanFull()` to span all columns where needed

#### 2. Filter Deferral
Filters are deferred by default in v4.
- Use `deferFilters(false)` on tables to restore previous behavior if needed

#### 3. Enum Field State
Field state always returns enum instances now, never raw values.
- Review any code that handles enum values from form fields

#### 4. Unique Validation
The `unique()` rule now ignores the current record by default (`ignoreRecord: true`).
- Review unique validation rules

#### 5. Authorization Methods
If overriding authorization methods, use `get*AuthorizationResponse()` instead of `can*()`.

#### 6. URL Parameter Naming
Several URL parameters were renamed:
- `activeRelationManager` → `relation`
- `activeTab` → `tab`
- `tableFilters` → `filters`
- `tableSort` → `sort`

### Files to Review

#### Resources
- [x] `src/Resources/MenuResource.php`
- [x] `src/Resources/PageResource.php`
- [x] `src/Resources/SettingsResource.php`
- [x] `src/Resources/TagResource.php`
- [x] `src/Resources/TagTypeResource.php`
- [x] `src/Resources/RedirectResource.php`

#### Resource Pages
- [x] All `CreateX.php` pages
- [x] All `EditX.php` pages
- [x] All `ListX.php` pages
- [x] `ManageMenuItems.php`

#### Forms & Tables
- [x] Review all form schemas for layout changes
- [x] Review all table configurations for filter behavior

#### Custom Components
- [x] Review any custom Filament components

---

## Phase 3b: Refactor to Schema Classes

### Pattern

Filament v4 supports extracting form/table schemas into separate classes for better organization.

**Directory Structure:**
```
src/Resources/
├── TagTypeResource.php
└── TagTypeResource/
    ├── Pages/
    │   ├── CreateTagType.php
    │   ├── EditTagType.php
    │   └── ListTagTypes.php
    └── Schemas/              ← NEW
        └── TagTypeFormSchema.php
```

**Schema Class Template:**
```php
<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Schemas;

use Filament\Schemas\Schema;

class TagTypeFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            // form fields moved here
        ]);
    }
}
```

**Resource (simplified):**
```php
public static function form(Schema $schema): Schema
{
    return TagTypeFormSchema::configure($schema);
}
```

### Resources to Refactor

| Resource | Form Complexity | Priority |
|----------|----------------|----------|
| TagTypeResource | Low | 1 - Start here as template |
| RedirectResource | Low | 2 |
| MenuResource | Low | 3 |
| SettingsResource | Medium | 4 |
| TagResource | Medium | 5 |
| PageResource | High (tabs) | 6 - Most complex |

### Completed Checklist

- [x] Create `TagTypeResource/Schemas/TagTypeFormSchema.php` and `TagTypeTableSchema.php`
- [x] Create `TagResource/Schemas/TagFormSchema.php` and `TagTableSchema.php`
- [x] Create `RedirectResource/Schemas/RedirectFormSchema.php` and `RedirectTableSchema.php`
- [x] Create `SettingsResource/Schemas/SettingsFormSchema.php` and `SettingsTableSchema.php`
- [x] Create `MenuResource/Schemas/MenuFormSchema.php` and `MenuTableSchema.php`
- [x] Create `PageResource/Schemas/PageFormSchema.php` and `PageTableSchema.php`
- [x] Update all Resource classes to use schema classes
- [x] Run tests to verify refactoring
- [x] Code cleanup (remove empty methods, standardize patterns)

---

## Phase 4: Optional Directory Structure Migration

Filament v4 has a new recommended directory structure. This is optional but can be done with:

```bash
php artisan filament:upgrade-directory-structure-to-v4 --dry-run
```

Review the dry-run output before applying.

---

## Progress Log

### Session 2025-01-27

#### Phase 1: Automated Upgrade Script
- Ran `vendor/bin/filament-v4` with `src` directory
- Script updated 35 files automatically (namespace changes, method renames, etc.)

#### Phase 2: Fixes Applied

**1. IconPicker namespace change** (`TagTypeResource.php`)
- Old: `Guava\FilamentIconPicker\Forms\IconPicker` and `Guava\FilamentIconPicker\Tables\IconColumn`
- New: `Guava\IconPicker\Forms\Components\IconPicker` and `Guava\IconPicker\Tables\Columns\IconColumn`

**2. EditAction::mutateFormDataBeforeSaveUsing() removed** (`ManageMenuItems.php`)
- Method doesn't exist in Filament v4
- Replaced with `->using()` to customize the save process directly
- Now handles data mutation within the custom save callback

**3. Redundant condition in ManagePageTree.php**
- Removed `&& $this->getModel()::has('parent')` as it was always true
- Simplified to just `if (method_exists($this->getModel(), 'parent'))`

#### Results
- All 67 tests passing
- PHPStan: No errors
- Code formatted with Laravel Pint

---

### Session 2026-01-27 (continued)

#### Phase 3: Schema Class Refactoring

Extracted form and table definitions into dedicated schema classes for all resources:

**Created Schema Classes:**
- `TagTypeResource/Schemas/TagTypeFormSchema.php`
- `TagTypeResource/Schemas/TagTypeTableSchema.php`
- `TagResource/Schemas/TagFormSchema.php`
- `TagResource/Schemas/TagTableSchema.php`
- `RedirectResource/Schemas/RedirectFormSchema.php`
- `RedirectResource/Schemas/RedirectTableSchema.php`
- `SettingsResource/Schemas/SettingsFormSchema.php`
- `SettingsResource/Schemas/SettingsTableSchema.php`
- `MenuResource/Schemas/MenuFormSchema.php`
- `MenuResource/Schemas/MenuTableSchema.php`
- `PageResource/Schemas/PageFormSchema.php`
- `PageResource/Schemas/PageTableSchema.php`

**Code Cleanup:**
- Removed unnecessary `BulkActionGroup` wrapping for single bulk actions
- Removed empty `getRelations()` methods from resources
- Removed empty `->filters([])` calls from table schemas
- Fixed PHPDoc type annotation (`resource` → `Resource`) in `TagPageService.php`
- Added missing newline at end of this file

#### Results
- All 67 tests passing
- PHPStan: No errors (106 files analyzed)
- Clean, consistent schema class pattern across all resources

---

## References

- [Filament v4 Upgrade Guide](https://filamentphp.com/docs/4.x/upgrade-guide)
- [Filament v4 Documentation](https://filamentphp.com/docs/4.x)
