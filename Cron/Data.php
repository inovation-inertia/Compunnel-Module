<?php

namespace Compunnel\Module\Cron;
 
class Data 
{
	/**
	 * 
	 * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
	 * @param Magento\Framework\Filesystem $filesystem
	 * @param \Magento\Framework\App\ResourceConnection $resourceConnection
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $datetime
	 * @param \Compunnel\Module\Helper\Data $helper
	 * @param Magento\Framework\App\Filesystem\DirectoryList $dirList
	 * @param Magento\Framework\App\Response\Http\FileFactory $fileFactory
	 */
	public function __construct(
			\Magento\Framework\App\Action\Context $context,
			\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
			\Magento\Framework\Filesystem $filesystem,
			\Magento\Framework\App\ResourceConnection $resourceConnection,
			\Magento\Framework\Stdlib\DateTime\TimezoneInterface $datetimeZone,
			\Compunnel\Module\Helper\Data $helper,
			\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
			\Magento\Framework\App\Filesystem\DirectoryList $directoryList,
			\Magento\Framework\App\Response\Http\FileFactory $fileFactory
	){
		$this->datetime      = $dateTime;
		$this->timeZone = $datetimeZone;
		$this->orderCollection = $orderCollection;
		$this->fileFactory = $fileFactory;
		$this->connection = $resourceConnection;
		$this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
		$this->direcorList = $directoryList;
		$this->helper = $helper;
		
	}
   
	
	public function execute()
    {
    	//Check Module is Enabled or not
        if(!$this->helper->isEnabled())
    		return $this;
        //Start Code Regarding to Report Generation
    	try{
    	     $toDate = $this->timeZone->date()->format('Y-m-d H:i:s');
	         $fromDate = date("Y-m-d H:i:s", strtotime("- 1hour", strtotime($toDate)));
             $orderData  = $this->orderCollection->create()->
             addFieldToFilter('created_at',[
    			                             'from' => $fromDate,
    			                             'to'   => $toDate,
                                           ]);
    	    $this->fileFactory->create('orderreport.csv', $this->getCsvFile($orderData->getData()), 'var');
    	    $path =  $this->direcorList->getPath(
                \Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR).'/Report/orderreport.csv';
    	     $this->helper->sendEmail($path);
    	    }catch(\Exception $e){
        		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/compunnel.log');
        		$logger = new \Zend\Log\Logger();
        		$logger->addWriter($writer);
        		$logger->info($e->getMessage());
    	}
       //End Code Regarding to Report Generation 
        
    }
    
    /**
     * 
     * @param $orderinfo
     */
    protected function getCsvFile($orderinfo){
    	 
    	$file = 'Report/orderrport.csv';
    	$this->directory->create('Report');
    	$stream = $this->directory->openFile($file, 'w+');
    	$stream->lock();
    	$connection  = $this->connection->getConnection();
    	$tableName = $connection->getTableName('sales_order');
    	$fields = array_keys($connection->describeTable($tableName));
    	
    	$stream->writeCsv($fields);
    	foreach ($orderinfo as $item) {
    		
    		$stream->writeCsv($this->getRowData($item, $fields));
    	}
    	
    	$stream->unlock();
    	$stream->close();
    	 
    }
    /**
     * 
     * @param $item
     * @param $fields
     */
    protected function getRowData($item,$fields)
    {
    	 
    	$row_info = [];
    	foreach ($fields as $column) {
    		if(isset($item[$column]))
    		$row_info[] = $item[$column];
    	}
    	
    	return $row;
    }
}
