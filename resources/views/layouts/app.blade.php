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

    {{-- Announcement Bar (controlled from Admin > Settings) --}}
    @php
        $announcementEnabled = \App\Models\Setting::get('announcement_enabled', '0') === '1';
        $announcementText = \App\Models\Setting::get('announcement_text', '');
        $announcementLink = \App\Models\Setting::get('announcement_link', '');
    @endphp
    @if($announcementEnabled && $announcementText)
        <div class="bg-gradient-to-r from-cyan-500 to-violet-500 text-white text-center text-sm font-semibold py-2.5 px-4 relative z-50">
            @if($announcementLink)
                <a href="{{ $announcementLink }}" class="hover:underline">{{ $announcementText }}</a>
            @else
                {{ $announcementText }}
            @endif
        </div>
    @endif

    @include('partials.navbar')

    <main class="pt-20">
        @yield('content')
    </main>

    @include('partials.footer')

    {{-- WhatsApp Chat Button (set number in Admin > Settings > whatsapp_number) --}}
    @php
        $whatsappNumber = preg_replace('/\D/', '', (string) \App\Models\Setting::get('whatsapp_number', ''));
        $whatsappMessage = \App\Models\Setting::get('whatsapp_message', 'Hi! I have a question.');
    @endphp
    @if($whatsappNumber)
        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode($whatsappMessage) }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
            class="fixed bottom-6 right-6 z-[9998] w-14 h-14 rounded-full bg-[#25D366] flex items-center justify-center shadow-lg hover:scale-110 transition-transform">
            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
            </svg>
        </a>
    @endif

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

