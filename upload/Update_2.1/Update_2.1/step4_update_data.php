//database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


$columnsToAdd = [
    'school_role' => 'string',
    'account_status' => 'string',
    'menu_permission' => 'text',
];

// Filter out the columns that already exist in the users table
$columnsToAdd = array_filter($columnsToAdd, function ($type, $column) {
    return !Schema::hasColumn('users', $column);
}, ARRAY_FILTER_USE_BOTH);

// Check if there are columns to add before modifying the table
if (!empty($columnsToAdd)) {
    Schema::table('users', function (Blueprint $table) use ($columnsToAdd) {
        foreach ($columnsToAdd as $column => $type) {
            if ($type === 'string') {
                $table->string($column)->nullable();
            } elseif ($type === 'text') {
                $table->text($column)->nullable();
            }
            // Add more types if needed
        }
    });
}

$schools = DB::table('schools')->get();

foreach ($schools as $school) {
   $school_admin = DB::table('users')->where('role_id', 2)->where('school_id', $school->id)->first();

   if ($school_admin) {   
        DB::table('users')->where('id', $school_admin->id)->update([
                    'school_role' => '1',
                ]);
    }
}
