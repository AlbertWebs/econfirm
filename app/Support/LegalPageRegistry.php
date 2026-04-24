<?php

namespace App\Support;

/**
 * CMS slugs and public routes for legal pages (see HomeController pageFromCmsOrFallback).
 */
final class LegalPageRegistry
{
    /**
     * @return list<array{slug: string, label: string, route: string}>
     */
    public static function entries(): array
    {
        return [
            [
                'slug' => 'terms-and-conditions',
                'label' => 'Terms & conditions',
                'route' => 'terms.conditions',
            ],
            [
                'slug' => 'privacy-policy',
                'label' => 'Privacy policy',
                'route' => 'privacy.policy',
            ],
            [
                'slug' => 'security',
                'label' => 'Security',
                'route' => 'security',
            ],
            [
                'slug' => 'complience',
                'label' => 'Compliance',
                'route' => 'complience',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_column(self::entries(), 'slug');
    }

    /**
     * @return array{slug: string, label: string, route: string}|null
     */
    public static function find(string $slug): ?array
    {
        foreach (self::entries() as $entry) {
            if ($entry['slug'] === $slug) {
                return $entry;
            }
        }

        return null;
    }
}
