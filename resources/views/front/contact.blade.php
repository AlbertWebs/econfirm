@extends('front.master')

@php
    $pubEmail = site_setting('contact_email') ?: 'info@econfirm.co.ke';
    $pubPhoneDisplay = site_setting('contact_phone_display') ?: '0748 349995';
    $pubPhoneE164 = site_setting('contact_phone_e164') ?: '+254748349995';
    $pubAddress = site_setting('physical_address') ?: "Prestige Plaza, Jambo Apt, Suite A18\nNgong Road, Nairobi";
@endphp

@section('content')
<!-- Contact Hero Section -->
<section class="relative py-16 lg:py-20 bg-gradient-to-br from-purple-50 via-white to-pink-50 overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-pink-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-2xl mb-6">
                <i class="fas fa-envelope text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                Contact Us
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-8">
                Have a question or need assistance? We're here to help. Reach out to us through any of the channels below.
            </p>
        </div>
    </div>
</section>

<!-- Contact Content Section -->
<section class="py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12">
            <!-- Contact Form -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Send us a Message</h2>
                <form class="space-y-6" x-data="{ submitting: false }" @submit.prevent="submitContactForm($el, $data)">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input type="text" name="name" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <select name="subject" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none">
                            <option value="">Select a subject</option>
                            <option value="general">General Inquiry</option>
                            <option value="support">Technical Support</option>
                            <option value="transaction">Transaction Issue</option>
                            <option value="billing">Billing Question</option>
                            <option value="partnership">Partnership Inquiry</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" rows="6" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Security check *</label>
                        <p class="text-sm text-gray-600 mb-2">What is <strong class="tabular-nums">{{ $contactMathA }}</strong> + <strong class="tabular-nums">{{ $contactMathB }}</strong>?</p>
                        <input type="number" name="math_answer" inputmode="numeric" autocomplete="off" required min="0" max="99" step="1"
                               class="w-full max-w-xs px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none"
                               placeholder="Your answer">
                    </div>

                    <button type="submit" 
                            :disabled="submitting"
                            class="w-full px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <span x-show="!submitting">Send Message</span>
                        <span x-show="submitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sending...
                        </span>
                    </button>
                </form>
            </div>

            <!-- Contact Information -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Get in Touch</h2>
                
                <div class="space-y-6 mb-8">
                    <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl border-2 border-purple-200">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                            <a href="mailto:{{ $pubEmail }}" class="text-purple-600 hover:text-purple-700 break-all">
                                {{ $pubEmail }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-green-50 to-blue-50 rounded-xl border-2 border-green-200">
                        <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Phone</h3>
                            <a href="tel:{{ preg_replace('/\s+/', '', $pubPhoneE164) }}" class="text-green-600 hover:text-green-700">
                                {{ $pubPhoneDisplay }}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 p-6 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl border-2 border-blue-200">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 mb-1">Address</h3>
                            <p class="text-gray-600 leading-relaxed">
                                {!! nl2br(e($pubAddress)) !!}<br>
                                <span class="text-gray-500 text-sm mt-1 inline-block">{{ site_setting('organization_legal_name') ?: 'Confirm Diligence Solutions Limited' }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Social Links -->
                <div>
                    <h3 class="font-semibold text-gray-900 mb-4">Follow Us</h3>
                    <div class="flex gap-3">
                        <a href="https://www.facebook.com/profile.php?id=61576961756928" target="_blank"
                           class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors">
                            <i class="fab fa-facebook-f text-white"></i>
                        </a>
                        <a href="https://x.com/econfirmke" target="_blank"
                           class="w-12 h-12 bg-black rounded-lg flex items-center justify-center hover:bg-gray-800 transition-colors">
                            <i class="fab fa-twitter text-white"></i>
                        </a>
                        <a href="https://www.instagram.com/econfirmke/" target="_blank"
                           class="w-12 h-12 bg-gradient-to-br from-purple-600 to-pink-600 rounded-lg flex items-center justify-center hover:from-purple-700 hover:to-pink-700 transition-colors">
                            <i class="fab fa-instagram text-white"></i>
                        </a>
                        <a href="https://www.linkedin.com/company/econfirmke/" target="_blank"
                           class="w-12 h-12 bg-blue-700 rounded-lg flex items-center justify-center hover:bg-blue-800 transition-colors">
                            <i class="fab fa-linkedin-in text-white"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Office Map (just above footer) -->
<section class="pb-16 lg:pb-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-xl border border-gray-200 shadow-sm">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16314137.736586282!2d35.3842978!3d-3.3639162500000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xbee781eaa08541b%3A0x1fb702f7c4a6317a!2sConfirm%20Diligence%20Solutions%20(E-Confirm)!5e0!3m2!1sen!2ske!4v1777301253850!5m2!1sen!2ske"
                class="w-full h-80 sm:h-96 lg:h-[28rem]"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<script>
function submitContactForm(formElement, alpineData) {
    if (alpineData.submitting) return;

    alpineData.submitting = true;

    const mathEl = formElement.querySelector('[name="math_answer"]');
    const rawMath = mathEl ? mathEl.value.trim() : '';
    const mathParsed = rawMath === '' ? null : parseInt(rawMath, 10);
    const formData = {
        name: formElement.querySelector('[name="name"]').value,
        email: formElement.querySelector('[name="email"]').value,
        phone: formElement.querySelector('[name="phone"]').value,
        subject: formElement.querySelector('[name="subject"]').value,
        message: formElement.querySelector('[name="message"]').value,
        math_answer: Number.isFinite(mathParsed) ? mathParsed : null
    };

    fetch(@json(route('submit.contact')), {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        alpineData.submitting = false;
        if (res.status === 429) {
            alert(data.message || 'Too many submissions. Please wait a minute and try again.');
            return;
        }
        if (res.ok && data.success) {
            alert(data.message || 'Thank you! Your message has been sent successfully.');
            window.location.reload();
            return;
        }
        let msg = data.message || 'Failed to send message. Please try again.';
        if (data.errors) {
            const first = Object.values(data.errors).flat()[0];
            if (first) msg = first;
        }
        alert(msg);
    })
    .catch(() => {
        alpineData.submitting = false;
        alert('An error occurred. Please try again.');
    });
}
</script>
@endsection
