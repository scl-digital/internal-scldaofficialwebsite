# PayPal Payment Integration - Debugging Guide

## Issue: "PayPal Not Available" Message

If you see the message **"⚠️ PayPal Not Available - The PayPal payment option is temporarily unavailable"**, follow these debugging steps:

### Quick Fix Steps

1. **Clear Browser Cache & Cookies**
   - Press `Ctrl+Shift+Delete` (Windows) or `Cmd+Shift+Delete` (Mac)
   - Clear all cached images and files
   - Close all tabs and restart browser
   - Navigate to checkout page again

2. **Check Browser Console for Errors**
   - Open Developer Tools: Press `F12`
   - Go to **Console** tab
   - Look for red error messages
   - Common messages and meanings:
     - `"Failed to load PayPal SDK"` → Network issue or CDN blocked
     - `"PayPal SDK not available"` → Script didn't load in time
     - `"Max PayPal load attempts exceeded"` → Multiple load failures

3. **If Still Not Working - Use Bank Transfer**
   - Click **"Use Bank Transfer"** button in the fallback panel
   - Complete payment using the bank details provided
   - Upload proof of payment

### Detailed Debugging

#### Step 1: Verify Network Connectivity
1. Open DevTools (`F12`)
2. Go to **Network** tab
3. Reload the page
4. Look for a request to `paypal.com/sdk/js...`
5. If missing → PayPal SDK didn't attempt to load
6. If red error → Check status code:
   - **401**: Authentication issue
   - **403**: Access forbidden
   - **404**: URL not found
   - **5xx**: Server error at PayPal

#### Step 2: Check Console Logs
1. Open DevTools Console (`F12` → Console tab)
2. Look for messages starting with:
   - `"loadAndRenderPayPalButton called"` → Function triggered
   - `"Loading PayPal SDK, attempt X"` → SDK loading initiated
   - `"PayPal SDK loaded successfully"` → SDK script loaded
   - `"PayPal SDK available immediately"` → SDK ready to render
   - `"Creating PayPal Buttons instance"` → Button creation started
   - `"PayPal button rendered successfully"` → SUCCESS! ✓

#### Step 3: Test Company Settings API
1. Open new browser tab
2. Go to: `http://localhost/mostrecent.softwarecreativelabs.com/api/company_settings.php`
3. Should see JSON response with `"paypal_client_id"` field
4. If error → Database issue with company settings

#### Step 4: Manually Test PayPal SDK URL
1. In DevTools Console, paste:
   ```javascript
   fetch('https://www.paypal.com/sdk/js?client-id=AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm&components=buttons&currency=ZAR')
     .then(r => r.status === 200 ? 'SUCCESS ✓' : 'FAILED: ' + r.status)
     .catch(e => 'ERROR: ' + e.message)
     .then(msg => console.log(msg))
   ```
2. Should print `"SUCCESS ✓"`
3. If error → Internet/CDN access issue

### Network Issues

#### If Behind Corporate Firewall
- PayPal SDK is hosted on `paypal.com` CDN
- May be blocked by corporate firewall
- **Solution**: Use Bank Transfer method or configure proxy

#### If Using VPN/Proxy
- Some VPNs block payment gateway scripts
- Try temporarily disabling VPN
- Check if issue persists

#### If Using Brave Browser
- Brave blocks some third-party scripts
- Go to Settings → Privacy & security
- Disable "Block scripts" option
- Reload page

### PayPal Configuration Issues

#### Empty PayPal Client ID
1. Check if company settings have PayPal Client ID configured
2. Go to `http://localhost/mostrecent.softwarecreativelabs.com/api/company_settings.php`
3. If `"paypal_client_id"` is empty → Fallback to demo sandbox ID
4. Demo ID should work for testing

#### Demo ID Details
- Client ID: `AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm`
- Mode: Sandbox (for testing only)
- Status: ✓ Active and working

### Server-Side Verification

#### Check PayPal PHP Library
Run in command prompt:
```powershell
cd c:\xampp\htdocs\mostrecent.softwarecreativelabs.com
php -r "require 'vendor/autoload.php'; echo (class_exists('PayPalCheckoutSdk\\Core\\PayPalHttpClient') ? 'OK' : 'MISSING'), PHP_EOL;"
```
Should print: `OK`

#### Test PayPal Order Creation
1. Open DevTools Console
2. Paste:
   ```javascript
   fetch('../api/paypal_create_order.php', {
     method: 'POST',
     headers: {'Content-Type': 'application/json'},
     body: JSON.stringify({amount: 100, currency: 'ZAR', items: []})
   }).then(r=>r.json()).then(d=>console.log(JSON.stringify(d, null, 2)))
   ```
3. Should see order data with `"order.id"`

### Browser Compatibility

| Browser | Status | Notes |
|---------|--------|-------|
| Chrome | ✓ Tested | Full support |
| Firefox | ✓ Tested | Full support |
| Safari | ✓ Tested | Full support |
| Edge | ✓ Tested | Full support |
| Brave | ⚠️ | May need script blocking disabled |
| Internet Explorer 11 | ✗ | Not supported |

### Alternative Solution: Bank Transfer

If PayPal continues to be unavailable:
1. Select **Bank Transfer** payment method
2. Use provided bank details:
   - **Bank**: FNB / RMB
   - **Account**: SCL Digital Agency (Pty) Ltd
   - **Number**: 63134146992
   - **Branch**: 250655
   - **Reference**: Use provided order reference
3. Upload proof of payment (PDF/PNG/JPG)
4. Order will be processed upon verification

### Getting Help

If issue persists after trying all steps above:

1. **Clear all logs from DevTools Console**
2. **Reload page and immediately check console**
3. **Copy ALL console messages**
4. **Contact support with:**
   - Browser name and version
   - Operating system
   - Console error messages
   - Network tab screenshot (if possible)
   - Whether bank transfer works

---

## Recent Improvements (v2.1)

✓ Enhanced PayPal SDK loading with retry logic (up to 5 attempts)
✓ Better error detection and fallback handling
✓ Comprehensive console logging for debugging
✓ Improved timeout handling (8 second timeout)
✓ Network error recovery
✓ Better user feedback with fallback UI

---

## Contact Support

Email: support@softwarecreativelabs.com
Hours: Monday - Friday, 9AM - 5PM SAST
Response Time: Within 2 business hours
