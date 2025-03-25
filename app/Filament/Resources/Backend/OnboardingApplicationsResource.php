<?php

namespace App\Filament\Resources\Backend;

use App\Filament\Resources\Backend;
use App\Filament\Resources\OnboardingApplicationsResource\Pages;
use App\Filament\Resources\OnboardingApplicationsResource\RelationManagers;
use App\Models\Approval;
use App\Models\OnboardingApplications;
use Illuminate\Support\Facades\Auth;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;

class OnboardingApplicationsResource extends Resource
{
    protected static ?string $model = OnboardingApplications::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationLabel = 'Applications';
    protected static bool $shouldRegisterNavigation = false;


    protected static ?int $navigationSort = 3;

    // protected static bool $shouldRegisterNavigation = false;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Company Details')
                            ->icon('heroicon-s-user')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Basic Details')
                                            ->relationship('company')
                                            ->schema([
                                                Forms\Components\TextInput::make('company_name')
                                                    ->label('Company Name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('trading_name')
                                                    ->label('Trading Name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('type_of_ownership')
                                                    ->label('Type of Ownership')
                                                    ->required(),
                                                Forms\Components\TextInput::make('rc_number')
                                                    ->label('RC Number')
                                                    ->required(),
                                                Forms\Components\TextInput::make('tpin')
                                                    ->label('TPIN')
                                                    ->required(),
                                                Forms\Components\DatePicker::make('date_registered')
                                                    ->label('Date Registered')
                                                    ->required(),
                                                Forms\Components\TextInput::make('nature_of_business')
                                                    ->label('Nature of Business')
                                                    ->required(),
                                                Forms\Components\TextInput::make('official_website')
                                                    ->label('Official Website')
                                                    ->url(),
                                            ])->columnSpan(1),

                                        Forms\Components\Section::make('Contact Information')
                                            ->relationship('company')
                                            ->schema([
                                                Forms\Components\TextInput::make('office_address')
                                                    ->label('Office Address')
                                                    ->required(),
                                                Forms\Components\TextInput::make('postal_address')
                                                    ->label('Postal Address')
                                                    ->required(),
                                                Forms\Components\TextInput::make('country_of_incorporation')
                                                    ->label('Country of Incorporation')
                                                    ->required(),
                                                Forms\Components\TextInput::make('office_telephone')
                                                    ->label('Office Telephone')
                                                    ->tel()
                                                    ->required(),
                                                Forms\Components\TextInput::make('official_email')
                                                    ->label('Official Email')
                                                    ->email()
                                                    ->required(),
                                                Forms\Components\TextInput::make('customer_service_telephone')
                                                    ->label('Customer Service Telephone')
                                                    ->tel()
                                                    ->required(),
                                                Forms\Components\TextInput::make('customer_service_email')
                                                    ->label('Customer Service Email')
                                                    ->email()
                                                    ->required(),
                                            ])->columnSpan(1),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Ownership Details')
                            ->icon('heroicon-s-book-open')
                            ->schema([
                                Forms\Components\Repeater::make('owners')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Section::make('Personal Information')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('salutation')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('full_names')
                                                            ->required(),
                                                        Forms\Components\DatePicker::make('date_of_birth')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('place_of_birth')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('mobile')
                                                            ->tel()
                                                            ->required(),
                                                        Forms\Components\TextInput::make('email')
                                                            ->email()
                                                            ->required(),
                                                    ])->columnSpan(1),

                                                Forms\Components\Section::make('Identification Details')
                                                    ->schema([
                                                        Forms\Components\TextInput::make('id_type')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('identification_number')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('nationality')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('country_of_residence')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('residential_address')
                                                            ->required(),
                                                        Forms\Components\TextInput::make('designation')
                                                            ->required(),
                                                    ])->columnSpan(1),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contact Details')
                            ->icon('heroicon-s-phone')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Primary Contact Information')
                                            ->relationship('contact')
                                            ->schema([
                                                Forms\Components\TextInput::make('primary_full_name')
                                                    ->required(),
                                                Forms\Components\TextInput::make('primary_country')
                                                    ->required(),
                                                Forms\Components\TextInput::make('primary_phone_number')
                                                    ->tel()
                                                    ->required(),
                                                Forms\Components\TextInput::make('primary_email')
                                                    ->email()
                                                    ->required(),
                                                Forms\Components\TextInput::make('primary_address')
                                                    ->required(),
                                                Forms\Components\TextInput::make('primary_town')
                                                    ->required(),
                                                Forms\Components\TextInput::make('primary_designation')
                                                    ->required(),
                                            ])->columnSpan(1),

                                        Forms\Components\Section::make('Secondary Contact Information')
                                            ->relationship('contact')
                                            ->schema([
                                                Forms\Components\TextInput::make('secondary_full_name'),
                                                Forms\Components\TextInput::make('secondary_country'),
                                                Forms\Components\TextInput::make('secondary_phone_number')
                                                    ->tel(),
                                                Forms\Components\TextInput::make('secondary_email')
                                                    ->email(),
                                                Forms\Components\TextInput::make('secondary_address'),
                                                Forms\Components\TextInput::make('secondary_town'),
                                                Forms\Components\TextInput::make('secondary_designation'),
                                            ])->columnSpan(1),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Bank Details')
                            ->icon('heroicon-s-document-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Bank Information')
                                    ->relationship('bank')
                                    ->schema([
                                        Forms\Components\TextInput::make('bank_name')
                                            ->required(),
                                        Forms\Components\TextInput::make('bank_branch')
                                            ->required(),
                                        Forms\Components\TextInput::make('bank_sort_code')
                                            ->required(),
                                        Forms\Components\TextInput::make('account_type')
                                            ->required(),
                                        Forms\Components\TextInput::make('account_number')
                                            ->required(),
                                        Forms\Components\TextInput::make('account_name')
                                            ->required(),
                                    ])->columnSpan(1),
                            ])->columns(1),

