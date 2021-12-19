## Customer Attribute System for Magento 2 CE
## Version 2.3.6
Product features:

- Manage attribute relation
+ Apply to only attribute which is dropdown type.
+ Apply to customer registration form, edit form
+ Apply to address registration form, address edit form
+ Apply to Billing & shipping address form

### To request support:

Feel free to contact us via email: support@x-mage2.com

### Demo version:
Frontend:

Register page: http://demo.x-mage2.com/customer/account/create/
Demo account:
username: demouser04@x-mage2.com
password: xmage2demouser

You can change account information and customer address at account page.

###1 - Installation

 * Download the extension
 * Unzip the file
 * Copy the content from the unzip folder to {Magento Root}/app/code

####2 -  Enable Extension
 * php -f bin/magento setup:upgrade
 * php bin/magento setup:static-content:deploy

####3 - Config Extension

Log into your Magento Admin, then go to Customer -> Attributes -> Configuration

###4 - Reindex Customer Grid

If you select 'yes' to show attribute on grid or filterable options, please reindex customer grid at: System -> Tools -> Index Management.
You can wait for cron job to index automatically or use this command: php bin/magento indexer:reindex.
If you are not technical person, please contact with hosting provider to receive support.


