<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Catalog\Model\Category\Attribute\Backend;

/**
 * Catalog Category Attribute Default and Available Sort By Backend Model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Sortby extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Validate process
     *
     * @param \Magento\Framework\Object $object
     * @return bool
     * @throws \Magento\Framework\Model\Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function validate($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $postDataConfig = $object->getData('use_post_data_config') ?: [];
        $isUseConfig = in_array($attributeCode, $postDataConfig);

        if ($this->getAttribute()->getIsRequired()) {
            $attributeValue = $object->getData($attributeCode);
            if ($this->getAttribute()->isValueEmpty($attributeValue) && !$isUseConfig) {
                return false;
            }
        }

        if ($this->getAttribute()->getIsUnique()) {
            if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
                $label = $this->getAttribute()->getFrontend()->getLabel();
                throw new \Magento\Framework\Model\Exception(__('The value of attribute "%1" must be unique.', $label));
            }
        }

        if ($attributeCode == 'default_sort_by') {
            $available = $object->getData('available_sort_by') ?: [];
            $available = is_array($available) ? $available : explode(',', $available);
            $data = !in_array(
                'default_sort_by',
                $postDataConfig
            ) ? $object->getData(
                $attributeCode
            ) : $this->_scopeConfig->getValue(
                "catalog/frontend/default_sort_by",
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            if (!in_array($data, $available) && !in_array('available_sort_by', $postDataConfig)) {
                throw new \Magento\Framework\Model\Exception(
                    __('Default Product Listing Sort by does not exist in Available Product Listing Sort By.')
                );
            }
        }

        return true;
    }

    /**
     * Before Attribute Save Process
     *
     * @param \Magento\Framework\Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if (!is_array($data)) {
                $data = [];
            }
            $object->setData($attributeCode, join(',', $data));
        }
        if (!$object->hasData($attributeCode)) {
            $object->setData($attributeCode, false);
        }
        return $this;
    }

    /**
     * After Load Attribute Process
     *
     * @param \Magento\Framework\Object $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == 'available_sort_by') {
            $data = $object->getData($attributeCode);
            if ($data) {
                $object->setData($attributeCode, explode(',', $data));
            }
        }
        return $this;
    }
}
