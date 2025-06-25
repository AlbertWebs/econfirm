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
            /* margin: 30px 40px; */
            color: #000;
        }
        .contract {
            border: 1px solid #e4e0e0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            letter-spacing: 1px;
        }
        .header p {
            margin: 3px 0;
            font-size: 10px;
        }
        .contract-title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 10px;
        }
        ul, ol {
            margin: 0 0 0 15px;
            padding: 0;
        }
        p, li {
            margin-bottom: 5px;
        }
        hr {
            border: none;
            border-top: 1px solid #000;
            margin: 10px 0 20px;
        }
    </style>
</head>
<body>
<div class="contract">
    <div class="header">
        {{-- If you're using DomPDF, use public_path() --}}
        {{-- <img src="{{ public_path('images/logo.png') }}" alt="e-confirm Logo" height="40"> --}}
        <h1>e-CONFIRM</h1>
        <p>A service of Confirm Diligence Solutions Limited</p>
        <p>Prestige Plaza, Ngong Road, Nairobi | support@econfirm.co.ke | https://econfirm.co.ke</p>
    </div>

    <hr>

    <div class="contract-title">Escrow Service Agreement</div>

    <p><strong>Transaction Date:</strong> {{ $created_at ?? '___ / ___ / 20__' }}</p>

    <p>This Escrow Service Agreement ("Agreement") is made and entered into by and between the following parties:</p>

    <p><strong>1. Escrow Provider:</strong> 
    <strong>e-confirm</strong>, a service of <strong>Confirm Diligence Solutions Limited</strong>


    <p><strong>2. Buyer (Depositor) Phone:</strong> {{ $sender_mobile ?? '__________' }} | <strong>3. Seller (Recipient) Phone:</strong> {{ $receiver_mobile ?? '__________' }}</p>


    <p class="section-title">1. Purpose</p>
    <p>This Agreement outlines the use of the e-confirm escrow platform to securely hold funds from the Buyer until the Seller fulfills the agreed terms.</p>

    <p class="section-title">2. Transaction Details</p>
    <ul>
        <li><strong>Description:</strong> {{ $transaction_details ?? '__________________' }}</li>
        <li><strong>Amount:</strong> KES {{ $transaction_amount ?? '_____' }}</li>
        <li><strong>Method:</strong> {{ $payment_method ?? 'M-Pesa / Bank Transfer' }}</li>
        <li><strong>Fee:</strong> KES {{ $transaction_fee ?? '_____' }} (paid by {{ $fee_payer ?? 'Buyer' }})</li>
        <li><strong>Disbursement Trigger:</strong> {{ $trigger ?? 'Buyer confirmation or 72 hours lapse' }}</li>
        <li><strong>Expected Completion Date:</strong> {{ $expected_completion_date ?? '7 Days' }}</li>
    </ul>

    <p class="section-title">3. Escrow Process</p>
    <ol>
        <li>Buyer deposits funds.</li>
        <li>Seller is notified and delivers as agreed.</li>
        <li>Buyer confirms or remains silent during the dispute window.</li>
        <li>e-confirm disburses funds and deducts applicable fees.</li>
    </ol>

    <p class="section-title">4. Responsibilities</p>
    <p><strong>Escrow Agent:</strong> Acts neutrally and ensures compliance with this agreement.</p>
    <p><strong>Buyer:</strong> Funds the escrow and confirms delivery within the agreed window.</p>
    <p><strong>Seller:</strong> Delivers as agreed and responds to any queries promptly.</p>

    <p class="section-title">5. Dispute Resolution</p>
    <p>Disputes must be raised within 72 hours of delivery. Both parties must provide evidence. e-confirm will review and resolve within 7 business days. Decision is binding for fund release purposes.</p>

    <p class="section-title">6. Fees</p>
    <p>Fees are non-refundable. Additional charges may apply in the event of escalated disputes.</p>

    <p class="section-title">7. Termination</p>
    <p>This Agreement terminates upon full fund disbursement, refund, or mutual written cancellation.</p>

    <p class="section-title">8. Limitation of Liability</p>
    <p>e-confirm is not liable for product or service quality, third-party failures, or incorrect user data. Maximum liability is limited to the escrowed amount.</p>

    <p class="section-title">9. Governing Law</p>
    <p>This Agreement shall be governed by the laws of the Republic of Kenya.</p>

    <p class="section-title">10. Entire Agreement</p>
    <p>This document represents the complete understanding between the parties. Any amendment must be in writing and agreed upon by all parties.</p>
</div>
</body>
</html>
