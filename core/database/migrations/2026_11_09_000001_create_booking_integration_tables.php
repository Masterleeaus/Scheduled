<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('booking_tenant_instances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('owner_user_id')->unique();
            $table->string('instance_key', 64)->unique();
            $table->string('database_reference', 160)->unique();
            $table->string('status', 24)->default('pending');
            $table->timestamps();
        });
        Schema::create('booking_customer_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_tenant_instance_id');
            $table->unsignedBigInteger('plusagency_customer_id');
            $table->unsignedBigInteger('cleanto_customer_id');
            $table->timestamps();
            $table->foreign('booking_tenant_instance_id', 'bcl_instance_fk')->references('id')->on('booking_tenant_instances')->cascadeOnDelete();
            $table->unique(['booking_tenant_instance_id', 'plusagency_customer_id'], 'bcl_pa_unique');
            $table->unique(['booking_tenant_instance_id', 'cleanto_customer_id'], 'bcl_ct_unique');
        });
        Schema::create('booking_service_links', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_tenant_instance_id');
            $table->unsignedBigInteger('plusagency_service_id');
            $table->unsignedBigInteger('cleanto_service_id');
            $table->timestamps();
            $table->foreign('booking_tenant_instance_id', 'bsl_instance_fk')->references('id')->on('booking_tenant_instances')->cascadeOnDelete();
            $table->unique(['booking_tenant_instance_id', 'plusagency_service_id'], 'bsl_pa_unique');
            $table->unique(['booking_tenant_instance_id', 'cleanto_service_id'], 'bsl_ct_unique');
        });
        Schema::create('booking_request_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('booking_tenant_instance_id');
            $table->string('idempotency_key_hash', 64);
            $table->string('request_digest', 64);
            $table->string('status', 24)->default('pending');
            $table->string('external_booking_id', 100)->nullable();
            $table->timestamps();
            $table->foreign('booking_tenant_instance_id', 'brk_instance_fk')->references('id')->on('booking_tenant_instances')->cascadeOnDelete();
            $table->unique(['booking_tenant_instance_id', 'idempotency_key_hash'], 'brk_tenant_key_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_request_keys');
        Schema::dropIfExists('booking_service_links');
        Schema::dropIfExists('booking_customer_links');
        Schema::dropIfExists('booking_tenant_instances');
    }
};
