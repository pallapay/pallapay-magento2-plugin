## Pallapay Crypto Payment Extension for Magento 2

Easy to use crypto payment gateway for magento 2, accept crypto in your website and get paid in cash.

#### Installation

* Go to Magento2 root directory
* Create dir app/code/Pallapay/PPG
  ```bash
  mkdir app/code/Pallapay
  mkdir app/code/Pallapay/PPG
  ```
* Upload the extension files to app/code/Pallapay/PPG.
* Run bellow commands
    ```bash
    php bin/magento setup:upgrade
    ```
    ```bash
    // Magento v2.0.x to 2.1.x
    php bin/magento setup:static-content:deploy
    
    // Magento version >2.2.x
    php bin/magento -f setup:static-content:deploy
    ```
    ```bash
    php bin/magento cache:flush
    ```

#### Easy to use

First signup, create API Key and [get you ApiKey, SecretKey from Pallapay website](https://www.pallapay.com)

Then configure the payment method in magento admin dashboard.


#### Contribution

Contributions are highly appreciated either in the form of pull requests for new features, bug fixes or just bug reports.

----------------------------------------------

[Pallapay Website](https://www.pallapay.com)
