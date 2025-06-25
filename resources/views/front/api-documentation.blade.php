
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>e-confirm Documentation</title>
    <meta name="description" content="e-confirm - Secure Escrow Service Documentation" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
      :root {
        --primary-color: #18743C;
        --primary-hover: #145a30;
        --text-muted: #6c757d;
        --border-color: #e9ecef;
      }
      
      .navbar-brand {
        color: var(--primary-color) !important;
        font-weight: 700;
        font-size: 1.5rem;
      }
      
      .sidebar {
        position: fixed;
        top: 76px;
        bottom: 0;
        left: 0;
        z-index: 100;
        padding: 48px 0 0;
        box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        background-color: #fff;
        border-right: 1px solid var(--border-color);
        overflow-x: hidden;
        overflow-y: auto;
      }
      
      .sidebar .nav-link {
        font-weight: 500;
        color: #333;
        padding: 0.75rem 1.5rem;
        border-left: 3px solid transparent;
        transition: all 0.2s ease;
      }
      
      .sidebar .nav-link:hover {
        color: var(--primary-color);
        background-color: #f8f9fa;
      }
      
      .sidebar .nav-link.active {
        color: var(--primary-color);
        background-color: #f0f8f4;
        border-left-color: var(--primary-color);
      }
      
      .main-content {
        margin-top: 76px;
        padding-top: 2rem;
      }
      
      .doc-content {
        max-width: 800px;
      }
      
      .section {
        margin-bottom: 4rem;
        scroll-margin-top: 100px;
      }
      
      .section h2 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--border-color);
      }
      
      .section h3 {
        color: #333;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 1rem;
      }
      
      .code-block {
        background-color: #f8f9fa;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 1rem;
        margin: 1rem 0;
        font-family: 'Monaco', 'Menlo', monospace;
        font-size: 0.875rem;
        overflow-x: auto;
        white-space: pre-wrap;
      }
      
      .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
      }
      
      .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
      }
      
      .card {
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
      }
      
      .text-primary {
        color: var(--primary-color) !important;
      }
      
      .badge.bg-primary {
        background-color: var(--primary-color) !important;
      }
      
      @media (max-width: 767.98px) {
        .sidebar {
          top: 0;
          position: relative;
          height: auto;
          border-right: none;
          border-bottom: 1px solid var(--border-color);
        }
        
        .main-content {
          margin-top: 0;
        }
      }
    </style>
  </head>

  <body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm fixed-top">
      <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="#introduction">
          e-confirm
        </a>
        
        <button
          class="navbar-toggler d-lg-none"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#sidebarMenu"
          aria-controls="sidebarMenu"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="navbar-nav ms-auto">
          <a class="nav-link text-muted" href="#contact">
            Support
          </a>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block bg-white sidebar collapse" id="sidebarMenu">
          <div class="position-sticky pt-3">
            <ul class="nav flex-column" id="sidebarNav">
              <li class="nav-item">
                <a class="nav-link active" href="#introduction">
                  <span class="me-2">📖</span>
                  Introduction
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#how-it-works">
                  <span class="me-2">⚙️</span>
                  How It Works
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#api-reference">
                  <span class="me-2">🔗</span>
                  API Reference
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#faqs">
                  <span class="me-2">❓</span>
                  FAQs
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#contact">
                  <span class="me-2">📧</span>
                  Contact
                </a>
              </li>
            </ul>
          </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
          <div class="doc-content">
            
            <!-- Introduction Section -->
            <section id="introduction" class="section">
              <h2>Introduction</h2>
              
              <div class="lead mb-4">
                Welcome to e-confirm, a secure and reliable escrow service designed to protect your transactions and build trust between parties.
              </div>
              
              <p>
                e-confirm provides a safe environment for buyers and sellers to complete transactions with confidence. 
                Our platform holds funds securely until all conditions are met, ensuring that both parties are protected 
                throughout the entire process.
              </p>
              
              <div class="row mt-4">
                <div class="col-md-4 mb-3">
                  <div class="card h-100">
                    <div class="card-body text-center">
                      <div class="text-primary fs-1 mb-3">🔒</div>
                      <h5 class="card-title">Secure</h5>
                      <p class="card-text">Bank-level security with encrypted transactions and secure fund holding.</p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4 mb-3">
                  <div class="card h-100">
                    <div class="card-body text-center">
                      <div class="text-primary fs-1 mb-3">⚡</div>
                      <h5 class="card-title">Fast</h5>
                      <p class="card-text">Quick transaction processing with real-time status updates.</p>
                    </div>
                  </div>
                </div>
                
                <div class="col-md-4 mb-3">
                  <div class="card h-100">
                    <div class="card-body text-center">
                      <div class="text-primary fs-1 mb-3">🤝</div>
                      <h5 class="card-title">Trusted</h5>
                      <p class="card-text">Trusted by thousands of users worldwide for secure transactions.</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-4">
                <h3>Getting Started</h3>
                <p>
                  To begin using e-confirm, you'll need to create an account and verify your identity. 
                  Once verified, you can start creating escrow transactions immediately.
                </p>
                
                <div class="code-block">
