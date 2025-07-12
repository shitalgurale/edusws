//database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

$data['key'] = 'front_logo';
$data['value'] = '17001224705.png';
DB::table('global_settings')->insert($data);

$data['key'] = 'features_title';
$data['value'] = NULL;
DB::table('global_settings')->insert($data);

$data['key'] = 'features_subtitle';
$data['value'] = 'Make your application more advanced with Ekattor 8';
DB::table('global_settings')->insert($data);

$data['key'] = 'off_pay_ins_text';
$data['value'] = 'You can make payments using your mobile banking number.';
DB::table('global_settings')->insert($data);

$data['key'] = 'off_pay_ins_file';
$data['value'] = Null;
DB::table('global_settings')->insert($data);