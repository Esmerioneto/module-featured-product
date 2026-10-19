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

namespace Egsn\FeaturedProduct\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Store\Model\StoreManagerInterface;

class SalableQtyProvider
{
    /**
     * Constructor.
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param GetProductSalableQtyInterface $getProductSalableQty
     */
    public function __construct(
        private readonly Config $config,
        private readonly StoreManagerInterface $storeManager,
        private readonly StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        private readonly GetProductSalableQtyInterface $getProductSalableQty
    ) {
    }

    /**
     * Salable quantity of the configured featured product in the current website stock.
     *
     * Null when the feature is disabled or the quantity cannot be resolved
     * (unknown SKU, product type without stock management, etc).
     *
     * @return float|null
     */
    public function get(): ?float
    {
        $store = $this->storeManager->getStore();
        $storeId = (int)$store->getId();
        $sku = $this->config->getSku($storeId);

        if (!$this->config->isEnabled($storeId) || $sku === '') {
            return null;
        }

        try {
            $stockId = (int)$this->stockByWebsiteIdResolver->execute((int)$store->getWebsiteId())->getStockId();

            return max(0.0, $this->getProductSalableQty->execute($sku, $stockId));
        } catch (LocalizedException) {
            return null;
        }
    }
}
