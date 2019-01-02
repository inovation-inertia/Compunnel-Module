<?php

namespace Compunnel\Module\Controller\Noroute;

class Index extends \Magento\Cms\Controller\Noroute\Index
{
	public function __construct(
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
			\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        )
    {
		$this->resultPageFactory = $resultPageFactory;
		$this->scopeConfig = $scopeConfig;
		
	}
    public function aroundExecute(\Magento\Cms\Controller\Noroute\Index $subject, 
        \Closure $proceed)
    {  
    	$enable = $this->scopeConfig->getValue('compunnel/module/enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	if(!$enable)
    	return $proceed();	
    	$resultPage = $this->resultPageFactory->create();
    	$resultPage->addHandle('catalogsearch_advanced_index');
    	return $resultPage;
        
    }
}