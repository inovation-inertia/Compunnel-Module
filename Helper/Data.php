<?php

namespace Compunnel\Module\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
  	/**
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
	 * @param \Custom\Module\Model\Mailattachement $transportBuilder
	 */
    public function __construct(
    		\Magento\Framework\App\Helper\Context $context,
    		\Magento\Store\Model\StoreManagerInterface $storeManager,
    		\Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
    		\Custom\Module\Model\Mailattachement $transportBuilder
    ) {
        $this->_storeManager = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        parent::__construct($context);
    }
    

    public function sendEmail($Report)
    {
    	
         $variables['compunnel_rec_name'] = $this->scopeConfig->getValue('compunnel/module/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
         $variables['storename'] = $storename;
         $name =$this->scopeConfig->getValue('compunnel/module/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
         $store_name = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
         $email = $this->scopeConfig->getValue('compunnel/module/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        $storename = $storename ? $storename: "Default Store";         
        $store_email = $this->scopeConfig->getValue('compunnel/module/store-email', 
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $store_email= $store_email ? $store_email:"admin@store.com";
        try {
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier('order_report_email')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($variables)
                ->setFrom([
                    'name' => $store_name,
                    'email'=> $store_email,
                ])
                ->addTo($email, $name)
                ->addAttachment(file_get_contents($Report));
 
        
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            
            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {     
          $this->messageManager->addError($e->getMessage()); 
        }
        
    }

    public function isEnabled()
    {
       $isEnabled =$this->scopeConfig->getValue('compunnel/module/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());	
       return $isEnabled;
    }
}