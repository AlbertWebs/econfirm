<?php

/**
 * Site-wide settings: keys, labels, validation groups, and defaults.
 * Values are stored in `site_settings`; missing keys fall back to `default`.
 */
return [
    'keys' => [
        'site_name' => [
            'label' => 'Site / brand name',
            'group' => 'general',
            'rules' => ['required', 'string', 'max:120'],
            'default' => 'eConfirm',
        ],
        'organization_legal_name' => [
            'label' => 'Organization legal name (copyright)',
            'group' => 'general',
            'rules' => ['required', 'string', 'max:255'],
            'default' => 'Confirm Diligence Solutions Limited',
        ],
        'footer_tagline' => [
            'label' => 'Footer trust line',
            'group' => 'general',
            'rules' => ['nullable', 'string', 'max:500'],
            'default' => 'Digital escrow management solution',
        ],
        'footer_about_blurb' => [
            'label' => 'Footer about text (HTML allowed)',
            'group' => 'general',
            'rules' => ['nullable', 'string', 'max:5000'],
            'default' => 'eConfirm is a trusted digital platform for secure, transparent escrow. We hold funds as a neutral third party until agreed conditions are met—so you can buy, sell, or partner online with confidence.',
        ],
        'logo_alt' => [
            'label' => 'Logo alt text',
            'group' => 'general',
            'rules' => ['nullable', 'string', 'max:255'],
            'default' => 'eConfirm — home',
        ],

        'contact_email' => [
            'label' => 'Public contact email',
            'group' => 'contact',
            'rules' => ['nullable', 'email', 'max:255'],
            'default' => '',
        ],
        'contact_phone_display' => [
            'label' => 'Contact phone (display)',
            'group' => 'contact',
            'rules' => ['nullable', 'string', 'max:80'],
            'default' => '',
        ],
        'contact_phone_e164' => [
            'label' => 'Contact phone (E.164 for schema, e.g. +254712345678)',
            'group' => 'contact',
            'rules' => ['nullable', 'string', 'max:32'],
            'default' => '+254700000000',
        ],
        'physical_address' => [
            'label' => 'Physical / mailing address',
            'group' => 'contact',
            'rules' => ['nullable', 'string', 'max:1000'],
            'default' => '',
        ],

        'social_facebook_url' => [
            'label' => 'Facebook URL',
            'group' => 'social',
            'rules' => ['nullable', 'url', 'max:500'],
            'default' => 'https://www.facebook.com/profile.php?id=61576961756928',
        ],
        'social_x_url' => [
            'label' => 'X (Twitter) URL',
            'group' => 'social',
            'rules' => ['nullable', 'url', 'max:500'],
            'default' => 'https://x.com/econfirmke',
        ],
        'social_instagram_url' => [
            'label' => 'Instagram URL',
            'group' => 'social',
            'rules' => ['nullable', 'url', 'max:500'],
            'default' => 'https://www.instagram.com/econfirmke/',
        ],
        'social_linkedin_url' => [
            'label' => 'LinkedIn URL',
            'group' => 'social',
            'rules' => ['nullable', 'url', 'max:500'],
            'default' => 'https://www.linkedin.com/company/econfirmke/',
        ],

        'default_seo_title' => [
            'label' => 'Default browser title (when page does not set one)',
            'group' => 'seo',
            'rules' => ['required', 'string', 'max:255'],
            'default' => 'Trusted Escrow Services for Secure M-Pesa Payments in Kenya | eConfirm',
        ],
        'default_seo_description' => [
            'label' => 'Default meta description',
            'group' => 'seo',
            'rules' => ['required', 'string', 'max:500'],
            'default' => 'eConfirm provides secure, fast, and transparent escrow services for peer-to-peer M-Pesa payments in Kenya. Safeguard your transactions for goods, services, and contracts.',
        ],
        'default_seo_keywords' => [
            'label' => 'Default meta keywords',
            'group' => 'seo',
            'rules' => ['nullable', 'string', 'max:500'],
            'default' => 'escrow services Kenya, M-Pesa escrow, secure peer to peer payments, escrow for goods and services, payment protection Kenya, eConfirm escrow platform, online escrow Kenya, buyer seller protection, safe M-Pesa payments, digital escrow solution',
        ],
        'meta_author' => [
            'label' => 'Meta author',
            'group' => 'seo',
            'rules' => ['nullable', 'string', 'max:120'],
            'default' => 'eConfirm',
        ],
        'jsonld_organization_description' => [
            'label' => 'Organization description (JSON-LD / search)',
            'group' => 'seo',
            'rules' => ['required', 'string', 'max:1000'],
            'default' => "eConfirm is Kenya's leading escrow platform for secure peer-to-peer payments via M-Pesa. Ideal for buyers and sellers of goods, services, or contracts.",
        ],
    ],

    'groups' => [
        'general' => 'General & branding',
        'contact' => 'Contact',
        'social' => 'Social links',
        'seo' => 'SEO & structured data',
    ],
];
