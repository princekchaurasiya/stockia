<?php

namespace App\Support;

use App\Models\User;

class SettingsMap
{
    /**
     * @return list<array{title: string, description: string, items: list<array{label: string, description: string, route: string, icon: string}>}>
     */
    public static function groupsFor(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        $isAdmin = in_array($user->role ?? '', ['admin', 'superadmin'], true);
        $isSuperAdmin = ($user->role ?? '') === 'superadmin';

        $groups = [
            [
                'title' => __('stockia.settings.account_title'),
                'description' => __('stockia.settings.account_description'),
                'items' => [
                    [
                        'label' => __('stockia.settings.profile'),
                        'description' => __('stockia.settings.profile_description'),
                        'route' => 'profile.edit',
                        'icon' => 'bi-person',
                    ],
                ],
            ],
            [
                'title' => __('stockia.settings.learning_title'),
                'description' => __('stockia.settings.learning_description'),
                'items' => [
                    [
                        'label' => __('stockia.settings.my_notes'),
                        'description' => __('stockia.settings.my_notes_description'),
                        'route' => 'notes.index',
                        'icon' => 'bi-journal-text',
                    ],
                    [
                        'label' => __('stockia.settings.trading_learning'),
                        'description' => __('stockia.settings.trading_learning_description'),
                        'route' => 'learning.index',
                        'icon' => 'bi-mortarboard',
                    ],
                    [
                        'label' => __('stockia.settings.live_classes'),
                        'description' => __('stockia.settings.live_classes_description'),
                        'route' => 'live_classes.index',
                        'icon' => 'bi-camera-video',
                    ],
                ],
            ],
            [
                'title' => __('stockia.settings.portal_title'),
                'description' => __('stockia.settings.portal_description'),
                'items' => [
                    [
                        'label' => __('stockia.settings.announcements'),
                        'description' => __('stockia.settings.announcements_description'),
                        'route' => 'announcements.index',
                        'icon' => 'bi-megaphone',
                    ],
                    [
                        'label' => __('stockia.settings.calendar'),
                        'description' => __('stockia.settings.calendar_description'),
                        'route' => 'calendar.index',
                        'icon' => 'bi-calendar-event',
                    ],
                    [
                        'label' => __('stockia.settings.research'),
                        'description' => __('stockia.settings.research_description'),
                        'route' => 'research.index',
                        'icon' => 'bi-file-earmark-bar-graph',
                    ],
                    [
                        'label' => __('stockia.settings.charts'),
                        'description' => __('stockia.settings.charts_description'),
                        'route' => 'charts.index',
                        'icon' => 'bi-graph-up',
                    ],
                    [
                        'label' => __('stockia.information_websites.page_title'),
                        'description' => __('stockia.settings.information_websites_description'),
                        'route' => 'information.websites.index',
                        'icon' => 'bi-link-45deg',
                    ],
                ],
            ],
        ];

        if ($isAdmin) {
            $groups[] = [
                'title' => __('stockia.settings.portal_admin_title'),
                'description' => __('stockia.settings.portal_admin_description'),
                'items' => [
                    [
                        'label' => __('stockia.information_link.section_title'),
                        'description' => __('stockia.settings.information_links_description'),
                        'route' => 'admin.information_links.index',
                        'icon' => 'bi-globe2',
                    ],
                ],
            ];

            $groups[] = [
                'title' => __('stockia.settings.learning_admin_title'),
                'description' => __('stockia.settings.learning_admin_description'),
                'items' => self::learningAdminItems(),
            ];

            $adminItems = [
                [
                    'label' => __('stockia.data_source.title'),
                    'description' => __('stockia.settings.data_source_description'),
                    'route' => 'admin.data_source_links.index',
                    'icon' => 'bi-database',
                ],
            ];

            if ($isSuperAdmin) {
                $adminItems[] = [
                    'label' => __('stockia.account.admins'),
                    'description' => __('stockia.settings.admins_description'),
                    'route' => 'admin.admins.index',
                    'icon' => 'bi-shield-lock',
                ];
            }

            $groups[] = [
                'title' => __('stockia.settings.administration_title'),
                'description' => __('stockia.settings.administration_description'),
                'items' => $adminItems,
            ];
        }

        return $groups;
    }

    /**
     * @return list<array{label: string, description: string, route: string, icon: string}>
     */
    private static function learningAdminItems(): array
    {
        $icons = [
            'dashboard' => 'bi-speedometer2',
            'batches' => 'bi-collection',
            'enrollments' => 'bi-people',
            'modules' => 'bi-folder',
            'lectures' => 'bi-book',
            'videos' => 'bi-play-circle',
            'documents' => 'bi-file-earmark-pdf',
        ];

        $descriptions = [
            'dashboard' => __('stockia.settings.learning_admin_dashboard_description'),
            'batches' => __('stockia.settings.learning_admin_batches_description'),
            'enrollments' => __('stockia.settings.learning_admin_students_description'),
            'modules' => __('stockia.settings.learning_admin_modules_description'),
            'lectures' => __('stockia.settings.learning_admin_lectures_description'),
            'videos' => __('stockia.settings.learning_admin_videos_description'),
            'documents' => __('stockia.settings.learning_admin_documents_description'),
        ];

        $items = [];

        foreach (LearningAdminNav::sections() as $key => $section) {
            $items[] = [
                'label' => $section['label'],
                'description' => $descriptions[$key] ?? $section['label'],
                'route' => $section['route'],
                'icon' => $icons[$key] ?? 'bi-arrow-right',
            ];
        }

        return $items;
    }
}
