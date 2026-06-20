/* =========================================
   SCL DIGITAL AGENCY — Case Studies Script
   ========================================= */

// ---- DATA ----
const CASE_STUDIES = [
  {
    id: 1,
    featured: true,
    client: "AfriPay",
    logo: "AP",
    logoColor: "#1A1A2E",
    title: "Rebuilding a Pan-African Payments Platform from the Ground Up",
    desc: "AfriPay needed a complete overhaul of their legacy fintech infrastructure. We architected a modern, micro-service payment engine supporting 14 African currencies with sub-200ms response times and PCI-DSS compliance.",
    industry: "fintech",
    service: "web",
    badge: "Featured",
    result: { label: "Transaction speed", value: "↑ 340%" },
    gradient: "linear-gradient(135deg, #1A1A2E 0%, #16213E 50%, #0F3460 100%)"
  },
  {
    id: 2,
    client: "NexaHealth",
    logo: "NH",
    logoColor: "#0D7A4E",
    title: "AI-Powered Patient Intake System",
    desc: "We integrated a custom LLM workflow into NexaHealth's clinic management suite, reducing patient intake time by 60% and automating appointment routing.",
    industry: "health",
    service: "ai",
    badge: "AI",
    result: { label: "Intake time saved", value: "60%" },
    gradient: "linear-gradient(135deg, #0D7A4E 0%, #0A5C3A 100%)"
  },
  {
    id: 3,
    client: "UrbanGrid",
    logo: "UG",
    logoColor: "#B5451B",
    title: "Smart City E-Commerce Hub for Local Vendors",
    desc: "A marketplace platform connecting 400+ Cape Town micro-retailers with consumers. Features real-time inventory, mobile money integration, and multilingual support.",
    industry: "ecommerce",
    service: "web",
    badge: "Marketplace",
    result: { label: "Vendor onboarding", value: "↑ 220%" },
    gradient: "linear-gradient(135deg, #B5451B 0%, #8B3214 100%)"
  },
  {
    id: 4,
    client: "PulseMedia",
    logo: "PM",
    logoColor: "#6D28D9",
    title: "Brand Identity & Digital Strategy Overhaul",
    desc: "End-to-end brand refresh for one of East Africa's fastest-growing media houses — from logo system and design tokens to a full social media playbook and editorial platform.",
    industry: "media",
    service: "branding",
    badge: "Brand",
    result: { label: "Audience growth", value: "↑ 88%" },
    gradient: "linear-gradient(135deg, #6D28D9 0%, #4C1D95 100%)"
  },
  {
    id: 5,
    client: "OrbisBank",
    logo: "OB",
    logoColor: "#0E4C96",
    title: "Mobile Banking App for the Unbanked",
    desc: "A USSD-first mobile banking application designed for low-connectivity environments. We shipped an MVP in 9 weeks that secured Series A funding for OrbisBank.",
    industry: "fintech",
    service: "mobile",
    badge: "Mobile",
    result: { label: "Users in 3 months", value: "18,000+" },
    gradient: "linear-gradient(135deg, #0E4C96 0%, #093070 100%)"
  },
  {
    id: 6,
    client: "BuildCo",
    logo: "BC",
    logoColor: "#1C1C1C",
    title: "Real Estate SaaS: Lease Management & Analytics",
    desc: "A cloud-based property management platform for mid-size commercial landlords. Automated rent collection, lease tracking, and built-in reporting dashboards.",
    industry: "realestate",
    service: "web",
    badge: "SaaS",
    result: { label: "Admin hours cut", value: "75%" },
    gradient: "linear-gradient(135deg, #2C2C2C 0%, #1C1C1C 100%)"
  },
  {
    id: 7,
    client: "KiwiSaaS",
    logo: "KS",
    logoColor: "#047857",
    title: "Growth Strategy & CRO for B2B Startup",
    desc: "We ran a 90-day growth sprint for KiwiSaaS: overhauling their funnel, running conversion rate optimisation experiments, and launching paid acquisition channels.",
    industry: "fintech",
    service: "growth",
    badge: "Growth",
    result: { label: "Trial-to-paid rate", value: "↑ 2.4×" },
    gradient: "linear-gradient(135deg, #047857 0%, #065F46 100%)"
  },
  {
    id: 8,
    client: "LaunchDark",
    logo: "LD",
    logoColor: "#5B4FE9",
    title: "Go-to-Market Strategy for AI Dev Tool",
    desc: "Product positioning, ICP definition, and a phased launch roadmap for a developer-facing AI tool — from beta waitlist to public launch in 6 weeks.",
    industry: "media",
    service: "strategy",
    badge: "Strategy",
    result: { label: "Beta signups", value: "4,200" },
    gradient: "linear-gradient(135deg, #5B4FE9 0%, #3730A3 100%)"
  },
  {
    id: 9,
    client: "ZeroPoint NGO",
    logo: "ZP",
    logoColor: "#B45309",
    title: "Digital Fundraising Platform & Donor Portal",
    desc: "We built a secure donation platform and CRM integration for ZeroPoint, enabling recurring giving, campaign microsites, and real-time impact reporting.",
    industry: "ngo",
    service: "web",
    badge: "NGO",
    result: { label: "Fundraising increase", value: "↑ 190%" },
    gradient: "linear-gradient(135deg, #B45309 0%, #92400E 100%)"
  }
];

