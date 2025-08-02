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

use Magento\Customer\Api\AccountManagementInterface as CustomerAccountManagement;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class NewAction
 * @package BelVG\Popup\Controller\Subscriber
 */
class NewAction extends \Magento\Newsletter\Controller\Subscriber
{
    /**
     * @var CustomerAccountManagement
     */
    protected $accountManagement;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * NewAction constructor.
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     * @param CustomerAccountManagement $accountManagement
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl,
        CustomerAccountManagement $accountManagement,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->accountManagement = $accountManagement;
        parent::__construct(
            $context,
            $subscriberFactory,
            $customerSession,
            $storeManager,
            $customerUrl
        );
        $this->resultJsonFactory = $jsonFactory;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function validateEmailAvailable()
    {
        $email = $this->getRequest()->getParam('email');
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        if ($this->_customerSession->getCustomerDataObject()->getEmail() !== $email
            && !$this->accountManagement->isEmailAvailable($email, $websiteId)
        ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    protected function validateGuestSubscription()
    {
        if ($this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')
                ->getValue(
                    \Magento\Newsletter\Model\Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ) != 1
            && !$this->_customerSession->isLoggedIn()
        ) {
            return false;
        } else {
            return true;
        }
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
                if (!$this->validateGuestSubscription()) {
                    return $resultJson->setData([
                            'hasError' => true,
                            'message' => 'The subscription isn\'t avaliable for guests.'
                        ]);
                }

                if (!$this->validateEmailAvailable()) {
                    return $resultJson->setData([
                            'hasError' => true,
                            'message' => 'Your email is already assigned.'
                        ]);
                }

                $status = $this->_subscriberFactory
                    ->create()
                    ->subscribe($email);

                if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE) {
                    return $resultJson->setData([
                            'hasError' => false,
                            'message' => 'The confirmation request has been sent.'
                        ]);
                } else {
                    return $resultJson->setData([
                            'hasError' => false,
                            'message' => 'Thank you for your subscription.'
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
