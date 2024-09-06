define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'pallapay_ppg',
                component: 'Pallapay_PPG/js/view/payment/method-renderer/pallapay-method'
            }
        );
        return Component.extend({});
    }
);
