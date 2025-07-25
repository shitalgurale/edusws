//database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('users', 'documents')) {
    Schema::table('users', function (Blueprint $table1) {
        $table1->string('documents')->nullable();;
    });
}

if (!Schema::hasColumn('exams', 'exam_category_id')) {
    Schema::table('exams', function (Blueprint $table1) {
        $table1->string('exam_category_id')->nullable();;
    });
}