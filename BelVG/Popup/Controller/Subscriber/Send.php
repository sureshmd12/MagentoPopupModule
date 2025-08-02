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

namespace BelVG\Popup\Controller\Subscriber;

use BelVG\Popup\Helper\Config;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class Send
 * @package BelVG\Popup\Controller\Subscriber
 */
class Send extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\SalesRule\Model\Coupon\Massgenerator
     */
    private $couponGenerator;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * Send constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param Config $config
     * @param SubscriberFactory $subscriberFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Magento\SalesRule\Model\Coupon\Massgenerator $couponGenerator
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        Config $config,
        SubscriberFactory $subscriberFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\Coupon\Massgenerator $couponGenerator

    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $jsonFactory;
        $this->config = $config;
        $this->subscriberFactory = $subscriberFactory;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
        $this->ruleFactory = $ruleFactory;
        $this->couponGenerator = $couponGenerator;
    }


    /**
     * @return bool
     */
    protected function validateEmail()
    {
        $email = $this->getRequest()->getParam('email');
        if (!\Zend_Validate::is($email, 'EmailAddress')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $email = $this->getRequest()->getParam('email');

        if ($email) {
            try {
                if (!$this->validateEmail()) {
                    return $resultJson->setData([
                        'hasError' => true,
                        'message' => 'You entered invalid email.'
                    ]);
                }
                $ruleId = $this->config->getConfig('rule_id');
                $rule = $this->ruleFactory->create()->load($ruleId);

                if (!$rule->hasRuleId()) {
                    return $resultJson->setData([
                        'hasError' => true,
                        'message' => __('Invalid data!')
                    ]);
                } else {
                    if($rule->getUseAutoGeneration()) {
                        $data = [
                            'rule_id' => $ruleId,
                            'qty' => '1',
                            'length' => '12',
                            'format' => 'alphanum',
                            'prefix' => '',
                            'suffix' => '',
                            'dash' => '0',
                            'uses_per_coupon' => '0',
                            'uses_per_customer' => '0',
                            'to_date' => ''
                        ];

                        $this->couponGenerator->setData($data);
                        $this->couponGenerator->generatePool();
                        $codes = $this->couponGenerator->getGeneratedCodes();
                        $code = current($codes);
                    } else {
                        $code = $rule->getCouponCode();
                    }

                    $dataObject = new \Magento\Framework\DataObject();
                    $dataObject->setData([
                        'coupon'=>$code,
                    ]);

                    $transport = $this->transportBuilder;
                    $store  = $this->storeManager->getStore();

                    $transport->setTemplateIdentifier('promopopup_coupon_email_template')
                        ->setTemplateOptions([
                                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                                'store' => $store->getId()
                        ])
                        ->setTemplateVars(['data' => $dataObject,'store'=>$store])
                        ->addTo([$email])
                        ->getTransport()
                        ->sendMessage();

                    return $resultJson->setData([
                        'hasError' => false,
                        'message' => __('Coupon sent on your email.')
                    ]);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return $resultJson->setData([
                    'hasError' => true,
                    'message' => $e->getMessage()
                ]);
            } catch (\Exception $e) {
                return $resultJson->setData([
                    'hasError' => true,
                    'message' => $e->getMessage()
                ]);
            }
        }

        return $resultJson->setData([
            'hasError' => true,
            'message' => 'You entered invalid email.'
        ]);
    }
}
