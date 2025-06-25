<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Escrow Legal Agreement</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            font-size: 10.5px;
            line-height: 1.4;
            margin: 30px 40px;
        }
        .contract {
            border: 1px solid #aaa;
            padding: 15px;
        }
        h2 {
            font-size: 13px;
            margin-bottom: 5px;
        }
        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 8px;
        }
        ul, ol {
            margin: 0 0 0 15px;
            padding: 0;
        }
        p, li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="contract">
    <h2>Escrow Service Agreement</h2>
    <p><strong>Transaction Date:</strong> {{ $created_at ?? '___ / ___ / 20__' }}</p>

    <p>This Escrow Service Agreement ("Agreement") is entered into by and between:</p>

    <p><strong>1. Escrow Provider:</strong><br>
    <strong>e-confirm</strong>, a service of <strong>Confirm Diligence Solutions Limited</strong><br>
    Address: {{ $escrow_address ?? 'Prestige Plaza, Ngong Road, Nairobi' }}<br>
    Email: {{ $escrow_email ?? 'support@econfirm.co.ke' }} | Website: https://econfirm.co.ke</p>

    <p><strong>2. Buyer:</strong> {{ $buyer_name ?? '________________' }} |
    Phone: {{ $sender_mobile ?? '__________' }} |
    Email: {{ $buyer_email ?? '__________' }}</p>

    <p><strong>3. Seller:</strong> {{ $seller_name ?? '________________' }} |
    Phone: {{ $receiver_mobile ?? '__________' }} |
    Email: {{ $seller_email ?? '__________' }}</p>

    <p class="section-title">1. Purpose</p>
    <p>This Agreement outlines the use of the e-confirm escrow platform to securely hold funds from the Buyer until the Seller fulfills agreed terms.</p>

    <p class="section-title">2. Transaction Details</p>
    <ul>
        <li><strong>Description:</strong> {{ $transaction_description ?? '__________________' }}</li>
        <li><strong>Amount:</strong> KES {{ $escrow_amount ?? '_____' }}</li>
        <li><strong>Method:</strong> {{ $payment_method ?? 'M-Pesa/Bank' }}</li>
        <li><strong>Fee:</strong> KES {{ $escrow_fee ?? '_____' }} (by {{ $fee_payer ?? 'Both' }})</li>
        <li><strong>Trigger:</strong> {{ $trigger ?? 'Buyer confirmation or 72hr lapse' }}</li>
        <li><strong>Deadline:</strong> {{ $expected_completion_date ?? '___ / ___ / 20__' }}</li>
    </ul>

    <p class="section-title">3. Process</p>
    <ol>
        <li>Buyer deposits funds.</li>
        <li>Seller is notified and delivers as agreed.</li>
        <li>Buyer confirms or waits out dispute window.</li>
        <li>e-confirm releases funds and deducts fee.</li>
    </ol>

    <p class="section-title">4. Responsibilities</p>
    <p><strong>Escrow Agent:</strong> Acts neutrally and ensures compliance.</p>
    <p><strong>Buyer:</strong> Funds escrow and confirms delivery.</p>
    <p><strong>Seller:</strong> Delivers as agreed and responds promptly.</p>

    <p class="section-title">5. Dispute Resolution</p>
    <p>Disputes must be raised within 72 hours. e-confirm will review submissions and make a decision within 7 business days. Decision is final for disbursement.</p>

    <p class="section-title">6. Fees</p>
    <p>Fees are non-refundable. Additional charges apply to escalated disputes.</p>

    <p class="section-title">7. Termination</p>
    <p>This Agreement ends after disbursement, refund, or mutual cancellation.</p>

    <p class="section-title">8. Limitation of Liability</p>
    <p>e-confirm is not liable for product/service quality or third-party issues. Maximum liability is limited to the escrowed amount.</p>

    <p class="section-title">9. Governing Law</p>
    <p>This Agreement is governed by the laws of the Republic of Kenya.</p>

    <p class="section-title">10. Entire Agreement</p>
    <p>This document represents the full agreement between the parties. Changes must be made in writing and signed by all parties.</p>
</div>
</body>
</html>
