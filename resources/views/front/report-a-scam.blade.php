@extends('front.master')

@section('seo_title', 'Report a Scam | eConfirm Scam Alert')
@section('seo_description', 'Report fraudulent websites, phone numbers, or email addresses to help protect others. Submit details and optional evidence through eConfirm Scam Alert.')
@section('canonical_url', route('scam.watch.report'))

@section('content')
<section class="relative py-10 lg:py-14 bg-gradient-to-br from-red-50 via-white to-orange-50 border-b border-red-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-sm text-gray-600 mb-6" aria-label="Breadcrumb">
            <ol class="flex flex-wrap items-center gap-2">
                <li><a href="{{ route('home') }}" class="hover:text-red-600">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('scam.watch') }}" class="hover:text-red-600">Scam Alert</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900 font-medium">Report a scam</li>
            </ol>
        </nav>
        <a href="{{ route('scam.watch') }}" class="inline-flex items-center gap-2 text-red-600 font-semibold hover:underline text-sm mb-6">
            <i class="fas fa-arrow-left"></i> Back to Scam Alert
        </a>
        <p class="text-sm font-semibold text-red-600 uppercase tracking-wide mb-2">Submit a report</p>
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Report a Scam</h1>
        <p class="text-lg text-gray-600 max-w-2xl">
            Help protect others by reporting fraudulent websites, phone numbers, or email addresses you've encountered.
        </p>
    </div>
</section>

