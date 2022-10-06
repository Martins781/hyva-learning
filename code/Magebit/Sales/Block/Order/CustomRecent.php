<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magebit\Sales\Block\Order;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\Config;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sales order history block
 *
 * @api
 * @since 100.0.2
 */
class CustomRecent extends \Magento\Framework\View\Element\Template
{
    /**
     * Limit of orders
     */
    const ORDER_LIMIT = 5;

    /**
     * @var CollectionFactoryInterface
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectionFactoryInterface $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CollectionFactoryInterface $orderCollectionFactory,
        Session $customerSession,
        Config $orderConfig,
        array $data = [],
        StoreManagerInterface $storeManager = null
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->_isScopePrivate = true;
        $this->storeManager = $storeManager ?: ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
        parent::__construct($context, $data);
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->getRecentOrders();
    }

    /**
     * Get recently placed orders. By default they will be limited by 5.
     */
    private function getRecentOrders()
    {
        $customerId = $this->_customerSession->getCustomerId();
        $orders = $this->_orderCollectionFactory->create($customerId)->addAttributeToSelect(
            'increment_id'
        )->addAttributeToSelect('created_at')
            ->addAttributeToSelect('customer_firstname')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('customer_lastname')
            ->addAttributeToSelect('grand_total')
            ->addAttributeToSelect('status')
            ->load();
        $this->setOrders($orders);
    }
    // increment_id, created_at, customer_firstname, grand_total, state,
    /**
     * Get order view URL
     *
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * Get order track URL
     *
     * @param object $order
     * @return string
     * @deprecated 102.0.3 Action does not exist
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTrackUrl($order)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        trigger_error('Method is deprecated', E_USER_DEPRECATED);
        return '';
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        if ($this->getOrders()->getSize() > 0) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Get reorder URL
     *
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }
}
