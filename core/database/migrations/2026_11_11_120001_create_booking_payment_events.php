<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('booking_payment_events',function(Blueprint $table){
            $table->bigIncrements('id');
            $table->string('provider',24);
            $table->string('event_id',140);
            $table->string('payment_id',140);
            $table->string('kind',32);
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency',3);
            $table->char('payload_sha256',64);
            $table->char('fingerprint',64);
            $table->string('state',32)->default('received');
            $table->timestamps();
            $table->unique(['provider','event_id'],'bpe_event_unique');
            $table->index(['provider','payment_id'],'bpe_payment_lookup');
            $table->index(['tenant_id','booking_id'],'bpe_booking_lookup');
        });
    }
    public function down(): void { Schema::dropIfExists('booking_payment_events'); }
};
