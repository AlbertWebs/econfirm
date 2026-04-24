<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class ServicePagesSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            'real-estate' => 'Real Estate Escrow',
            'vehicle' => 'Vehicle Escrow',
            'business' => 'Business Escrow',
            'ecommerce' => 'E-commerce Escrow',
            'services' => 'Services Escrow',
            'freelancer' => 'Freelancer Escrow',
            'rental' => 'Rental Escrow',
            'import-export' => 'Import & Export Escrow',
            'digital-asset' => 'Digital Asset Escrow',
            'construction' => 'Construction Escrow',
            'equipment-machinery' => 'Equipment / Machinery Escrow',
            'tender-contract' => 'Tender / Contract Escrow',
            'education-school-fees' => 'Education / School Fees Escrow',
            'marketplace' => 'Marketplace Escrow',
        ];

        foreach ($services as $slug => $name) {
            $seoTitle = $name.' in Kenya | eConfirm M-Pesa Escrow Protection';
            $metaDescription = 'Use eConfirm '.$name.' to secure buyer-seller payments with trusted M-Pesa escrow releases, fraud protection, and clear transaction milestones.';

            $body = implode("\n", [
                '<p>'.$name.' by eConfirm helps buyers and sellers complete high-trust transactions safely using M-Pesa escrow workflows. Funds are held under agreed terms and released only when both parties confirm milestones, reducing fraud, non-delivery, and payment disputes that are common in peer-to-peer and business deals.</p>',
                '<p>Our escrow process for '.$name.' is designed for Kenya-focused transactions and supports transparent tracking, communication records, and dispute-ready evidence. Whether you are closing a Jiji-style marketplace deal, formal contract payment, or milestone service transfer, eConfirm gives both parties confidence through neutral payment control and verification steps.</p>',
            ]);

            Page::query()->updateOrCreate(
                ['slug' => 'escrow-'.$slug],
                [
                    'title' => $seoTitle,
                    'body' => $body,
                    'meta_description' => $metaDescription,
                    'is_published' => true,
                    'type' => 'service',
                ]
            );
        }
    }
}