<strong>Quick Start:</strong>
1. Sign up for an account
2. Complete identity verification
3. Create your first escrow transaction
4. Invite parties to participate
                </div>
              </div>
            </section>

            <!-- How It Works Section -->
            <section id="how-it-works" class="section">
              <h2>How It Works</h2>
              
              <p>
                e-confirm's escrow process is designed to be simple, secure, and transparent. 
                Here's how our three-step process works:
              </p>
              
              <div class="row mt-4">
                <div class="col-lg-4 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-primary rounded-circle p-3 me-3">1</div>
                        <h5 class="card-title mb-0">Agreement</h5>
                      </div>
                      <p class="card-text">
                        Buyer and seller agree on transaction terms. The buyer deposits funds into the secure escrow account.
                      </p>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-primary rounded-circle p-3 me-3">2</div>
                        <h5 class="card-title mb-0">Delivery</h5>
                      </div>
                      <p class="card-text">
                        Seller delivers goods or services as agreed. Both parties can track progress through our platform.
                      </p>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-primary rounded-circle p-3 me-3">3</div>
                        <h5 class="card-title mb-0">Release</h5>
                      </div>
                      <p class="card-text">
                        Once buyer confirms satisfaction, funds are released to the seller. Disputes are handled by our team.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              
              <h3>Transaction States</h3>
              <p>Every escrow transaction goes through several states:</p>
              
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>State</th>
                      <th>Description</th>
                      <th>Actions Available</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="badge bg-warning">Pending</span></td>
                      <td>Transaction created, awaiting funding</td>
                      <td>Fund escrow, Cancel</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-info">Funded</span></td>
                      <td>Funds secured, awaiting delivery</td>
                      <td>Confirm delivery, Request refund</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-primary">In Progress</span></td>
                      <td>Goods/services being delivered</td>
                      <td>Update status, Communicate</td>
                    </tr>
                    <tr>
                      <td><span class="badge bg-success">Complete</span></td>
                      <td>Transaction successfully completed</td>
                      <td>Leave feedback</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <h3>Security Measures</h3>
              <ul>
                <li><strong>Identity Verification:</strong> All users must verify their identity before participating</li>
                <li><strong>Secure Storage:</strong> Funds are held in segregated accounts with leading financial institutions</li>
                <li><strong>Encrypted Communication:</strong> All communications are encrypted end-to-end</li>
                <li><strong>Dispute Resolution:</strong> Professional mediation service for disputed transactions</li>
              </ul>
            </section>

            <!-- API Reference Section -->
            <section id="api-reference" class="section">
              <h2>API Reference</h2>
              
              <p>
                The e-confirm API allows you to integrate escrow functionality directly into your applications. 
                Our RESTful API is designed to be simple and intuitive while providing powerful functionality.
              </p>
              
              <h3>Authentication</h3>
              <p>All API requests require authentication using an API key. Include your API key in the Authorization header:</p>
              
              <div class="code-block">
<strong>Authorization:</strong> Bearer YOUR_API_KEY
              </div>
              
              <h3>Base URL</h3>
              <div class="code-block">
https://api.econfirm.co.ke/v1
              </div>
              
              <h3>Create Escrow Transaction</h3>
              <p>Creates a new escrow transaction between a buyer and seller.</p>
              
              <div class="code-block">
<strong>POST</strong> /transactions

<strong>Request Body:</strong>
{
  "buyer_email": "buyer@example.com",
  "seller_email": "seller@example.com",
  "amount": 1000.00,
  "currency": "USD",
  "description": "Website development services",
  "terms": "Payment upon completion of website"
}
              </div>
              
              <h3>Get Transaction Status</h3>
              <p>Retrieves the current status of an escrow transaction.</p>
              
              <div class="code-block">
<strong>GET</strong> /transactions/{transaction_id}

<strong>Response:</strong>
{
  "id": "txn_123456789",
  "status": "funded",
  "amount": 1000.00,
  "currency": "USD",
  "created_at": "2024-01-15T10:00:00Z",
  "buyer": {
    "email": "buyer@example.com",
    "verified": true
  },
  "seller": {
    "email": "seller@example.com",
    "verified": true
  }
}
              </div>
              
              <h3>Release Funds</h3>
              <p>Releases escrowed funds to the seller upon buyer confirmation.</p>
              
              <div class="code-block">
