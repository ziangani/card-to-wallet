<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Merchants extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'logo',
        'description',
        'status',
        'primary_channel',
        'created_by',
        'updated_by'
    ];
    const CHANNEL_MPGS = 'MPGS';
    const CHANNEL_MPGS_CYBERSOURCE = 'MPGS CYBERSOURCE';
    const CHANNEL_TECHPAY = 'TECHPAY';
    
    const CHANNELS = [
        self::CHANNEL_MPGS => 'MPGS',
        self::CHANNEL_MPGS_CYBERSOURCE => 'MPGS CYBERSOURCE',
        self::CHANNEL_TECHPAY => 'TECHPAY',
    ];

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DISABLED = 'DISABLED';
    const  STATUSES = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_DISABLED => 'Disabled',
    ];

    public function company()
    {
        return $this->belongsTo(CompanyDetail::class, 'company_id');
    }

    public function apis()
    {
        return $this->hasMany(MerchantApis::class, 'merchant_id');
    }

    public function terminalHeartbeats()
    {
        return $this->hasManyThrough(TerminalHeartbeat::class, Terminals::class);
    }
    
    /**
     * Get reconciliations for this merchant.
     */
    public function reconciliations()
    {
        return $this->hasMany(MerchantReconciliation::class, 'merchant_id', 'code');
    }
    
    /**
     * Get payouts for this merchant.
     */
    public function payouts()
    {
        return $this->hasMany(MerchantPayout::class, 'merchant_id', 'code');
    }
    
    /**
     * Get fines for this merchant.
     */
    public function fines()
    {
        return $this->hasMany(MerchantFine::class, 'merchant_id', 'code');
    }
    
    /**
     * Calculate profitability for this merchant within a date range.
     */
    public function calculateProfitability($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subYear();
        $endDate = $endDate ?? now();
        
        // Get all reconciliations in date range
        $reconciliations = MerchantReconciliation::where('merchant_id', $this->code)
            ->where('status', 'ACTIVE')
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
        
        // Calculate total revenue (fees)
        $totalRevenue = $reconciliations->sum('platform_fee') + 
                       $reconciliations->sum('application_fee') + 
                       $reconciliations->sum('bank_fee');
        
        // Get all completed payouts
        $payouts = MerchantPayout::where('merchant_id', $this->code)
            ->where('status', 'COMPLETED')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->get();
        
        $totalPayoutAmount = $payouts->sum('amount');
        $totalRemittanceFees = $payouts->sum('remittance_fee');
        
        // Get all fines
        $fines = MerchantFine::where('merchant_id', $this->code)
            ->where('status', 'PAID')
            ->whereBetween('paid_date', [$startDate, $endDate])
            ->get();
        
        $totalFines = $fines->where('paid_by', 'PLATFORM')->sum('amount');
        
        // Calculate net profit
        $netProfit = $totalRevenue + $totalRemittanceFees - $totalPayoutAmount - $totalFines;
        
        return [
            'total_revenue' => $totalRevenue,
            'total_payouts' => $totalPayoutAmount,
            'total_remittance_fees' => $totalRemittanceFees,
            'total_fines_paid' => $totalFines,
            'net_profit' => $netProfit,
            'is_profitable' => $netProfit > 0,
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
        ];
    }
}
