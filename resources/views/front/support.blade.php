@extends('front.master')

@section('content')
<!-- Support Hero Section -->
<section class="relative py-16 lg:py-20 bg-gradient-to-br from-green-50 via-white to-blue-50 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl mb-6">
                <i class="fas fa-headset text-3xl text-green-600"></i>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                Support Center
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-8">
                We're here to help you with any questions or issues you may have. Find answers to common questions or get in touch with our support team.
            </p>
        </div>
    </div>
</section>

<!-- Support Content Section -->
<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Quick Help -->
            <div class="lg:col-span-2">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Quick Help</h2>
                
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-question-circle text-green-600"></i>
                            How do I create a transaction?
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Creating a transaction is simple. Fill out the transaction form on our homepage with the required details including transaction amount, sender and receiver information, and transaction type. Once submitted, you'll receive a transaction ID to track your escrow.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-money-bill-wave text-green-600"></i>
                            How do I make a payment?
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            After creating a transaction, you'll receive payment instructions via SMS. You can pay using M-Pesa by following the prompts. Our system will automatically detect your payment and update the transaction status.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-check-circle text-green-600"></i>
                            How do I release funds?
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Once you've received the goods or services as agreed, log into your dashboard and approve the transaction. The funds will be released to the receiver within minutes. Both parties must approve for the release to proceed.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-green-600"></i>
                            Is my money safe?
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Yes, your funds are held securely in an escrow account until both parties are satisfied. We use bank-grade security and are fully licensed. Funds are only released when all conditions are met.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-clock text-green-600"></i>
                            How long does a transaction take?
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Payment processing is instant via M-Pesa. Once both parties approve the transaction, funds are released immediately. The entire process typically takes minutes, depending on how quickly both parties respond.
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-undo text-green-600"></i>
                            Can I cancel a transaction?
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Transactions can be cancelled if payment hasn't been made yet. Once payment is received, both parties must agree to cancel. Contact our support team if you need assistance with cancellation.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Support Options -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl p-8 border-2 border-green-200 sticky top-24">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Get Help</h2>
                    
                    <div class="space-y-4 mb-6">
                        <a href="{{ route('help') }}" class="block w-full px-4 py-3 bg-white border-2 border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all duration-200 text-center font-medium text-gray-700">
                            <i class="fas fa-book mr-2 text-green-600"></i>
                            Help Center
                        </a>
                        <a href="{{ route('contact') }}" class="block w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all duration-200 text-center font-medium shadow-md hover:shadow-lg">
                            <i class="fas fa-envelope mr-2"></i>
                            Contact Us
                        </a>
                    </div>

                    <div class="border-t border-gray-300 pt-6">
                        <h3 class="font-semibold text-gray-900 mb-3">Support Hours</h3>
                        <p class="text-sm text-gray-600 mb-2">
                            <strong>Monday - Friday:</strong><br>
                            8:00 AM - 6:00 PM EAT
                        </p>
                        <p class="text-sm text-gray-600">
                            <strong>Saturday:</strong><br>
                            9:00 AM - 2:00 PM EAT
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
