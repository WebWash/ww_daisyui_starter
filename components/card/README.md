# Card

Displays content in a card layout with optional image, title, summary, and link.

## Props

- **media** (object): Media and Alt for the card image
- **title** (string): Card title text
- **summary** (string): Card summary/description text
- **link_url** (string): URL for the card link (default: 'https://www.example.com')
- **link_label** (string): Label text for the card link

## Usage

```twig
{% include 'webwash_daisyui_starter:card' with {
  media: {
    src: '/path/to/image.jpg',
    alt: 'Image description'
  },
  title: 'Card Title',
  summary: 'This is the card summary text that provides a brief description.',
  link_url: 'https://www.example.com',
  link_label: 'Read More'
} %}
```
