import './bootstrap';
import '../css/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.querySelector('[data-mobile-toggle]');
    const mobileMenu = document.querySelector('[data-mobile-menu]');

    if (toggleButton && mobileMenu) {
        toggleButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Render Lucide icons loaded from CDN
    if (window.lucide?.createIcons) {
        window.lucide.createIcons();
    }

    // AJAX add-to-cart
    document.querySelectorAll('form[data-cart-add]').forEach((form) => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn?.textContent;

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                if (!res.ok) throw new Error('Request failed');
                const data = await res.json();

                // Update cart badges
                document.querySelectorAll('[data-cart-count]').forEach((el) => {
                    el.textContent = data.quantity ?? data.items ?? 0;
                });

                if (submitBtn) {
                    submitBtn.textContent = 'Added!';
                    submitBtn.classList.add('ring-2', 'ring-cyan-500/40');
                    setTimeout(() => {
                        submitBtn.textContent = originalText || 'Add to Cart';
                        submitBtn.classList.remove('ring-2', 'ring-cyan-500/40');
                    }, 1200);
                }
            } catch (error) {
                // Fallback: submit normally
                form.submit();
            }
        });
    });

    // Toast helper
    const toastRoot = document.getElementById('toast-root');
    function showToast(message, type = 'success') {
        if (!message || !toastRoot) return;
        const el = document.createElement('div');
        const colors = type === 'error'
            ? 'bg-red-500/10 border-red-500/40 text-red-100'
            : 'bg-emerald-500/10 border-emerald-500/40 text-emerald-50';
        el.className = `glass-card border ${colors} rounded-xl px-4 py-3 shadow-lg flex items-center gap-2 text-sm`;
        el.innerHTML = `
            <i data-lucide="${type === 'error' ? 'alert-circle' : 'check-circle'}" class="w-4 h-4"></i>
            <span>${message}</span>
        `;
        toastRoot.appendChild(el);
        if (window.lucide?.createIcons) window.lucide.createIcons({ icons: { AlertCircle: lucide.icons['alert-circle'], CheckCircle: lucide.icons['check-circle'] } });
        setTimeout(() => {
            el.classList.add('opacity-0', 'translate-x-2');
            setTimeout(() => el.remove(), 150);
        }, 3200);
    }

    // SweetAlert helper
    function showSweetAlert(message, type = 'success') {
        if (!message || !window.Swal) return;
        window.Swal.fire({
            icon: type,
            title: type === 'error' ? 'Oops' : 'Success',
            text: message,
            timer: 2800,
            showConfirmButton: false,
            timerProgressBar: true,
        });
    }

    // Consume server flash messages
    const bodyEl = document.body;
    const success = bodyEl.dataset.toastSuccess;
    const errorMsg = bodyEl.dataset.toastError;

    if (success) {
        showSweetAlert(success, 'success');
        showToast(success, 'success');
    }

    if (errorMsg) {
        showSweetAlert(errorMsg, 'error');
        showToast(errorMsg, 'error');
    }
});
