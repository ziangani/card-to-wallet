<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiLogsTable extends Migration
{
//    protected $connection = 'logs';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //connection('logs')->
        Schema::create('api_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->dateTime('request_time');
            $table->dateTime('response_time');
            $table->longText('source_ip')->nullable();
            $table->json('entity_state')->nullable();
            $table->json('new_state')->nullable();
            $table->char('reference', 150)->nullable();
            $table->char('source_reference', 250)->nullable();
            $table->char('request_type', 50)->nullable();
            $table->char('request_status', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_logs');
    }
}
