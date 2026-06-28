<?php

declare(strict_types=1);

namespace Drupal\ww_daisyui_starter\Hook;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Contains hook implementations for the WebWash DaisyUI Starter theme.
 */
final class ThemeHooks {

  public function __construct(
    private readonly RouteMatchInterface $routeMatch,
    private readonly ConfigFactoryInterface $configFactory,
    private readonly MenuLinkTreeInterface $menuTree,
    private readonly MenuActiveTrailInterface $menuActiveTrail,
    private readonly EntityTypeManagerInterface $entityTypeManager,
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

    // Build the processed main-menu link tree for the desktop megamenu. We
    // reuse Drupal's standard menu handling (active-trail aware tree
    // parameters + the access-check and sort manipulators that
    // SystemMenuBlock applies) so the `items` handed to the megamenu
    // component are access-filtered, weight-sorted and carry in_active_trail.
    // The drawer keeps rendering the separate main-menu block unchanged.
    $variables['main_menu_items'] = $this->buildMainMenuItems($variables);

    if (\Drupal::request()->query->get('test_alerts') === '1') {
      $messenger = \Drupal::messenger();
      $messenger->addStatus('Your changes have been saved.');
      $messenger->addWarning('You are using a deprecated feature.');
      $messenger->addError('Failed to connect to the upstream service.');
    }
  }

  /**
   * Builds the processed main-menu link tree for the megamenu component.
   *
   * Loads the `main` menu with the same tree parameters and manipulators the
   * core SystemMenuBlock uses (active-trail metadata, access checking, weight
   * sorting), builds it to a render array, and returns the processed `#items`
   * array — the same structure template_preprocess_menu() exposes (each item
   * has `title`, `url`, `below` and `in_active_trail`). The built array's
   * cacheability is bubbled so menu edits and access/route contexts invalidate
   * the page cache correctly.
   *
   * @param array $variables
   *   The preprocess variables, used to bubble the menu's cache metadata onto
   *   the page render array via its #cache key.
   *
   * @return array
   *   The processed menu items, or an empty array when the menu is empty.
   */
  protected function buildMainMenuItems(array &$variables): array {
    $menu_name = 'main';
    $settings = $this->mainMenuBlockSettings();

    // Mirror the parameter logic of core's SystemMenuBlock so the placed
    // main-menu block's configuration — in particular its "Expand all menu
    // links" checkbox — is the single source of truth for how much of the
    // tree the megamenu sees. With expand-all on, the whole tree loads (every
    // top-level item keeps its children); with it off, only the active trail
    // expands, exactly like the rendered block.
    // @see \Drupal\system\Plugin\Block\SystemMenuBlock::build()
    if ($settings['expand_all_items']) {
      $parameters = new MenuTreeParameters();
      $parameters->setActiveTrail($this->menuActiveTrail->getActiveTrailIds($menu_name));
    }
    else {
      $parameters = $this->menuTree->getCurrentRouteMenuTreeParameters($menu_name);
    }

    $level = $settings['level'];
    $depth = $settings['depth'];
    $parameters->setMinDepth($level);
    if ($depth > 0) {
      $parameters->setMaxDepth(min($level + $depth - 1, $this->menuTree->maxDepth()));
    }

    $tree = $this->menuTree->load($menu_name, $parameters);

    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $this->menuTree->transform($tree, $manipulators);
    $build = $this->menuTree->build($tree);

    // Bubble the built menu's cache metadata (tags/contexts/max-age) onto the
    // page so menu edits, route and access contexts invalidate it correctly.
    $metadata = CacheableMetadata::createFromRenderArray($build);
    $metadata->merge(CacheableMetadata::createFromRenderArray($variables))
      ->applyTo($variables);

    return $build['#items'] ?? [];
  }

  /**
   * Returns the placed main-menu block's relevant tree settings.
   *
   * Looks up the system_menu_block:main block placed in the active theme and
   * returns its level / depth / expand_all_items configuration so the
   * megamenu tree build can follow the same settings an editor sees in the
   * block UI. Falls back to SystemMenuBlock's defaults when no such block is
   * placed.
   *
   * @return array{level: int, depth: int, expand_all_items: bool}
   *   The resolved settings.
   */
  protected function mainMenuBlockSettings(): array {
    $defaults = ['level' => 1, 'depth' => 0, 'expand_all_items' => FALSE];

    $theme = $this->configFactory->get('system.theme')->get('default');
    $blocks = $this->entityTypeManager->getStorage('block')->loadByProperties([
      'theme' => $theme,
      'plugin' => 'system_menu_block:main',
    ]);
    $block = reset($blocks);
    if (!$block) {
      return $defaults;
    }

    $settings = $block->get('settings');
    return [
      'level' => (int) ($settings['level'] ?? $defaults['level']),
      'depth' => (int) ($settings['depth'] ?? $defaults['depth']),
      'expand_all_items' => (bool) ($settings['expand_all_items'] ?? $defaults['expand_all_items']),
    ];
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
