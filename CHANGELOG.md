# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased] - 2026-03-31

### Added
- **PDF Viewer Support**: Migrated `joaopaulolndev/filament-pdf-viewer` to `^3.0` to ensure compatibility with Filament v5.
- **CKP KIPAPP PDF Preview**: Added `PdfViewerEntry` to the `CkpKipappResource` infolist to display PDF preview on the detail page (`ViewRecord`).
- **CKP KIPAPP PDF Form Preview**: Added `PdfViewerField` to the `CkpKipappForm` schema to display a PDF preview in the edit/view modal below the file upload field.

### Fixed
- **MySQL Group By Constraint**: Fixed `ONLY_FULL_GROUP_BY` error in `NilaiPegawaiRekapWidget` by using an Eloquent `fromSub` subquery approach to aggregate `AVG()` and `COUNT()` logic.
- **Table Actions Namespace**: Restored the correct `Filament\Actions\*` namespace in `CkpKipappsTable.php` (for `EditAction`, `ViewAction`, `DeleteBulkAction`) which is the standardized namespace for Actions in Filament v5.

### Rules Established
- **Filament V5 Action Namespace Rule**: Implemented a core rule to always use `Filament\Actions` for all actions (including table actions) instead of legacy `Filament\Tables\Actions` to prevent "Class Not Found" conflicts in Filament v5.
- **Secure File URL Binding Rule**: Established pattern for `filament-pdf-viewer`; always use a dedicated field name (like `nama_file_viewer`) bound to `->fileUrl()` closure to avoid filament state-binding conflicts when showing documents from secure or custom routes.
