<?php

declare(strict_types=1);

namespace Drupal\ww_daisyui_starter\Hook;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Render\Markup;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Contains hook implementations for the WebWash DaisyUI Starter theme.
 */
final class ThemeHooks {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly FileSystemInterface $fileSystem,
  ) {}

  /**
   * Implements template_preprocess_page().
   */
  #[Hook('preprocess_page')]
  public function preprocessPage(array &$variables): void {
    $route_name = $this->routeMatch->getRouteName();
    $variables['is_edge'] = $route_name === 'entity.canvas_page.canonical'
      || str_starts_with((string) $route_name, 'canvas.api.layout')
      || str_starts_with((string) $route_name, 'ww_styleguide.');
  }

  /**
   * Implements hook_preprocess_HOOK() for block--system-branding-block.html.twig.
   */
  #[Hook('preprocess_block__system_branding_block')]
  public function preprocessBlockSystemBranding(array &$variables): void {
    // No text colour here: an inlined SVG logo paints with `currentColor`, so
    // it inherits from whichever region holds the block (base-content in the
    // header, the footer's light-on-dark text in the footer).
    $variables['attributes']['class'][] = 'text-2xl font-bold w-[200px]';
    $variables['site_logo_inline'] = $this->inlineLogoSvg($variables['site_logo'] ?? '');
  }

  /**
   * Reads an SVG logo so it can be inlined.
   *
   * The file contents are marked safe and used as-is. Uploading a logo
   * requires the "administer themes" permission, which already implies
   * "administer modules" and so amounts to full trust; core and the contrib
   * themes here inline or serve uploaded SVGs without filtering them too.
   * Sizing is handled in CSS (src/css/components/branding.css) rather than by
   * rewriting the markup.
   *
   * @param string $url
   *   The site logo URL, as produced by the branding block.
   *
   * @return \Drupal\Component\Render\MarkupInterface|null
   *   The SVG markup, already marked safe so the template can print it
   *   without `|raw`, or NULL when the logo is not a local SVG and should be
   *   rendered with core's <img> fallback instead.
   */
  private function inlineLogoSvg(string $url): ?MarkupInterface {
    if ($url === '') {
      return NULL;
    }

    // Strip any query string or fragment before testing the extension, and
    // ignore off-site logos: only files this site serves can be read here.
    $path = parse_url($url, PHP_URL_PATH) ?: '';
    if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) !== 'svg') {
      return NULL;
    }
    if ((parse_url($url, PHP_URL_HOST) ?? '') !== '') {
      return NULL;
    }

    $file = $this->fileSystem->realpath(ltrim($path, '/'));
    if ($file === FALSE || !is_file($file) || !is_readable($file)) {
      return NULL;
    }

    $svg = file_get_contents($file);

    return $svg === FALSE || $svg === '' ? NULL : Markup::create($svg);
  }

  /**
   * Implements hook_preprocess_HOOK() for block--local-tasks-block.html.twig.
   */
  #[Hook('preprocess_block__local_tasks_block')]
  public function preprocessBlockLocalTasks(array &$variables): void {
    $variables['attributes']['class'][] = 'ww-canvas--tabs';
  }

  /**
   * Implements hook_preprocess_HOOK() for field templates.
   *
   * Adds the `prose` class to long-text body fields (text_long,
   * text_with_summary, text) so editor-authored HTML automatically picks up
   * the prose styling defined in `src/css/components/typography.css`.
   * Drupal core doesn't add `field--type-*` classes by default, so we tag
   * these fields explicitly rather than relying on selectors that aren't there.
   */
  #[Hook('preprocess_field')]
  public function preprocessField(array &$variables): void {
    $proseFieldTypes = ['text_long', 'text_with_summary', 'text'];
    if (in_array($variables['field_type'] ?? '', $proseFieldTypes, TRUE)) {
      $variables['attributes']['class'][] = 'prose';
    }
  }

  /**
   * Implements hook_page_attachments_alter().
   *
   * Applies a saved daisyUI theme from localStorage before first paint to
   * avoid a flash of the default theme. (Themes can't implement
   * hook_page_attachments - only the _alter variant.)
   */
  #[Hook('page_attachments_alter')]
  public function pageAttachmentsAlter(array &$attachments): void {
    $script = "(function(){try{var t=localStorage.getItem('ww-theme');if(t){document.documentElement.setAttribute('data-theme',t);}}catch(e){}})();";
    $attachments['#attached']['html_head'][] = [
      [
        '#tag' => 'script',
        '#value' => $script,
      ],
      'ww_daisyui_starter_theme_init',
    ];
  }

}
