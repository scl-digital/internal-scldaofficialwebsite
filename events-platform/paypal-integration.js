// PayPal Integration for Events Platform
const DEMO_PAYPAL_CLIENT_ID = 'AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm';
let _paypalScriptLoaded = false;
let _paypalButtonsRendered = false;
let _paypalLoadAttempts = 0;
let _paypalLoadInProgress = false;
const MAX_PAYPAL_LOAD_ATTEMPTS = 5;

function loadAndRenderPayPalButton() {
  console.log('loadAndRenderPayPalButton called');

  if (_paypalButtonsRendered) {
    console.log('PayPal button already rendered');
    return;
  }

  if (_paypalLoadInProgress) {
    console.log('PayPal loading already in progress');
    return;
  }

  if (window.paypal && window.paypal.Buttons) {
    console.log('PayPal SDK already available, rendering button');
    renderPayPalButton();
    return;
  }

  _paypalLoadInProgress = true;
  _paypalLoadAttempts++;

  if (_paypalLoadAttempts > MAX_PAYPAL_LOAD_ATTEMPTS) {
    console.error('Max PayPal load attempts exceeded');
    _paypalLoadInProgress = false;
    showPayPalFallback();
    return;
  }

  console.log('Loading PayPal SDK, attempt', _paypalLoadAttempts);

  fetch('../api/get_payment_settings.php')
    .then(r => {
      if (!r.ok) throw new Error('Failed to fetch payment settings: ' + r.status);
      return r.json();
    })
    .then(cfg => {
      let clientId = '';
      let currency = 'ZAR';
      try {
        clientId = cfg.data && cfg.data.paypal_client_id ? cfg.data.paypal_client_id : '';
        currency = cfg.data && cfg.data.currency ? cfg.data.currency : 'ZAR';
      } catch (e) {
        console.error('Error parsing payment settings:', e);
        clientId = '';
      }

      if (!clientId) {
        clientId = DEMO_PAYPAL_CLIENT_ID;
        console.warn('Using DEMO_PAYPAL_CLIENT_ID for PayPal SDK (sandbox)');
      } else {
        console.log('Using PayPal Client ID from settings');
      }

      loadPayPalScript(clientId, currency);
    })
    .catch(err => {
      console.warn('Error fetching payment settings, using demo client ID:', err.message);
      _paypalLoadInProgress = false;
      loadPayPalScript(DEMO_PAYPAL_CLIENT_ID, 'ZAR');
    });
}

function loadPayPalScript(clientId, currency) {
  console.log('loadPayPalScript called with currency:', currency);
  window._paypalCurrency = currency || 'ZAR';

  const existingScript = document.querySelector('script[src*="paypal.com/sdk/js"]');
  if (existingScript) {
    console.log('PayPal script already exists');
    if (window.paypal && window.paypal.Buttons) {
      _paypalLoadInProgress = false;
      _paypalScriptLoaded = true;
      renderPayPalButton();
      return;
    }
  }

  const script = document.createElement('script');
  script.src = `https://www.paypal.com/sdk/js?client-id=${encodeURIComponent(clientId)}&components=buttons&currency=${encodeURIComponent(currency)}&intent=capture&commit=true&enable-funding=card&disable-funding=credit,paylater`;
  script.async = true;

  script.onload = () => {
    console.log('PayPal script loaded');
    _paypalScriptLoaded = true;

    if (window.paypal && window.paypal.Buttons) {
      _paypalLoadInProgress = false;
      renderPayPalButton();
    } else {
      setTimeout(() => {
        if (window.paypal && window.paypal.Buttons) {
          _paypalLoadInProgress = false;
          renderPayPalButton();
        } else {
          _paypalLoadInProgress = false;
          showPayPalFallback();
        }
      }, 2000);
    }
  };

  script.onerror = (err) => {
    console.error('Failed to load PayPal script:', err);
    _paypalLoadInProgress = false;
    showPayPalFallback();
  };

  document.head.appendChild(script);
}

