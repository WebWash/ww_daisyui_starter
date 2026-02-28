# WebWash Tailwind Starter Theme

This is a starterkit theme and is totally experimental for now.

A modern Drupal theme built with Tailwind CSS v4 and Single Directory Components (SDC).

## Technology Stack

- Drupal 11
- Tailwind CSS v4
- Vite (build tool)
- Single Directory Components (SDC)
- Node.js v22.14.0

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
   npm install
   ```

### Development

Run the development build with watch mode (automatically rebuilds on file changes):
```bash
npm run dev
```

### Production Build

Create an optimized production build:
```bash
npm run build
```

## Single Directory Components (SDC)

This theme uses Drupal's Single Directory Components architecture. Each component is self-contained in its own directory with:
- Component definition (`.component.yml`)
- Twig template (`.twig`)
- Styles (imported into Vite build)
- Documentation (`README.md`)

Components are located in the `/components` directory.
