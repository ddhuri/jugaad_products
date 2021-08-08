<?php

namespace Drupal\jugaad_products\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\NodeInterface;
use CodeItNow\BarcodeBundle\Utils\QrCode;
use Drupal\Core\Render\Markup;
use Drupal\Core\Url;
use Drupal\Component\Utility\UrlHelper;

/**
 * Provides a jugaad product qr code block.
 *
 * @Block(
 *   id = "product_url_qr_code_block",
 *   admin_label = @Translation("Jugaad Product QR Code Block"),
 * )
 */
class ProductUrlQrCodeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * RouteMatch used to get parameter Node.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Construct Drupal\jugaad_products\Plugin\Block\ProductUrlQrCodeBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = $this->routeMatch->getParameter('node');

    if ($node instanceof NodeInterface) {
      // Show block only for product pages.
      if ($node->bundle() == 'products') {
        // Get the product url from link field.
        $product_url = $node->get('field_product_link')->getValue()[0]['uri'];
        if (!empty($product_url)) {
          // Check added URL is external or not.
          if (!UrlHelper::isExternal($product_url)) {
            global $base_url;
            $product_url = $base_url . '' . Url::fromUri($product_url)->toString();
          }

          // Create a QR code.
          $qrCode = new QrCode();

          $qrCode->setText($product_url)
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0])
            ->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0])
            ->setLabel('Scan Qr Code')
            ->setLabelFontSize(16)
            ->setImageType(QrCode::IMAGE_TYPE_PNG);

          // Print the QR code in block.
          return [
            '#markup' => Markup::create('<img src="data:' . $qrCode->getContentType() . ';base64,' . $qrCode->generate() . '" />'),
          ];
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