<section class="py-12 lg:py-16 bg-gradient-to-br from-red-50 to-orange-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
            <script>
            function scamReportForm() {
                return {
                    reportType: 'website',
                    category: '',
                    files: [],
                    isDragging: false,
                    submitted: false,
                    submitting: false,
                    addFiles(event) {
                        const fileList = event.target.files || event.dataTransfer.files;
                        Array.from(fileList).forEach(file => {
                            if (this.files.length < 5) {
                                this.files.push({
                                    file: file,
                                    name: file.name,
                                    size: file.size,
                                    type: file.type
                                });
                            }
                        });
                    },
                    removeFile(index) {
                        this.files.splice(index, 1);
                    },
                    formatFileSize(bytes) {
                        if (bytes === 0) return '0 Bytes';
                        const k = 1024;
                        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                        const i = Math.floor(Math.log(bytes) / Math.log(k));
                        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
                    },
                    async submitForm(event) {
                        event.preventDefault();
                        this.submitting = true;

                        const form = event.target.closest('form');
                        const formData = new FormData(form);

                        formData.append('report_type', this.reportType);

                        this.files.forEach((fileObj) => {
                            formData.append('evidence_files[]', fileObj.file);
                        });

                        try {
                            const response = await fetch('{{ url('/submit-scam-report') }}', {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                                },
                                body: formData
                            });

                            const data = await response.json();

                            if (data.success) {
                                this.submitted = true;
                                this.files = [];
                                // Do not call form.reset() here — it desyncs Alpine x-model from the DOM and can
                                // collapse the success panel. No auto-redirect so users can use the Google review link.
                            } else {
                                alert(data.message || 'Failed to submit report. Please try again.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred while submitting your report. Please try again.');
                        } finally {
                            this.submitting = false;
                        }
                    }
                };
            }
            </script>

            <form class="space-y-6" x-data="scamReportForm()">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Type *</label>
                    <select x-model="reportType" @change="if (reportType !== 'website') { $refs.websiteInput && ($refs.websiteInput.value = '') }; if (reportType !== 'phone') { $refs.phoneInput && ($refs.phoneInput.value = '') }; if (reportType !== 'email') { $refs.reportedEmailInput && ($refs.reportedEmailInput.value = '') }" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="website">Website</option>
                        <option value="phone">Phone Number</option>
                        <option value="email">Email Address</option>
                    </select>
                </div>

                <div x-show="reportType === 'website'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website URL or Name *</label>
                    <input type="text"
                           name="website"
                           x-ref="websiteInput"
                           placeholder="e.g., example-scam-site.com"
                           :required="reportType === 'website'"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div x-show="reportType === 'phone'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel"
                           name="phone"
                           x-ref="phoneInput"
                           placeholder="e.g., +254712345678 or 0712345678"
                           :required="reportType === 'phone'"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div x-show="reportType === 'email'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email"
                           name="reported_email"
                           x-ref="reportedEmailInput"
                           placeholder="e.g., scam@example.com"
                           :required="reportType === 'email'"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" x-model="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="">Select a category</option>
                        <option value="ecommerce">E-commerce</option>
                        <option value="services">Services</option>
                        <option value="investment">Investment</option>
                        <option value="job">Job Scams</option>
                        <option value="romance">Romance Scams</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div x-show="category === 'other'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specify other type *</label>
                    <input type="text"
                           name="category_other"
                           placeholder="Briefly describe the type of scam (e.g. charity fraud, fake lottery)"
                           :required="category === 'other'"
                           maxlength="255"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description"
                              rows="4"
                              placeholder="Describe the scam, what happened, and any relevant details..."
                              required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Evidence Files (Optional)</label>
                    <div @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; addFiles($event)"
                         :class="isDragging ? 'border-red-500 bg-red-50' : 'border-gray-300'"
                         class="border-2 border-dashed rounded-lg p-6 text-center transition-colors duration-200 cursor-pointer hover:border-red-400 hover:bg-red-50/50"
                         @click="$refs.fileInput.click()">
                        <input type="file"
                               x-ref="fileInput"
                               @change="addFiles($event)"
                               multiple
                               accept="image/*,.pdf,.doc,.docx"
                               class="hidden">
                        <div class="space-y-2">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    <span class="text-red-600">Click to upload</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Images, PDF, DOC, DOCX (Max 5 files, 10MB each)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div x-show="files.length > 0" class="mt-4 space-y-2" style="display: none;">
                        <template x-for="(file, index) in files" :key="index">
                            <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <i class="fas fa-file text-gray-400 flex-shrink-0"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate" x-text="file.name"></p>
                                        <p class="text-xs text-gray-500" x-text="formatFileSize(file.size)"></p>
                                    </div>
                                </div>
                                <button type="button"
                                        @click="removeFile(index)"
                                        class="ml-2 p-1 text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your phone number (Optional)</label>
                    <input type="tel"
                           name="reporter_phone"
                           autocomplete="tel"
                           placeholder="e.g. +254712345678 — for follow-up only, not shown publicly"
                           maxlength="40"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                    <p class="text-xs text-gray-500 mt-1.5">We may use this to contact you about your report. It is not published with the scam listing.</p>
                </div>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Email (Optional)</label>
                        <input type="email"
                               name="email"
                               placeholder="your@email.com"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Incident</label>
                        <input type="date"
                               name="date_of_incident"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               required
                               class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 mb-1">
                                Public Information Consent *
                            </p>
                            <p class="text-xs text-gray-600">
                                I understand and consent that the information I provide in this report (excluding my contact email and phone number) may be published publicly on this website to help protect others from scams. I confirm that the information provided is accurate to the best of my knowledge.
                            </p>
                        </div>
                    </label>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-yellow-600 mt-0.5"></i>
                        <div class="text-sm text-yellow-800">
                            <p class="font-semibold mb-1">Important Notice</p>
                            <p>All reports are reviewed before being published. False reports may be removed. Please provide accurate information to help protect others.</p>
                        </div>
                    </div>
                </div>

                <button type="submit"
                        @click.prevent="submitForm($event)"
                        :disabled="submitting || submitted"
                        x-show="!submitted"
                        class="w-full px-6 py-4 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span x-show="!submitting">
                        <i class="fas fa-paper-plane"></i> Submit Report
                    </span>
                    <span x-show="submitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Submitting...
                    </span>
                </button>

                <div x-show="submitted"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     id="success-message"
                     class="bg-green-50 border-2 border-green-200 rounded-xl p-6 space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Report Submitted Successfully!</h3>
                            <p class="text-gray-600 mb-4">
                                Thank you for helping protect others. Your report has been received and will be reviewed before being published.
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <p class="text-sm font-medium text-gray-700 mb-3">Enjoying our service? Help us grow!</p>
                                <a href="https://g.page/r/CXoxpsT3ArcfEAE/review"
                                   target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fab fa-google"></i>
                                    Leave us a Google Review
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                            </div>
                            <a href="{{ route('scam.watch') }}"
                               class="mt-5 inline-flex items-center gap-2 text-red-600 font-semibold hover:text-red-700 hover:underline">
                                <i class="fas fa-arrow-right"></i> Continue to Scam Alert
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
