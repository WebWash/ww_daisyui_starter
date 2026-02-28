# Section Grid

Responsive grid layout container with configurable columns and optional background highlight.

## Props

- **title** (string): Section title displayed above the grid
- **highlight** (boolean): Add light grey background to highlight the section (default: false)
- **columns** (string): Grid column layout - '100', '50-50', '33-33-33', or '25-25-25-25' (default: '33-33-33')

## Slots

- **grid**: Content to be displayed within the grid layout

## Usage

```twig
{% embed 'ww_tailwind_starter:section_grid' with {
  title: 'Our Services',
  highlight: true,
  columns: '33-33-33'
} %}
  {% block grid %}
    {% include 'ww_tailwind_starter:card' with {...} %}
    {% include 'ww_tailwind_starter:card' with {...} %}
    {% include 'ww_tailwind_starter:card' with {...} %}
  {% endblock %}
{% endembed %}
```
