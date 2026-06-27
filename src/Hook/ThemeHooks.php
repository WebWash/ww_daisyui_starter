<?php

declare(strict_types=1);

namespace Drupal\ww_daisyui_starter\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Contains hook implementations for the WebWash DaisyUI Starter theme.
 */
final class ThemeHooks {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly ConfigFactoryInterface $configFactory,
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
    $variables['site_name'] = $this->configFactory->get('system.site')->get('name');

    if (\Drupal::request()->query->get('test_alerts') === '1') {
      $messenger = \Drupal::messenger();
      $messenger->addStatus('Your changes have been saved.');
      $messenger->addWarning('You are using a deprecated feature.');
      $messenger->addError('Failed to connect to the upstream service.');
    }
  }

  /**
   * Implements hook_preprocess_HOOK() for block--system-branding-block.html.twig.
   */
  #[Hook('preprocess_block__system_branding_block')]
  public function preprocessBlockSystemBranding(array &$variables): void {
    $variables['attributes']['class'][] = 'text-2xl font-bold text-gray-800 w-[200px]';
  }

  /**
   * Implements hook_preprocess_HOOK() for block--local-tasks-block.html.twig.
   */
  #[Hook('preprocess_block__local_tasks_block')]
  public function preprocessBlockLocalTasks(array &$variables): void {
    $variables['attributes']['class'][] = 'ww-canvas--tabs';
  }

  /**
   * Implements hook_preprocess_HOOK() for menu--main.html.twig.
   */
  #[Hook('preprocess_menu__main')]
  public function preprocessMenuMain(array &$variables): void {
    $variables['attributes']['class'][] = 'menu lg:menu-horizontal px-1';
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