                        Forms\Components\Tabs\Tab::make('Website Information')
                            ->icon('heroicon-s-arrows-right-left')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Website Details')
                                            ->relationship('website')
                                            ->schema([
                                                Forms\Components\TextInput::make('url')
                                                    ->url()
                                                    ->required(),
                                                Forms\Components\Toggle::make('accept_international_payments')
                                                    ->required(),
                                                Forms\Components\TextInput::make('products_services')
                                                    ->required(),
                                                Forms\Components\TextInput::make('delivery_days')
                                                    ->numeric()
                                                    ->required(),
                                                Forms\Components\TextInput::make('total_sales_points')
                                                    ->numeric()
                                                    ->required(),
                                                Forms\Components\Toggle::make('secure_platform')
                                                    ->required(),
                                                Forms\Components\Textarea::make('security_details')
                                                    ->required(),
                                            ])->columnSpan(1),

                                        Forms\Components\Section::make('Payment Services')
                                            ->relationship('website')
                                            ->schema([
                                                Forms\Components\TagsInput::make('payment_services_request')
                                                    ->required(),
                                                Forms\Components\TagsInput::make('techpay_services_requested')
                                                    ->required(),
                                                Forms\Components\TagsInput::make('policies')
                                                    ->required(),
                                            ])->columnSpan(1),
                                    ]),
                            ]),
                    ])->columnSpan(2)->persistTab(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Submitted')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reference')
                    ->label('Reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.company_name')
                    ->label('Names')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_level_name')
                    ->label('Current Level')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        'NEEDS_CLARITY' => 'warning',
                        default => 'info',
                    })
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Review'),
                Tables\Actions\EditAction::make()
                    ->visible(fn (OnboardingApplications $record): bool =>
                        $record->approval_level === 0 && $record->status !== 'APPROVED'
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            Backend\OnboardingApplicationsResource\RelationManagers\FinanceRelationManager::class,
            Backend\OnboardingApplicationsResource\RelationManagers\AttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Backend\OnboardingApplicationsResource\Pages\ListOnboardingApplications::route('/'),
            'create' => Backend\OnboardingApplicationsResource\Pages\CreateOnboardingApplications::route('/create'),
            'edit' => Backend\OnboardingApplicationsResource\Pages\EditOnboardingApplications::route('/{record}/edit'),
            'view' => Backend\OnboardingApplicationsResource\Pages\ViewApplication::route('/{record}'),
        ];
    }


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Company Details')
                            ->icon('heroicon-s-user')
                            ->schema([

                                Components\Grid::make(2)
                                    ->schema([
                                        Components\Section::make('Basic Details')
                                            ->schema([
                                                Components\TextEntry::make('company.company_name')
                                                    ->label('Company Name:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.trading_name')
                                                    ->label('Trading Name:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.type_of_ownership')
                                                    ->label('Type of Ownership:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.rc_number')
                                                    ->label('RC Number:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.tpin')
                                                    ->label('TPIN:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.date_registered')
                                                    ->label('Date Registered:')
                                                    ->date()
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.nature_of_business')
                                                    ->label('Nature of Business:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.official_website')
                                                    ->label('Official Website:')
                                                    ->inlineLabel(),
                                            ])->columnSpan(1),

                                        Components\Section::make('Contact Information')
                                            ->schema([
                                                Components\TextEntry::make('company.office_address')
                                                    ->label('Office Address:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.postal_address')
                                                    ->label('Postal Address:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.country_of_incorporation')
                                                    ->label('Country of Incorporation:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.office_telephone')
                                                    ->label('Office Telephone:')
                                                    ->inlineLabel(),

                                                Components\TextEntry::make('company.official_email')
                                                    ->label('Official Email:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.customer_service_telephone')
                                                    ->label('Customer Service Telephone:')
                                                    ->inlineLabel(),
                                                Components\TextEntry::make('company.customer_service_email')
                                                    ->label('Customer Service Email:')
                                                    ->inlineLabel(),

                                            ])->columnSpan(1),
                                    ]),

                            ]),
                        Tabs\Tab::make('Ownership Details')
                            ->icon('heroicon-s-book-open')
                            ->schema([
                                Components\RepeatableEntry::make('owners')
                                    ->schema([
                                        Components\Grid::make(2)
                                            ->schema([
                                                Components\Section::make('Personal Information')
                                                    ->schema([
                                                        Components\TextEntry::make('salutation')
                                                            ->label('Salutation:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('full_names')
                                                            ->label('Full Names:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('date_of_birth')
                                                            ->label('Date of Birth:')
                                                            ->date()
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('place_of_birth')
                                                            ->label('Place of Birth:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('mobile')
                                                            ->label('Mobile:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('email')
                                                            ->label('Email:')
                                                            ->inlineLabel(),
                                                    ])->columnSpan(1),

                                                Components\Section::make('Identification Details')
                                                    ->schema([
                                                        Components\TextEntry::make('id_type')
                                                            ->label('ID Type:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('identification_number')
                                                            ->label('Identification Number:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('nationality')
                                                            ->label('Nationality:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('country_of_residence')
                                                            ->label('Country of Residence:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('residential_address')
                                                            ->label('Residential Address:')
                                                            ->inlineLabel(),
                                                        Components\TextEntry::make('designation')
                                                            ->label('Designation:')
                                                            ->inlineLabel(),
                                                    ])->columnSpan(1),
                                            ]),
                                    ])
                                    ->columnSpanFull()
                                    ->contained(false)
                                    ->label('Business Owners'),
                            ]),

                        Tabs\Tab::make('Contact Details')
                            ->icon('heroicon-s-phone')
                            ->schema([

                                Components\Section::make('Primary Contact Information')
                                    ->schema([
                                        Components\TextEntry::make('contact.primary_full_name')
                                            ->label('Full Name:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.primary_country')
                                            ->label('Country:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.primary_phone_number')
                                            ->label('Phone Number:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.primary_email')
                                            ->label('Email:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.primary_address')
                                            ->label('Address:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.primary_town')
                                            ->label('Town:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.primary_designation')
                                            ->label('Designation:')
                                            ->inlineLabel(),
                                    ])->columnSpan(1),

                                Components\Section::make('Secondary Contact Information')
                                    ->schema([
                                        Components\TextEntry::make('contact.secondary_full_name')
                                            ->label('Full Name:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.secondary_country')
                                            ->label('Country:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.secondary_phone_number')
                                            ->label('Phone Number:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.secondary_email')
                                            ->label('Email:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.secondary_address')
                                            ->label('Address:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.secondary_town')
                                            ->label('Town:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('contact.secondary_designation')
                                            ->label('Designation:')
                                            ->inlineLabel(),
                                    ])->columnSpan(1),
                            ])->columns(2),

                        Tabs\Tab::make('Bank Details')
                            ->icon('heroicon-s-document-currency-dollar')
                            ->schema([

                                Components\Section::make('Bank Information')
                                    ->schema([
                                        Components\TextEntry::make('bank.bank_name')
                                            ->label('Bank Name:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('bank.bank_branch')
                                            ->label('Bank Branch:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('bank.bank_sort_code')
                                            ->label('Bank Sort Code:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('bank.account_type')
                                            ->label('Account Type:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('bank.account_number')
                                            ->label('Account Number:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('bank.account_name')
                                            ->label('Account Name:')
                                            ->inlineLabel(),
                                    ])->columnSpan(1),
                            ])->columns(1),

                        Tabs\Tab::make('Website Information')
                            ->icon('heroicon-s-arrows-right-left')
                            ->schema([

                                Components\Section::make('Website Details')
                                    ->schema([
                                        Components\TextEntry::make('website.url')
                                            ->label('Website URL:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.accept_international_payments')
                                            ->label('Accept International Payments:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.products_services')
                                            ->label('Products/Services:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.delivery_days')
                                            ->label('Delivery Days:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.total_sales_points')
                                            ->label('Total Sales Points:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.secure_platform')
                                            ->label('Secure Platform:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.security_details')
                                            ->label('Security Details:')
                                            ->inlineLabel(),
                                    ])->columnSpan(1),

                                Components\Section::make('Payment Services')
                                    ->schema([
                                        Components\TextEntry::make('website.payment_services_request')
                                            ->label('Payment Services Requested:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.techpay_services_requested')
                                            ->label('TechPay Services Requested:')
                                            ->inlineLabel(),
                                        Components\TextEntry::make('website.policies')
                                            ->label('Policies:')
                                            ->inlineLabel(),
                                    ])->columnSpan(1),
                            ])->columns(2),

                        Tabs\Tab::make('Approval History')
                            ->icon('heroicon-s-check-circle')
                            ->schema([
                                Components\Section::make('Current Status')
                                    ->schema([
                                        Components\TextEntry::make('current_level_name')
                                            ->label('Current Level')
                                            ->badge(),
                                        Components\TextEntry::make('status')
                                            ->label('Status')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'APPROVED' => 'success',
                                                'REJECTED' => 'danger',
                                                'NEEDS_CLARITY' => 'warning',
                                                default => 'info',
                                            }),
                                    ]),
                                Components\Section::make('Approval Timeline')
                                    ->schema([
                                        Components\ViewEntry::make('approvals')
                                            ->view('filament.infolists.components.approvals-table')
                                            ->columnSpanFull(),
                                    ]),
                                Components\Section::make('Change History')
                                    ->schema([
                                        Components\ViewEntry::make('activities')
                                            ->view('filament.infolists.components.activity-log-table')
                                            ->columnSpanFull(),
                                    ]),
                            ])
                    ])->persistTab()->columnSpan(3),
            ]);
    }
    public static function getNavigationBadge(): ?string
    {
        /** @var class-string<Model> $modelClass */
        $modelClass = static::$model;

        return (string)$modelClass::where('status', 'PENDING')->count();
    }


}
