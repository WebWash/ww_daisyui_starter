# Heading

Renders a configurable heading (h1-h6) with alignment options.

## Props

- **heading_text** (string): The heading text content (default: 'Enter title')
- **level** (number): The heading level 1-6 corresponding to h1-h6 tags (default: 2)
- **align** (string): Text alignment - 'left', 'center', or 'right' (default: 'left')

## Usage

```twig
{% include 'ww_tailwind_starter:heading' with {
  heading_text: 'My Page Title',
  level: 1,
  align: 'center'
} %}
```
