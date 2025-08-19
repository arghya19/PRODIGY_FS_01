// Complete Browser Navigation Prevention Script
// Prevents back/forward navigation + disables back button visually

(function () {
    'use strict';

    // CSS to visually disable browser navigation buttons
    const disableNavigationCSS = `
        /* Hide browser navigation buttons */
        ::-webkit-scrollbar-button { display: none !important; }
        
        /* Additional method - inject CSS to make navigation area less prominent */
        body { 
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Prevent drag and drop */
        * {
            -webkit-user-drag: none;
            -khtml-user-drag: none;
            -moz-user-drag: none;
            -o-user-drag: none;
            user-drag: none;
        }
    `;

    // Inject CSS
    const style = document.createElement('style');
    style.textContent = disableNavigationCSS;
    document.head.appendChild(style);

    // Method 1: History API manipulation
    function preventNavigation() {
        // Push current state to history
        history.pushState(null, null, location.href);

        // Listen for popstate event (triggered by back/forward buttons)
        window.addEventListener('popstate', function (event) {
            // Push the state again to prevent navigation
            history.pushState(null, null, location.href);
        });
    }

    // Method 2: Enhanced prevention with beforeunload
    function preventNavigationEnhanced() {
        // Push current state to history
        history.pushState(null, null, location.href);

        // Prevent back/forward navigation
        window.addEventListener('popstate', function (event) {
            history.pushState(null, null, location.href);
        });

        // Optional: Show confirmation dialog when trying to leave
        window.addEventListener('beforeunload', function (event) {
            event.preventDefault();
            event.returnValue = ''; // Some browsers require this
            return ''; // Legacy browsers
        });
    }

    // Method 3: Complete navigation blocking (most restrictive)
    function blockAllNavigationNoPrompt() {
        // Push a fake state into history
        history.pushState(null, null, location.href);

        // Prevent back/forward navigation
        window.addEventListener('popstate', function () {
            history.pushState(null, null, location.href);
        });

        // Prevent common keyboard shortcuts for navigation
        document.addEventListener('keydown', function (event) {
            // Block F5
            if (event.key === 'F5') event.preventDefault();

            // Block Ctrl+R
            if (event.ctrlKey && event.key.toLowerCase() === 'r') event.preventDefault();

            // Block Ctrl+F5
            if (event.ctrlKey && event.key === 'F5') event.preventDefault();

            // Block Alt+Left/Right (back/forward)
            if (event.altKey && (event.key === 'ArrowLeft' || event.key === 'ArrowRight')) event.preventDefault();

            // Block Backspace outside input/textarea
            if (event.key === 'Backspace' &&
                !['INPUT', 'TEXTAREA'].includes(event.target.tagName) &&
                !event.target.isContentEditable) {
                event.preventDefault();
            }
        });

        // Block mouse back/forward buttons
        document.addEventListener('mousedown', function (event) {
            if (event.button === 3 || event.button === 4) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        // Optional: block right-click
        document.addEventListener('contextmenu', function (event) {
            event.preventDefault();
        });

        // Disable text selection & dragging
        document.addEventListener('selectstart', function (event) {
            event.preventDefault();
        });
        document.addEventListener('dragstart', function (event) {
            event.preventDefault();
        });
    }

    // Method 4: User-friendly version with custom message
    function preventNavigationWithMessage(customMessage = 'Are you sure you want to leave this page?') {
        history.pushState(null, null, location.href);

        window.addEventListener('popstate', function (event) {
            if (confirm(customMessage)) {
                // User confirmed, allow navigation
                window.removeEventListener('popstate', arguments.callee);
                history.back();
            } else {
                // User cancelled, stay on page
                history.pushState(null, null, location.href);
            }
        });
    }

    // Method 5: Conditional prevention (can be enabled/disabled)
    const NavigationController = {
        isEnabled: false,

        enable: function () {
            if (!this.isEnabled) {
                this.isEnabled = true;
                history.pushState(null, null, location.href);
                window.addEventListener('popstate', this.handlePopState.bind(this));
            }
        },

        disable: function () {
            if (this.isEnabled) {
                this.isEnabled = false;
                window.removeEventListener('popstate', this.handlePopState.bind(this));
            }
        },

        handlePopState: function (event) {
            if (this.isEnabled) {
                history.pushState(null, null, location.href);
            }
        }
    };

    // Initialize with complete navigation blocking (prevents ALL navigation including UI buttons)
    blockAllNavigation();

    // Expose controller globally for dynamic control
    window.NavigationController = NavigationController;

})();

// Additional method: Try to hide browser UI (limited browser support)
try {
    // Request fullscreen to hide browser navigation
    document.addEventListener('DOMContentLoaded', function () {
        // Optional: Hide browser interface in supported browsers
        if (document.documentElement.requestFullscreen) {
            // Uncomment below if you want fullscreen mode (hides all browser UI)
            // document.documentElement.requestFullscreen().catch(console.log);
        }

        // Alternative: Try to hide address bar on mobile
        window.scrollTo(0, 1);
    });
} catch (e) {
    console.log('Browser UI control not supported');
}