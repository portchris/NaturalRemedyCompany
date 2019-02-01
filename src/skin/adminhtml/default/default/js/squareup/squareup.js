// TODO use all the callback methods and add error parsing
// TODO add the continue part to the payment info
Squareup = {};
Squareup.options = {};
Squareup.paymentForm = {};
Squareup.init = function () {
    Squareup.initOptions();
};
Squareup.initPayment = function () {
    Squareup.paymentForm = new SqPaymentForm(Squareup.options);
    Squareup.paymentForm.build();
};
Squareup.requestCardNonce = function (event) {
    event.preventDefault();
    Squareup.paymentForm.requestCardNonce();
};
Squareup.callbacks = {
    /*
     * callback function: methodsSupported
     * Triggered when: the page is loaded.
     */
    methodsSupported: function (methods) {

        var applePayBtn = document.getElementById('sq-apple-pay');
        var applePayLabel = document.getElementById('sq-apple-pay-label');
        var masterpassBtn = document.getElementById('sq-masterpass');
        var masterpassLabel = document.getElementById('sq-masterpass-label');

        // Only show the button if Apple Pay for Web is enabled
        // Otherwise, display the wallet not enabled message.
        if (methods.applePay === true) {
            applePayBtn.style.display = 'inline-block';
            applePayLabel.style.display = 'none' ;
        }

        // Only show the button if Masterpass is enabled
        // Otherwise, display the wallet not enabled message.
        if (methods.masterpass === true) {
            masterpassBtn.style.display = 'inline-block';
            masterpassLabel.style.display = 'none';
        }
    },

    /*
     * callback function: createPaymentRequest
     * Triggered when: a digital wallet payment button is clicked.
     */
    createPaymentRequest: function () {

        var paymentRequestJson ;
        /* ADD CODE TO SET/CREATE paymentRequestJson */
        return paymentRequestJson ;
    },

    /*
     * callback function: validateShippingContact
     * Triggered when: a shipping address is selected/changed in a digital
     *                 wallet UI that supports address selection.
     */
    validateShippingContact: function (contact) {

        var validationErrorObj ;
        /* ADD CODE TO SET validationErrorObj IF ERRORS ARE FOUND */
        return validationErrorObj ;
    },

    /*
     * callback function: cardNonceResponseReceived
     * Triggered when: SqPaymentForm completes a card nonce request
     */
    cardNonceResponseReceived: function (errors, nonce, cardData) {
        if (errors) {
            // Log errors from nonce generation to the Javascript console
            console.log("Encountered errors:");
            errors.forEach(
                function (error) {
                    console.log('  ' + error.message);
                }
            );

            return;
        }

        // Assign the nonce value to the hidden form field
        document.getElementById('card-nonce').value = nonce;
        console.log('Nonce received');
        // payment.save();
    },

    /*
     * callback function: unsupportedBrowserDetected
     * Triggered when: the page loads and an unsupported browser is detected
     */
    unsupportedBrowserDetected: function () {
        /* PROVIDE FEEDBACK TO SITE VISITORS */
    },

    /*
     * callback function: inputEventReceived
     * Triggered when: visitors interact with SqPaymentForm iframe elements.
     */
    inputEventReceived: function (inputEvent) {
        switch (inputEvent.eventType) {
            case 'focusClassAdded':
                /* HANDLE AS DESIRED */
                break;
            case 'focusClassRemoved':
                /* HANDLE AS DESIRED */
                break;
            case 'errorClassAdded':
                /* HANDLE AS DESIRED */
                break;
            case 'errorClassRemoved':
                /* HANDLE AS DESIRED */
                break;
            case 'cardBrandChanged':
                /* HANDLE AS DESIRED */
                break;
            case 'postalCodeChanged':
                /* HANDLE AS DESIRED */
                break;
        }
    },

    /*
     * callback function: paymentFormLoaded
     * Triggered when: SqPaymentForm is fully loaded
     */
    paymentFormLoaded: function () {
        /* HANDLE AS DESIRED */
    }
};
Squareup.initOptions = function () {
    Squareup.options = {
        applicationId: SquareupApplicationId,
        locationId: SquareupLocationId,
        inputClass: 'sq-input',

        // Customize the CSS for SqPaymentForm iframe elements
        inputStyles: [{
            fontSize: '.9em'
        }],

        // Initialize Apple Pay placeholder ID
        applePay: {
            elementId: 'sq-apple-pay'
        },

        // Initialize Masterpass placeholder ID
        masterpass: {
            elementId: 'sq-masterpass'
        },

        // Initialize the credit card placeholders
        cardNumber: {
            elementId: 'sq-card-number',
            placeholder: '•••• •••• •••• ••••'
        },
        cvv: {
            elementId: 'sq-cvv',
            placeholder: 'CVV'
        },
        expirationDate: {
            elementId: 'sq-expiration-date',
            placeholder: 'MM/YY'
        },
        postalCode: {
            elementId: 'sq-postal-code'
        },

        // SqPaymentForm callback functions
        callbacks: Squareup.callbacks
    };
};