<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            if (!Schema::hasColumn('vessels', 'deleted_at')) {
                $table->softDeletes();
            }
            // Add unique index on name if not already present
            $table->unique('name', 'vessels_name_unique');
            // Optional helpful indexes for sorting
            $table->index(['type', 'size'], 'vessels_type_size_index');
        });
    }

    public function down(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            // Drop indexes if they exist
            $table->dropUnique('vessels_name_unique');
            $table->dropIndex('vessels_type_size_index');
            if (Schema::hasColumn('vessels', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
