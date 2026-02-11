<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('target', 20)->default('_self');
            $table->string('icon')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_external')->default(false);
            $table->timestamps();

            $table->foreign('parent_id')
                ->references('id')
                ->on('menus')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
