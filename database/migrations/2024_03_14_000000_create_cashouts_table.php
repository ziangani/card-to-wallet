<?php

use App\Models\Merchants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_outs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Merchants::class, 'merchant_id');
            $table->string('batch_id')->unique();
            $table->string('reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->decimal('fee', 10, 2);
            $table->decimal('our_charge', 10, 2);
            $table->decimal('third_party_charge', 10, 2);
            $table->string('batch_status');
            $table->string('transaction_status');
            $table->string('account_type');
            $table->string('account_number');
            $table->string('bank_name')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('swift_code')->nullable();
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_outs');
    }
};
