<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // In the new migration file (e.g., xxxx_xx_xx_xxxxxx_add_reason_type_to_reports_table.php)
public function up(): void
{
    Schema::table('reports', function (Blueprint $table) {
        // Add the new column for predefined reasons
        $table->string('reason_type')->nullable()->after('reportable_id');
        // Make the old 'reason' column nullable, for cases where user just selects a type
        $table->text('reason')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            //
        });
    }
};
