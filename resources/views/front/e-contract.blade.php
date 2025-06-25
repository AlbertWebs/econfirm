<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Legal Agreement</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 14px;
            line-height: 1.7;
            margin: 80px;
           
        }
        .contract{
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        h1, h2 {
            /* text-align: center; */
        }
        .section-title {
            margin-top: 30px;
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-line {
            margin-top: 50px;
        }
        .signature-line div {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="contract">
    <h1>Legal Agreement</h1>
    <p>Date: {{ $agreement_date ?? '___ / ___ / 20__' }}</p>

    <p>This Escrow Service Agreement ("Agreement") is entered into by and between the following parties:</p>

    <p><strong>1. Escrow Provider</strong><br>
    <strong>e-confirm</strong>, a service of <strong>Confirm Diligence Solutions Limited</strong><br>
    Address: {{ $escrow_address ?? 'Prestige Plaza, Ngong Road, Nairobi, Kenya' }}<br>
    Email: {{ $escrow_email ?? 'support@econfirm.co.ke' }}<br>
    Website: https://econfirm.co.ke</p>

    <p><strong>2. Buyer (Depositor)</strong><br>
    Name: {{ $buyer_name ?? '________________________' }}<br>
    Phone: {{ $buyer_phone ?? '________________________' }}<br>
    Email: {{ $buyer_email ?? '________________________' }}</p>

    <p><strong>3. Seller (Recipient)</strong><br>
    Name: {{ $seller_name ?? '________________________' }}<br>
    Phone: {{ $seller_phone ?? '________________________' }}<br>
    Email: {{ $seller_email ?? '________________________' }}</p>

    <h2 class="section-title">1. Purpose of the Agreement</h2>
    <p>This Agreement outlines the use of the e-confirm escrow platform to facilitate secure and neutral holding of funds deposited by the Buyer until the Seller fulfills the agreed terms.</p>

    <h2 class="section-title">2. Transaction Details</h2>
    <ul>
        <li><strong>Description:</strong> {{ $transaction_description ?? '______________________________' }}</li>
        <li><strong>Escrow Amount:</strong> KES {{ $escrow_amount ?? '__________' }}</li>
        <li><strong>Payment Method:</strong> {{ $payment_method ?? 'M-Pesa / Bank Transfer' }}</li>
        <li><strong>Escrow Fee:</strong> KES {{ $escrow_fee ?? '__________' }} paid by {{ $fee_payer ?? '[Buyer/Seller/Both]' }}</li>
        <li><strong>Disbursement Trigger:</strong> {{ $trigger ?? 'Buyer confirmation or 72 hours lapse' }}</li>
        <li><strong>Expected Completion Date:</strong> {{ $expected_completion_date ?? '___ / ___ / 20__' }}</li>
    </ul>

    <h2 class="section-title">3. Escrow Process</h2>
    <ol>
        <li>Buyer deposits funds into the e-confirm account.</li>
        <li>Seller is notified and delivers goods/services.</li>
        <li>Buyer confirms receipt or remains silent for the dispute window.</li>
        <li>Funds are disbursed by e-confirm accordingly.</li>
        <li>Transaction is marked complete and service fee is deducted.</li>
    </ol>

    <h2 class="section-title">4. Responsibilities</h2>
    <p><strong>Escrow Agent:</strong> Acts impartially, securely holds funds, and ensures terms are followed.</p>
    <p><strong>Buyer:</strong> Pays on time and confirms or disputes within the set window.</p>
    <p><strong>Seller:</strong> Delivers as described and responds to disputes promptly.</p>

    <h2 class="section-title">5. Dispute Resolution</h2>
    <p>Disputes must be raised within 72 hours. Both parties will submit evidence. e-confirm will resolve within 7 business days and issue a final decision for fund release.</p>

    <h2 class="section-title">6. Fees</h2>
    <p>The Escrow Fee is non-refundable once a transaction is initiated. Additional dispute charges may apply for escalated cases.</p>

    <h2 class="section-title">7. Termination</h2>
    <p>This agreement terminates upon successful disbursement, refund, or mutual cancellation.</p>

    <h2 class="section-title">8. Limitation of Liability</h2>
    <p>Confirm Diligence Solutions Limited is not responsible for product/service quality, external delays, or inaccurate buyer/seller input. Liability is limited to the escrowed amount.</p>

    <h2 class="section-title">9. Governing Law</h2>
    <p>This Agreement is governed by the laws of the Republic of Kenya.</p>

    <h2 class="section-title">10. Entire Agreement</h2>
    <p>This document represents the complete understanding between parties. Any amendments must be written and signed by all parties.</p>

    </div>

</body>
</html>
