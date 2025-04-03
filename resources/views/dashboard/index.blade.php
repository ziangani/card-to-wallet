@extends('layouts.app')

@section('title', 'Dashboard - ' . config('app.name'))
@section('meta_description', 'Manage your card-to-wallet transfers and account')
@section('header_title', 'Dashboard')

@section('content')

            <!-- Verification Status Card -->
            @include('dashboard.partials.verification-status')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                <div class="lg:col-span-2">
                    <!-- Quick Transaction Widget -->
{{--                    @include('dashboard.partials.quick-transaction')--}}

                    <!-- Recent Transactions -->
                    @include('dashboard.partials.recent-transactions')
                </div>

                <div>
                    <!-- Saved Beneficiaries -->
                    @include('dashboard.partials.saved-beneficiaries')
                </div>
            </div>
@endsection
