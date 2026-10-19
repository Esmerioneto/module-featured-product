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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'egsn_featuredproduct/general/enabled';
    private const XML_PATH_SKU = 'egsn_featuredproduct/general/sku';
    private const XML_PATH_REFRESH_INTERVAL = 'egsn_featuredproduct/general/refresh_interval';
    private const MIN_REFRESH_INTERVAL = 3;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Whether the featured product box is enabled.
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Configured featured product SKU.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getSku(?int $storeId = null): string
    {
        return trim((string)$this->scopeConfig->getValue(self::XML_PATH_SKU, ScopeInterface::SCOPE_STORE, $storeId));
    }

    /**
     * Salable quantity refresh interval, in seconds.
     *
     * @param int|null $storeId
     * @return int
     */
    public function getRefreshInterval(?int $storeId = null): int
    {
        $interval = (int)$this->scopeConfig->getValue(
            self::XML_PATH_REFRESH_INTERVAL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return max(self::MIN_REFRESH_INTERVAL, $interval);
    }
}
