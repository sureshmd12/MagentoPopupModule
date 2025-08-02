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

use Magento\Framework\Serialize\Serializer\Json;
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
     * @var Json
     */
    protected $serializer;

    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory,
        Json $serializer
    ) {
        $this->blockFactory = $blockFactory;
        $this->serializer = $serializer;
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
                'conditions_serialized' => $this->serializer->serialize([
                    'type' => 'Magento\SalesRule\Model\Rule\Condition\Combine',
                    'attribute' => null,
                    'operator' => null,
                    'value' => '1',
                    'is_value_processed' => null,
                    'aggregator' => 'all',
                ]),
                'actions_serialized' => $this->serializer->serialize([
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

        $setup->endSetup();
    }
}
