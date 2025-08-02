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

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Upgrades data for Braintree module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $this->installFileData();
        }
    }

    protected function installFileData()
    {
        $mediaPath = BP . '/pub/media/promopopup/';
        $dataPath = dirname(__FILE__) . '/Data/promopopup/';

        $folders = [
            'images/christmas/popup',
            'images/black-friday/popup',
            'images/cyber-monday/popup',
            'images/universal/popup',
            'templates'
        ];

        $files = [
            'images/christmas/popup/1.png',
            'images/christmas/popup/2.png',
            'images/christmas/popup/3.png',
            'images/christmas/popup/4.png',
            'images/christmas/popup/5.png',
            'images/black-friday/popup/black-friday.png',
            'images/black-friday/popup/black-friday-2.png',
            'images/black-friday/popup/black-friday-3.png',
            'images/black-friday/popup/black-friday-4.png',
            'images/black-friday/popup/black-friday-5.png',
            'images/black-friday/popup/black-friday-6.png',
            'images/black-friday/popup/black-friday-7.png',
            'images/black-friday/popup/black-friday-8.png',
            'images/black-friday/popup/black-friday-9.png',
            'images/cyber-monday/popup/cyber-monday.png',
            'images/cyber-monday/popup/cyber-monday-2.png',
            'images/cyber-monday/popup/cyber-monday-3.png',
            'images/cyber-monday/popup/cyber-monday-4.png',
            'images/cyber-monday/popup/cyber-monday-5.png',
            'templates/1-1_black-friday_newsletter.html',
            'templates/1-2_black-friday_coupon.html',
            'templates/1-3_black-friday_banner.html',
            'templates/2-1_black-friday_newsletter.html',
            'templates/2-2_black-friday_coupon.html',
            'templates/2-3_black-friday_banner.html',
            'templates/3-1_black-friday_newsletter.html',
            'templates/3-2_black-friday_coupon.html',
            'templates/3-3_black-friday_banner.html',
            'templates/4-1_black-friday_newsletter.html',
            'templates/4-2_black-friday_coupon.html',
            'templates/4-3_black-friday_banner.html',
            'templates/5-1_black-friday_newsletter.html',
            'templates/5-2_black-friday_coupon.html',
            'templates/5-3_black-friday_banner.html',
            'templates/6-1_black-friday_newsletter.html',
            'templates/6-2_black-friday_coupon.html',
            'templates/6-3_black-friday_banner.html',
            'templates/7-1_black-friday_newsletter.html',
            'templates/7-2_black-friday_coupon.html',
            'templates/7-3_black-friday_banner.html',
            'templates/8-1_black-friday_newsletter.html',
            'templates/8-2_black-friday_coupon.html',
            'templates/8-3_black-friday_banner.html',
            'templates/9-1_black-friday_newsletter.html',
            'templates/9-2_black-friday_coupon.html',
            'templates/9-3_black-friday_banner.html',
            'templates/1-1_cyber-monday_newsletter.html',
            'templates/1-2_cyber-monday_coupon.html',
            'templates/1-3_cyber-monday_banner.html',
            'templates/2-2_cyber-monday_coupon.html',
            'templates/2-1_cyber-monday_newsletter.html',
            'templates/2-3_cyber-monday_banner.html',
            'templates/3-2_cyber-monday_coupon.html',
            'templates/3-3_cyber-monday_banner.html',
            'templates/3-1_cyber-monday_newsletter.html',
            'templates/4-2_cyber-monday_coupon.html',
            'templates/4-3_cyber-monday_banner.html',
            'templates/4-1_cyber-monday_newsletter.html',
            'templates/5-1_cyber-monday_newsletter.html',
            'templates/5-2_cyber-monday_coupon.html',
            'templates/5-3_cyber-monday_banner.html',
            'templates/10-1_christmas_newsletter.html',
            'templates/11-1_christmas_newsletter.html',
            'templates/12-1_christmas_newsletter.html',
            'templates/13-1_christmas_newsletter.html',
            'templates/14-1_christmas_newsletter.html',
            'templates/10-2_christmas_coupon.html',
            'templates/11-2_christmas_coupon.html',
            'templates/12-2_christmas_coupon.html',
            'templates/13-2_christmas_coupon.html',
            'templates/14-2_christmas_coupon.html',
            'templates/10-3_christmas_banner.html',
            'templates/11-3_christmas_banner.html',
            'templates/12-3_christmas_banner.html',
            'templates/13-3_christmas_banner.html',
            'templates/14-3_christmas_banner.html',


            'images/universal/popup/1.png',
            'images/universal/popup/2.png',
            'images/universal/popup/3.png',
            'images/universal/popup/4.png',
            'images/universal/popup/5.png',
            'templates/15-1_universal_newsletter.html',
            'templates/16-1_universal_newsletter.html',
            'templates/17-1_universal_newsletter.html',
            'templates/18-1_universal_newsletter.html',
            'templates/19-1_universal_newsletter.html',
            'templates/15-2_universal_coupon.html',
            'templates/16-2_universal_coupon.html',
            'templates/17-2_universal_coupon.html',
            'templates/18-2_universal_coupon.html',
            'templates/19-2_universal_coupon.html',
            'templates/15-3_universal_banner.html',
            'templates/16-3_universal_banner.html',
            'templates/17-3_universal_banner.html',
            'templates/18-3_universal_banner.html',
            'templates/19-3_universal_banner.html',
        ];

        /*Create Folder*/
        foreach ($folders as $item) {
            @\mkdir($mediaPath . $item, 0777, true);
        }

        /*Copy files*/
        foreach ($files as $item) {
            @\copy($dataPath . $item, $mediaPath . $item);
        }

    }
}
