<?php

use App\Models\Estimate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('text')->nullable();
            $table->string('type'); // text | prices

            $table->unsignedInteger('order')->default(0);

            $table->foreignIdFor(Estimate::class)->constrained()->onDelete('cascade');

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
        Schema::dropIfExists('sections');
    }
}
