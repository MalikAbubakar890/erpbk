/**
 * Session and Authentication Fix for Laravel Application
 * This script handles session timeouts, CSRF token refresh, and authentication errors
 */

(function() {
    'use strict';

    // Global variables
    let sessionTimeoutWarning = null;
    let csrfToken = null;
    let isRefreshingToken = false;

    // Initialize the session fix
    function init() {
        // Get initial CSRF token
        updateCsrfToken();
        
        // Set up AJAX global handlers
        setupAjaxHandlers();
        
        // Set up session timeout warning
        setupSessionTimeoutWarning();
        
        // Set up periodic token refresh
        setupTokenRefresh();
        
        // Set up page visibility change handler
        setupVisibilityChangeHandler();
    }

    // Update CSRF token from meta tag
    function updateCsrfToken() {
        const metaTag = document.querySelector('meta[name="csrf-token"]');
        if (metaTag) {
            csrfToken = metaTag.getAttribute('content');
        }
    }

    // Setup global AJAX handlers
    function setupAjaxHandlers() {
        // Set up CSRF token for all AJAX requests
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                    updateCsrfToken();
                    if (csrfToken) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                    }
                }
            },
            error: function(xhr, status, error) {
                handleAjaxError(xhr, status, error);
            }
        });

        // Override jQuery's ajax method to handle authentication errors
        const originalAjax = $.ajax;
        $.ajax = function(options) {
            const originalSuccess = options.success;
            const originalError = options.error;

            options.success = function(data, textStatus, jqXHR) {
                // Check if response indicates session timeout
                if (data && data.session_expired) {
                    handleSessionExpired();
                    return;
                }
                
                if (originalSuccess) {
                    originalSuccess.call(this, data, textStatus, jqXHR);
                }
            };

            options.error = function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status === 419 || jqXHR.status === 401) {
                    handleAuthenticationError(jqXHR);
                    return;
                }
                
                if (originalError) {
                    originalError.call(this, jqXHR, textStatus, errorThrown);
                }
            };

            return originalAjax.call(this, options);
        };
    }

    // Handle AJAX errors
    function handleAjaxError(xhr, status, error) {
        if (xhr.status === 419) {
            // CSRF token mismatch
            handleCsrfTokenMismatch();
        } else if (xhr.status === 401) {
            // Unauthorized - session expired
            handleSessionExpired();
        } else if (xhr.status === 0) {
            // Network error or session timeout
            handleNetworkError();
        }
    }

    // Handle CSRF token mismatch
    function handleCsrfTokenMismatch() {
        if (isRefreshingToken) return;
        
        isRefreshingToken = true;
        
        // Try to refresh the CSRF token
        $.get(window.location.href)
            .done(function(data) {
                // Extract new CSRF token from response
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                const newToken = doc.querySelector('meta[name="csrf-token"]');
                
                if (newToken) {
                    csrfToken = newToken.getAttribute('content');
                    // Update meta tag
                    const metaTag = document.querySelector('meta[name="csrf-token"]');
                    if (metaTag) {
                        metaTag.setAttribute('content', csrfToken);
                    }
                    
                    showNotification('Session refreshed. Please try again.', 'info');
                } else {
                    handleSessionExpired();
                }
            })
            .fail(function() {
                handleSessionExpired();
            })
            .always(function() {
                isRefreshingToken = false;
            });
    }

    // Handle authentication error
    function handleAuthenticationError(xhr) {
        if (xhr.responseJSON && xhr.responseJSON.message) {
            if (xhr.responseJSON.message.includes('Unauthenticated') || 
                xhr.responseJSON.message.includes('session')) {
                handleSessionExpired();
            } else {
                showNotification(xhr.responseJSON.message, 'error');
            }
        } else {
            handleSessionExpired();
        }
    }

    // Handle session expired
    function handleSessionExpired() {
        showNotification('Your session has expired. Please log in again.', 'warning');
        
        // Redirect to login page after a short delay
        setTimeout(function() {
            window.location.href = '/login';
        }, 2000);
    }

    // Handle network error
    function handleNetworkError() {
        showNotification('Network error. Please check your connection and try again.', 'error');
    }

    // Setup session timeout warning
    function setupSessionTimeoutWarning() {
        const sessionLifetime = 2880; // 48 hours in minutes
        const warningTime = 30; // Show warning 30 minutes before expiry
        
        sessionTimeoutWarning = setTimeout(function() {
            showSessionTimeoutWarning();
        }, (sessionLifetime - warningTime) * 60 * 1000);
    }

    // Show session timeout warning
    function showSessionTimeoutWarning() {
        if (confirm('Your session will expire in 30 minutes. Would you like to extend it?')) {
            // Refresh the session by making a simple request
            $.get(window.location.href)
                .done(function() {
                    showNotification('Session extended successfully.', 'success');
                    // Reset the warning timer
                    setupSessionTimeoutWarning();
                })
                .fail(function() {
                    handleSessionExpired();
                });
        }
    }

    // Setup periodic token refresh
    function setupTokenRefresh() {
        // Refresh CSRF token every 30 minutes
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                updateCsrfToken();
            }
        }, 30 * 60 * 1000);
    }

    // Setup page visibility change handler
    function setupVisibilityChangeHandler() {
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Page became visible, refresh CSRF token
                updateCsrfToken();
            }
        });
    }

    // Show notification
    function showNotification(message, type) {
        // Use existing notification system if available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else if (typeof showNotification === 'function') {
            showNotification(message, type);
        } else {
            // Fallback to alert
            alert(message);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose functions globally for manual use
    window.SessionFix = {
        refreshToken: updateCsrfToken,
        handleSessionExpired: handleSessionExpired,
        getCsrfToken: function() { return csrfToken; }
    };

})();
