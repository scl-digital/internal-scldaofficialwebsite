/**
 * SCL SHIELD — SECURE GATEWAY
 * gateway.js
 *
 * Handles:
 *   - Animated canvas background
 *   - Country/language selection
 *   - Token request (POST /api/token)
 *   - Secure content load (GET /secure/home)
 *   - Panel transitions
 *   - Session management
 */

/* ─────────────────────────────────────────────
   1. CANVAS BACKGROUND — Animated grid/scan
───────────────────────────────────────────── */
(function initCanvas() {
  const canvas = document.getElementById('bg-canvas');
  const ctx    = canvas.getContext('2d');

  let W, H, animFrame;
  let scanY = 0;
  let dots  = [];

  function resize() {
    W = canvas.width  = window.innerWidth;
    H = canvas.height = window.innerHeight;
    buildDots();
  }

  function buildDots() {
    dots = [];
    const cols = Math.floor(W / 60);
    const rows = Math.floor(H / 60);
    for (let c = 0; c <= cols; c++) {
      for (let r = 0; r <= rows; r++) {
        dots.push({
          x: c * 60,
          y: r * 60,
          alpha: Math.random() * 0.5 + 0.1
        });
      }
    }
  }

  function draw(ts) {
    ctx.clearRect(0, 0, W, H);

    // Grid lines
    ctx.strokeStyle = 'rgba(0, 255, 178, 0.04)';
    ctx.lineWidth = 1;
    const cols = Math.floor(W / 60);
    const rows = Math.floor(H / 60);

    for (let c = 0; c <= cols; c++) {
      ctx.beginPath();
      ctx.moveTo(c * 60, 0);
      ctx.lineTo(c * 60, H);
      ctx.stroke();
    }
    for (let r = 0; r <= rows; r++) {
      ctx.beginPath();
      ctx.moveTo(0, r * 60);
      ctx.lineTo(W, r * 60);
      ctx.stroke();
    }

    // Intersection dots
    dots.forEach(d => {
      const dist = Math.abs(d.y - scanY);
      const boost = Math.max(0, 1 - dist / 120);
      ctx.beginPath();
      ctx.arc(d.x, d.y, 1.5, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(0, 255, 178, ${d.alpha + boost * 0.6})`;
      ctx.fill();
    });

    // Scan line
    scanY = (scanY + 0.8) % H;
    const grad = ctx.createLinearGradient(0, scanY - 60, 0, scanY + 60);
    grad.addColorStop(0,   'rgba(0,255,178,0)');
    grad.addColorStop(0.5, 'rgba(0,255,178,0.08)');
    grad.addColorStop(1,   'rgba(0,255,178,0)');
    ctx.fillStyle = grad;
    ctx.fillRect(0, scanY - 60, W, 120);

    animFrame = requestAnimationFrame(draw);
  }

  window.addEventListener('resize', resize);
  resize();
  animFrame = requestAnimationFrame(draw);
})();


/* ─────────────────────────────────────────────
   2. LANGUAGE MAP
───────────────────────────────────────────── */
const LANG_MAP = {
  ZA:    'EN — ENGLISH',
  US:    'EN — ENGLISH',
  GB:    'EN — ENGLISH',
  NG:    'EN — ENGLISH',
  KE:    'EN — ENGLISH',
  FR:    'FR — FRANÇAIS',
  DE:    'DE — DEUTSCH',
  OTHER: 'EN — ENGLISH',
};


/* ─────────────────────────────────────────────
   3. TOKEN STORE (in-memory, single-use)
   In production: use server-side Redis/memory
───────────────────────────────────────────── */
const TokenStore = (() => {
  const store = new Map();

  function generate() {
    const raw   = crypto.getRandomValues(new Uint8Array(18));
    const token = btoa(String.fromCharCode(...raw))
      .replace(/[+/=]/g, '')
      .substring(0, 24)
      .toUpperCase();

    store.set(token, {
      created: Date.now(),
      expires: Date.now() + 60000,   // 60 second TTL
      used: false,
    });

    return token;
  }

  function validate(token) {
    const data = store.get(token);
    if (!data)             return { valid: false, reason: 'TOKEN_NOT_FOUND' };
    if (data.used)         return { valid: false, reason: 'TOKEN_ALREADY_USED' };
    if (Date.now() > data.expires) {
      store.delete(token);
      return { valid: false, reason: 'TOKEN_EXPIRED' };
    }
    return { valid: true, data };
  }

  function consume(token) {
    const entry = store.get(token);
    if (entry) {
      entry.used = true;
      // Auto-clean after a few seconds
      setTimeout(() => store.delete(token), 5000);
    }
  }

  return { generate, validate, consume };
})();


/* ─────────────────────────────────────────────
   4. SIMULATED API
   Replace these with real fetch() calls to your
   actual backend endpoints.
───────────────────────────────────────────── */
const API = {
  /**
   * POST /api/token
   * Returns { token, expiresIn }
   */
  async requestToken(country) {
    // Simulate network latency
    await sleep(900 + rand(600));

    /*
    // ── REAL BACKEND CALL ──
    const res = await fetch('/api/token', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ country })
    });
    if (!res.ok) throw new Error(`Token request failed: ${res.status}`);
    return res.json();
    */

    // Simulated response
    const token = TokenStore.generate();
    return { token, expiresIn: 60 };
  },

  /**
   * GET /secure/home?token=...
   * Returns HTML string for the protected page
   */
  async loadSecureContent(token, country) {
    await sleep(600 + rand(400));

    /*
    // ── REAL BACKEND CALL ──
    const res = await fetch(`/secure/home?token=${encodeURIComponent(token)}`);
    if (res.status === 403) throw new Error('FORBIDDEN');
    if (!res.ok) throw new Error(`Content load failed: ${res.status}`);
    return res.text();
    */

    // Validate token locally (mirrors server logic)
    const result = TokenStore.validate(token);
    if (!result.valid) throw new Error(result.reason);

    TokenStore.consume(token);

    // Return simulated secure content
    return buildSecureContent(token, country);
  }
};


/* ─────────────────────────────────────────────
   5. UI UTILITIES
───────────────────────────────────────────── */
function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
function rand(n)   { return Math.floor(Math.random() * n); }

function setStatus(text, type = 'ok') {
  const dot  = document.querySelector('.status-dot');
  const span = document.getElementById('status-text');
  span.textContent = text;
  dot.className    = 'status-dot' + (type !== 'ok' ? ` ${type}` : '');
}

function showPanel(id) {
  document.querySelectorAll('.panel').forEach(p => {
    if (p.id === id) {
      p.classList.add('active', 'slide-in');
      p.classList.remove('slide-out');
    } else {
      p.classList.remove('active', 'slide-in');
    }
  });
}

function typeText(el, text, speed = 35) {
  return new Promise(resolve => {
    el.textContent = '';
    let i = 0;
    const tick = setInterval(() => {
      el.textContent += text[i++];
      if (i >= text.length) { clearInterval(tick); resolve(); }
    }, speed);
  });
}


/* ─────────────────────────────────────────────
   6. GATEWAY FLOW
───────────────────────────────────────────── */
const countryEl   = document.getElementById('country');
const langDisplay = document.getElementById('lang-display');
const continueBtn = document.getElementById('continue-btn');

// Update language on country change
countryEl.addEventListener('change', () => {
  const lang = LANG_MAP[countryEl.value] || 'EN — ENGLISH';
  langDisplay.textContent = lang;
  langDisplay.style.color = 'var(--accent)';
  setTimeout(() => langDisplay.style.color = '', 600);
});

// Main entry flow
continueBtn.addEventListener('click', async () => {
  const country = countryEl.value;

  continueBtn.disabled = true;
  setStatus('INITIALISING SESSION...');

  // Transition to loading panel
  await sleep(300);
  showPanel('loading-panel');

  const loaderLabel  = document.getElementById('loader-label');
  const tokenPreview = document.getElementById('token-preview');

  try {
    // Step 1 – Request token
    await typeText(loaderLabel, 'REQUESTING TOKEN...', 40);
    const { token, expiresIn } = await API.requestToken(country);

    // Show partial token
    loaderLabel.textContent = 'TOKEN ISSUED';
    tokenPreview.classList.add('visible');
    await typeText(tokenPreview, `TKN:${token}`, 18);

    await sleep(600);

    // Step 2 – Load secure content
    await typeText(loaderLabel, 'LOADING SECURE CONTENT...', 35);
    tokenPreview.textContent = '';
    const html = await API.loadSecureContent(token, country);

    await typeText(loaderLabel, 'ACCESS GRANTED', 40);
    await sleep(500);

    // Redirect to live.html
    window.location.href = 'live.html';

  } catch (err) {
    const msg = err.message || 'UNKNOWN_ERROR';
    loaderLabel.textContent = `ERROR: ${msg}`;
    loaderLabel.style.color = 'var(--danger)';
    tokenPreview.textContent = 'ACCESS DENIED — RETURNING TO GATEWAY';
    tokenPreview.classList.add('visible');
    tokenPreview.style.color = 'var(--danger)';

    await sleep(2800);

    // Reset and go back
    loaderLabel.style.color   = '';
    tokenPreview.style.color  = '';
    tokenPreview.classList.remove('visible');
    continueBtn.disabled = false;
    setStatus('SESSION FAILED — RETRY', 'error');
    showPanel('gateway');
  }
});


/* ─────────────────────────────────────────────
   7. SESSION TIMER (mirrors server-side TTL)
───────────────────────────────────────────── */
let sessionTimerInterval = null;

function startSessionTimer(seconds, token) {
  clearInterval(sessionTimerInterval);
  let remaining = seconds;

  const tokenEl = document.getElementById('session-token-display');

  sessionTimerInterval = setInterval(() => {
    remaining--;
    if (remaining <= 0) {
      clearInterval(sessionTimerInterval);
      tokenEl.textContent = 'EXPIRED';
      tokenEl.style.color = 'var(--danger)';
    } else if (remaining <= 10) {
      tokenEl.style.color = 'var(--warn)';
    }
  }, 1000);
}


/* ─────────────────────────────────────────────
   8. LOGOUT
───────────────────────────────────────────── */
document.getElementById('logout-btn').addEventListener('click', async () => {
  clearInterval(sessionTimerInterval);
  document.getElementById('app-content').innerHTML = '';
  document.getElementById('session-token-display').textContent = '—';

  const continueBtn = document.getElementById('continue-btn');
  continueBtn.disabled = false;
  setStatus('SESSION ENDED — AWAITING INPUT');
  showPanel('gateway');
});


/* ─────────────────────────────────────────────
   9. PROTECTED CONTENT BUILDER
   In production this HTML comes from your server.
   Replace this function with a real API call.
───────────────────────────────────────────── */
function buildSecureContent(token, country) {
  const ts      = new Date().toISOString();
  const region  = country || 'ZA';
  const lang    = LANG_MAP[region] || 'EN — ENGLISH';

  return `
    <div class="app-welcome">
      <div class="protected-badge">⬡ &nbsp; PROTECTED CONTENT LOADED</div>

      <h2>Welcome to <span>SCL</span></h2>

      <p>Your session is authenticated and active. This content was
         dynamically served after token verification.</p>

      <div class="info-grid">
        <div class="info-card">
          <div class="card-label">// TOKEN (CONSUMED)</div>
          <div class="card-value">${token.substring(0, 12)}...</div>
        </div>
        <div class="info-card">
          <div class="card-label">// REGION</div>
          <div class="card-value">${region}</div>
        </div>
        <div class="info-card">
          <div class="card-label">// LANGUAGE</div>
          <div class="card-value">${lang.split(' ')[0]}</div>
        </div>
        <div class="info-card">
          <div class="card-label">// TIMESTAMP</div>
          <div class="card-value">${ts.substring(0, 19).replace('T', ' ')}</div>
        </div>
      </div>

      <p style="font-size:10px; color: var(--text-dim);">
        // Your protected application content renders here. Replace this
        section via GET /secure/home on your backend.
      </p>
    </div>
  `;
}
