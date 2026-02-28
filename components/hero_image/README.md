# Hero Image

Full-width hero section with background image, heading, subtitle, and call-to-action.

## Props

- **media** (object): Background image for the hero section
- **heading_text** (string): Main heading text displayed on the hero
- **subtitle** (string): Subtitle text displayed below the heading
- **link_url** (string): URL for the call-to-action button
- **link_label** (string): Label text for the call-to-action button

## Usage

```twig
{% include 'ww_tailwind_starter:hero_image' with {
  media: {
    src: '/path/to/hero-image.jpg',
    alt: 'Hero background'
  },
  heading_text: 'Welcome to Our Site',
  subtitle: 'Discover amazing features and services',
  link_url: '/get-started',
  link_label: 'Get Started'
} %}
```
