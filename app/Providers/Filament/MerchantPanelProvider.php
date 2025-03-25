<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class MerchantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('merchant')
            ->path('merchant')
            ->colors([
                'primary' => Color::Purple,
            ])

            ->topNavigation()
            ->login()
            ->discoverResources(in: app_path('Filament/Merchant/Resources'), for: 'App\\Filament\\Merchant\\Resources')
            ->discoverPages(in: app_path('Filament/Merchant/Pages'), for: 'App\\Filament\\Merchant\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Merchant/Widgets'), for: 'App\\Filament\\Merchant\\Widgets')
            ->widgets([
//                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
                \App\Filament\Merchant\Widgets\MerchantOverView::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->unsavedChangesAlerts()
//            ->font('Red Hat Text')
            ->assets([
//                Js::make('techpay', resource_path('js/techpay.js')),
//                Js::make('custom-script', resource_path('js/custom.js')),
            ])
            ->databaseTransactions();
    }
}
