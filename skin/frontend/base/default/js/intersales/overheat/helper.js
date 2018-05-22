var overheatHelper = overheatHelper || {};

overheatHelper.submitCheckoutStep = function(page, opt) {
    opt = opt || {};
    opt['current_page'] = page;
    overheat('checkout_step', opt);
};

/*var overheat = overheat || function (command, params) {
        console.log('overheat testinstance called with: %o -> %o', command, params);
    };*/