<?php

namespace Compunnel\Module\Model;
 
class Attachement extends \Magento\Framework\Mail\Template\TransportBuilder
{
    //Here to Attached a File   
    public function addAttachment($orderCsv)
    {
        $this->message->createAttachment(
            $orderCsv,
            'text/csv',
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            'orderreport.csv'
        );
        return $this;
    }
}