// ---- STATE ----
const state = {
  industryFilter: "all",
  serviceFilter: "all"
};

// ---- DOM HELPERS ----
const $ = id => document.getElementById(id);
const $$ = sel => document.querySelectorAll(sel);

// ---- WORLD MAP CANVAS ----
function initWorldMap() {
  const canvas = $("worldMap");
  if (!canvas) return;
  const ctx = canvas.getContext("2d");

  function resize() {
    canvas.width = canvas.offsetWidth;
    canvas.height = canvas.offsetHeight;
  }
  resize();
  window.addEventListener("resize", resize);

  // Generate dot-grid world map pattern
  const dots = [];
  // Simplified world landmass regions as normalised (x,y) bounds
  const regions = [
    // North America
    { x1: 0.05, y1: 0.1, x2: 0.28, y2: 0.52, density: 0.7 },
    // South America
    { x1: 0.18, y1: 0.5, x2: 0.32, y2: 0.92, density: 0.65 },
    // Europe
    { x1: 0.44, y1: 0.08, x2: 0.58, y2: 0.38, density: 0.85 },
    // Africa
    { x1: 0.45, y1: 0.35, x2: 0.62, y2: 0.82, density: 0.75 },
    // Asia
    { x1: 0.57, y1: 0.06, x2: 0.92, y2: 0.6, density: 0.8 },
    // Australia
    { x1: 0.75, y1: 0.6, x2: 0.92, y2: 0.82, density: 0.6 },
    // Greenland
    { x1: 0.28, y1: 0.04, x2: 0.43, y2: 0.22, density: 0.4 },
    // UK / Scandinavia hint
    { x1: 0.44, y1: 0.05, x2: 0.56, y2: 0.15, density: 0.5 }
  ];

  const GRID = 18;

  function seedDots() {
    dots.length = 0;
    const W = canvas.width;
    const H = canvas.height;
    const cols = Math.ceil(W / GRID);
    const rows = Math.ceil(H / GRID);

    for (let r = 0; r < rows; r++) {
      for (let c = 0; c < cols; c++) {
        const nx = c / cols;
        const ny = r / rows;
        let inLand = false;
        let density = 0;
        for (const reg of regions) {
          if (nx >= reg.x1 && nx <= reg.x2 && ny >= reg.y1 && ny <= reg.y2) {
            inLand = Math.random() < reg.density;
            density = reg.density;
            break;
          }
        }
        if (inLand) {
          dots.push({
            x: c * GRID + GRID / 2,
            y: r * GRID + GRID / 2,
            r: Math.random() * 1.2 + 0.9,
            baseAlpha: Math.random() * 0.25 + 0.15,
            phase: Math.random() * Math.PI * 2,
            speed: Math.random() * 0.6 + 0.3
          });
        }
      }
    }
  }
  seedDots();
  window.addEventListener("resize", () => { resize(); seedDots(); });

  // Highlight dots for key cities (Cape Town, Kigali, London, NY)
  const highlights = [
    { nx: 0.51, ny: 0.75, label: "Cape Town" },   // Africa south
    { nx: 0.56, ny: 0.55, label: "Kigali" },       // Central Africa
    { nx: 0.49, ny: 0.17, label: "London" },
    { nx: 0.2,  ny: 0.26, label: "New York" }
  ];

  let t = 0;
  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const W = canvas.width;
    const H = canvas.height;
    const isDark = document.body.getAttribute("data-theme") === "dark";
    const dotColor = isDark ? "255,255,255" : "91,79,233";

    // Draw dots
    for (const d of dots) {
      const pulse = Math.sin(t * d.speed + d.phase) * 0.08 + d.baseAlpha;
      ctx.beginPath();
      ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
      ctx.fillStyle = `rgba(${dotColor},${pulse})`;
      ctx.fill();
    }

    // Draw highlight pings
    for (const h of highlights) {
      const px = h.nx * W;
      const py = h.ny * H;
      const pingR = (Math.sin(t * 1.5 + h.nx * 10) * 0.5 + 0.5) * 12 + 4;
      ctx.beginPath();
      ctx.arc(px, py, pingR, 0, Math.PI * 2);
      ctx.strokeStyle = `rgba(91,79,233,${0.6 - pingR / 30})`;
      ctx.lineWidth = 1.5;
      ctx.stroke();

      ctx.beginPath();
      ctx.arc(px, py, 3, 0, Math.PI * 2);
      ctx.fillStyle = "rgba(91,79,233,0.9)";
      ctx.fill();

      // Draw connector lines between highlights
    }

    // Draw lines between Cape Town → Kigali → London
    const lineAlpha = 0.12 + Math.sin(t * 0.8) * 0.05;
    const pts = highlights.map(h => ({ x: h.nx * W, y: h.ny * H }));
    ctx.beginPath();
    ctx.moveTo(pts[0].x, pts[0].y);
    for (let i = 1; i < pts.length; i++) {
      ctx.lineTo(pts[i].x, pts[i].y);
    }
    ctx.strokeStyle = `rgba(91,79,233,${lineAlpha})`;
    ctx.lineWidth = 1;
    ctx.setLineDash([4, 8]);
    ctx.stroke();
    ctx.setLineDash([]);

    t += 0.018;
    requestAnimationFrame(draw);
  }
  draw();
}

