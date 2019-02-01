// Set the application ID
var applicationId = "sandbox-sq0idp-7KOoHqg39fq1jF9qBxXd7g";
// var applicationId = SquareupApplicationId;

// Set the location ID
var locationId = "CBASEPcv99YW4QnQsdH1OxRIEHggAQ";
// var locationId = SquareupLocationId;

/*
 * function: requestCardNonce
 *
 * requestCardNonce is triggered when the "Pay with credit card" button is
 * clicked
 *
 * Modifying this function is not required, but can be customized if you
 * wish to take additional action when the form button is clicked.
 */
function requestCardNonce(event)
{

    // Don't submit the form until SqPaymentForm returns with a nonce
    event.preventDefault();

    // Request a nonce from the SqPaymentForm object
    paymentForm.requestCardNonce();
}

// Create and initialize a payment form object
var paymentForm = new SqPaymentForm(
    {
        // Initialize the payment form elements
        applicationId: applicationId,
        locationId: locationId,
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
    }
);