function renderPayPalButton() {
  console.log('renderPayPalButton called');

  if (!window.paypal || !window.paypal.Buttons) {
    console.error('PayPal SDK not available');
    showPayPalFallback();
    return;
  }

  if (_paypalButtonsRendered) {
    console.log('PayPal button already rendered');
    return;
  }

  _paypalButtonsRendered = true;

  const container = document.getElementById('paypal-button-container');
  if (!container) {
    console.error('PayPal container not found');
    _paypalButtonsRendered = false;
    showPayPalFallback();
    return;
  }

  try {
    paypal.Buttons({
      fundingSource: paypal.FUNDING.CARD,
      style: {
        layout: 'vertical',
        color: 'black',
        shape: 'rect',
        label: 'pay'
      },
      createOrder: function () {
        console.log('PayPal createOrder called');
        const ticketCount = parseInt(document.getElementById('ticketCount').value) || 1;
        const ticketPrice = 500;
        const total = (ticketCount * ticketPrice).toFixed(2);

        window._paypalExpected = total;
        const currency = window._paypalCurrency || 'ZAR';

        const items = [{
          name: 'Event Ticket',
          unit_amount: { currency_code: currency, value: ticketPrice.toFixed(2) },
          quantity: String(ticketCount)
        }];

        console.log('Creating PayPal order with total:', total);
        return fetch('../api/paypal_create_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ amount: total, currency: currency, items: items })
        })
          .then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
          })
          .then(resp => {
            if (resp.success && resp.order && resp.order.id) {
              console.log('Order created:', resp.order.id);
              return resp.order.id;
            }
            throw new Error(resp.error || 'Failed to create order');
          })
          .catch(err => {
            console.error('Error creating PayPal order:', err);
            alert('Error creating payment order. Please try again.');
            throw err;
          });
      },
      onApprove: function (data) {
        console.log('PayPal onApprove, orderID:', data.orderID);
        return fetch('../api/paypal_capture_order.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            orderID: data.orderID,
            expected_amount: window._paypalExpected || null,
            expected_currency: window._paypalCurrency || null
          })
        })
          .then(r => {
            if (!r.ok) throw new Error('Network response was not ok: ' + r.status);
            return r.json();
          })
          .then(resp => {
            console.log('PayPal capture response:', resp);
            if (resp.success) {
              // Save booking and generate ticket
              return saveBookingAndGenerateTicket(data.orderID);
            } else {
              alert('Payment capture failed: ' + (resp.error || 'Unknown error'));
            }
          })
          .catch(err => {
            console.error('Error capturing PayPal payment:', err);
            alert('Payment error: ' + err.message);
          });
      },
      onError: function (err) {
        console.error('PayPal onError:', err);
        alert('Payment error occurred. Please try again.');
      },
      onCancel: function (data) {
        console.log('PayPal payment cancelled');
        alert('Payment was cancelled.');
      }
    }).render('#paypal-button-container')
      .then(() => {
        console.log('PayPal button rendered successfully');
      })
      .catch(err => {
        console.error('Error rendering PayPal button:', err);
        _paypalButtonsRendered = false;
        showPayPalFallback();
      });
  } catch (err) {
    console.error('Error in renderPayPalButton:', err);
    _paypalButtonsRendered = false;
    showPayPalFallback();
  }
}

function showPayPalFallback() {
  console.log('PayPal fallback invoked');
  const fallback = document.getElementById('paypal-fallback-payment');
  if (fallback) {
    fallback.style.display = 'block';
  }
}

function getEventId() {
  const urlParams = new URLSearchParams(window.location.search);
  return parseInt(urlParams.get('id')) || 1;
}

function saveBookingAndGenerateTicket(paypalOrderId) {
  const bookingData = {
    event_id: getEventId(),
    customer_name: document.getElementById('name').value,
    customer_email: document.getElementById('email').value,
    organization: document.getElementById('organization').value || '',
    ticket_count: parseInt(document.getElementById('ticketCount').value),
    total_amount: window._paypalExpected,
    currency: window._paypalCurrency,
    paypal_order_id: paypalOrderId
  };

  return fetch('../api/save_event_booking.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(bookingData)
  })
    .then(r => r.json())
    .then(resp => {
      if (resp.success) {
        showSuccessMessage(resp.booking_reference, resp.customer_email, resp.email_sent);
      } else {
        alert('Booking save failed: ' + (resp.error || 'Unknown error'));
      }
    })
    .catch(err => {
      console.error('Error saving booking:', err);
      alert('Error saving booking: ' + err.message);
    });
}

function showSuccessMessage(bookingRef, email, emailSent) {
  // Redirect to success page with booking details
  const params = new URLSearchParams({
    ref: bookingRef,
    name: document.getElementById('name').value,
    email: email,
    tickets: document.getElementById('ticketCount').value,
    amount: window._paypalExpected,
    currency: window._paypalCurrency,
    email_sent: emailSent ? 'true' : 'false'
  });

  window.location.href = 'payment-success.html?' + params.toString();
}
