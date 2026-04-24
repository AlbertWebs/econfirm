@extends('front.master')

@section('content')
<section class="py-10">
    <div class="container mx-auto max-w-4xl px-4">
        <h1 class="mb-6 text-2xl font-bold text-slate-900">Scam Alert - Terms of Use</h1>
        <p class="mb-6 text-sm text-slate-600">Last updated: {{ now()->format('F j, Y') }}</p>

        <div class="space-y-8 text-sm leading-7 text-slate-800">
            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">1. Purpose of the Scam Alert Service</h2>
                <p>Scam Alert is a public awareness feature provided by eConfirm to allow individuals and businesses to share information about suspected scams, fraudulent activities, or suspicious transactions.</p>
                <p class="mt-3">The purpose of this service is to promote awareness, encourage reporting, and help users make informed decisions when engaging in transactions.</p>
                <p class="mt-3">Scam Alert is provided for informational purposes only.</p>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">2. User-Generated Content Disclaimer (Important)</h2>
                <p>All reports, comments, screenshots, and information submitted to Scam Alert are provided by users or third parties.</p>
                <p class="mt-3">The content displayed on Scam Alert does not represent the opinions, views, findings, or conclusions of eConfirm or its operators.</p>
                <p class="mt-3">eConfirm:</p>
                <ul class="mt-2 list-disc space-y-1 pl-6">
                    <li>Does not independently verify all submissions</li>
                    <li>Does not guarantee the accuracy, completeness, or reliability of any report</li>
                    <li>Does not endorse or confirm allegations made in user submissions</li>
                    <li>Does not make legal determinations about whether an activity is fraudulent</li>
                </ul>
                <p class="mt-3">Users are solely responsible for the accuracy and legality of the information they submit.</p>
                <p class="mt-3">This approach is standard on reporting platforms, where submissions reflect the responsibility of the submitter rather than the platform hosting them.</p>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">3. No Legal or Investigative Authority</h2>
                <p>Scam Alert is not a law enforcement agency, regulator, or investigative body.</p>
                <p class="mt-3">eConfirm:</p>
                <ul class="mt-2 list-disc space-y-1 pl-6">
                    <li>Does not conduct criminal investigations</li>
                    <li>Does not determine guilt or liability</li>
                    <li>Does not provide legal advice</li>
                    <li>Does not guarantee prevention of fraud or scams</li>
                </ul>
                <p class="mt-3">Users are encouraged to report suspected criminal activity to the appropriate authorities.</p>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">4. Accuracy and Verification</h2>
                <p>While eConfirm may review submissions for compliance with platform rules, the platform does not guarantee that all information published is accurate, current, or complete.</p>
                <p class="mt-3">Users should:</p>
                <ul class="mt-2 list-disc space-y-1 pl-6">
                    <li>Verify information independently</li>
                    <li>Exercise caution when relying on reports</li>
                    <li>Conduct their own due diligence before making decisions</li>
                </ul>
                <p class="mt-3">Information on the platform is provided "as is" without warranties of any kind.</p>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">5. User Responsibilities</h2>
                <p>By submitting information to Scam Alert, you agree that:</p>
                <p class="mt-3 font-semibold">You will:</p>
                <ul class="mt-2 list-disc space-y-1 pl-6">
                    <li>Provide truthful and accurate information</li>
                    <li>Submit content based on genuine experiences or evidence</li>
                    <li>Avoid posting false, misleading, or defamatory statements</li>
                    <li>Respect the rights and privacy of others</li>
                </ul>
                <p class="mt-3 font-semibold">You will not:</p>
                <ul class="mt-2 list-disc space-y-1 pl-6">
                    <li>Submit fabricated or malicious claims</li>
                    <li>Harass, threaten, or impersonate individuals or businesses</li>
                    <li>Upload illegal, abusive, or offensive content</li>
                    <li>Use Scam Alert to settle personal disputes</li>
                </ul>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">6. Right to Review, Edit, or Remove Content</h2>
                <p>eConfirm reserves the right to review submissions before or after publication, edit content for clarity or compliance, remove content that violates these Terms, and suspend or restrict users who misuse the platform.</p>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">7. Limitation of Liability</h2>
                <p>To the maximum extent permitted by law, eConfirm shall not be liable for losses resulting from reliance on user-submitted information, damages caused by inaccurate or incomplete reports, disputes between users or third parties, or actions taken based on information published on Scam Alert.</p>
                <p class="mt-3">Use of the Scam Alert service is at your own risk.</p>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">8. Reporting Errors or Requesting Removal</h2>
                <p>If you believe that information published on Scam Alert is inaccurate, misleading, or violates your rights, you may request a review or removal by contacting eConfirm through official support channels.</p>
                <p class="mt-3">Requests must include:</p>
                <ul class="mt-2 list-disc space-y-1 pl-6">
                    <li>Identification of the report</li>
                    <li>Explanation of the concern</li>
                    <li>Supporting evidence where applicable</li>
                </ul>
            </div>

            <div>
                <h2 class="mb-2 text-lg font-semibold text-slate-900">9. Changes to These Terms</h2>
                <p>eConfirm reserves the right to update or modify these Terms of Use at any time. Continued use of the Scam Alert feature after changes are published constitutes acceptance of the updated Terms.</p>
            </div>

            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-900">
                <p class="font-semibold">Legal note:</p>
                <p class="mt-1">Reports published on Scam Alert are user-submitted and do not represent the views or verification of eConfirm.</p>
            </div>
        </div>
    </div>
</section>
@endsection
