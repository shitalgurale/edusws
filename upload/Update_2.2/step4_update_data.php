use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$columnsToAdd = [
    'discounted_price' => 'integer',
    'amount' => 'integer',
];

// Filter out the columns that already exist in the student_fee_managers table
$columnsToAdd = array_filter($columnsToAdd, function ($type, $column) {
    return !Schema::hasColumn('student_fee_managers', $column);
}, ARRAY_FILTER_USE_BOTH);

// Check if there are columns to add before modifying the table
if (!empty($columnsToAdd)) {
    Schema::table('student_fee_managers', function (Blueprint $table) use ($columnsToAdd) {
        foreach ($columnsToAdd as $column => $type) {
            if ($type === 'integer') {
                $table->integer($column)->nullable();
            } elseif ($type === 'text') {
                $table->text($column)->nullable();
            }
            // Add more types if needed
        }
    });
}

$columnsToAdd = [
    'student_info' => 'text',
];

// Filter out the columns that already exist in the users table
$columnsToAdd = array_filter($columnsToAdd, function ($type, $column) {
    return !Schema::hasColumn('users', $column);
}, ARRAY_FILTER_USE_BOTH);

// Check if there are columns to add before modifying the table
if (!empty($columnsToAdd)) {
    Schema::table('users', function (Blueprint $table) use ($columnsToAdd) {
        foreach ($columnsToAdd as $column => $type) {
            if ($type === 'integer') {
                $table->integer($column)->nullable();
            } elseif ($type === 'text') {
                $table->text($column)->nullable();
            }
            // Add more types if needed
        }
    });
}
