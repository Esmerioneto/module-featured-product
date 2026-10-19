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

namespace Egsn\FeaturedProduct\Controller\Stock;

use Egsn\FeaturedProduct\Model\SalableQtyProvider;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

class Qty implements HttpGetActionInterface
{
    /**
     * Constructor.
     *
     * @param JsonFactory $jsonFactory
     * @param SalableQtyProvider $salableQtyProvider
     */
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly SalableQtyProvider $salableQtyProvider
    ) {
    }

    /**
     * Return the featured product salable quantity as JSON. Never cached.
     *
     * @return Json
     */
    public function execute(): Json
    {
        return $this->jsonFactory->create()
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true)
            ->setHeader('Pragma', 'no-cache', true)
            ->setData(['qty' => $this->salableQtyProvider->get()]);
    }
}
