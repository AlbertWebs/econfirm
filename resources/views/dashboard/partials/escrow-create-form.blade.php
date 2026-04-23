@php
    $heading = $formHeading ?? 'Create a new escrow';
@endphp
<style>
    .dashboard-escrow-form {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border: 3px solid #18743c;
    }
    .dashboard-escrow-form .form-container {
        padding: 1.5rem;
    }
    .dashboard-escrow-form .form-container h3 {
        text-align: center;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }
    .dashboard-escrow-form .transaction-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .dashboard-escrow-form .form-group {
        display: flex;
        flex-direction: column;
    }
    .dashboard-escrow-form .form-group label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.25rem;
        font-size: 0.875rem;
    }
    .dashboard-escrow-form .form-group input,
    .dashboard-escrow-form .form-group select,
    .dashboard-escrow-form .form-group textarea {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .dashboard-escrow-form .form-group input:focus,
    .dashboard-escrow-form .form-group select:focus,
    .dashboard-escrow-form .form-group textarea:focus {
        outline: none;
        border-color: #18743c;
        box-shadow: 0 0 0 3px rgba(24, 116, 60, 0.1);
    }
    .dashboard-escrow-form .input-with-prefix { position: relative; }
    .dashboard-escrow-form .prefix {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #000000;
        font-size: 0.875rem;
    }
    .dashboard-escrow-form .input-with-prefix input { padding-left: 3rem; }
    .dashboard-escrow-form .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    @media (max-width: 575.98px) {
        .dashboard-escrow-form .form-row { grid-template-columns: 1fr; }
    }
    .dashboard-escrow-form .form-disclaimer {
        font-size: 0.75rem;
        color: #000000;
        text-align: center;
        margin-top: 0.5rem;
    }
    .dashboard-escrow-form .form-disclaimer a { color: #18743c; text-decoration: none; }
    .dashboard-escrow-form .form-disclaimer a:hover { text-decoration: underline; }
    .btn-full { width: 100%; }
</style>

<div class="dashboard-escrow-form" style="max-width: 600px; margin: 0 auto;">
    <div class="form-container">
        <h3 class="mb-0">{{ $heading }}</h3>
        <p class="text-center text-muted small mb-3 mb-md-4">Set up and fund a secure M-Pesa escrow from your account.</p>
        <form class="transaction-form">
            <div class="form-group">
                <label for="transaction-type">Transaction Type</label>
                <select id="transaction-type" name="transaction-type">
                    <option value="">Select a transaction type</option>
                    <option value="ecommerce">E-commerce Marketplace Transactions</option>
                    <option value="services">Professional Services (Consulting, Legal, Accounting)</option>
                    <option value="real-estate">Real Estate (Land, Plots, Rentals)</option>
                    <option value="vehicle">Vehicle Sales (Cars, Motorbikes, Trucks)</option>
                    <option value="business">Business Transfers & Partnerships</option>
                    <option value="freelance">Freelance Work & Digital Services</option>
                    <option value="goods">High-Value Goods (Electronics, Machinery, Furniture)</option>
                    <option value="construction">Construction & Renovation Projects</option>
                    <option value="agriculture">Agricultural Produce & Equipment</option>
                    <option value="legal">Legal Settlements & Compensation</option>
                    <option value="import-export">Import/Export Transactions</option>
                    <option value="tenders">Government or Corporate Tender Payments</option>
                    <option value="education">Education Payments (International Tuition, School Fees)</option>
                    <option value="personal">Personal Loans & Informal Lending</option>
                    <option value="crypto">Crypto & Forex Trading Agreements</option>
                    <option value="rentals">Equipment & Property Rentals</option>
                    <option value="charity">Charity Donations & Fundraising</option>
                    <option value="events">Event Ticket Sales & Bookings</option>
                    <option value="subscriptions">Subscription Services (Software, Memberships)</option>
                    <option value="affiliate">Affiliate Marketing Payments</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group" id="custom-transaction-type-group" style="display:none;">
                <label for="custom-transaction-type">Specify Transaction Type</label>
                <input type="text" id="custom-transaction-type" name="custom-transaction-type" placeholder="Enter custom transaction type" class="w-100">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="payment-method">Payment Method</label>
                    <select id="payment-method" name="payment-method">
                        <option value="mpesa">M-Pesa Number</option>
                        <option value="paybill">Paybill/Buy Goods</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="transaction-amount">Transaction Amount</label>
                    <div class="input-with-prefix">
                        <span class="prefix">kes &nbsp;</span>
                        <input type="number" id="transaction-amount" name="transaction-amount" placeholder="Amount" class="w-100">
                    </div>
                </div>
            </div>
            <div class="form-group" id="paybill-till-group" style="display:none;">
                <label for="paybill-till-number">Buy Goods or Paybill Number</label>
                <div>
                    <input type="text" id="paybill-till-number" name="paybill-till-number" value="{{ old('paybill-till-number') }}" placeholder="Buy Goods or Paybill Number" class="w-100">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="sender-mobile">Your Mobile Number</label>
                    <input type="tel" value="{{ Auth::User()->phone }}" id="sender-mobile" name="sender-mobile" placeholder="07XXXXXXXX or +2547XXXXXXXX" autocomplete="tel">
                </div>
                <div class="form-group">
                    <label for="receiver-mobile">Recipient Mobile Number</label>
                    <input type="tel" id="receiver-mobile" name="receiver-mobile" placeholder="07XXXXXXXX or +2547XXXXXXXX" autocomplete="tel">
                </div>
            </div>
            <div class="form-group">
                <label for="transaction-details">Transaction Details</label>
                <textarea id="transaction-details" name="transaction-details" rows="3" placeholder="Describe your transaction..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                Fund Your Escrow
                <svg style="vertical-align: middle; margin-left: 8px;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <polyline points="12 5 19 12 12 19" />
                </svg>
            </button>
            <p style="text-align:center; margin:0 auto; font-size:10px; display:none;" id="mpesa-response"></p>
            <div id="mpesa-manual-check-wrap" style="display: none; text-align: center; margin-top: 0.5rem;">
                <button type="button" id="mpesa-check-status-btn" class="btn btn-outline-primary btn-sm">Check payment status now</button>
            </div>
            <p class="form-disclaimer">
                By submitting this form, you agree to our <a target="_blank" rel="noopener noreferrer" href="{{ route('terms.conditions') }}">Terms of Service</a> and <a target="_blank" rel="noopener noreferrer" href="{{ route('privacy.policy') }}">Privacy Policy</a>.
            </p>
        </form>
    </div>
</div>
