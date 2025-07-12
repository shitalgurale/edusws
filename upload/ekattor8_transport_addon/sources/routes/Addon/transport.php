<?php

use App\Http\Controllers\Addon\TransportController;
use Illuminate\Support\Facades\Route;

//Online course
Route::controller(TransportController::class)->group(function () {

    // admin driver menu
    Route::get('admin/driver/list', 'driver_list')->name('admin.driver.list');
    Route::get('admin/driver/create_modal', 'driver_create_modal')->name('admin.driver.create_modal');
    Route::post('admin/driver/create', 'driver_create')->name('admin.driver.create');
    Route::get('admin/driver/edit_modal/{id}', 'driver_edit_modal')->name('admin.driver.edit_modal');
    Route::post('admin/driver/update/{id}', 'driver_update')->name('admin.driver.update');
    Route::get('admin/driver/delete/{id}', 'driver_delete')->name('admin.driver.delete');
    Route::get('admin/driver/search/result', 'driver_list')->name('admin.driver.search');

    // admin vehicle menu
    Route::get('admin/vehicle/list', 'vehicle_list')->name('admin.vehicle.list');
    Route::get('admin/vehicle/create_modal', 'vehicle_create_modal')->name('admin.vehicle.create_modal');
    Route::post('admin/vehicle/create', 'vehicle_create')->name('admin.vehicle.create');
    Route::get('admin/vehicle/edit_modal/{id}', 'vehicle_edit_modal')->name('admin.vehicle.edit_modal');
    Route::post('admin/vehicle/update/{id}', 'vehicle_update')->name('admin.vehicle.update');
    Route::get('admin/vehicle/delete/{id}', 'vehicle_delete')->name('admin.vehicle.delete');
    Route::get('admin/vehicle/search/result', 'vehicle_list')->name('admin.vehicle.search');

    // assign student
    Route::get('admin/assign/student/list', 'assign_student_list')->name('admin.assign.student.list');
    Route::get('admin/assign/individual', 'assign_individual')->name('admin.assign.individual');
    Route::post('admin/assign/individual/create', 'create_individual')->name('assign.individual.create');
    Route::get('student/by/class/{id}', 'studentByClass')->name('student.by.class');
    Route::get('admin/assign/by_class', 'assign_by_class')->name('admin.assign.by_class');
    Route::post('admin/assign/by_class/create', 'create_by_class')->name('assign.by_class.create');
    Route::get('admin/assign/student/delete/{id}', 'assign_student_remove')->name('assign.student.remove');

    Route::get('filter/category/{type}', 'filter_category')->name('filter.category');

    // driver panel
    Route::get('driver/dashboard', 'driver_dashboard')->name('driver.dashboard');

    Route::get('driver/noticeboard', 'driver_noticeboard')->name('driver.noticeboard');
    Route::get('driver/noticeboard/{id}', 'driver_noticeboard')->name('driver.edit.noticeboard');
    Route::get('driver/events/list', 'driver_event_list')->name('driver.events.list');
    Route::get('driver/assigned/student/list', 'assigned_student_list')->name('assigned.student.list');

    Route::post('driver/assigned/student/list', 'assigned_student_list')->name('assigned.student.list');
    Route::post('driver/show/student', 'show_student')->name('driver.show.student');

    Route::get('driver/profile', 'driver_profile')->name('driver.profile');
    Route::post('driver/profile/update', 'driver_profile_update')->name('driver.profile.update');
    Route::any('driver/password/{action_type}', 'driver_password')->name('driver.password');

    // trip
    Route::any('driver/trips/list', 'trip_list')->name('driver.trips.list');
    Route::post('driver/trips/create', 'trip_create')->name('driver.trips.create');
    Route::get('driver/trip/delete/{id}', 'trip_delete')->name('driver.trip.delete');
    Route::post('driver/trip/ongoing/{id}', 'trip_end')->name('driver.trips.end');

    Route::get('driver/vehicle/route/{id}', 'routeByVehicle')->name('driver.vehicle.route');
    // location update
    Route::post('update/location', 'update_location')->name('update.location');
    Route::post('get/location', 'get_location')->name('get.location');

    /*--------------------------------------------------------------------------------------------------*/
    // parent panel
    /*--------------------------------------------------------------------------------------------------*/
    Route::any('parent/trips/list/', 'trips_list')->name('parent.trips.list');

});
