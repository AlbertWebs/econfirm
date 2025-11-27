@extends('front.master')

@section('content')
<style>
    /* Custom Styles for V2 - Classy Animated Version */
    :root {
        --primary: #18743c;
        --primary-dark: #145a2f;
        --accent: #10b981;
        --dark: #0f172a;
        --light: #f8fafc;
        --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    body {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        overflow-x: hidden;
    }

    /* Animated Background Elements */
    .animated-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .animated-bg::before,
    .animated-bg::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.1;
        animation: float 20s ease-in-out infinite;
    }

    .animated-bg::before {
        width: 500px;
        height: 500px;
        background: var(--primary);
        top: -200px;
        left: -200px;
        animation-delay: 0s;
    }

    .animated-bg::after {
        width: 400px;
        height: 400px;
        background: var(--accent);
        bottom: -150px;
        right: -150px;
        animation-delay: 10s;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0) scale(1); }
        33% { transform: translate(30px, -30px) scale(1.1); }
        66% { transform: translate(-20px, 20px) scale(0.9); }
    }

    /* Hero Section V2 */
    .hero-v2 {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        padding: 8rem 0 4rem;
        z-index: 1;
    }

    .hero-content-v2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4rem;
        align-items: center;
        position: relative;
    }

    .hero-text-v2 {
        animation: fadeInUp 1s ease-out;
    }

    .hero-badge-v2 {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, rgba(24, 116, 60, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
        border: 1px solid rgba(24, 116, 60, 0.2);
        color: var(--primary);
        padding: 0.75rem 1.25rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 2rem;
        animation: slideInLeft 0.8s ease-out;
    }

    .hero-badge-v2 svg {
        animation: pulse 2s ease-in-out infinite;
    }

    .hero-title-v2 {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 1.5rem;
        color: var(--dark);
        animation: fadeInUp 1s ease-out 0.2s both;
    }

    .hero-title-v2 .gradient-text-v2 {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        position: relative;
    }

    .hero-description-v2 {
        font-size: 1.25rem;
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 2.5rem;
        animation: fadeInUp 1s ease-out 0.4s both;
    }

    .hero-buttons-v2 {
        display: flex;
        gap: 1rem;
        margin-bottom: 3rem;
        animation: fadeInUp 1s ease-out 0.6s both;
    }

    .btn-v2 {
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-v2::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn-v2:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-primary-v2 {
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        color: white;
        border: none;
        box-shadow: 0 10px 30px rgba(24, 116, 60, 0.3);
    }

    .btn-primary-v2:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(24, 116, 60, 0.4);
    }

    .btn-outline-v2 {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .btn-outline-v2:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-2px);
    }

    /* Terminal Component */
    .terminal-container {
        position: relative;
        animation: fadeInRight 1s ease-out 0.8s both;
    }

    .terminal {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
        overflow: hidden;
        position: relative;
    }

    .terminal-header {
        background: rgba(0, 0, 0, 0.3);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .terminal-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        animation: pulse 2s ease-in-out infinite;
    }

    .terminal-dot:nth-child(1) { background: #ef4444; }
    .terminal-dot:nth-child(2) { background: #f59e0b; animation-delay: 0.2s; }
    .terminal-dot:nth-child(3) { background: #10b981; animation-delay: 0.4s; }

    .terminal-title {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.875rem;
        font-weight: 500;
        margin-left: auto;
    }

    .terminal-body {
        padding: 2rem;
        font-family: 'Courier New', monospace;
        color: #e2e8f0;
        min-height: 400px;
        position: relative;
    }

    .terminal-line {
        margin-bottom: 1rem;
        opacity: 0;
        animation: fadeIn 0.5s ease-out forwards;
    }

    .terminal-prompt {
        color: #10b981;
        font-weight: 600;
    }

    .terminal-command {
        color: #60a5fa;
    }

    .terminal-output {
        color: #cbd5e1;
        margin-top: 0.5rem;
        padding-left: 1.5rem;
    }

    .terminal-cursor {
        display: inline-block;
        width: 8px;
        height: 18px;
        background: #10b981;
        margin-left: 4px;
        animation: blink 1s infinite;
        vertical-align: middle;
    }

    @keyframes blink {
        0%, 50% { opacity: 1; }
        51%, 100% { opacity: 0; }
    }

    /* Floating SVG Icons */
    .floating-icons {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: -1;
    }

    .floating-icon {
        position: absolute;
        opacity: 0.1;
        animation: floatIcon 15s ease-in-out infinite;
    }

    .floating-icon svg {
        width: 60px;
        height: 60px;
        fill: var(--primary);
    }

    @keyframes floatIcon {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        25% { transform: translate(20px, -30px) rotate(90deg); }
        50% { transform: translate(-20px, -60px) rotate(180deg); }
        75% { transform: translate(-40px, -30px) rotate(270deg); }
    }

    /* Features Section V2 */
    .features-v2 {
        padding: 8rem 0;
        position: relative;
    }

    .section-header-v2 {
        text-align: center;
        margin-bottom: 5rem;
    }

    .section-badge-v2 {
        display: inline-block;
        background: linear-gradient(135deg, rgba(24, 116, 60, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
        color: var(--primary);
        padding: 0.5rem 1.25rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .section-title-v2 {
        font-size: 3rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 1rem;
    }

    .section-description-v2 {
        font-size: 1.25rem;
        color: #64748b;
        max-width: 700px;
        margin: 0 auto;
    }

    .features-grid-v2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .feature-card-v2 {
        background: white;
        border-radius: 20px;
        padding: 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(24, 116, 60, 0.1);
    }

    .feature-card-v2::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .feature-card-v2:hover::before {
        transform: scaleX(1);
    }

    .feature-card-v2:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(24, 116, 60, 0.15);
    }

    .feature-icon-v2 {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, rgba(24, 116, 60, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .feature-icon-v2 svg {
        width: 32px;
        height: 32px;
        stroke: var(--primary);
        animation: iconFloat 3s ease-in-out infinite;
    }

    @keyframes iconFloat {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    .feature-title-v2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 1rem;
    }

    .feature-description-v2 {
        color: #64748b;
        line-height: 1.7;
    }

    /* Stats Section */
    .stats-v2 {
        padding: 6rem 0;
        background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
        position: relative;
        overflow: hidden;
    }

    .stats-v2::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.1;
    }

    .stats-grid-v2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 3rem;
        position: relative;
        z-index: 1;
    }

    .stat-item-v2 {
        text-align: center;
        color: white;
    }

    .stat-number-v2 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: linear-gradient(180deg, #ffffff 0%, rgba(255, 255, 255, 0.8) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label-v2 {
        font-size: 1.125rem;
        opacity: 0.9;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    /* Responsive */
    @media (max-width: 968px) {
        .hero-content-v2 {
            grid-template-columns: 1fr;
        }

        .hero-title-v2 {
            font-size: 2.5rem;
        }

        .terminal-container {
            order: -1;
        }
    }
</style>

<div class="animated-bg"></div>

<!-- Hero Section V2 -->
<section class="hero-v2" id="home">
    <div class="container">
        <div class="hero-content-v2">
            <div class="hero-text-v2">
                <div class="hero-badge-v2">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                    <span>Trusted by 10,000+ clients worldwide</span>
                </div>
                
                <h1 class="hero-title-v2">
                    Secure Transactions with <span class="gradient-text-v2">e-confirm Escrow</span>
                </h1>
                
                <p class="hero-description-v2">
                    Experience the future of secure payments. Our advanced escrow platform protects both buyers and sellers with cutting-edge technology and seamless transactions.
                </p>
                
                <div class="hero-buttons-v2">
                    <button class="btn-v2 btn-primary-v2" onclick="window.location.href='#home'">
                        Get Started
                        <svg style="margin-left: 8px; display: inline-block; vertical-align: middle;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                    <button class="btn-v2 btn-outline-v2" onclick="window.location.href='#features'">
                        Learn More
                    </button>
                </div>
            </div>
            
            <div class="terminal-container">
                <div class="terminal">
                    <div class="terminal-header">
                        <div class="terminal-dot"></div>
                        <div class="terminal-dot"></div>
                        <div class="terminal-dot"></div>
                        <div class="terminal-title">e-confirm Terminal</div>
                    </div>
                    <div class="terminal-body" id="terminalBody">
                        <!-- Terminal content will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="floating-icons">
        <div class="floating-icon" style="top: 10%; left: 5%; animation-delay: 0s;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="floating-icon" style="top: 30%; right: 10%; animation-delay: 2s;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                <circle cx="12" cy="16" r="1"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        <div class="floating-icon" style="bottom: 20%; left: 15%; animation-delay: 4s;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-v2">
    <div class="container">
        <div class="stats-grid-v2">
            <div class="stat-item-v2">
                <div class="stat-number-v2" data-count="10000">0</div>
                <div class="stat-label-v2">Active Users</div>
            </div>
            <div class="stat-item-v2">
                <div class="stat-number-v2" data-count="50000">0</div>
                <div class="stat-label-v2">Transactions</div>
            </div>
            <div class="stat-item-v2">
                <div class="stat-number-v2" data-count="99.9">0</div>
                <div class="stat-label-v2">Uptime %</div>
            </div>
            <div class="stat-item-v2">
                <div class="stat-number-v2" data-count="24">0</div>
                <div class="stat-label-v2">Support Hours</div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section V2 -->
<section class="features-v2" id="features">
    <div class="container">
        <div class="section-header-v2">
            <div class="section-badge-v2">Why Choose Us</div>
            <h2 class="section-title-v2">Features That Set Us Apart</h2>
            <p class="section-description-v2">
                Our comprehensive escrow service is designed to provide security, convenience, and peace of mind for all types of transactions.
            </p>
        </div>
        
        <div class="features-grid-v2">
            <div class="feature-card-v2">
                <div class="feature-icon-v2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h3 class="feature-title-v2">Bank-Level Security</h3>
                <p class="feature-description-v2">
                    Advanced encryption and security protocols ensure your transactions are protected at every step.
                </p>
            </div>
            
            <div class="feature-card-v2">
                <div class="feature-icon-v2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <circle cx="12" cy="16" r="1"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h3 class="feature-title-v2">Fraud Protection</h3>
                <p class="feature-description-v2">
                    Multi-layered verification and monitoring systems to prevent fraud and ensure transaction integrity.
                </p>
            </div>
            
            <div class="feature-card-v2">
                <div class="feature-icon-v2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <h3 class="feature-title-v2">Lightning Fast</h3>
                <p class="feature-description-v2">
                    Process transactions in real-time with instant notifications and status updates.
                </p>
            </div>
            
            <div class="feature-card-v2">
                <div class="feature-icon-v2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3 class="feature-title-v2">Dispute Resolution</h3>
                <p class="feature-description-v2">
                    Professional mediation services to resolve disputes fairly and efficiently.
                </p>
            </div>
            
            <div class="feature-card-v2">
                <div class="feature-icon-v2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <h3 class="feature-title-v2">Verified Transactions</h3>
                <p class="feature-description-v2">
                    Every transaction is verified and tracked with complete transparency.
                </p>
            </div>
            
            <div class="feature-card-v2">
                <div class="feature-icon-v2">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"/>
                        <line x1="12" y1="22.08" x2="12" y2="12"/>
                    </svg>
                </div>
                <h3 class="feature-title-v2">API Integration</h3>
                <p class="feature-description-v2">
                    Seamlessly integrate escrow services into your platform with our robust API.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <div class="cta-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h2>Ready to Secure Your Transactions?</h2>
            <p>Join thousands of satisfied clients who trust our escrow service for their important transactions.</p>
            <div class="cta-buttons">
                <a href="#home" class="btn btn-primary btn-lg">Get Started Now</a>
                <a href="mailto:support@econfirm.co.ke" class="btn btn-outline btn-lg">Contact Support</a>
            </div>
            <p class="cta-note">No obligation. Cancel anytime.</p>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Terminal Typing Animation
    const terminalBody = document.getElementById('terminalBody');
    const commands = [
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow init --secure',
            output: 'Initializing secure escrow session...\n  ✓ Encryption enabled\n  ✓ Multi-factor auth active\n  ✓ Ready for transactions'
        },
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow create --type "real-estate" --amount 5000000',
            output: '✓ Transaction created successfully\n  ID: E-XYZ789\n  Type: Real Estate\n  Amount: KES 5,000,000\n  Status: Pending Payment'
        },
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow fund --method mpesa --phone +254712345678',
            output: 'Processing M-Pesa payment...\n  ✓ STK Push sent\n  ✓ Waiting for confirmation\n  ✓ Funds secured in escrow\n  Status: Funds Held'
        },
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow verify --transaction E-XYZ789',
            output: 'Verifying transaction details...\n  ✓ Buyer identity: Verified\n  ✓ Seller identity: Verified\n  ✓ Funds: Secured (KES 5,000,000)\n  ✓ Contract terms: Validated'
        },
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow monitor --transaction E-XYZ789',
            output: 'Monitoring transaction status...\n  [████████████████] 100%\n  ✓ Payment: Confirmed\n  ✓ Delivery: Pending\n  ✓ Escrow: Active'
        },
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow release --transaction E-XYZ789 --approve',
            output: 'Releasing funds to seller...\n  ✓ Buyer approval: Confirmed\n  ✓ Funds transferred: KES 5,000,000\n  ✓ Transaction: Completed\n  ✓ Notifications: Sent to all parties'
        },
        {
            prompt: 'econfirm@escrow:~$',
            command: 'escrow stats --global',
            output: 'Global Statistics:\n  Active Transactions: 1,234\n  Total Volume: KES 12.5M\n  Success Rate: 99.8%\n  Avg. Completion: 2.3 days\n  System Uptime: 99.9%'
        }
    ];

    let currentCommand = 0;
    let currentChar = 0;
    let isTypingCommand = true;
    let isTypingOutput = false;
    let outputChar = 0;
    let currentLine = null;
    let outputDiv = null;

    function typeTerminal() {
        if (currentCommand >= commands.length) {
            // Clear and restart
            setTimeout(() => {
                terminalBody.innerHTML = '';
                currentCommand = 0;
                currentChar = 0;
                isTypingCommand = true;
                isTypingOutput = false;
                outputChar = 0;
                currentLine = null;
                outputDiv = null;
                setTimeout(typeTerminal, 500);
            }, 3000);
            return;
        }

        const cmd = commands[currentCommand];

        if (isTypingCommand) {
            if (!currentLine) {
                currentLine = document.createElement('div');
                currentLine.className = 'terminal-line';
                terminalBody.appendChild(currentLine);
                
                const prompt = document.createElement('span');
                prompt.className = 'terminal-prompt';
                prompt.textContent = cmd.prompt + ' ';
                currentLine.appendChild(prompt);
            }

            if (currentChar < cmd.command.length) {
                const commandSpan = currentLine.querySelector('.terminal-command') || document.createElement('span');
                if (!currentLine.querySelector('.terminal-command')) {
                    commandSpan.className = 'terminal-command';
                    currentLine.appendChild(commandSpan);
                }
                commandSpan.textContent = cmd.command.substring(0, currentChar + 1);
                currentChar++;
                setTimeout(typeTerminal, 40 + Math.random() * 20); // Variable typing speed
            } else {
                // Add cursor after command
                const cursor = document.createElement('span');
                cursor.className = 'terminal-cursor';
                currentLine.appendChild(cursor);
                
                setTimeout(() => {
                    cursor.remove();
                    isTypingCommand = false;
                    isTypingOutput = true;
                    outputChar = 0;
                    
                    // Create output div
                    outputDiv = document.createElement('div');
                    outputDiv.className = 'terminal-output';
                    currentLine.appendChild(outputDiv);
                    
                    typeTerminal();
                }, 300);
            }
        } else if (isTypingOutput) {
            // Type output character by character
            if (outputChar < cmd.output.length) {
                const char = cmd.output[outputChar];
                if (char === '\n') {
                    outputDiv.innerHTML += '<br>';
                } else {
                    outputDiv.textContent += char;
                }
                outputChar++;
                setTimeout(typeTerminal, 20 + Math.random() * 10); // Faster output typing
            } else {
                // Output complete, move to next command
                isTypingOutput = false;
                currentCommand++;
                currentChar = 0;
                currentLine = null;
                isTypingCommand = true;
                
                setTimeout(typeTerminal, 1500);
            }
        }
    }

    // Start typing after a short delay
    setTimeout(typeTerminal, 1000);

    // Animated Counter for Stats
    function animateCounter(element) {
        const target = parseFloat(element.getAttribute('data-count'));
        const duration = 2000;
        const steps = 60;
        const increment = target / steps;
        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target % 1 === 0 ? Math.floor(target) : target.toFixed(1);
                clearInterval(timer);
            } else {
                element.textContent = target % 1 === 0 ? Math.floor(current) : current.toFixed(1);
            }
        }, duration / steps);
    }

    // Observe stats section for animation
    const statsSection = document.querySelector('.stats-v2');
    if (statsSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stat-number-v2');
                    counters.forEach(counter => {
                        if (!counter.classList.contains('animated')) {
                            counter.classList.add('animated');
                            animateCounter(counter);
                        }
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(statsSection);
    }

    // Animate feature cards on scroll
    const featureCards = document.querySelectorAll('.feature-card-v2');
    const cardObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '0';
                    entry.target.style.transform = 'translateY(30px)';
                    entry.target.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, 50);
                }, index * 100);
                cardObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    featureCards.forEach(card => {
        card.style.opacity = '0';
        cardObserver.observe(card);
    });
});
</script>
@endsection

