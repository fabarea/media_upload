## 4.1.3 (2026-11-21)

[TASK] Move addStaticFile to TCA/Overrides

## 4.1.2 (2026-11-21)

[SECURITY] Fix path traversal via unanchored qquuid (upload/delete)
[SECURITY] Reject "/", "\", "..", ":" in qquuid and uploaded filenames
[SECURITY] Constrain upload/delete paths to typo3temp/MediaUpload
[SECURITY] Re-validate filename after normalization (NTFS ADS / FileNameValidator bypass)

## 4.1.1 (2026-08-08)

[BUGFIX] Resolve uploaded file paths against public path (eID/CLI cwd)

## 4.1.0 (2026-08-08)

[FEATURE] Compatibility v13
[BUGFIX] Fix MediaUploadController Extbase DI (no parent::__construct)
[BUGFIX] Resolve frontend user from request attribute
[TASK] Allow typo3/cms-core ^12 || ^13
[DOCS] Update TYPO3 compatibility matrix

## 4.0.0 (2025-12-03)

[TASK] TYPO3 v12 compatibility

