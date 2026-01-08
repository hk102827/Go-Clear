use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE driver_services 
            MODIFY pricing_type 
            ENUM(
                'fixed',
                'per_mile',
                'per_hour',
                'per_load',
                'per_ton_kg'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE driver_services 
            MODIFY pricing_type 
            ENUM(
                'fixed',
                'per_mile',
                'per_hour',
                'per_load'
            ) NOT NULL
        ");
    }
};
