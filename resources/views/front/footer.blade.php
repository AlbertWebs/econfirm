<footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <div class="footer-logo">
                        {{-- logo here --}}
                        <img src="{{ asset('uploads/logo-hoz.png') }}" alt="SecureEscrow Logo" style="height: 50px;">
                        {{-- <span class="logo-text">SecureEscrow</span> --}}
                    </div>
                    <p>e-confirm is a trusted digital platform that provides secure and transparent escrow services for individuals and businesses. We act as a neutral third party to hold and regulate funds during transactions, ensuring that payment is only released once all agreed-upon conditions are fully met. Whether you're buying, selling, or partnering online, e-confirm offers peace of mind with every transaction.</p>
                   
                </div>
                
                <div class="footer-column">
                    <h3>Products</h3>
                    <ul>
                        <li><a href="#">Real Estate Escrow</a></li>
                        <li><a href="#">Vehicle Escrow</a></li>
                        <li><a href="#">Business Escrow</a></li>
                        <li><a href="#">e-commerce Escrow</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Company</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Knowledge Base</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Legal</h3>
                    <ul>
                        <li><a href="{{ route('terms.conditions') }}">Terms of Service</a></li>
                        <li><a href="{{ route('privacy.policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('security') }}">Security & Assurance</a></li>
                        <li><a href="{{ route('complience') }}">Compliance</a></li>
                    </ul>
                </div>
             
            </div>

               {{--  --}}
                <div class="social-links" style="margin:0 auto; border:2px solid rgba(24, 116, 60, 0.1); text-align:center; padding:10px; border-radius:5px;">
                    <a href="https://www.facebook.com/profile.php?id=61576961756928">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                    </a>
                    <a href="https://x.com/econfirmke">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/>
                        </svg>
                    </a>
                    <a href="https://www.instagram.com/econfirmke/">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/company/econfirmke/">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
                            <rect width="4" height="12" x="2" y="9"/>
                            <circle cx="4" cy="4" r="2"/>
                        </svg>
                    </a>
                </div>
                {{--  --}}
            
            <div class="footer-bottom">
                <p>&copy; {{date('Y')}} Confirm Diligence Solutions Limited - All rights reserved.</p>
                <div class="footer-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22,4 12,14.01 9,11.01"/>
                    </svg>
                    <span>Licensed and regulated escrow service</span>
                </div>
            </div>
        </div>
    </footer>