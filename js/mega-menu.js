function showCategory(element, categoryId) {
    const megaMenu = element.closest('.mega-menu');
    if (!megaMenu) return;

    // Remove active class from sidebar items within this mega menu
    const sidebarItems = megaMenu.querySelectorAll('.sidebar-item');
    sidebarItems.forEach(item => item.classList.remove('active'));

    // Remove active class from category contents within this mega menu
    const categoryContents = megaMenu.querySelectorAll('.category-content');
    categoryContents.forEach(content => content.classList.remove('active'));

    // Add active class to current element
    element.classList.add('active');

    // Find target content within this mega menu
    const targetContent = megaMenu.querySelector('#category-' + categoryId);
    if (targetContent) {
        targetContent.classList.add('active');
    }
}

function toggleSearchBar() {
    const searchBar = document.getElementById('searchBar');
    if (!searchBar) return;
    searchBar.classList.toggle('active');
    if (searchBar.classList.contains('active')) {
        const input = searchBar.querySelector('.search-input');
        if (input) input.focus();
    }
}
