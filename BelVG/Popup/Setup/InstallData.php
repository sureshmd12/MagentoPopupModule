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
 * @package    BelVG_ThankYouPage
 * @copyright  Copyright (c) 2010 - 2018 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

namespace BelVG\Popup\Setup;

use Magento\Framework\Serialize\JsonConverter;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected  $blockFactory;

    /**
     * @var JsonConverter
     */
    protected $jsonConverter;

    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        JsonConverter $jsonConverter
    ) {
        $this->blockFactory = $blockFactory;
        $this->jsonConverter = $jsonConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $salesrule = $setup->getTable('salesrule');
        $salesrule_web = $setup->getTable('salesrule_website');
        $salesrule_group = $setup->getTable('salesrule_customer_group');
        $connection = $setup->getConnection();

        $connection->insert(
            $salesrule,
            [
                'name' => 'BelVG Promo Popup: Coupons 5% OFF',
                'uses_per_customer' => 0,
                'is_active' => 1,
                'conditions_serialized' => $this->jsonConverter->convert([
                    'type' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                    'attribute' => null,
                    'operator' => null,
                    'value' => '1',
                    'is_value_processed' => null,
                    'aggregator' => 'all',
                ]),
                'actions_serialized' => $this->jsonConverter->convert([
                    'type' => 'Magento\SalesRule\Model\Rule\Condition\Product\Combine',
                    'attribute' => null,
                    'operator' => null,
                    'value' => '1',
                    'is_value_processed' => null,
                    'aggregator' => 'all',
                ]),
                'stop_rules_processing' => 0,
                'is_advanced' => 1,
                'sort_order' => 0,
                'simple_action' => 'by_percent',
                'discount_amount' => '5.0000',
                'discount_step' => 0,
                'apply_to_shipping' => 0,
                'times_used' => 1,
                'is_rss' => 1,
                'coupon_type' => 2,
                'use_auto_generation' => 1,
                'uses_per_coupon' => 0,
                'simple_free_shipping' => 0
            ]
        );

        $rule_id = $connection->lastInsertId($salesrule);

        $connection->insert(
            $salesrule_web,
            [
                'rule_id' => $rule_id,
                'website_id' => 1,
            ]
        );

        for ($i = 0; $i < 4; $i++) {
            $connection->insert(
                $salesrule_group,
                [
                    'rule_id' => $rule_id,
                    'customer_group_id' => $i,
                ]
            );
        };

        $data = [
            'scope' => 'default',
            'scope_id' => 0,
            'path' => 'promopopup/settings/rule_id',
            'value' => $rule_id,
        ];

        $setup->getConnection()
            ->insertOnDuplicate($setup->getTable('core_config_data'), $data, ['value']);

        $this->installFileData();

        $setup->endSetup();
    }

    /**
     * @param array $data
     * @return \Magento\Cms\Model\Block
     */
    protected function saveCmsBlock($data)
    {
        $cmsBlock = $this->blockFactory->create();
        $cmsBlock->getResource()->load($cmsBlock, $data['identifier']);
        if (!$cmsBlock->getData()) {
            $cmsBlock->setData($data);
        } else {
            $cmsBlock->addData($data);
        }
        $cmsBlock->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID]);
        $cmsBlock->setIsActive(1);
        $cmsBlock->save();
        return $cmsBlock;
    }


    protected function installFileData() {
        $mediaPath = BP . '/pub/media/promopopup/';
        $dataPath = dirname(__FILE__) . '/Data/promopopup/';

        $folders =  [
                'css',
                'images/halloween/popup1',
                'images/halloween/popup2',
                'images/halloween/popup3',
                'images/halloween/popup4',
                'templates'
            ];

        $files =  [
            'css/halloween/styles.css',
            'images/halloween/popup1/halloween.png',
            'images/halloween/popup2/halloween.png',
            'images/halloween/popup3/halloween.png',
            'images/halloween/popup4/halloween.png',
            'templates/1-1_halloween_newsletter.html',
            'templates/1-2_halloween_coupon.html',
            'templates/1-3_halloween_banner.html',
            'templates/2-1_halloween_newsletter.html',
            'templates/2-2_halloween_coupon.html',
            'templates/2-3_halloween_banner.html',
            'templates/3-1_halloween_newsletter.html',
            'templates/3-2_halloween_coupon.html',
            'templates/3-3_halloween_banner.html',
            'templates/4-1_halloween_newsletter.html',
            'templates/5-2_halloween_coupon.html',
            'templates/6-3_halloween_banner.html',
        ];

        /*Create Folder*/
        foreach ($folders as $item) {
            @\mkdir($mediaPath.$item,0777,true);
        }

        /*Copy files*/
        foreach ($files as $item) {
            @\copy($dataPath.$item, $mediaPath.$item);
        }

    }
}
