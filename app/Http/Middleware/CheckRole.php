<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check direct legacy role match
        if ($user->role && in_array($user->role, $roles)) {
            return $next($request);
        }

        // Check dynamic roles (from user_role pivot table)
        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        // Check if user has menu access for current route (menu-based authorization)
        if ($this->hasMenuAccessForRoute($user, $request)) {
            return $next($request);
        }

        // Allow users with dosen data to access dosen routes
        if (in_array('dosen', $roles) && $user->canAccessDosenFeatures()) {
            return $next($request);
        }

        // Check if dosen has additional roles (dekan/kaprodi)
        if ($user->role === 'dosen') {
            // Allow dosen who is also dekan to access dekan routes
            if (in_array('dekan', $roles) && $user->isDekan()) {
                return $next($request);
            }
            
            // Allow dosen who is also kaprodi to access kaprodi routes
            if (in_array('kaprodi', $roles) && $user->isKaprodi()) {
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }

    /**
     * Check if user has menu access for the current route.
     * This allows dynamic role-based access without hardcoding roles in routes.
     */
    protected function hasMenuAccessForRoute($user, Request $request): bool
    {
        $routeName = $request->route()?->getName();
        
        if (!$routeName) {
            return false;
        }

        // Get user's accessible menus
        $userMenus = $user->getMenus();
        
        if ($userMenus->isEmpty()) {
            return false;
        }

        // Check if any menu matches current route or its parent route
        foreach ($userMenus as $menu) {
            if ($menu->route_name && $this->routeMatches($routeName, $menu->route_name)) {
                return true;
            }
            
            // Check children menus
            if ($menu->children) {
                foreach ($menu->children as $child) {
                    if ($child->route_name && $this->routeMatches($routeName, $child->route_name)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if current route matches menu route.
     * Supports prefix matching for resource routes (e.g., pmb.dashboard matches pmb.*)
     */
    protected function routeMatches(string $currentRoute, string $menuRoute): bool
    {
        // Exact match
        if ($currentRoute === $menuRoute) {
            return true;
        }

        // Prefix match: if menu is "pmb.dashboard", allow all "pmb.*" routes
        $menuPrefix = explode('.', $menuRoute)[0];
        $currentPrefix = explode('.', $currentRoute)[0];
        
        if ($menuPrefix === $currentPrefix) {
            return true;
        }

        return false;
    }
}
