# WebWash DaisyUI Starter Theme

This is a starterkit theme and is totally experimental for now.

A modern Drupal theme built with Tailwind CSS v4 and Single Directory Components (SDC).

## Technology Stack

- Drupal 11.4+
- Tailwind CSS v4
- daisyUI v5
- Vite (build tool)
- Single Directory Components (SDC)
- Node.js v22.14.0

## Requirements

This theme requires **Drupal 11**. Drupal 11.4 or later is recommended.

The components use the `html_cva()` Twig function (Class Variance Authority) to
build their variant classes.

- **Drupal 11.4 and later** — `html_cva()` is included in core. Nothing else to
  install.
- **Drupal 11.3 and earlier** — `html_cva()` is not in core, so you must install
  the contrib [CVA module](https://www.drupal.org/project/cva) or the components
  will fail to render:

  ```bash
  composer require drupal/cva
  drush en cva
  ```

Note that the contrib CVA module is deprecated and will not be released for
Drupal 12, so upgrading to Drupal 11.4+ is the recommended path.

## Setup Instructions

### Prerequisites

This theme requires Node.js v22.14.0. If you use nvm (Node Version Manager), the correct version will be automatically selected.

### Installation

1. Use the correct Node.js version:

   ```bash
   nvm use
   ```

2. Install dependencies:
   ```bash
   pnpm install
   ```

### Development

Run the development build with watch mode (automatically rebuilds on file changes):

```bash
pnpm dev
```

### Production Build

Create an optimized production build:

```bash
pnpm build
```

## Single Directory Components (SDC)

This theme uses Drupal's Single Directory Components architecture. Each component is self-contained in its own directory with:

- Component definition (`.component.yml`)
- Twig template (`.twig`)
- Styles (imported into Vite build)
- Documentation (`README.md`)

Components are located in the `/components` directory.
