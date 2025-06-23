@extends('front.master-page')

@section('content')
<section id="features" class="features text-center">
    <style>
        .container.my-5 p,
        .container.my-5 ul,
        .container.my-5 ol {
            margin-left: 40px;
        }
    </style>

<div class="container my-5">
    <br><h3><strong>Security of Funds</strong></h3>
    <p>
        At e-confirm, the security of user funds is our top priority. Our platform uses a peer-to-peer escrow system designed to eliminate fraud, reduce transaction risks, and ensure both parties uphold their obligations. The process is secure, structured, and built on mutual trust backed by technology and transparent rules.
    </p>

    <br><h3><strong>Peer-to-Peer Escrow Model</strong></h3>
    <p>
        e-confirm acts as a trusted intermediary between the Sender (payer) and the Receiver (payee). Once the Sender initiates a transaction and makes payment through <strong>M-Pesa</strong>, the funds are held securely in escrow by the platform. These funds are not accessible by either the Sender or Receiver until the agreed conditions are fulfilled.
    </p>

    <p>
        <strong>Important:</strong> The escrow wallet is not a bank account. It is a temporary holding account with one clear purpose — to safeguard funds until the transaction is successfully completed.
    </p>

    <br><h5><strong>Mapped to Our Process Flow</strong></h5>
    <ol>
        <li>
            <strong>Step 1 – Payment Holding:</strong><br>
            When the Sender deposits funds via M-Pesa, the money is immediately locked in an escrow account. These funds cannot be moved, withdrawn, or tampered with by any party.
        </li>
        <li>
            <strong>Step 2 – Automatic Transaction Contract:</strong><br>
            A digital contract is automatically created, outlining the terms agreed upon by both parties (e.g., delivery time, item or service description). The Receiver is notified and can view the terms, but cannot access the funds.
        </li>
        <li>
            <strong>Step 3 – Delivery Assurance:</strong><br>
            The Receiver is expected to deliver the promised goods or services. The Sender then verifies the delivery and quality of what was received.
        </li>
        <li>
            <strong>Step 4 – Controlled Release by Sender:</strong><br>
            Only the Sender can release the funds by clicking a "Confirm & Release" button after successful delivery. This design ensures that funds are only transferred when the agreed outcome is met.
        </li>
        <li>
            <strong>Step 5 – Dispute and Mediation Safety Net:</strong><br>
            If the delivery fails or is unacceptable, the funds remain locked. e-confirm mediators step in to review the issue. If resolution isn’t possible, the case is escalated to legal authorities. At no point can e-confirm release the funds unilaterally.
        </li>
    </ol>

    <br><h5><strong>Security Measures</strong></h5>
    <ul>
        <li><strong>Escrow Wallet Isolation:</strong> Funds are held in a separate escrow account, independent of user wallets or system revenue. This ensures integrity and prevents misuse.</li>
        <li><strong>Transaction Encryption:</strong> All payment and account information is secured using industry-standard encryption protocols to prevent fraud or interception.</li>
        <li><strong>Audit Trails:</strong> Every action (funds sent, delivery updates, disputes raised, releases made) is logged and timestamped for verification and evidence in case of conflict.</li>
        <li><strong>Release Authorization:</strong> Only the verified Sender can approve and initiate the release of funds. No staff or admin at e-confirm can override this control.</li>
    </ul>

    <p>
        By combining automated processes, user-controlled fund release, and neutral mediation, e-confirm ensures that all transactions are secure, fair, and fraud-resistant — giving both Senders and Receivers peace of mind in every deal.
    </p>
</div>




</section>
@endsection
