<footer class="bg-gray-900 text-gray-300 pb-16 lg:pb-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-8">
            <!-- Brand Column -->
            <div class="sm:col-span-2 lg:col-span-2">
                <div class="mb-4">
                    <img src="{{ asset('uploads/logo-hoz.png') }}" alt="e-confirm Logo" class="h-12">
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    e-confirm is a trusted digital platform that provides secure and transparent escrow services for individuals and businesses. We act as a neutral third party to hold and regulate funds during transactions, ensuring that payment is only released once all agreed-upon conditions are fully met. Whether you're buying, selling, or partnering online, e-confirm offers peace of mind with every transaction.
                </p>
            </div>
            
            <!-- Products Column -->
            <div>
                <h3 class="text-white font-semibold text-lg mb-4">Products</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Real Estate Escrow</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Vehicle Escrow</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Business Escrow</a></li>
                    <li><a href="#" class="text-gray-400 hover:text-green-400 transition-colors text-sm">e-commerce Escrow</a></li>
                </ul>
            </div>
            
            <!-- Legal Column -->
            <div>
                <h3 class="text-white font-semibold text-lg mb-4">Legal</h3>
                <ul class="space-y-3">
                    <li><a href="{{ route('terms.conditions') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Terms of Service</a></li>
                    <li><a href="{{ route('privacy.policy') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Privacy Policy</a></li>
                    <li><a href="{{ route('security') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Security & Assurance</a></li>
                    <li><a href="{{ route('complience') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm">Compliance</a></li>
                </ul>
            </div>
            
            
        </div>

        <!-- Support Links -->
        <div class="border-t border-gray-800 pt-8 mb-6">
            <div class="flex flex-wrap justify-center items-center gap-4 sm:gap-6">
                <a href="{{ route('support') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm font-medium flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                </a>
                <a href="{{ route('help') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm font-medium flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-book"></i>
                    <span>Help</span>
                </a>
                <a href="{{ route('contact') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm font-medium flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-envelope"></i>
                    <span>Contact</span>
                </a>
                <a href="{{ route('scam.watch') }}" class="text-gray-400 hover:text-green-400 transition-colors text-sm font-medium flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-800">
                    <i class="fas fa-shield-alt"></i>
                    <span>Scam Watch</span>
                </a>
            </div>
        </div>

        <!-- Social Links -->
        <div class="border-t border-gray-800 pt-8 mb-8">
            <div class="flex justify-center items-center gap-4">
                <a href="https://www.facebook.com/profile.php?id=61576961756928" 
                   target="_blank"
                   class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-lg hover:bg-green-600 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                    </svg>
                </a>
                <a href="https://x.com/econfirmke" 
                   target="_blank"
                   class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-lg hover:bg-green-600 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                    </svg>
                </a>
                <a href="https://www.instagram.com/econfirmke/" 
                   target="_blank"
                   class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-lg hover:bg-green-600 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                    </svg>
                </a>
                <a href="https://www.linkedin.com/company/econfirmke/" 
                   target="_blank"
                   class="w-10 h-10 flex items-center justify-center bg-gray-800 rounded-lg hover:bg-green-600 transition-colors group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                        <rect width="4" height="12" x="2" y="9"/>
                        <circle cx="4" cy="4" r="2"/>
                    </svg>
                </a>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="border-t border-gray-800 pt-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400">
                    &copy; {{date('Y')}} Confirm Diligence Solutions Limited - All rights reserved.
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-400">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
                    </svg>
                    <span>Licensed and regulated escrow service</span>
                </div>
            </div>
        </div>
    </div>
</footer>
