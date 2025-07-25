//database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


//Table structure for table `hr_daily_attendences`
if(!Schema::hasTable('hr_daily_attendences'))
{
    Schema::create('hr_daily_attendences', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->integer('role_id');
        $table->integer('school_id');
        $table->integer('session_id');
        $table->integer('status');
        $table->string('created_at');
        $table->string('updated_at')->nullable();
    });
}


//Table structure for table `hr_payroll`
if(!Schema::hasTable('hr_payroll'))
{
    Schema::create('hr_payroll', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->decimal('allowances', 10,2);
        $table->decimal('deducition', 10,2);
        $table->string('created_at');
        $table->string('updated_at')->nullable();
        $table->string('status');
        $table->integer('school_id');
    });
}


//Table structure for table `hr_roles`
if(!Schema::hasTable('hr_roles'))
{
    Schema::create('hr_roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('permanent')->default('no');
        $table->integer('school_id');
        $table->integer('created_at');
    });
}


//Table structure for table `hr_user_list`
if(!Schema::hasTable('hr_user_list'))
{
    Schema::create('hr_user_list', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->integer('role_id')->nullable();
        $table->longText('address')->nullable();
        $table->string('phone')->nullable();
        $table->string('gender')->nullable();
        $table->string('blood_group')->nullable();
        $table->longText('joining_salary')->nullable();
        $table->integer('school_id')->nullable();
    });
}


//Table structure for table `leavelists`
if(!Schema::hasTable('leavelists'))
{
    Schema::create('leavelists', function (Blueprint $table) {
        $table->id();
        $table->integer('user_id');
        $table->integer('role_id');
        $table->longText('start_date');
        $table->longText('end_date');
        $table->longText('reason');
        $table->integer('status');
        $table->string('created_at');
        $table->string('updated_at');
        $table->integer('school_id')->nullable();
    });
}


//Hr permanent roles add for multiple school
$schools = DB::table('schools')->get();
foreach($schools as $school) {
	$admin_role = DB::table('hr_roles')->where('name', 'admin')->where('permanent', 'yes')->where('school_id', $school->id);

	if($admin_role->get()->count() == 0){
		DB::table('hr_roles')->insert([
			'name' => 'admin',
			'permanent' => 'yes',
			'school_id' => $school->id,
			'created_at' => '0',
		]);
	}

	$teacher_role = DB::table('hr_roles')->where('name', 'teacher')->where('permanent', 'yes')->where('school_id', $school->id);

	if($teacher_role->get()->count() == 0){
		DB::table('hr_roles')->insert([
			'name' => 'teacher',
			'permanent' => 'yes',
			'school_id' => $school->id,
			'created_at' => '0',
		]);
	}

	$accountant_role = DB::table('hr_roles')->where('name', 'accountant')->where('permanent', 'yes')->where('school_id', $school->id);

	if($accountant_role->get()->count() == 0){
		DB::table('hr_roles')->insert([
			'name' => 'accountant',
			'permanent' => 'yes',
			'school_id' => $school->id,
			'created_at' => '0',
		]);
	}

	$librarian_role = DB::table('hr_roles')->where('name', 'librarian')->where('permanent', 'yes')->where('school_id', $school->id);

	if($librarian_role->get()->count() == 0){
		DB::table('hr_roles')->insert([
			'name' => 'librarian',
			'permanent' => 'yes',
			'school_id' => $school->id,
			'created_at' => '0',
		]);
	}	
}