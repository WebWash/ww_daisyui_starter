# Text

Displays formatted HTML text content with alignment options.

## Props

- **text** (string/HTML): The text content to display (supports HTML markup)
- **align** (string): Text alignment - 'left', 'center', or 'right' (default: 'left')

## Usage

```twig
{% include 'webwash_daisyui_starter:text' with {
  text: '<p>This is some formatted text with <strong>bold</strong> and <em>italic</em> content.</p>',
  align: 'center'
} %}
```
