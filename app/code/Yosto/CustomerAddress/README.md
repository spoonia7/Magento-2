## Customer Attribute System for Magento 2 CE
## Version 2.3.6
Product features:

- User can add attribute to customer address templates.
- Able to change entity type of attribute, customer => address and vice versa.
- Custom validation for shipping/billing form fields
- Allow attribute to show on address create page and address edit page
- Show custom address attributes to billing/shipping registration form.
- Apply custom attribute for guest in checkout process.
- Show custom attributes in the admin order, the customer oder and the order email.
- Support One Page Checkout (OPC) extension such as Swissup FireCheckout.

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