// ---- RIBBON DUPLICATE ----
function initRibbon() {
  const track = $("ribbonTrack");
  if (!track) return;
  // Duplicate for seamless loop
  track.innerHTML += track.innerHTML;
}

// ---- RENDER CARDS ----
function renderCards(data) {
  const grid = $("caseGrid");
  const count = $("resultCount");
  const noResults = $("noResults");

  grid.innerHTML = "";

  if (data.length === 0) {
    noResults.hidden = false;
    count.textContent = "No projects found";
    return;
  }

  noResults.hidden = true;
  count.textContent = `Showing ${data.length} project${data.length !== 1 ? "s" : ""}`;

  data.forEach((cs, i) => {
    const card = document.createElement("article");
    card.className = `card fade-in-scroll${cs.featured ? " card--featured" : ""}`;
    card.style.transitionDelay = `${(i % 6) * 0.07}s`;
    card.dataset.industry = cs.industry;
    card.dataset.service = cs.service;

    card.innerHTML = `
      <div class="card__image">
        <div class="card__img-placeholder" style="background:${cs.gradient}">
          ${cs.logo}
        </div>
        <span class="card__badge">${cs.badge}</span>
      </div>
      <div class="card__body">
        <div class="card__client-logo" style="background:${cs.logoColor}">
          ${cs.logo}
        </div>
        <div class="card__tags">
          <span class="card__tag">${formatTag(cs.industry)}</span>
          <span class="card__tag">${formatTag(cs.service)}</span>
        </div>
        <h3 class="card__title">${cs.title}</h3>
        <p class="card__desc">${cs.desc}</p>
        <div class="card__footer">
          <a href="#" class="card__cta">
            View Case Study <span class="card__cta-arrow">→</span>
          </a>
          <span class="card__result">
            ${cs.result.label}: <strong>${cs.result.value}</strong>
          </span>
        </div>
      </div>
    `;
    grid.appendChild(card);
  });

  // Trigger scroll observer for new cards
  observeCards();
}

function formatTag(val) {
  const map = {
    fintech: "Fintech", health: "Healthcare", ecommerce: "E-Commerce",
    media: "Media", realestate: "Real Estate", ngo: "NGO",
    branding: "Branding", web: "Web Dev", mobile: "Mobile App",
    ai: "AI Integration", growth: "Growth", strategy: "Strategy"
  };
  return map[val] || val;
}

