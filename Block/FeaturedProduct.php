<?php

/**
 * Esmerio Neto
 *
 * NOTICE OF LICENSE
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *
 * @category Egsn
 * @package Egsn_FeaturedProduct
 *
 * @copyright Copyright (c) 2026 Esmerio Neto.
 *
 * @author Esmerio Neto <esmerioneto@gmail.com>
 */

declare(strict_types=1);

namespace Egsn\FeaturedProduct\Block;

use Egsn\FeaturedProduct\Model\Config;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Featured product box rendered on the homepage.
 *
 * Name, price and image are rendered server side (FPC friendly, invalidated by the product cache tags);
 * only the salable quantity is fetched and refreshed client side by the Knockout component in jsLayout.
 */
class FeaturedProduct extends Template implements IdentityInterface
{
    private const STOCK_COMPONENT = 'egsnFeaturedProductStock';

    /**
     * @var Product|null
     */
    private ?Product $product = null;

    /**
     * @var bool
     */
    private bool $isProductLoaded = false;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Config $config
     * @param ProductRepositoryInterface $productRepository
     * @param ImageFactory $imageFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        private readonly Config $config,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ImageFactory $imageFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Featured product, or null when disabled, missing or not displayable in the current store.
     *
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        if (!$this->isProductLoaded) {
            $this->product = $this->loadProduct();
            $this->isProductLoaded = true;
        }

        return $this->product;
    }

    /**
     * Product base image block.
     *
     * @param Product $product
     * @return string
     */
    public function getImageHtml(Product $product): string
    {
        return $this->imageFactory->create($product, (string)$this->getData('image_id'), [])->toHtml();
    }

    /**
     * Final price HTML through the default price renderer (handles special price, tax display, etc).
     *
     * @param Product $product
     * @return string
     */
    public function getPriceHtml(Product $product): string
    {
        /** @var Render|false $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        return $priceRender
            ? $priceRender->render(FinalPrice::PRICE_CODE, $product, ['zone' => Render::ZONE_ITEM_LIST])
            : '';
    }

    /**
     * Name of the stock UI component declared in jsLayout.
     *
     * @return string
     */
    public function getStockComponentName(): string
    {
        return self::STOCK_COMPONENT;
    }

    /**
     * Inject runtime configuration into the stock component declared in layout.
     *
     * @return string
     */
    public function getJsLayout(): string
    {
        $this->jsLayout['components'][self::STOCK_COMPONENT]['config']['updateUrl']
            = $this->getUrl('egsn_featuredproduct/stock/qty');
        $this->jsLayout['components'][self::STOCK_COMPONENT]['config']['refreshInterval']
            = $this->config->getRefreshInterval() * 1000;

        return parent::getJsLayout();
    }

    /**
     * Product cache tags, so the homepage FPC is purged when the product changes.
     *
     * @return string[]
     */
    public function getIdentities(): array
    {
        return $this->getProduct()?->getIdentities() ?? [];
    }

    /**
     * Render nothing when there is no product to feature.
     *
     * @return string
     */
    protected function _toHtml(): string
    {
        return $this->getProduct() ? parent::_toHtml() : '';
    }

    /**
     * Load the configured product, validating it can be shown in the current store.
     *
     * @return Product|null
     */
    private function loadProduct(): ?Product
    {
        $sku = $this->config->getSku();

        if (!$this->config->isEnabled() || $sku === '') {
            return null;
        }

        try {
            $store = $this->_storeManager->getStore();
            /** @var Product $product */
            $product = $this->productRepository->get($sku, false, (int)$store->getId());
        } catch (NoSuchEntityException) {
            return null;
        }

        $isDisplayable = (int)$product->getStatus() === Status::STATUS_ENABLED
            && $product->isVisibleInCatalog()
            && in_array((int)$store->getWebsiteId(), array_map('intval', $product->getWebsiteIds()), true);

        return $isDisplayable ? $product : null;
    }
}
