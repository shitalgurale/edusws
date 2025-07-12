<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Addon\HrController;



// HR Management Routes
Route::controller(HrController::class)->group(function () {

    // User Roles
    Route::get('hr/user/roles',  'user_role_index')->name('hr.user_role_index');
    Route::get('hr/user/roles/edit/{id}',  'user_role_edit')->name('hr.user_role_edit');
    Route::post('hr/user/roles/update/{id}',  'user_role_update')->name('hr.user_role_update');
    Route::get('hr/user/roles/delete/{id}',  'user_role_delete')->name('hr.user_role_detele');
    Route::get('hr/user/roles/create',  'user_role_create')->name('hr.user_role_create');
    Route::post('hr/user/roles/create/post',  'user_role_create_post')->name('hr.user_role_create_post');

    // User List
    Route::get('hr/user/list',  'userlist_index')->name('hr.userlist_index');
    Route::get('hr/user/create',  'create_user')->name('hr.create_user');
    Route::post('hr/user/create/post',  'create_user_post')->name('hr.create_user_post');
    Route::get('hr/user/list/import',  'userlist_import')->name('hr.userlist_import');
    Route::post('hr/user/list/import/post',  'userlist_import_post')->name('hr.userlist_import_post');
    Route::get('hr/user/list/show',  'userlist_show')->name('hr.userlist_show');
    Route::get('hr/user/list/user/edit/{id}',  'user_lists_user_edit')->name('hr.user_lists_user_edit');
    Route::post('hr/user/list/user/edit/post/{id}',  'user_lists_user_edit_post')->name('hr.user_lists_user_edit_post');
    Route::get('hr/user/list/user/delete/{id}',  'user_lists_user_delete')->name('hr.user_lists_user_delete');

    // HR Daily Attendence
    Route::get('attendence/list', 'list_of_attendence')->name('hr.list_of_attendence');
    Route::get('hr/take_attendence', 'show_take_attendence_modal')->name('hr.show_take_attendence_modal');
    Route::get('hr/role_wise_userlist',  'roleWiseUserlist')->name('hr.roleWiseUserlist');
    Route::post('hr/attendance_take', 'hr_take_attendance')->name('hr.hr_take_attendance');
    Route::get('attendance/filter', 'hrdailyAttendanceFilter')->name('hr.hr_daily_attendance.filter');
    Route::get('hr/attendance/csv', 'hrdailyAttendanceFilter_csv')->name('hr.hrdailyAttendanceFilter_csv');

    // HR Leave Request
    Route::get('hr/leave_list', 'list_of_leaves')->name('hr.list_of_leaves');
    Route::get('hr/leave_request', 'show_leave_request_modal')->name('hr.show_leave_request_modal');
    Route::post('leave_request/{action}/{id}', 'add_update_delete_leave_request')->name('hr.add_update_delete_leave_request');
    Route::get('update/leave_request/{id}', 'show_leave_update_request_modal')->name('hr.show_leave_update_request_modal');
    Route::get('delete/leave_request/{id}', 'delete_leave_request')->name('hr.delete_leave_request');
    Route::get('leave/action/{id}/{action}', 'actions_on_employee_leave')->name('hr.actions_on_employee_leave');
    Route::get('hr/create_leave', 'show_leave_request_modal_for_admin')->name('hr.show_leave_request_modal_for_admin');
    Route::get('role_wise_user/{id}',  'roleWiseUser')->name('hr.roleWiseUser');

    // Payroll Admin
    Route::get('payroll/list',  'list_of_payrolls')->name('hr.list_of_payrolls');
    Route::get('payroll/details',  'payrolls_details')->name('hr.payrolls_details');
    Route::get('payroll/payslip/{id}',  'payslip')->name('hr.payslip');
    Route::get('payroll/invoice/{id}',  'print_invoice')->name('hr.print_invoice');
    Route::get('payroll/udpate/status/{id}/{after_update_date}', 'update_payroll_status')->name('hr.update_payroll_status');
    Route::get('payroll/create/payslip',   'create_payslip')->name('hr.create_payslip');
    Route::get('payroll/get_use/by_role',  'get_user_by_role')->name('hr.get_user_by_role');
    Route::get('payroll/paysilp/details',  'payroll_add_view')->name('hr.payroll_add_view');
    Route::post('payroll/paysilp/create',  'insert_payslip_to_db')->name('hr.insert_payslip_to_db');

    // Payroll User
    Route::get('payment/list',  'user_list_of_payrolls')->name('hr.user_list_of_payrolls');
    Route::get('payment/details/user/{payroll_id}',  'user_payroll_print_details')->name('hr.user_payroll_print_details');
    Route::get('payment/details/user/print/{payroll_id}',  'user_payroll_print_details_print')->name('hr.user_payroll_print_details_print');

    //common checks
    Route::get('role_check/{id}', 'roleCheck')->name('hr.role_check');
});