// ---- FILTER ----
function applyFilters() {
  const { industryFilter, serviceFilter } = state;
  return CASE_STUDIES.filter(cs => {
    const indOk = industryFilter === "all" || cs.industry === industryFilter;
    const svcOk = serviceFilter === "all" || cs.service === serviceFilter;
    return indOk && svcOk;
  });
}

function initFilters() {
  const industryPills = $$("[data-filter='industry']");
  const servicePills  = $$("[data-filter='service']");

  function setActive(pills, value) {
    pills.forEach(p => p.classList.toggle("active", p.dataset.value === value));
  }

  // Default active
  setActive(industryPills, "all");
  setActive(servicePills, "all");

  industryPills.forEach(pill => {
    pill.addEventListener("click", () => {
      state.industryFilter = pill.dataset.value;
      setActive(industryPills, state.industryFilter);
      renderCards(applyFilters());
    });
  });

  servicePills.forEach(pill => {
    pill.addEventListener("click", () => {
      state.serviceFilter = pill.dataset.value;
      setActive(servicePills, state.serviceFilter);
      renderCards(applyFilters());
    });
  });

  function clearAll() {
    state.industryFilter = "all";
    state.serviceFilter = "all";
    setActive(industryPills, "all");
    setActive(servicePills, "all");
    renderCards(CASE_STUDIES);
  }

  $("clearFilters")?.addEventListener("click", clearAll);
  $("clearFilters2")?.addEventListener("click", clearAll);
}

// ---- SCROLL OBSERVER ----
function observeCards() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add("visible");
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.1, rootMargin: "0px 0px -40px 0px" });

  $$(".fade-in-scroll").forEach(el => observer.observe(el));
}

// ---- STATS COUNTER ----
function initCounters() {
  const items = $$(".stat-item__num");
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const target = parseInt(el.dataset.target, 10);
      let current = 0;
      const duration = 1400;
      const step = Math.ceil(target / (duration / 16));
      const timer = setInterval(() => {
        current = Math.min(current + step, target);
        el.textContent = current + (el.dataset.target === "98" ? "%" : "+");
        if (current >= target) {
          el.textContent = target + (el.dataset.target === "98" ? "%" : "+");
          clearInterval(timer);
        }
      }, 16);
      obs.unobserve(el);
    });
  }, { threshold: 0.5 });
  items.forEach(i => obs.observe(i));
}

// ---- NAV SCROLL ----
function initNav() {
  const nav = $("nav") || document.querySelector(".nav");
  const filtersSection = $("filtersSection");

  window.addEventListener("scroll", () => {
    nav.classList.toggle("scrolled", window.scrollY > 20);
    if (filtersSection) {
      const rect = filtersSection.getBoundingClientRect();
      filtersSection.classList.toggle("stuck", rect.top <= 68);
    }
  }, { passive: true });
}

// ---- DARK MODE ----
function initTheme() {
  const btn = $("themeToggle");
  const icon = btn?.querySelector(".theme-toggle__icon");
  const saved = localStorage.getItem("sclTheme") || "light";
  document.body.setAttribute("data-theme", saved);
  if (icon) icon.textContent = saved === "dark" ? "☽" : "☀";

  btn?.addEventListener("click", () => {
    const current = document.body.getAttribute("data-theme");
    const next = current === "dark" ? "light" : "dark";
    document.body.setAttribute("data-theme", next);
    if (icon) icon.textContent = next === "dark" ? "☽" : "☀";
    localStorage.setItem("sclTheme", next);
  });
}

// ---- HAMBURGER MENU ----
function initHamburger() {
  const btn = $("hamburger");
  const links = document.querySelector(".nav__links");
  btn?.addEventListener("click", () => {
    links.classList.toggle("open");
    const open = links.classList.contains("open");
    btn.setAttribute("aria-expanded", open);
  });
}

// ---- INIT ----
document.addEventListener("DOMContentLoaded", () => {
  initTheme();
  initNav();
  initHamburger();
  initWorldMap();
  initRibbon();
  initFilters();
  renderCards(CASE_STUDIES);
  initCounters();

  // Observe static fade-in-scroll elements
  observeCards();
});
