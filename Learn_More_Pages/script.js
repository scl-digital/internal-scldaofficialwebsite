// Initialize Lucide Icons
lucide.createIcons();

// Scroll Reveal Logic
window.addEventListener('scroll', () => {
    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(el => {
        const windowHeight = window.innerHeight;
        const revealTop = el.getBoundingClientRect().top;
        if (revealTop < windowHeight - 150) {
            el.classList.add('active');
        }
    });
});

// Toggle Deliverables Function
function toggleDeliverables(button) {
    const content = button.nextElementSibling;
    const isActive = content.classList.contains('active');
    
    // Close all other deliverables
    document.querySelectorAll('.deliverables-content').forEach(el => {
        if (el !== content) {
            el.classList.remove('active');
            el.previousElementSibling.classList.remove('active');
        }
    });
    
    // Toggle current
    content.classList.toggle('active');
    button.classList.toggle('active');
    
    // Reinitialize icons after toggle
    lucide.createIcons();
}

// Reinitialize icons on page load
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();
});