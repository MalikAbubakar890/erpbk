<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CloudwaysSessionFix
{
    /**
     * Handle an incoming request to fix Cloudways session issues.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Fix for Cloudways proxy headers
        $this->fixProxyHeaders($request);

        // Ensure proper session configuration
        $this->ensureSessionConfiguration();

        // Fix timezone issues
        $this->fixTimezone();

        $response = $next($request);

        // Add security headers for Cloudways
        $this->addSecurityHeaders($response);

        return $response;
    }

    /**
     * Fix proxy headers for Cloudways load balancer
     */
    protected function fixProxyHeaders(Request $request)
    {
        // Trust Cloudways proxy with correct header constants
        $trustedHeaders = Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO;

        $request->setTrustedProxies(['*'], $trustedHeaders);

        // Fix HTTPS detection
        if ($request->header('X-Forwarded-Proto') === 'https') {
            $request->server->set('HTTPS', 'on');
            $request->server->set('SERVER_PORT', 443);
        }
    }

    /**
     * Ensure proper session configuration for Cloudways
     */
    protected function ensureSessionConfiguration()
    {
        // Extend session lifetime if needed
        if (config('cloudways.session_fixes.extend_session_lifetime')) {
            ini_set('session.gc_maxlifetime', 172800); // 48 hours
            ini_set('session.cookie_lifetime', 172800);
        }

        // Force secure cookies only for HTTPS and when in production
        if (config('cloudways.session_fixes.force_secure_cookies') && request()->isSecure() && app()->environment('production')) {
            ini_set('session.cookie_secure', '1');
        } else {
            // Allow non-secure cookies for local development
            ini_set('session.cookie_secure', '0');
        }

        // Set proper session path and configuration
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');

        // Additional session configuration for stability
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_domain', '');

        // Increase session garbage collection probability
        ini_set('session.gc_probability', '1');
        ini_set('session.gc_divisor', '100');
    }

    /**
     * Fix timezone issues
     */
    protected function fixTimezone()
    {
        $timezone = config('cloudways.server_timezone', 'UTC');
        if (!date_default_timezone_get() || date_default_timezone_get() !== $timezone) {
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Add security headers
     */
    protected function addSecurityHeaders(Response $response)
    {
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        if (config('cloudways.force_https')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
