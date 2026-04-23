# Asset Duplicate Audit

Date: 2026-04-23

The Dusk theme currently stores theme-specific assets and copied shared assets under `resources/themes/dusk/views/public/assets`.

Safe cleanup done in this slice:

- Shared Vite Blade-refresh and PostCSS setup is centralized in `vite.shared.js`.
- Atom theme CSS now imports the global stylesheet and keeps only Atom-specific badge drawer rules locally.

Not deleted in this slice:

- Files under `resources/themes/dusk/views/public/assets`.

Reason:

The Dusk asset tree mixes theme-only images (`assets/images/dusk/*`) with shared copied assets (`assets/css/*`, `assets/images/icons/*`). Deleting those copies without first rewriting all Dusk view references would risk broken production asset URLs. The safe next step is to update Dusk Blade templates to reference either `/assets/...` for shared files or a dedicated `/themes/dusk/...` publish path for Dusk-only files, then remove the copied shared assets.
