<?php

use App\Models\Section;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('description');
            $table->decimal('duration_rate')->default(0);
            $table->string('duration')->nullable();
            $table->decimal('price')->nullable()->default(0);
            $table->boolean('obligatory')->default(false);

            $table->unsignedInteger('order')->default(0);

            $table->foreignIdFor(Section::class)->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
}
