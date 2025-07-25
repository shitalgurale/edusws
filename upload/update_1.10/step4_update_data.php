//database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$data['key'] = 'recaptcha_site_key';
$data['value'] = Null;  
DB::table('global_settings')->insert($data);

$data['key'] = 'recaptcha_secret_key';
$data['value'] = NULL;
DB::table('global_settings')->insert($data);

$data['key'] = 'recaptcha_switch_value';
$data['value'] = 'No';
DB::table('global_settings')->insert($data);


// Columns to be added to the users table
$columnsToAdd = [
    'language',
];

// Filter out the columns that already exist in the users table
$columnsToAdd = array_filter($columnsToAdd, function ($column) {
    return !Schema::hasColumn('users', $column);
});

// Check if there are columns to add before modifying the table
if (!empty($columnsToAdd)) {
    Schema::table('users', function (Blueprint $table) use ($columnsToAdd) {
        foreach ($columnsToAdd as $column) {
            $table->string($column)->nullable();
        }
    });
}

// Columns to be added to the exams table
$columnsToAdd = [
    'room_number',
];

// Filter out the columns that already exist in the exams table
$columnsToAdd = array_filter($columnsToAdd, function ($column) {
    return !Schema::hasColumn('exams', $column);
});

// Check if there are columns to add before modifying the table
if (!empty($columnsToAdd)) {
    Schema::table('exams', function (Blueprint $table) use ($columnsToAdd) {
        foreach ($columnsToAdd as $column) {
            $table->string($column)->nullable();
        }
    });
}