<strong>POST</strong> /transactions/{transaction_id}/release

<strong>Request Body:</strong>
{
  "confirmation_code": "ABC123",
  "notes": "Goods received in perfect condition"
}
              </div>
              
              <h3>Error Handling</h3>
              <p>The API uses conventional HTTP response codes to indicate success or failure:</p>
              
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Description</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>200</td>
                      <td>Success</td>
                    </tr>
                    <tr>
                      <td>400</td>
                      <td>Bad Request - Invalid parameters</td>
                    </tr>
                    <tr>
                      <td>401</td>
                      <td>Unauthorized - Invalid API key</td>
                    </tr>
                    <tr>
                      <td>404</td>
                      <td>Not Found - Resource doesn't exist</td>
                    </tr>
                    <tr>
                      <td>500</td>
                      <td>Internal Server Error</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              
              <div class="alert alert-info mt-4">
                <strong>Rate Limits:</strong> API requests are limited to 1000 requests per hour per API key. 
                Contact support if you need higher limits.
              </div>
            </section>

            <!-- FAQs Section -->
            <section id="faqs" class="section">
              <h2>Frequently Asked Questions</h2>
              
              <div class="accordion" id="faqAccordion">
                
                <div class="accordion-item">
                  <h3 class="accordion-header" id="faq1">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                      What is escrow and how does it work?
                    </button>
                  </h3>
                  <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      Escrow is a financial arrangement where a third party (e-confirm) holds and regulates payment of funds 
                      between two parties involved in a transaction. It helps make transactions more secure by keeping the 
                      payment in a secure escrow account until all conditions are met.
                    </div>
                  </div>
                </div>
                
                <div class="accordion-item">
                  <h3 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                      How long does the escrow process take?
                    </button>
                  </h3>
                  <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      The escrow process duration depends on the agreed terms between buyer and seller. Simple transactions 
                      can be completed in a few hours, while complex transactions may take several weeks. e-confirm provides 
                      real-time updates throughout the process.
                    </div>
                  </div>
                </div>
                
                <div class="accordion-item">
                  <h3 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                      What are the fees for using e-confirm?
                    </button>
                  </h3>
                  <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      e-confirm charges a competitive fee based on the transaction amount. Fees start at 1.5% for transactions 
                      under ksh1,000 and decrease for larger amounts. There are no setup fees or monthly charges - you only pay 
                      when you use the service.
                    </div>
                  </div>
                </div>
                
                <div class="accordion-item">
                  <h3 class="accordion-header" id="faq4">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4">
                      Is my money safe with e-confirm?
                    </button>
                  </h3>
                  <div id="collapse4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      Yes, your funds are completely secure. We hold all escrowed funds in segregated accounts with top-tier 
                      financial institutions. Your money is FDIC insured and never mixed with our operating funds. We also 
                      use bank-level security measures to protect your information.
                    </div>
                  </div>
                </div>
                
                <div class="accordion-item">
                  <h3 class="accordion-header" id="faq5">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5">
                      What happens if there's a dispute?
                    </button>
                  </h3>
                  <div id="collapse5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      If a dispute arises, e-confirm provides professional mediation services. Our dispute resolution team 
                      reviews all evidence and communications to make a fair decision. Most disputes are resolved within 
                      5-7 business days. Our goal is to ensure a fair outcome for all parties.
                    </div>
                  </div>
                </div>
                
                <div class="accordion-item">
                  <h3 class="accordion-header" id="faq6">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6">
                      Can I cancel an escrow transaction?
                    </button>
                  </h3>
                  <div id="collapse6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      Yes, transactions can be cancelled under certain conditions. If funds haven't been deposited yet, 
                      either party can cancel freely. Once funded, cancellation requires agreement from both parties or 
                      can be processed through our dispute resolution system if there's a valid reason.
                    </div>
                  </div>
                </div>
                
              </div>
              
              <div class="mt-5 p-4 bg-light rounded">
                <h4>Still have questions?</h4>
                <p class="mb-3">
                  Can't find the answer you're looking for? Our support team is here to help you with any questions 
                  about our escrow services.
                </p>
                <a href="#contact" class="btn btn-primary">Contact Support</a>
              </div>
            </section>

            <!-- Contact Section -->
            <section id="contact" class="section">
              <h2>Contact Us</h2>
              
              <p>
                We're here to help! Whether you have questions about our escrow services, need technical support, 
                or want to discuss a custom solution, our team is ready to assist you.
              </p>
              
              <div class="row mt-4">
                <div class="col-lg-8">
                  <div class="row">
                    <div class="col-md-6 mb-4">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="d-flex align-items-center mb-3">
                            <div class="text-primary fs-3 me-3">📧</div>
                            <h5 class="card-title mb-0">Email Support</h5>
                          </div>
                          <p class="card-text">Get help with your account or transactions</p>
                          <a href="mailto:tickets@e-confirm.p.tawk.email" class="btn btn-outline-primary">
                            support@econfirm.co.ke
                          </a>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="d-flex align-items-center mb-3">
                            <div class="text-primary fs-3 me-3">💬</div>
                            <h5 class="card-title mb-0">Live Chat</h5>
                          </div>
                          <p class="card-text">Chat with our support team in real-time</p>
                          <a target="new" href="https://tawk.to/chat/6859484414b543191cac8bc1/1iuec4jii" class="btn btn-primary">Start Chat</a>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="d-flex align-items-center mb-3">
                            <div class="text-primary fs-3 me-3">📞</div>
                            <h5 class="card-title mb-0">Phone Support</h5>
                          </div>
                          <p class="card-text">Speak directly with our support team</p>
                          <a href="tel:+254 723000000" class="btn btn-outline-primary">
                            +254 72 300 0000
                          </a>
                        </div>
                      </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                      <div class="card h-100">
                        <div class="card-body">
                          <div class="d-flex align-items-center mb-3">
                            <div class="text-primary fs-3 me-3">🔧</div>
                            <h5 class="card-title mb-0">API Support</h5>
                          </div>
                          <p class="card-text">Technical support for developers</p>
                          <a href="mailto:api@econfirm.co.ke" class="btn btn-outline-primary">
                            api@econfirm.co.ke
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="col-lg-4">
                  <div class="card">
                    <div class="card-header">
                      <h5 class="mb-0">Business Hours</h5>
                    </div>
                    <div class="card-body">
                      <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                          <strong>Monday - Friday:</strong><br/>
                          6:00 AM - 11:00 PM EAT
                        </li>
                        <li class="mb-2">
                          <strong>Saturday:</strong><br/>
                          6:00 AM - 9:00 PM EAT
                        </li>
                        <li class="mb-2">
                          <strong>Sunday:</strong><br/>
                          9:00 AM - 9:00 PM EAT
                        </li>
                        <li class="mb-2">
                          <strong>Holidays:</strong><br/>
                          9:00 AM - 9:00 PM EAT
                        </li>
                        <br>
                      </ul>
                      
                      <hr />
                      
                      <div class="text-muted small">
                        <strong>Emergency Support:</strong><br/>
                        For urgent issues outside business hours, 
                        email us and we'll respond within 2 hours.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row mt-5">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Office Location</h5>
                      <div class="row">
                        <div class="col-md-6">
                          <address>
                            <strong>Confirm Diligence Solutions Limited</strong><br/>
                            Prestige Plaza<br/>
                            Ngong Road, Nairobi<br/>
                            Kenya
                          </address>
                        </div>
                        <div class="col-md-6">
                          <p>
                            <strong>Visit us:</strong> By appointment only<br/>
                            <strong>Parking:</strong> Free visitor parking available<br/>
                            <strong>Accessibility:</strong> Wheelchair accessible
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-5 p-4 bg-light rounded">
                <div class="row align-items-center">
                  <div class="col-md-8">
                    <h4 class="mb-2">Ready to get started?</h4>
                    <p class="mb-0">
                      Create your first escrow transaction today and experience the security and peace of mind 
                      that e-confirm provides.
                    </p>
                  </div>
                  <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <button class="btn btn-primary btn-lg">Get Started</button>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </main>
      </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript for sidebar active state -->
    <script>
      // Handle active sidebar navigation
      document.addEventListener('DOMContentLoaded', function() {
        const sidebarLinks = document.querySelectorAll('#sidebarNav .nav-link');
        const sections = document.querySelectorAll('.section');
        
        // Function to update active link
        function updateActiveLink() {
          let current = '';
          sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (scrollY >= (sectionTop - 200)) {
              current = section.getAttribute('id');
            }
          });
          
          sidebarLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
              link.classList.add('active');
            }
          });
        }
        
        // Update active link on scroll
        window.addEventListener('scroll', updateActiveLink);
        
        // Smooth scroll for sidebar links
        sidebarLinks.forEach(link => {
          link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
              targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
          });
        });
        
        // Initial call to set active link
        updateActiveLink();
      });
    </script>
  </body>
</html>
