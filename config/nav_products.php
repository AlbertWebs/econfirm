<?php

/**
 * Main navigation “Products” dropdown — must match slugs in routes/web.php escrow.product (whereIn).
 */
return [
    'dropdown' => [
        'image' => 'uploads/logo-hoz.png',
        'image_alt' => 'e-confirm — secure escrow for every product',
        'image_title' => 'M-Pesa escrow for buyers & sellers',
    ],
    'items' => [
        ['slug' => 'real-estate', 'label' => 'Real Estate Escrow'],
        ['slug' => 'vehicle', 'label' => 'Vehicle Escrow'],
        ['slug' => 'ecommerce', 'label' => 'E-commerce Escrow'],
        ['slug' => 'business', 'label' => 'Business Escrow'],
        ['slug' => 'services', 'label' => 'Services Escrow'],
        ['slug' => 'freelancer', 'label' => 'Freelancer Escrow'],
        ['slug' => 'import-export', 'label' => 'Import & Export Escrow'],
        ['slug' => 'construction', 'label' => 'Construction Escrow'],
        ['slug' => 'rental', 'label' => 'Rental Escrow'],
        ['slug' => 'marketplace', 'label' => 'Marketplace Escrow'],
    ],
];
