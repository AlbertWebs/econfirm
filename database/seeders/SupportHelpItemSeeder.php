<?php

namespace Database\Seeders;

use App\Models\SupportHelpItem;
use Illuminate\Database\Seeder;

class SupportHelpItemSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'ref_key' => 'quick_help_1',
                'kind' => SupportHelpItem::KIND_QUICK_HELP,
                'title' => 'How do I create a transaction?',
                'body' => '<p class="text-gray-600 leading-relaxed">Creating a transaction is simple. Fill out the transaction form on our homepage with the required details including transaction amount, sender and receiver information, and transaction type. Once submitted, you\'ll receive a transaction ID to track your escrow.</p>',
                'icon' => 'fas fa-question-circle',
                'sort_order' => 10,
            ],
            [
                'ref_key' => 'quick_help_2',
                'kind' => SupportHelpItem::KIND_QUICK_HELP,
                'title' => 'How do I make a payment?',
                'body' => '<p class="text-gray-600 leading-relaxed">After creating a transaction, you\'ll receive payment instructions via SMS. You can pay using M-Pesa by following the prompts. Our system will automatically detect your payment and update the transaction status.</p>',
                'icon' => 'fas fa-money-bill-wave',
                'sort_order' => 20,
            ],
            [
                'ref_key' => 'quick_help_3',
                'kind' => SupportHelpItem::KIND_QUICK_HELP,
                'title' => 'How do I release funds?',
                'body' => '<p class="text-gray-600 leading-relaxed">Once you\'ve received the goods or services as agreed, log into your dashboard and approve the transaction. The funds will be released to the receiver within minutes. Both parties must approve for the release to proceed.</p>',
                'icon' => 'fas fa-check-circle',
                'sort_order' => 30,
            ],
            [
                'ref_key' => 'quick_help_4',
                'kind' => SupportHelpItem::KIND_QUICK_HELP,
                'title' => 'Is my money safe?',
                'body' => '<p class="text-gray-600 leading-relaxed">Yes, your funds are held securely in an escrow account until both parties are satisfied. We use bank-grade security and are fully licensed. Funds are only released when all conditions are met.</p>',
                'icon' => 'fas fa-shield-alt',
                'sort_order' => 40,
            ],
            [
                'ref_key' => 'quick_help_5',
                'kind' => SupportHelpItem::KIND_QUICK_HELP,
                'title' => 'How long does a transaction take?',
                'body' => '<p class="text-gray-600 leading-relaxed">Payment processing is instant via M-Pesa. Once both parties approve the transaction, funds are released immediately. The entire process typically takes minutes, depending on how quickly both parties respond.</p>',
                'icon' => 'fas fa-clock',
                'sort_order' => 50,
            ],
            [
                'ref_key' => 'quick_help_6',
                'kind' => SupportHelpItem::KIND_QUICK_HELP,
                'title' => 'Can I cancel a transaction?',
                'body' => '<p class="text-gray-600 leading-relaxed">Transactions can be cancelled if payment hasn\'t been made yet. Once payment is received, both parties must agree to cancel. Contact our support team if you need assistance with cancellation.</p>',
                'icon' => 'fas fa-undo',
                'sort_order' => 60,
            ],
            [
                'ref_key' => 'help_faq_1',
                'kind' => SupportHelpItem::KIND_HELP_FAQ,
                'title' => 'What transaction types are supported?',
                'body' => '<p class="mt-4 text-gray-600 leading-relaxed">We support various transaction types including goods, services, real estate, vehicles, and business transactions. You can select the appropriate type when creating your transaction.</p>',
                'icon' => null,
                'sort_order' => 10,
            ],
            [
                'ref_key' => 'help_faq_2',
                'kind' => SupportHelpItem::KIND_HELP_FAQ,
                'title' => 'What are the fees for using e-confirm?',
                'body' => '<p class="mt-4 text-gray-600 leading-relaxed">Our fees are transparent and competitive. Transaction fees are calculated based on the transaction amount and type. You\'ll see the exact fee before completing your transaction. Contact us for detailed fee information.</p>',
                'icon' => null,
                'sort_order' => 20,
            ],
            [
                'ref_key' => 'help_faq_3',
                'kind' => SupportHelpItem::KIND_HELP_FAQ,
                'title' => 'How do I track my transaction?',
                'body' => '<p class="mt-4 text-gray-600 leading-relaxed">You can track your transaction using the transaction ID provided when you create it. Use the "Search Escrow" feature on our website or log into your dashboard to view all your transactions.</p>',
                'icon' => null,
                'sort_order' => 30,
            ],
            [
                'ref_key' => 'help_faq_4',
                'kind' => SupportHelpItem::KIND_HELP_FAQ,
                'title' => 'What if there\'s a dispute?',
                'body' => '<p class="mt-4 text-gray-600 leading-relaxed">If there\'s a dispute, contact our support team immediately. We\'ll work with both parties to resolve the issue fairly. Funds remain in escrow until the dispute is resolved.</p>',
                'icon' => null,
                'sort_order' => 40,
            ],
            [
                'ref_key' => 'help_faq_5',
                'kind' => SupportHelpItem::KIND_HELP_FAQ,
                'title' => 'Is my personal information secure?',
                'body' => '<p class="mt-4 text-gray-600 leading-relaxed">Yes, we use bank-grade encryption and security measures to protect your personal and financial information. Please review our Privacy Policy and Security pages for more details.</p>',
                'icon' => null,
                'sort_order' => 50,
            ],
        ];

        foreach ($rows as $row) {
            $refKey = $row['ref_key'];
            $attrs = [
                'kind' => $row['kind'],
                'title' => $row['title'],
                'body' => $row['body'],
                'icon' => $row['icon'],
                'sort_order' => $row['sort_order'],
                'is_published' => true,
            ];
            SupportHelpItem::query()->updateOrCreate(
                ['ref_key' => $refKey],
                array_merge($attrs, ['ref_key' => $refKey])
            );
        }
    }
}
