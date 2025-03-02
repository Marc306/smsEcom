document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    // Toggle sidebar function with enhanced animation
    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('active');
        
        if (isOpen) {
            closeSidebar();
        } else {
            openSidebar();
        }
    }
    
    // Open sidebar function with transform
    function openSidebar() {
        sidebar.style.visibility = 'visible';
        requestAnimationFrame(() => {
            sidebar.classList.add('active');
            overlay.classList.add('active');
            document.body.classList.add('sidebar-open');
        });
        
        // Add event listeners
        overlay.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', handleEscKey);
    }
    
    // Close sidebar function with transform
    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
        
        // Remove event listeners
        overlay.removeEventListener('click', closeSidebar);
        document.removeEventListener('keydown', handleEscKey);
        
        // Hide sidebar after animation
        setTimeout(() => {
            if (!sidebar.classList.contains('active')) {
                sidebar.style.visibility = 'hidden';
            }
        }, 500); // Match this with CSS transition duration
    }
    
    // Handle Escape key press
    function handleEscKey(e) {
        if (e.key === 'Escape') {
            closeSidebar();
        }
    }
    
    // Add click events to buttons
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function(e) {
            e.preventDefault();
            closeSidebar();
        });
    }
    
    // Handle swipe gestures for mobile with transform
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;
    
    document.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });
    
    document.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeDistanceX = touchEndX - touchStartX;
        const swipeDistanceY = touchEndY - touchStartY;
        const threshold = 50; // minimum distance for swipe
        
        // Only handle horizontal swipes (ignore vertical swipes)
        if (Math.abs(swipeDistanceX) > Math.abs(swipeDistanceY)) {
            // Swipe right to open
            if (swipeDistanceX > threshold && !sidebar.classList.contains('active')) {
                openSidebar();
            }
            // Swipe left to close
            else if (swipeDistanceX < -threshold && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        }
    }
    
    // Handle active sidebar link
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath.split('/').pop()) {
            link.classList.add('active');
        }
    });
    
    // Close sidebar on window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth > 1024 && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        }, 250);
    });
});
