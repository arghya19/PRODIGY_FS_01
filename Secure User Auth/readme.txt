Username   = 'rstate.noreply@gmail.com'
database name: user_auth

to disable right click and ctrl U
// Disable right-click
document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

// Disable Ctrl+U and other developer shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+U
    if (e.ctrlKey && e.key.toLowerCase() === 'u') {
        e.preventDefault();
    }
    // Ctrl+Shift+I (DevTools)
    if (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'i') {
        e.preventDefault();
    }
    // F12 (DevTools)
    if (e.key === 'F12') {
        e.preventDefault();
    }
});
