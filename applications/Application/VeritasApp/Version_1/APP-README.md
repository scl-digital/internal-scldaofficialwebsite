# VRE Application Interface

This is the **actual application interface** - the minimal, distraction-free wrapper that users interact with when running web applications through VRE.

## 📦 Files

- `app.html` - The main application interface
- `app-styles.css` - Minimal, functional styling
- `app-script.js` - Full application logic and controls

## 🎯 What This Is

This is the **wrapper interface** that users see after launching an application. It provides:

- **Minimal top bar** with essential controls
- **Full-screen iframe** for the actual web application
- **Zero distractions** - no browser chrome, tabs, or bookmarks
- **Native feel** - looks and behaves like a desktop application

## ✨ Features

### Top Bar Controls

**Left Side:**
- **VRE Indicator** - Shows the engine is running
- **App Title** - Displays the current application name
- **URL Bar** - Shows the current URL (read-only, secure)

**Right Side:**
- **Connection Status** - Real-time connection indicator
- **Refresh Button** - Reload the application
- **Home Button** - Return to home URL
- **Settings Button** - Open settings panel
- **Window Controls** - Minimize, maximize, close

### Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Ctrl+R` / `F5` | Refresh |
| `Ctrl+W` | Close window |
| `Ctrl+B` | Toggle top bar visibility |
| `F11` | Fullscreen mode |
| `Ctrl +` | Zoom in |
| `Ctrl -` | Zoom out |
| `Ctrl 0` | Reset zoom |
| `Esc` | Exit fullscreen |

### Settings Panel

Access via the settings button or integrate with desktop wrapper:

**Display:**
- Always on Top
- Hide Top Bar

**Performance:**
- Hardware Acceleration
- Enable Cache

**Privacy:**
- Block Third-Party Cookies
- Clear Data on Close

**Info:**
- Version, Build, Engine details
- Real-time memory usage

### Quick Actions Bar

Auto-shows at bottom on mouse hover:
- 📸 **Screenshot** - Capture current view
- ⛶ **Fullscreen** - Toggle fullscreen mode
- 🔍 **Zoom** - Adjust zoom level

## 🚀 Usage

### Standalone (Demo)

```html
<!-- Open directly in browser -->
app.html?url=https://your-app.example.com
```

### Electron Integration

```javascript
// main.js
const { BrowserWindow } = require('electron');

function createVREWindow(targetUrl) {
  const win = new BrowserWindow({
    width: 1200,
    height: 800,
    frame: false, // Use custom frame from VRE
    webPreferences: {
      nodeIntegration: false,
      contextIsolation: true,
      preload: path.join(__dirname, 'preload.js')
    }
  });

  win.loadFile('app.html', {
    query: { url: targetUrl }
  });
  
  return win;
}
```

### Preload API (Electron)

```javascript
// preload.js
const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('electronAPI', {
  minimize: () => ipcRenderer.send('window-minimize'),
  maximize: () => ipcRenderer.send('window-maximize'),
  close: () => ipcRenderer.send('window-close'),
  setAlwaysOnTop: (flag) => ipcRenderer.send('set-always-on-top', flag),
  takeScreenshot: () => ipcRenderer.invoke('take-screenshot'),
  getMemoryUsage: () => ipcRenderer.invoke('get-memory-usage')
});
```

## 🎨 Design Philosophy

**Minimalism**: Every pixel serves a purpose  
**Context Isolation**: No browser distractions  
**Native Feel**: Desktop application, not a website  
**Efficiency**: Lightweight, fast, resource-conscious

## 🔧 Customization

### Change Color Scheme

Edit `app-styles.css`:

```css
:root {
  --bg-primary: #1a1d23;      /* Main background */
  --accent-blue: #3b82f6;     /* Primary accent */
  --text-primary: #e4e6eb;    /* Main text */
}
```

### Adjust Top Bar Height

```css
:root {
  --topbar-height: 44px; /* Change to desired height */
}
```

### Custom Branding

Replace the VRE indicator SVG in `app.html`:

```html
<svg class="indicator-icon" viewBox="0 0 24 24">
  <!-- Your custom icon -->
</svg>
```

## 🔐 Security Features

1. **Sandboxed iframe** - Restricted permissions
2. **HTTPS enforcement** - Secure connections only
3. **Context isolation** - Separated from wrapper
4. **Cookie controls** - Block third-party tracking
5. **Clear on close** - Optional data clearing

## 📊 State Management

### Settings Persistence

Settings are saved to `localStorage`:

```javascript
{
  alwaysOnTop: false,
  hideTopbar: false,
  hardwareAccel: true,
  cacheEnabled: true,
  blockCookies: false,
  clearOnClose: false,
  lastUrl: "https://example.com",
  homeUrl: ""
}
```

### Session State

- Current URL
- Zoom level
- Fullscreen status
- Connection status

## 🎯 Use Cases

1. **Dedicated Work Apps** - Slack, Notion, Linear
2. **Web-Based Tools** - Figma, Miro, Canva
3. **Internal Dashboards** - Analytics, monitoring
4. **Education Platforms** - LMS, virtual classrooms
5. **Productivity Apps** - Email, calendar, tasks

## 🐛 Troubleshooting

**Iframe not loading:**
- Check CORS headers on target site
- Verify HTTPS connection
- Check X-Frame-Options header

**Blank screen:**
- Open console (F12)
- Check iframe sandbox restrictions
- Verify URL is accessible

**Settings not saving:**
- Check localStorage is enabled
- Clear browser cache
- Check for quota errors

## 🔄 Update Flow

When integrated with Electron:

1. Check for updates on launch
2. Download in background
3. Notify user when ready
4. Apply on next restart

## 📈 Performance

**Memory Footprint:**
- Base wrapper: ~50-80 MB
- + Web app memory usage
- Hardware acceleration reduces CPU

**Startup Time:**
- Wrapper: <100ms
- Web app load: Depends on app

**Resource Usage:**
- Lower than full browser
- No tab overhead
- No extension overhead

## 🚀 Future Enhancements

- [ ] Multi-tab support within wrapper
- [ ] Built-in screenshot/recording
- [ ] Workspace/profile switching
- [ ] Keyboard shortcut customization
- [ ] DevTools integration
- [ ] Network traffic monitoring
- [ ] Auto-update system
- [ ] Plugin/extension system

## 📱 Platform Support

Designed for desktop platforms:
- Windows 10/11
- macOS 10.14+
- Linux (Ubuntu, Fedora, etc.)

## 🤝 Integration Examples

### With Tauri

```rust
// main.rs
#[tauri::command]
fn create_vre_window(url: String) {
  let window = tauri::WindowBuilder::new(
    app,
    "vre",
    tauri::WindowUrl::App(format!("app.html?url={}", url).into())
  )
  .decorations(false)
  .build()
  .unwrap();
}
```

### With NW.js

```json
{
  "name": "VRE App",
  "main": "app.html",
  "window": {
    "frame": false,
    "width": 1200,
    "height": 800
  }
}
```

## 📝 License

Part of the Veritas-Rhythm-Engine project.

---

**This is the actual application interface** - minimal, focused, distraction-free. It's what users interact with daily, not the launcher.

*VRE Application v1.0.0*
