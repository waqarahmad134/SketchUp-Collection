<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- SEO Meta Tags --}}
    <x-seo-meta :model="$seoModel ?? null" />
    
    {{-- Favicons --}}
    <x-favicons />
    
    {{-- Custom Scripts (Head) --}}
    <x-custom-scripts position="head" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('head')
</head>
<body
    class="antialiased bg-background text-foreground"
    data-toast-success="{{ session('status') }}"
    data-toast-error="{{ session('error') ?? ($errors->first() ?? '') }}"
    data-daily-bonus-claimed="{{ $dailyBonusClaimed ?? null }}"
>
    {{-- Custom Scripts (Body Start) --}}
    <x-custom-scripts position="body_start" />
    
    @include('partials.navbar')

    <main class="pt-20">
        @yield('content')
    </main>

    @include('partials.footer')

    <div id="toast-root" class="fixed top-5 right-5 z-[9999] space-y-3"></div>

    <!-- Daily Login Bonus Modal (Available on all pages) -->
    <div id="daily-login-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="glass-card rounded-3xl p-8 max-w-md w-full mx-4 relative">
            <button onclick="closeDailyLoginModal()" class="absolute top-4 right-4 text-muted-foreground hover:text-foreground transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
            
            <div class="text-center mb-6">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-gradient-to-br from-yellow-500/20 to-orange-500/20 flex items-center justify-center">
                    <i data-lucide="gift" class="w-10 h-10 text-yellow-400"></i>
                </div>
                <h2 class="text-2xl font-bold font-display mb-2">Daily Login Bonus!</h2>
                <p class="text-muted-foreground">Claim your daily reward</p>
            </div>

            <div id="daily-login-content" class="space-y-4">
                <!-- Content will be loaded via JavaScript -->
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="closeDailyLoginModal()" class="flex-1 px-4 py-3 rounded-xl border border-border hover:border-foreground transition text-sm font-semibold">
                    Close
                </button>
                <button onclick="claimDailyBonus()" id="claim-daily-btn" class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 text-background font-semibold shadow-lg hover:shadow-xl transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Claim Now
                </button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Global Daily Login Bonus Functions (Available on all pages)
        let dailyLoginStatus = null;

        async function checkDailyLoginStatus() {
            try {
                const response = await fetch('{{ route("daily-login.status") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                const data = await response.json();
                dailyLoginStatus = data;
                return data;
            } catch (error) {
                console.error('Failed to check daily login status:', error);
                return null;
            }
        }

        function showDailyLoginModal(status) {
            const modal = document.getElementById('daily-login-modal');
            const content = document.getElementById('daily-login-content');
            const claimBtn = document.getElementById('claim-daily-btn');
            
            if (!modal || !content) return;
            
            // Build content - show different message if not logged in
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            let html = '';
            let canClaim = false;
            
            if (isLoggedIn && status.can_claim) {
                canClaim = true;
                html = `
                    <div class="text-center space-y-3">
                        <div class="inline-block px-4 py-2 rounded-full bg-yellow-500/20 text-yellow-400 text-sm font-semibold">
                            Day ${status.streak_day} Streak
                        </div>
                        <div class="text-4xl font-bold gradient-text">
                            +${status.points_for_today.toLocaleString()} SKP
                        </div>
                        <p class="text-sm text-muted-foreground">
                            ${status.remaining_weekly.toLocaleString()} coins remaining this week
                        </p>
                    </div>
                `;
            } else if (isLoggedIn && !status.can_claim) {
                // Already claimed today
                canClaim = false;
                html = `
                    <div class="text-center space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-full bg-green-500/20 flex items-center justify-center mb-3">
                            <i data-lucide="check-circle" class="w-8 h-8 text-green-400"></i>
                        </div>
                        <div class="inline-block px-4 py-2 rounded-full bg-green-500/20 text-green-400 text-sm font-semibold">
                            Already Claimed Today!
                        </div>
                        <div class="text-2xl font-bold gradient-text">
                            Daily Bonus Collected
                        </div>
                        <p class="text-sm text-muted-foreground">
                            You've already claimed your daily bonus today. Come back tomorrow for more rewards!
                        </p>
                    </div>
                `;
            } else if (!isLoggedIn) {
                html = `
                    <div class="text-center space-y-3">
                        <div class="inline-block px-4 py-2 rounded-full bg-yellow-500/20 text-yellow-400 text-sm font-semibold">
                            Daily Bonus Available!
                        </div>
                        <div class="text-4xl font-bold gradient-text">
                            Earn SKP Coins
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Login or sign up to claim your daily bonus and start earning rewards!
                        </p>
                    </div>
                `;
            }
            
            content.innerHTML = html;
            
            // Handle claim button state
            if (claimBtn) {
                if (canClaim || !isLoggedIn) {
                    claimBtn.disabled = false;
                    claimBtn.style.display = 'block';
                    claimBtn.textContent = 'Claim Now';
                    claimBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    // Already claimed - disable button
                    claimBtn.disabled = true;
                    claimBtn.style.display = 'block';
                    claimBtn.textContent = 'Already Claimed';
                    claimBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }
        }

        function closeDailyLoginModal() {
            const modal = document.getElementById('daily-login-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        async function claimDailyBonus() {
            const btn = document.getElementById('claim-daily-btn');
            if (!btn || btn.disabled) return;
            
            const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
            
            // If not logged in, redirect to login with claim intent
            if (!isLoggedIn) {
                window.location.href = '{{ route("login") }}?claim_daily_bonus=1';
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Claiming...';
            
            try {
                const response = await fetch('{{ route("daily-login.claim") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    const content = document.getElementById('daily-login-content');
                    if (content) {
                        content.innerHTML = `
                            <div class="text-center space-y-3">
                                <div class="w-16 h-16 mx-auto rounded-full bg-green-500/20 flex items-center justify-center">
                                    <i data-lucide="check-circle" class="w-8 h-8 text-green-400"></i>
                                </div>
                                <h3 class="text-xl font-bold">Bonus Claimed!</h3>
                                <p class="text-2xl font-bold gradient-text">+${data.points_awarded.toLocaleString()} SKP</p>
                                <p class="text-sm text-muted-foreground">New Balance: ${data.new_balance.toLocaleString()} SKP</p>
                            </div>
                        `;
                        if (window.lucide?.createIcons) {
                            window.lucide.createIcons();
                        }
                    }
                    
                    // Update points in header if exists
                    const pointsDisplay = document.querySelector('[data-user-points]');
                    if (pointsDisplay) {
                        pointsDisplay.textContent = data.new_balance.toLocaleString();
                    }
                    
                    // Hide claim button
                    btn.style.display = 'none';
                    
                    // Auto close after 3 seconds
                    setTimeout(() => {
                        closeDailyLoginModal();
                    }, 3000);
                } else {
                    alert(data.message || 'Failed to claim bonus');
                    btn.disabled = false;
                    btn.textContent = 'Claim Now';
                }
            } catch (error) {
                console.error('Failed to claim daily bonus:', error);
                alert('Failed to claim bonus. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Claim Now';
            }
        }

        // Global function to open daily bonus modal (called from navbar on any page)
        async function openDailyBonusModal() {
            const modal = document.getElementById('daily-login-modal');
            if (!modal) {
                console.error('Daily login modal not found');
                return;
            }
            
            // Fetch current status and show modal immediately
            const data = await checkDailyLoginStatus();
            if (data) {
                showDailyLoginModal(data);
            } else {
                // Fallback if fetch fails
                showDailyLoginModal({ enabled: true, can_claim: false });
            }
        }

        // Initialize Lucide icons on page load
        window.addEventListener('DOMContentLoaded', () => {
            if (window.lucide?.createIcons) {
                window.lucide.createIcons();
            }
            
            // Show success message if daily bonus was auto-claimed after login
            const dailyBonusClaimed = document.body.getAttribute('data-daily-bonus-claimed');
            if (dailyBonusClaimed) {
                setTimeout(() => {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Daily Bonus Claimed!',
                            text: `You've earned ${parseInt(dailyBonusClaimed).toLocaleString()} SKP coins!`,
                            timer: 3000,
                            showConfirmButton: false,
                        });
                    }
                }, 1000);
            }
        });
    </script>
    
    @stack('scripts')
    
    {{-- Custom Scripts (Body End) --}}
    <x-custom-scripts position="body_end" />
</body>
</html>

