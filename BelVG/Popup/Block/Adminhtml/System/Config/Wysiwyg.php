<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   BelVG
 * @package    BelVG_Popup
 * @copyright  Copyright (c) BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */
namespace BelVG\Popup\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Wysiwyg
 * @package BelVG\Popup\Block\Adminhtml\System\Config
 */
class Wysiwyg extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var WysiwygConfig
     */
    protected $_wysiwygConfig;

    /**
     * Wysiwyg constructor.
     * @param Context $context
     * @param WysiwygConfig $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $config = $this->_wysiwygConfig->getConfig($element);
        $config->setData('hidden',true);
        $element->setData('rows',30);
        $config->setData('settings',[
            'forced_root_block' => "",
            'theme_advanced_path'=>false,
            'valid_children' => '+div[link],+body[link]',


        ]);
        $element->setWysiwyg(true)->setConfig($config);
        return parent::_getElementHtml($element);
    }
}