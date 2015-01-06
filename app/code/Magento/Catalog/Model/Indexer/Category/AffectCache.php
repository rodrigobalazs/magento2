<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Catalog\Model\Indexer\Category;

/**
 * Class AffectCache
 */
class AffectCache
{
    /**
     * @var \Magento\Indexer\Model\CacheContext
     */
    protected $context;

    /**
     * @param \Magento\Indexer\Model\CacheContext $context
     */
    public function __construct(
        \Magento\Indexer\Model\CacheContext $context
    ) {
        $this->context = $context;
    }

    /**
     * @param \Magento\Indexer\Model\ActionInterface $subject
     * @param array $ids
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(\Magento\Indexer\Model\ActionInterface $subject, $ids)
    {
        $this->context->registerEntities(\Magento\Catalog\Model\Category::CACHE_TAG, $ids);
        return [$ids];
    }
}
