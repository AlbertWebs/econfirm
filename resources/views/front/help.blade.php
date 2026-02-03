@extends('front.master')

@section('content')
<!-- Help Hero Section -->
<section class="relative py-16 lg:py-20 bg-gradient-to-br from-blue-50 via-white to-purple-50 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-2xl mb-6">
                <i class="fas fa-book text-3xl text-blue-600"></i>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                Help Center
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-8">
                Comprehensive guides and documentation to help you get the most out of e-confirm escrow services.
            </p>
        </div>
    </div>
</section>

<!-- Help Content Section -->
<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Getting Started -->
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Getting Started</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-xl p-6 border-2 border-green-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">1</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Create Transaction</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Start by filling out the transaction form with all required details. Make sure to include accurate information about the transaction amount, parties involved, and transaction type.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6 border-2 border-blue-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">2</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Make Payment</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Receive payment instructions via SMS and complete your M-Pesa payment. The system will automatically detect and confirm your payment.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border-2 border-purple-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">3</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Complete Transaction</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Once goods or services are delivered, both parties approve the transaction and funds are released automatically to the receiver.
                    </p>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-6 border-2 border-orange-200">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900">Stay Protected</h3>
                    </div>
                    <p class="text-gray-600 leading-relaxed">
                        Your funds are held securely in escrow until all conditions are met. Both parties must approve before funds are released.
                    </p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h2>
            <div class="space-y-4">
                <details class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-lg text-gray-900 flex items-center justify-between">
                        <span>What transaction types are supported?</span>
                        <i class="fas fa-chevron-down text-green-600"></i>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        We support various transaction types including goods, services, real estate, vehicles, and business transactions. You can select the appropriate type when creating your transaction.
                    </p>
                </details>

                <details class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-lg text-gray-900 flex items-center justify-between">
                        <span>What are the fees for using e-confirm?</span>
                        <i class="fas fa-chevron-down text-green-600"></i>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        Our fees are transparent and competitive. Transaction fees are calculated based on the transaction amount and type. You'll see the exact fee before completing your transaction. Contact us for detailed fee information.
                    </p>
                </details>

                <details class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-lg text-gray-900 flex items-center justify-between">
                        <span>How do I track my transaction?</span>
                        <i class="fas fa-chevron-down text-green-600"></i>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        You can track your transaction using the transaction ID provided when you create it. Use the "Search Transaction" feature on our website or log into your dashboard to view all your transactions.
                    </p>
                </details>

                <details class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-lg text-gray-900 flex items-center justify-between">
                        <span>What if there's a dispute?</span>
                        <i class="fas fa-chevron-down text-green-600"></i>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        If there's a dispute, contact our support team immediately. We'll work with both parties to resolve the issue fairly. Funds remain in escrow until the dispute is resolved.
                    </p>
                </details>

                <details class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <summary class="cursor-pointer font-semibold text-lg text-gray-900 flex items-center justify-between">
                        <span>Is my personal information secure?</span>
                        <i class="fas fa-chevron-down text-green-600"></i>
                    </summary>
                    <p class="mt-4 text-gray-600 leading-relaxed">
                        Yes, we use bank-grade encryption and security measures to protect your personal and financial information. Please review our Privacy Policy and Security pages for more details.
                    </p>
                </details>
            </div>
        </div>

        <!-- Additional Resources -->
        <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl p-8 border-2 border-green-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Additional Resources</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <a href="{{ route('support') }}" class="flex items-center gap-3 p-4 bg-white rounded-lg hover:shadow-md transition-all duration-200">
                    <i class="fas fa-headset text-green-600 text-xl"></i>
                    <span class="font-medium text-gray-900">Support</span>
                </a>
                <a href="{{ route('contact') }}" class="flex items-center gap-3 p-4 bg-white rounded-lg hover:shadow-md transition-all duration-200">
                    <i class="fas fa-envelope text-green-600 text-xl"></i>
                    <span class="font-medium text-gray-900">Contact Us</span>
                </a>
                <a href="{{ route('api-documentation') }}" class="flex items-center gap-3 p-4 bg-white rounded-lg hover:shadow-md transition-all duration-200">
                    <i class="fas fa-code text-green-600 text-xl"></i>
                    <span class="font-medium text-gray-900">API Docs</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
