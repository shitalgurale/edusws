// Database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//Table structure for table `assigned_students`
if(!Schema::hasTable('assigned_students'))
{
    Schema::create('assigned_students', function (Blueprint $table) {
        $table->id();
        $table->integer('school_id');
        $table->integer('vehicle_id')->nullable();
        $table->integer('class_id')->nullable();
        $table->integer('user_id')->nullable();
        $table->integer('driver_id')->nullable();
        $table->timestamp('updated_at')->nullable();
        $table->timestamp('created_at')->nullable();
    });
}

//Table structure for table `trips`
if(!Schema::hasTable('trips'))
{
    Schema::create('trips', function (Blueprint $table) {
        $table->id();
        $table->integer('school_id');
        $table->string('vehicle_id')->nullable();
        $table->string('start_from')->nullable();
        $table->string('update_location')->nullable();
        $table->string('start_time')->nullable();
        $table->string('end_time')->nullable();
        $table->integer('active')->nullable();
        $table->timestamp('updated_at')->nullable();
        $table->timestamp('created_at')->nullable();
    });
}

//Table structure for table `vehicles`
if(!Schema::hasTable('vehicles'))
{
    Schema::create('vehicles', function (Blueprint $table) {
        $table->id();
        $table->integer('school_id');
        $table->integer('driver_id')->nullable();
        $table->string('vehicle_number')->nullable();
        $table->string('vehicle_model')->nullable();
        $table->string('chassis_number')->nullable();
        $table->integer('seat')->nullable();
        $table->text('route')->nullable();
        $table->string('made_year')->nullable();
        $table->timestamp('updated_at')->nullable();
        $table->timestamp('created_at')->nullable();
    });
}

// Columns to be dropped from the trips table
$columnsToDrop = [
    'driver_id', 
    'user_id'
];

// Filter out the columns that exist in the trips table
$columnsToDrop = array_filter($columnsToDrop, function ($column) {
    return Schema::hasColumn('trips', $column);
});

if (!empty($columnsToDrop)) {
    Schema::table('trips', function (Blueprint $table) use ($columnsToDrop) {
        $table->dropColumn($columnsToDrop);
    });
}