/**
 * Safe Navigation Handler for Parent Dashboard
 * Prevents session data loss during navigation
 */
document.addEventListener('DOMContentLoaded', function() {
    // Handle all navigation links in parent stat cards
    const navigationLinks = document.querySelectorAll('[data-safe-nav]');
    
    navigationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetRoute = this.getAttribute('href');
            
            // Perform safe navigation with session preservation
            performSafeNavigation(targetRoute);
        });
    });
    
    // Handle rapot navigation specifically
    const rapotLinks = document.querySelectorAll('a[href*="rapot"]');
    
    rapotLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading state
            showLoadingState(this);
            
            // Perform AJAX check before navigation
            fetch('/orangtua/session-check', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.authenticated) {
                    window.location.href = this.getAttribute('href');
                } else {
                    window.location.href = '/login';
                }
            })
            .catch(error => {
                console.error('Navigation error:', error);
                // Fallback to direct navigation
                window.location.href = this.getAttribute('href');
            })
            .finally(() => {
                hideLoadingState(this);
            });
        });
    });
});

function performSafeNavigation(targetRoute) {
    // Add session preservation headers
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = targetRoute;
    
    // Add CSRF token if needed
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken.getAttribute('content');
        form.appendChild(tokenInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

function showLoadingState(element) {
    element.style.opacity = '0.7';
    element.style.pointerEvents = 'none';
}

function hideLoadingState(element) {
    element.style.opacity = '1';
    element.style.pointerEvents = 'auto';
}