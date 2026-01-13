<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <title>eLibrary - Library Management System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/png" href="{{ asset('images/library.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/library.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/library.png') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}"> <!-- Bootstrap -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"> <!-- Custom stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> <!-- Font Awesome -->
</head>

<body>
    <div id="header">
        <!-- HEADER -->
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="logo" style="text-align: left; display: flex; align-items: center; height: 100%;">
                        <a href="{{ route('dashboard') }}"><img src="{{ asset('images/library.png') }}"></a>
                    </div>
                </div>
                <div class="offset-md-4 col-md-4">
                    <div class="dropdown" style="position: relative; display: flex; align-items: center; justify-content: flex-end; height: 100%;">
                        <button class="btn btn-secondary dropdown-toggle d-flex align-items-center" type="button" id="dropdownMenuButton"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="gap: 10px; padding: 0.5rem 1rem; cursor: pointer; user-select: none; pointer-events: auto; position: relative; z-index: 1001;">
                            @php
                                $user = auth()->user();
                            @endphp
                            @if($user->profile_picture)
                                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture) ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture) : asset('storage/' . $user->profile_picture) }}"
                                    alt="{{ $user->name }}"
                                    style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.4); flex-shrink: 0;">
                            @else
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.25); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.4); flex-shrink: 0;">
                                    <i class="fas fa-user" style="color: white; font-size: 1rem; margin: 0; padding: 0; line-height: 1;"></i>
                                </div>
                            @endif
                            <div class="d-flex flex-column align-items-start" style="line-height: 1.2; flex: 1; min-width: 0;">
                                <span style="font-weight: 600; font-size: 0.95rem; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px;">{{ $user->name }}</span>
                                <span class="badge badge-{{ $user->role === 'Admin' ? 'danger' : ($user->role === 'Student' ? 'success' : 'primary') }}" style="font-size: 0.65rem; padding: 0.15rem 0.5rem; margin-top: 2px; font-weight: 500; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-{{ $user->role === 'Admin' ? 'user-shield' : ($user->role === 'Student' ? 'user-graduate' : 'user') }}" style="margin: 0; padding: 0; line-height: 1;"></i> {{ $user->role }}
                                </span>
                            </div>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="z-index: 1050; position: absolute; top: 100%; right: 0; margin-top: 0.125rem;">
                            <a class="dropdown-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a class="dropdown-item {{ request()->routeIs('change_password') ? 'active' : '' }}" href="{{ route('change_password') }}">
                                <i class="fas fa-key"></i> Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="document.getElementById('logoutForm').submit()">
                                <i class="fas fa-sign-out-alt"></i> Log Out
                            </a>
                        </div>
                        <form method="post" id="logoutForm" action="{{ route('logout') }}">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /HEADER -->
    <div id="menubar">
        <!-- Menu Bar -->
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="menu">
                        <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Dashboard</a></li>
                        @if(auth()->user()->role == 'Admin')
                            <li><a href="{{ route('authors') }}" class="{{ request()->routeIs('authors') ? 'active' : '' }}"><i class="fas fa-user-edit"></i> Authors</a></li>
                            <li><a href="{{ route('publishers') }}" class="{{ request()->routeIs('publishers') ? 'active' : '' }}"><i class="fas fa-building"></i> Publishers</a></li>
                            <li><a href="{{ route('categories') }}" class="{{ request()->routeIs('categories') ? 'active' : '' }}"><i class="fas fa-tags"></i> Categories</a></li>
                            <li><a href="{{ route('students') }}" class="{{ request()->routeIs('students') ? 'active' : '' }}"><i class="fas fa-users"></i> Members</a></li>
                        @endif
                        <li><a href="{{ route('books') }}" class="{{ request()->routeIs('books') || request()->routeIs('book.*') ? 'active' : '' }}"><i class="fas fa-book"></i> Books</a></li>
                        @if(auth()->user()->role == 'Student')
                            <li><a href="{{ route('book_issue.create') }}" class="{{ request()->routeIs('book_issue.create') ? 'active' : '' }}"><i class="fas fa-hand-holding"></i> Request Book</a></li>
                            <li><a href="{{ route('book_issued') }}" class="{{ request()->routeIs('book_issued') ? 'active' : '' }}"><i class="fas fa-list"></i> My Requests</a></li>
                        @endif
                        @if(auth()->user()->role == 'Admin')
                            <li><a href="{{ route('book_issued') }}" class="{{ request()->routeIs('book_issued') ? 'active' : '' }}"><i class="fas fa-hand-holding"></i> Book Issue</a></li>
                            <li><a href="{{ route('book_issue.pending') }}" class="{{ request()->routeIs('book_issue.pending') ? 'active' : '' }}"><i class="fas fa-clock"></i> Pending Requests</a></li>
                            <li><a href="{{ route('reservations.index') }}" class="{{ request()->routeIs('reservations.*') ? 'active' : '' }}"><i class="fas fa-calendar-check"></i> Reservations</a></li>
                            <li><a href="{{ route('fines.index') }}" class="{{ request()->routeIs('fines.*') ? 'active' : '' }}"><i class="fas fa-dollar-sign"></i> Fines</a></li>
                        @endif
                        @if(auth()->user()->role == 'Student')
                            <li><a href="{{ route('fines.index') }}" class="{{ request()->routeIs('fines.*') ? 'active' : '' }}"><i class="fas fa-dollar-sign"></i> My Fines</a></li>
                            <li><a href="{{ route('reading.index') }}" class="{{ request()->routeIs('reading.*') ? 'active' : '' }}"><i class="fas fa-book-reader"></i> Read Books Online</a></li>
                        @endif
                        @if(auth()->user()->role == 'Admin')
                            <li><a href="{{ route('reports') }}" class="{{ request()->routeIs('reports') || request()->routeIs('reports.*') ? 'active' : '' }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
                        @endif
                        @if(auth()->user()->role == 'Admin')
                            <li><a href="{{ route('registrations.pending') }}" class="{{ request()->routeIs('registrations.*') ? 'active' : '' }}"><i class="fas fa-user-clock"></i> Pending Registrations</a></li>
                        @endif
                        @if(auth()->user()->role == 'Admin')
                            <li><a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a></li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div> <!-- /Menu Bar -->

    @yield('content')

    <!-- Floating Chatbot Button -->
    @if(auth()->user()->role == 'Student')
    <a href="{{ route('chatbot.index') }}" class="chatbot-float-btn" title="Chat with Library Assistant">
        <i class="fas fa-robot"></i>
        <span class="chatbot-tooltip">Chatbot</span>
    </a>
    @endif

    <!-- FOOTER -->
    <div id="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <span>© Copyright {{ now()->format("Y") }} Library Management System. All Rights Reserved.</span>
                </div>
            </div>
        </div>
    </div>
    <!-- /FOOTER -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        // Ensure menu bar stays sticky below header while scrolling
        (function() {
            function setMenuBarSticky() {
                const header = document.getElementById('header');
                const menubar = document.getElementById('menubar');
                
                if (!header || !menubar) {
                    // Retry if elements not found
                    setTimeout(setMenuBarSticky, 100);
                    return;
                }
                
                // Get header height
                const headerHeight = header.offsetHeight || header.getBoundingClientRect().height;
                
                // Set header sticky
                header.style.cssText += 'position: sticky !important; top: 0 !important; z-index: 1000 !important; width: 100% !important;';
                
                // Set menu bar sticky below header
                menubar.style.cssText += 'position: sticky !important; top: ' + headerHeight + 'px !important; z-index: 999 !important; width: 100% !important;';
                
                // Store header height in CSS variable for future use
                document.documentElement.style.setProperty('--header-height', headerHeight + 'px');
            }
            
            // Run immediately
            setMenuBarSticky();
            
            // Run on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', setMenuBarSticky);
            }
            
            // Run on window load
            window.addEventListener('load', setMenuBarSticky);
            
            // Run on scroll (with throttling)
            let scrollTimer = null;
            window.addEventListener('scroll', function() {
                if (scrollTimer === null) {
                    scrollTimer = setTimeout(function() {
                        setMenuBarSticky();
                        scrollTimer = null;
                    }, 50);
                }
            }, { passive: true });
            
            // Run on resize
            let resizeTimer = null;
            window.addEventListener('resize', function() {
                if (resizeTimer === null) {
                    resizeTimer = setTimeout(function() {
                        setMenuBarSticky();
                        resizeTimer = null;
                    }, 100);
                }
            });
        })();
    </script>
    <style>
        /* Global styles for all search suggestion dropdowns across the project */
        .suggestions-dropdown {
            position: absolute !important;
            background: white !important;
            z-index: 999999 !important;
            top: 100% !important;
            left: 0 !important;
            margin-top: 2px !important;
        }
        /* Prevent parent containers from clipping dropdowns */
        .card, .card-body, .card-header, .row, .container, #admin-content,
        .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6,
        .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12,
        form, #author-search-form, #book-search-form, #dashboard-search-form {
            overflow: visible !important;
        }
        /* Ensure form groups with search inputs have proper positioning */
        .form-group[style*="position: relative"],
        .form-group {
            position: relative !important;
            z-index: 1 !important;
        }
        /* Ensure tables don't overlap dropdowns - lower z-index and remove overflow */
        .content-table,
        #admin-content .content-table,
        table.content-table,
        table.content-table thead,
        #admin-content .content-table thead {
            position: relative !important;
            z-index: 1 !important;
            overflow: visible !important;
        }
        /* Override the overflow: hidden from style.css */
        #admin-content .content-table {
            overflow: visible !important;
        }
        /* Ensure all rows with tables have lower z-index */
        .row .content-table,
        .row table.content-table {
            position: relative !important;
            z-index: 1 !important;
        }
    </style>
    <script>
        // Ensure profile dropdown works reliably - simplified and robust approach
        (function() {
            'use strict';

            let initialized = false;
            let button = null;
            let menu = null;

            function initDropdown() {
                if (initialized) return;

                button = document.getElementById('dropdownMenuButton');
                if (!button) {
                    setTimeout(initDropdown, 100);
                    return;
                }

                menu = button.nextElementSibling;
                if (!menu || !menu.classList.contains('dropdown-menu')) {
                    setTimeout(initDropdown, 100);
                    return;
                }

                initialized = true;

                // Remove data-toggle to prevent Bootstrap auto-init conflicts
                button.removeAttribute('data-toggle');

                // Direct click handler - simple and reliable
                button.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isOpen = menu.classList.contains('show');

                    // Close all other dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(function(m) {
                        if (m !== menu) m.classList.remove('show');
                    });
                    document.querySelectorAll('[aria-expanded="true"]').forEach(function(b) {
                        if (b !== button) b.setAttribute('aria-expanded', 'false');
                    });

                    // Toggle this dropdown
                    if (isOpen) {
                        menu.classList.remove('show');
                        button.setAttribute('aria-expanded', 'false');
                    } else {
                        menu.classList.add('show');
                        button.setAttribute('aria-expanded', 'true');
                    }
                };

                // Close on outside click
                document.addEventListener('click', function(e) {
                    if (menu && menu.classList.contains('show') &&
                        !button.contains(e.target) &&
                        !menu.contains(e.target)) {
                        menu.classList.remove('show');
                        button.setAttribute('aria-expanded', 'false');
                    }
                });

                // Close on Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && menu && menu.classList.contains('show')) {
                        menu.classList.remove('show');
                        button.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Initialize immediately if DOM ready, otherwise wait
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDropdown);
            } else {
                initDropdown();
            }

            // Backup initialization after scripts load
            if (typeof jQuery !== 'undefined') {
                jQuery(function() {
                    setTimeout(initDropdown, 50);
                });
            }

            window.addEventListener('load', function() {
                setTimeout(initDropdown, 100);
            });
        })();
    </script>
</body>

</html>
