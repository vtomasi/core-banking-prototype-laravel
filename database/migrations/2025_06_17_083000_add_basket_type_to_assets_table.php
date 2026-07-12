<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (! Schema::hasColumn('assets', 'is_basket')) {
                $table->boolean('is_basket')->default(false);

                $table->index('is_basket');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE assets
                MODIFY COLUMN type
                ENUM(
                    'fiat',
                    'crypto',
                    'commodity',
                    'custom',
                    'basket'
                )
            ");
        }

        // PostgreSQL:
        // Nessuna modifica necessaria.
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::table('assets')
                ->where('type', 'basket')
                ->delete();

            DB::statement("
                ALTER TABLE assets
                MODIFY COLUMN type
                ENUM(
                    'fiat',
                    'crypto',
                    'commodity',
                    'custom'
                )
            ");
        }

        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'is_basket')) {
                $table->dropIndex(['is_basket']);
                $table->dropColumn('is_basket');
            }
        });
    }
};
