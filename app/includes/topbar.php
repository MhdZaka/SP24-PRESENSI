<div class="top-bar">
    <div class="top-bar-left">
        <button class="mobile-menu-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?= ucfirst($tab) ?></h1>
    </div>
    <div class="user-info">
        <i class="fas fa-user-circle"></i> 
        <?= htmlspecialchars($_SESSION['user']['first_name'] ?? $_SESSION['user']['username'] ?? 'User') ?>
    </div>
</div>
<script>
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
        
        let overlay = document.querySelector('.sidebar-overlay');
        if (sidebar.classList.contains('active')) {
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.className = 'sidebar-overlay';
                overlay.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 998; backdrop-filter: blur(2px);';
                overlay.onclick = toggleSidebar;
                document.body.appendChild(overlay);
            }
            overlay.style.display = 'block';
        } else {
            if (overlay) overlay.style.display = 'none';
        }
    }
}

let touchstartX = 0;
let touchendX = 0;

document.addEventListener('touchstart', function(e) {
    touchstartX = e.changedTouches[0].screenX;
}, {passive: true});

document.addEventListener('touchend', function(e) {
    touchendX = e.changedTouches[0].screenX;
    if (touchstartX - touchendX > 50) { 
        const sidebar = document.querySelector('.sidebar');
        if (sidebar && sidebar.classList.contains('active')) {
            toggleSidebar(); 
        }
    }
}, {passive: true});
</script>