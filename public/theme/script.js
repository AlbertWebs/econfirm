// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle tab switching
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs and content
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding content
            const targetId = this.getAttribute('data-bs-target');
            const targetPane = document.querySelector(targetId);
            if (targetPane) {
                targetPane.classList.add('show', 'active');
            }
        });
    });

    // Handle pill navigation within tabs
    const pills = document.querySelectorAll('[data-bs-toggle="pill"]');
    pills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Find parent pill container
            const pillContainer = this.closest('.nav-pills');
            if (pillContainer) {
                // Remove active from siblings
                pillContainer.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                });
            }
            
            // Add active to clicked pill
            this.classList.add('active');
            
            // Show corresponding content
            const targetId = this.getAttribute('data-bs-target');
            const parentTabContent = this.closest('.tab-pane').querySelector('.tab-content');
            if (parentTabContent) {
                parentTabContent.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });
                
                const targetPane = parentTabContent.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.add('show', 'active');
                }
            }
        });
    });

    // File upload handling
    const fileInput = document.getElementById('fileUpload');
    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Create a simple file preview
                const uploadArea = document.querySelector('.border-dashed');
                const originalContent = uploadArea.innerHTML;
                
                uploadArea.innerHTML = `
                    <div class="alert alert-info">
                        <h6>File Selected: ${file.name}</h6>
                        <p class="mb-0">Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        <button class="btn btn-primary btn-sm mt-2" onclick="uploadFile()">Upload Document</button>
                        <button class="btn btn-outline-secondary btn-sm mt-2 ms-2" onclick="resetUpload()">Cancel</button>
                    </div>
                `;
                
                // Store original content for reset
                uploadArea.dataset.originalContent = originalContent;
            }
        });
    }

    // Search functionality for transactions
    const transactionSearch = document.querySelector('#transactions input[placeholder="Search transactions..."]');
    if (transactionSearch) {
        transactionSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const transactions = document.querySelectorAll('.transaction-item');
            
            transactions.forEach(transaction => {
                const title = transaction.querySelector('h6').textContent.toLowerCase();
                const id = transaction.querySelector('[data-id]') ? 
                    transaction.querySelector('[data-id]').textContent.toLowerCase() : '';
                
                if (title.includes(searchTerm) || id.includes(searchTerm)) {
                    transaction.style.display = 'block';
                } else {
                    transaction.style.display = 'none';
                }
            });
        });
    }

    // Two-factor authentication toggle
    const twoFactorSwitch = document.getElementById('twoFactorSwitch');
    if (twoFactorSwitch) {
        twoFactorSwitch.addEventListener('change', function() {
            if (this.checked) {
                // Show QR code area
                const qrArea = document.createElement('div');
                qrArea.className = 'mt-3 p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded';
                qrArea.innerHTML = `
                    <p class="text-primary mb-2 small">Scan this QR code with your authenticator app:</p>
                    <div class="bg-white border border-primary rounded p-4 d-inline-block">
                        <div style="width: 120px; height: 120px;" class="d-flex align-items-center justify-content-center border-2 border-primary border-opacity-25">
                            <small class="text-muted">QR Code</small>
                        </div>
                    </div>
                `;
                
                // Insert after the switch container
                const switchContainer = this.closest('.border');
                switchContainer.parentNode.insertBefore(qrArea, switchContainer.nextSibling);
            } else {
                // Remove QR code area
                const qrArea = document.querySelector('.bg-primary.bg-opacity-10');
                if (qrArea) {
                    qrArea.remove();
                }
            }
        });
    }
});

// Global functions for file upload
function uploadFile() {
    // Simulate file upload
    const uploadArea = document.querySelector('.border-dashed');
    uploadArea.innerHTML = `
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Success!</strong> Document uploaded successfully.
        </div>
    `;
    
    setTimeout(() => {
        resetUpload();
    }, 3000);
}

function resetUpload() {
    const uploadArea = document.querySelector('.border-dashed');
    const originalContent = uploadArea.dataset.originalContent;
    if (originalContent) {
        uploadArea.innerHTML = originalContent;
    }
    
    // Reset file input
    const fileInput = document.getElementById('fileUpload');
    if (fileInput) {
        fileInput.value = '';
    }
}

// Progress bar animations
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
            bar.style.transition = 'width 1s ease-in-out';
        }, 100);
    });
}

// Initialize animations when page loads
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(animateProgressBars, 500);
});
