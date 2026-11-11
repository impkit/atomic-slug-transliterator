# ImpKit Atomic Slug Transliterator

An atomic, single-purpose WordPress plugin that reliably transliterates non-Latin URL slugs (such as Cyrillic) into standard Latin (ISO 9) across Gutenberg, Classic Editor, and REST API environments.

## ⚡ Features

- **100% Autonomous:** Works completely out of the box via `functions.php` / plugin architecture without heavy third-party dependencies.
- **REST API Middleware:** Intercepts Gutenberg block editor asynchronous "Save Draft" network packets "on the fly" to prevent slug generation failures.
- **Taxonomy Support:** Correctly processes categories and tags created both in the traditional WP admin panels and on the fly inside the block editor block sidebar.
- **Performance Optimized:** Uses fast internal string-translation mechanisms (`strtr`) bypassing heavy regular expression overhead.

## 🛠️ Installation

1. Download the latest stable version as a ZIP archive.
2. Upload via WordPress Admin Dashboard (`Plugins -> Add New -> Upload Plugin`).
3. Activate the plugin.

## 📋 About ImpKit

This utility is part of the **ImpKit** ecosystem — focused, lightweight, and atomic digital utilities designed for modern web environments. 

Developed by [ImpKit Engineering](https://github.com/impkit).

