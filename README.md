/*Manual Process*/

1.Unzip extension package and upload them into Magento root directory
Magento root directory -: app/code/Compunnel/Module

2.After Extract a Package Run Following Command -:
a.php bin/magento setup:upgrade
b.php bin/magento setup:static-content:deploy
c.php bin/magento cache:flush 
[Mandatory only @ M2.1 But No Need M2.2.5 Later Version]

3.After Install a Module First Enable a Module From Configuration
Store >> Configuration >> Compunnel (Tab)


>> Module Feature's 
1.Compatibility : 2.2.x & 2.3.x  
2.Cover a Order Report Attachment through a Email
3.404 page Customized through a Advanced Search 

