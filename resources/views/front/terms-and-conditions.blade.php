@extends('master')

@section('content')
<section id="features" class="features text-center">

<div class="container my-5">
    <style>
        .container.my-5 p,
        .container.my-5 ul,
        .container.my-5 ol {
            margin-left: 40px;
        }
    </style>
    <h3 class="mb-4"><strong>e-Confirm Terms and Conditions</strong></h3>

    <br><h3>1. <strong>Introduction</strong></h3>
    <p>
        Welcome to e-confirm, a digital escrow service owned and managed by Confirm Delegence Solutions. By accessing or using the e-confirm platform (“Platform”), you agree to comply with and be legally bound by these Terms and Conditions (“Terms”). These Terms apply to all users, whether they are Senders (payers) or Receivers (payees). If you do not accept these Terms, you must not use the Platform.
    </p>

    <br><h3>2. <strong>How e-confirm Works</strong></h3>
    <p>
        e-confirm acts as a neutral and secure third-party escrow platform that facilitates digital transactions. It helps ensure that both the Sender and Receiver fulfill their obligations before money is released. When a Sender initiates a payment, the funds are held in escrow until the Receiver delivers the goods or services as agreed, and the Sender confirms satisfactory completion.
    </p>

    <br><h3>2.1 <strong>Transaction Flow</strong></h3>
    <ol>
        <li>
            <strong>Step 1: Payment via M-Pesa</strong><br>
            The transaction begins when the Sender deposits funds into the e-confirm escrow account using M-Pesa. This ensures that money is securely stored and cannot be accessed by either party until the transaction terms are fulfilled. At this point, a <strong>1% transaction fee</strong> is automatically deducted from the deposited amount.
        </li>
        <li>
            <strong>Step 2: Notification to Receiver</strong><br>
            Once the deposit is successful, e-confirm instantly notifies the Receiver that a transaction has been initiated. The notification includes the transaction reference and details of what has been agreed between the parties.
        </li>
        <li>
            <strong>Step 3: Fulfillment by Receiver</strong><br>
            The Receiver is then required to fulfill their side of the agreement by delivering the product or service as discussed and documented. Timeliness and quality are essential to prevent disputes.
        </li>
        <li>
            <strong>Step 4: Release of Funds by Sender Only</strong><br>
            Once the Sender confirms they are satisfied with what was delivered, they alone have the authority to release the funds from escrow. e-confirm has no power to release funds without explicit instruction from the Sender, under any circumstances.
        </li>
        <li>
            <strong>Step 5: Dispute Resolution</strong><br>
            If the Sender is not satisfied, they may raise a dispute. e-confirm will appoint a neutral mediator to review evidence and facilitate a resolution. If mediation fails, the issue may be referred to local law enforcement or appropriate authorities for formal legal action.
        </li>
    </ol>

    <br><h3>3. <strong>Obligations of the Parties</strong></h3>
    <ul>
        <li>
            <strong>Sender</strong>
            <ul>
                <li>Must use the e-confirm M-Pesa payment process to initiate a transaction.</li>
                <li>Is the only party authorized to approve the release of funds after delivery is confirmed.</li>
                <li>Should provide full and honest details regarding the transaction and raise any concerns or disputes in a timely manner.</li>
                <li>Must cooperate with the mediation process in the event of a dispute.</li>
            </ul>
        </li>
        <li>
            <strong>Receiver</strong>
            <ul>
                <li>Is obligated to fulfill the transaction as per the agreement, including quality and delivery time.</li>
                <li>Must not ask for direct payments or attempt to bypass the e-confirm escrow system.</li>
                <li>Understands that funds will not be released unless the Sender explicitly approves the release.</li>
            </ul>
        </li>
    </ul>

    <br><h3>4. <strong>Funds Holding and Transaction Agreement</strong></h3>
    <p>
        Once the Sender makes a payment via M-Pesa into e-confirm, a binding agreement is formed between the Sender and the Receiver. e-confirm acts as an independent escrow agent and solely holds the funds. It does not judge or interfere with transaction agreements unless a dispute arises and mediation is required. All transactions are considered valid and binding once the payment is successfully received into the system.
    </p>

    <br><h3>5. <strong>Dispute Resolution</strong></h3>
    <ul>
        <li>Disputes must be reported as soon as they arise, along with supporting evidence such as screenshots, messages, or delivery confirmation.</li>
        <li>e-confirm assigns a neutral third-party mediator to assess the facts and facilitate resolution.</li>
        <li>Parties are expected to provide truthful and timely responses to mediation requests.</li>
        <li>If mediation does not result in a solution, the dispute may be escalated to appropriate legal or enforcement authorities for further investigation and resolution.</li>
    </ul>

    <br><h3>6. <strong>Fees and Charges</strong></h3>
    <p>
        e-confirm charges a <strong>1% platform fee</strong> on every transaction. This fee is automatically deducted from the total amount deposited. Additional fees may be charged for services such as dispute mediation, special requests, or administrative handling. All charges will be transparently disclosed before proceeding.
    </p>

    <br><h3>7. <strong>Limitation of Liability</strong></h3>
    <p>
        e-confirm’s responsibility is strictly limited to securely holding and releasing funds based on instructions from the Sender. The platform does not verify the quality, quantity, or legality of the goods or services involved. e-confirm is not liable for any damages, fraud, or issues outside the scope of holding funds and facilitating release instructions.
    </p>

    <br><h3>8. <strong>Termination and Account Suspension</strong></h3>
    <p>
        e-confirm reserves the right to suspend or terminate any user account if:
    </p>
    <ul>
        <li>The user engages in fraudulent, dishonest, or illegal activities.</li>
        <li>The user misuses the platform or attempts to bypass the escrow process.</li>
        <li>The user repeatedly violates these Terms and Conditions.</li>
    </ul>
    <p>
        Suspension or termination may be done without prior notice if the platform deems the action necessary to protect other users or the platform’s integrity.
    </p>

    <br><h3>9. <strong>Governing Law</strong></h3>
    <p>
        These Terms and all matters relating to your use of the platform shall be governed by and interpreted in accordance with the laws of the Republic of Kenya. In case of any legal proceedings, the courts of Kenya shall have exclusive jurisdiction.
    </p>

    <br><h3>10. <strong>Amendments</strong></h3>
    <p>
        e-confirm reserves the right to revise or update these Terms at any time. Significant changes will be communicated via the platform or through registered email addresses. Continued use of the platform after such changes are made constitutes acceptance of the updated Terms. Users are encouraged to review these Terms regularly.
    </p>
</div>


</section>
@endsection
