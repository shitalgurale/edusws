<?php
use App\Http\Controllers\Addon\SmsController;
use Illuminate\Support\Facades\Route;

    // Sms Settings
    Route::get('admin/sms_center/sms_settings', [SmsController::class, 'settingsIndex'])->name('admin.sms_center.index');
    Route::post('admin/sms_center/settings_update', [SmsController::class, 'settingsUpdate'])->name('admin.sms_center.settingsUpdate');
    Route::post('admin/sms_center/settings_insert', [SmsController::class, 'settingsInsert'])->name('admin.sms_center.settingsInsert');
    Route::post('admin/sms_center/twiliostore', [SmsController::class, 'settingsTwilioStore'])->name('admin.sms_center.twiliostore');

    Route::post('admin/sms_center/msg', [SmsController::class, 'settingsMsgStore'])->name('admin.sms_center.msgstore');
    Route::get('admin/sms_center/sms_sender', [SmsController::class, 'smsSenderIndex'])->name('admin.sms_center.sms_sender');
    Route::post('admin/sms_center/tuiliomsg', [SmsController::class, 'tuiliomsg'])->name('admin.sms_center.tuiliomsg');
    Route::post('admin/sms_center/msg91', [SmsController::class, 'msg91'])->name('admin.sms_center.msg91');
    Route::any('admin/sms_center/receivers', [SmsController::class, 'receivers'])->name('admin.sms_center.receivers');
    Route::post('admin/sms_center/sms_sending', [SmsController::class, 'smsSending'])->name('admin.sms_center.sms_sending');

?>
