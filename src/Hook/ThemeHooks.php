<?php

declare(strict_types=1);

namespace Drupal\ww_tailwind_starter\Hook;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Contains hook implementations for the WebWash Tailwind Starter theme.
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
    $variables['is_canvas_page'] = $route_name === 'entity.canvas_page.canonical'
      || str_starts_with($route_name, 'canvas.api.layout');
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

}
