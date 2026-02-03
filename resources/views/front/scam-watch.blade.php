@extends('front.master')

@section('content')
<!-- Scam Watch Hero Section -->
<section class="relative py-16 lg:py-20 bg-gradient-to-br from-red-50 via-white to-orange-50 overflow-hidden">
    <!-- Background Pattern -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-100 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-2xl mb-6">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                Scam Watch
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-8">
                Stay informed about reported scams and fraudulent websites. Help protect others by reporting suspicious activities.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#report-scam" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> Report a Scam
                </a>
                <a href="#scam-list" class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:border-red-500 hover:text-red-600 transition-all duration-200">
                    View Reported Scams
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Scam List Section -->
<section id="scam-list" class="py-16 lg:py-20 bg-white" x-data="{ 
    searchQuery: '', 
    selectedCategory: '', 
    selectedDate: '',
    matchesFilter(report) {
        if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            const reportedValue = (report.reported_value || '').toLowerCase();
            const description = (report.description || '').toLowerCase();
            const category = (report.category || '').toLowerCase();
            if (!reportedValue.includes(query) && !description.includes(query) && !category.includes(query)) {
                return false;
            }
        }
        if (this.selectedCategory && report.category !== this.selectedCategory) {
            return false;
        }
        if (this.selectedDate) {
            const reportDate = new Date(report.created_at);
            const now = new Date();
            const daysDiff = (now - reportDate) / (1000 * 60 * 60 * 24);
            if (this.selectedDate === 'week' && daysDiff > 7) return false;
            if (this.selectedDate === 'month' && daysDiff > 30) return false;
            if (this.selectedDate === '3months' && daysDiff > 90) return false;
        }
        return true;
    }
}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Reported Scams & Fraudulent Activities</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Browse through reported scams including fraudulent websites, phone numbers, and email addresses. Always verify before making any transactions or sharing personal information.
            </p>
        </div>

        <!-- Search and Filter -->
        <div class="mb-8 bg-gray-50 rounded-xl p-6">
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.300ms=""
                           placeholder="Search by website, name, or description..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select x-model="selectedCategory" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="">All Categories</option>
                        <option value="ecommerce">E-commerce</option>
                        <option value="services">Services</option>
                        <option value="investment">Investment</option>
                        <option value="job">Job Scams</option>
                        <option value="romance">Romance Scams</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Reported</label>
                    <select x-model="selectedDate" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="">All Time</option>
                        <option value="week">Last Week</option>
                        <option value="month">Last Month</option>
                        <option value="3months">Last 3 Months</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button @click="searchQuery = ''; selectedCategory = ''; selectedDate = ''" 
                        class="px-6 py-2 bg-white border-2 border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-times text-sm"></i>
                    Clear Filters
                </button>
                <button class="px-6 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-search text-sm"></i>
                    Search
                </button>
            </div>
        </div>

        <!-- Scam List -->
        <div class="space-y-4">
            @forelse($reports as $report)
                <div x-show="matchesFilter({
                    reported_value: '{{ $report->reported_value }}',
                    description: '{{ addslashes($report->description) }}',
                    category: '{{ $report->category }}',
                    created_at: '{{ $report->created_at }}'
                })"
                     class="bg-white border-2 border-red-200 rounded-xl p-6 hover:shadow-lg transition-all duration-200">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <h3 class="text-xl font-bold text-gray-900">
                                    @if($report->report_type === 'website')
                                        Reported Scam Website
                                    @elseif($report->report_type === 'phone')
                                        Suspicious Phone Number
                                    @else
                                        Fraudulent Email Address
                                    @endif
                                </h3>
                                @if($report->report_count > 1)
                                    <span class="px-3 py-1 bg-red-500 text-white text-sm font-bold rounded-full flex items-center gap-1">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        {{ $report->report_count }} {{ Str::plural('Report', $report->report_count) }}
                                    </span>
                                @endif
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full capitalize">{{ $report->category }}</span>
                                <span class="px-2 py-1 
                                    @if($report->report_type === 'website') bg-blue-100 text-blue-700
                                    @elseif($report->report_type === 'phone') bg-purple-100 text-purple-700
                                    @else bg-green-100 text-green-700
                                    @endif text-xs font-semibold rounded-full capitalize">
                                    {{ ucfirst($report->report_type) }}
                                </span>
                            </div>
                            <p class="text-gray-600 mb-2">
                                <strong>
                                    @if($report->report_type === 'website')
                                        Website:
                                    @elseif($report->report_type === 'phone')
                                        Phone:
                                    @else
                                        Email:
                                    @endif
                                </strong> 
                                <span class="text-red-600 font-mono">{{ $report->reported_value }}</span>
                            </p>
                            <p class="text-gray-600 mb-4">
                                {{ $report->description }}
                            </p>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-calendar text-xs"></i> First reported: {{ $report->created_at->diffForHumans() }}
                                </span>
                                <span class="flex items-center gap-1 font-semibold text-red-600">
                                    <i class="fas fa-users text-xs"></i> Reported {{ $report->report_count }} {{ Str::plural('time', $report->report_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i> 
                                    @if($report->report_count >= 10)
                                        <span class="text-red-600 font-semibold">High Risk</span>
                                    @elseif($report->report_count >= 5)
                                        <span class="text-orange-600 font-semibold">Medium Risk</span>
                                    @else
                                        <span class="text-yellow-600 font-semibold">Low Risk</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                @if($report->report_type === 'website')
                                    <i class="fas fa-globe text-red-600"></i>
                                @elseif($report->report_type === 'phone')
                                    <i class="fas fa-phone text-red-600"></i>
                                @else
                                    <i class="fas fa-envelope text-red-600"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 pt-4 border-t border-gray-100">
                        <button 
                            type="button"
                            x-data="{ 
                                liked: false, 
                                likesCount: {{ $report->likes_count ?? 0 }},
                                loading: false 
                            }"
                            x-init="likesCount = {{ $report->likes_count ?? 0 }}"
                            @click="likeReport({{ $report->id }}, $el)"
                            :class="liked ? 'text-red-600' : 'text-gray-600 hover:text-red-600'"
                            class="text-sm transition-colors duration-200 flex items-center gap-2 disabled:opacity-50"
                            :disabled="loading || liked">
                            <i class="fas fa-thumbs-up text-xs" :class="liked ? 'text-red-600' : ''"></i>
                            <span x-text="liked ? 'Liked!' : 'Like if this was helpful'"></span>
                            <span x-show="likesCount > 0" class="text-xs font-semibold" x-text="'(' + likesCount + ')'"></span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-shield-alt text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg">No scam reports available yet</p>
                    <p class="text-sm mt-2">Help protect others by reporting scams you encounter</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($reports->hasPages())
                <div class="mt-8">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Report Scam Section -->
<section id="report-scam" class="py-16 lg:py-20 bg-gradient-to-br from-red-50 to-orange-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Report a Scam</h2>
                <p class="text-gray-600">
                    Help protect others by reporting fraudulent websites, phone numbers, or email addresses you've encountered.
                </p>
            </div>

            <script>
            function likeReport(reportId, buttonElement) {
                const button = buttonElement;
                const xData = Alpine.$data(button);
                
                if (xData.loading || xData.liked) return;
                
                xData.loading = true;
                
                fetch(`/like-scam-report/${reportId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                })
                .then(res => res.json())
                .then(data => {
                    xData.loading = false;
                    if (data.success) {
                        xData.liked = true;
                        xData.likesCount = data.likes_count;
                    } else {
                        alert(data.message || 'Unable to like this report.');
                    }
                })
                .catch(() => {
                    xData.loading = false;
                    alert('An error occurred. Please try again.');
                });
            }

            function scamReportForm() {
                return {
                    reportType: 'website',
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
                            const response = await fetch('/submit-scam-report', {
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
                                form.reset();
                                this.files = [];
                                // Reload page after 2 seconds to show the new report
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
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
                    <select x-model="reportType" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="website">Website</option>
                        <option value="phone">Phone Number</option>
                        <option value="email">Email Address</option>
                    </select>
                </div>

                <div x-show="reportType === 'website'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website URL or Name *</label>
                    <input type="text" 
                           name="website"
                           placeholder="e.g., example-scam-site.com" 
                           :required="reportType === 'website'"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div x-show="reportType === 'phone'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                    <input type="tel" 
                           name="phone"
                           placeholder="e.g., +254712345678 or 0712345678" 
                           :required="reportType === 'phone'"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div x-show="reportType === 'email'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                    <input type="email" 
                           name="reported_email"
                           placeholder="e.g., scam@example.com" 
                           :required="reportType === 'email'"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none">
                        <option value="">Select a category</option>
                        <option value="ecommerce">E-commerce</option>
                        <option value="services">Services</option>
                        <option value="investment">Investment</option>
                        <option value="job">Job Scams</option>
                        <option value="romance">Romance Scams</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description"
                              rows="4" 
                              placeholder="Describe the scam, what happened, and any relevant details..." 
                              required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none resize-none"></textarea>
                </div>

                <!-- File Upload Dropzone -->
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
                    
                    <!-- Uploaded Files List -->
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

                <!-- Public Consent Checkbox -->
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
                                I understand and consent that the information I provide in this report (excluding my email) may be published publicly on this website to help protect others from scams. I confirm that the information provided is accurate to the best of my knowledge.
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

                <!-- Success Message with Google Review Link -->
                <div x-show="submitted"
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     id="success-message"
                     class="bg-green-50 border-2 border-green-200 rounded-xl p-6 space-y-4"
                     style="display: none;">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">Report Submitted Successfully!</h3>
                            <p class="text-gray-600 mb-4">
                                Thank you for helping protect others. Your report has been received and will be reviewed before being published.
                            </p>
                            <div class="bg-white rounded-lg p-4 border border-green-200">
                                <p class="text-sm font-medium text-gray-700 mb-3">Enjoying our service? Help us grow!</p>
                                <a href="https://g.page/r/CXoxpsT3ArcfEAE/review" 
                                   target="_blank"
                                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fab fa-google"></i>
                                    Leave us a Google Review
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
