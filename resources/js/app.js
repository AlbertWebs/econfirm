import './bootstrap';

import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';

Alpine.plugin(intersect);

document.addEventListener('alpine:init', () => {
    Alpine.data('pwaInstall', () => ({
        showInstallPrompt: false,
        deferredPrompt: null,
        isInstalled: false,
        init() {
            const isStandalone =
                window.matchMedia('(display-mode: standalone)').matches ||
                window.navigator.standalone;

            if (isStandalone) {
                this.isInstalled = true;
                return;
            }

            const dismissed = sessionStorage.getItem('pwa-prompt-dismissed');
            if (dismissed === 'true') {
                return;
            }

            const showPrompt = () => {
                if (
                    !this.isInstalled &&
                    sessionStorage.getItem('pwa-prompt-dismissed') !== 'true'
                ) {
                    this.showInstallPrompt = true;
                }
            };

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                this.deferredPrompt = e;
                setTimeout(showPrompt, 2000);
            });

            window.addEventListener('appinstalled', () => {
                this.isInstalled = true;
                this.showInstallPrompt = false;
                this.deferredPrompt = null;
            });

            setTimeout(() => {
                if (
                    !this.isInstalled &&
                    sessionStorage.getItem('pwa-prompt-dismissed') !== 'true'
                ) {
                    this.showInstallPrompt = true;
                }
            }, 3000);
        },
        async installApp() {
            if (this.deferredPrompt) {
                this.deferredPrompt.prompt();
                const { outcome } = await this.deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    this.isInstalled = true;
                }
                this.deferredPrompt = null;
            } else {
                alert(
                    'To install this app:\n\n' +
                        'Chrome/Edge: Click the install icon in the address bar\n' +
                        'Firefox: Click the menu button and select "Install"\n' +
                        'Safari: Tap Share button and select "Add to Home Screen"'
                );
            }
            this.showInstallPrompt = false;
        },
        dismissPrompt() {
            this.showInstallPrompt = false;
            sessionStorage.setItem('pwa-prompt-dismissed', 'true');
        },
    }));
});

window.Alpine = Alpine;

Alpine.start();
