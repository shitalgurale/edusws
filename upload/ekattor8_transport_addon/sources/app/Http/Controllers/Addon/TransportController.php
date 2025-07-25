<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers\Addon;

use App\Http\Controllers\Controller;
use App\Models\FrontendEvent;
use App\Models\Noticeboard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TransportController extends Controller
{
    /*--------------------------------------------------------------------------------------------------*/
    // driver panel
    /*--------------------------------------------------------------------------------------------------*/
    public function driver_dashboard()
    {
        return view('driver.dashboard');
    }

    public function driver_profile()
    {
        return view('driver.profile.view');
    }

    public function driver_profile_update(Request $request)
    {
        $data['name'] = $request->name;
        $data['email'] = $request->email;

        $user_info['birthday'] = strtotime($request->eDefaultDateRange);
        $user_info['gender'] = $request->gender;
        $user_info['phone'] = $request->phone;
        $user_info['address'] = $request->address;

        if (empty($request->photo)) {
            $user_info['photo'] = $request->old_photo;
        } else {
            $file_name = random(10) . '.png';
            $user_info['photo'] = $file_name;

            $request->photo->move(public_path('assets/uploads/user-images/'), $file_name);
        }

        $data['user_information'] = json_encode($user_info);
        User::where('id', auth()->user()->id)->update($data);

        return redirect(route('driver.profile'))->with('message', get_phrase('Profile info updated'));
    }

    public function driver_password($action_type = null, Request $request)
    {
        if ($action_type == 'update') {

            if ($request->new_password != $request->confirm_password) {
                return back()->with("error", "Confirm Password Doesn't match!");
            }
            if (!Hash::check($request->old_password, auth()->user()->password)) {
                return back()->with("error", "Current Password Doesn't match!");
            }

            $data['password'] = Hash::make($request->new_password);
            User::where('id', auth()->user()->id)->update($data);

            return redirect(route('driver.password', 'edit'))->with('message', get_phrase('Password changed'));
        }

        return view('driver.profile.password');
    }

    public function driver_noticeboard()
    {
        $notices = Noticeboard::get()->where('school_id', auth()->user()->school_id);
        $events = array();

        foreach ($notices as $notice) {
            if ($notice->end_date != "") {
                if ($notice->start_date != $notice->end_date) {
                    $end_date = strtotime($notice->end_date) + 24 * 60 * 60;
                    $end_date = date('Y-m-d', $end_date);
                } else {
                    $end_date = date('Y-m-d', strtotime($notice->end_date));
                }
            }

            if ($notice->end_date == "" && $notice->start_time == "" && $notice->end_time == "") {
                $info = array(
                    'id' => $notice->id,
                    'title' => $notice->notice_title,
                    'start' => date('Y-m-d', strtotime($notice->start_date)),
                );
            } else if ($notice->start_time != "" && ($notice->end_date == "" && $notice->end_time == "")) {
                $info = array(
                    'id' => $notice->id,
                    'title' => $notice->notice_title,
                    'start' => date('Y-m-d', strtotime($notice->start_date)) . 'T' . $notice->start_time,
                );
            } else if ($notice->end_date != "" && ($notice->start_time == "" && $notice->end_time == "")) {
                $info = array(
                    'id' => $notice->id,
                    'title' => $notice->notice_title,
                    'start' => date('Y-m-d', strtotime($notice->start_date)),
                    'end' => $end_date,
                );
            } else if ($notice->end_date != "" && $notice->start_time != "" && $notice->end_time != "") {
                $info = array(
                    'id' => $notice->id,
                    'title' => $notice->notice_title,
                    'start' => date('Y-m-d', strtotime($notice->start_date)) . 'T' . $notice->start_time,
                    'end' => date('Y-m-d', strtotime($notice->end_date)) . 'T' . $notice->end_time,
                );
            } else {
                $info = array(
                    'id' => $notice->id,
                    'title' => $notice->notice_title,
                    'start' => date('Y-m-d', strtotime($notice->start_date)),
                );
            }
            array_push($events, $info);
        }
        $events = json_encode($events);
        return view('driver.noticeboard.noticeboard', ['events' => $events]);
    }

    public function driver_editNoticeboard($id = "")
    {
        $notice = Noticeboard::find($id);
        return view('driver.noticeboard.edit', ['notice' => $notice]);
    }

    public function driver_event_list(Request $request)
    {
        $search = $request['search'] ?? "";
        if ($search != "") {
            $events = FrontendEvent::where(function ($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%");
            })->paginate(10);

        } else {
            $events = FrontendEvent::where('school_id', auth()->user()->school_id)
                ->paginate(10);
        }

        return view('driver.events.events', compact('events', 'search'));
    }

    public function assigned_student_list(Request $request)
    {
        $data['assigned_students'] = DB::table('assigned_students')
            ->where('driver_id', auth()->user()->id)
            ->where('school_id', auth()->user()->school_id)
            ->paginate(10);

        $data['vehicles'] = DB::table('vehicles')
            ->where('school_id', auth()->user()->school_id)
            ->where('driver_id', auth()->user()->id)
            ->get();

        $vehicle_id = $request['vehicle_number'] ?? "";
        if ($vehicle_id != "") {
            $data['assigned_students'] = DB::table('assigned_students')
                ->where('driver_id', auth()->user()->id)
                ->where('school_id', auth()->user()->school_id)
                ->where('vehicle_id', $request->vehicle_number)
                ->paginate(10);
        }

        return view('driver.assigned_student.list', $data);
    }

    /*--------------------------------------------------------------------------------------------------*/
    // admin panel->driver menu
    /*--------------------------------------------------------------------------------------------------*/
    public function driver_list(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {
            $driver_info = User::where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 8);
            })->orWhere(function ($query) use ($search) {
                $query->where('email', 'LIKE', "%{$search}%")
                    ->where('school_id', auth()->user()->school_id)
                    ->where('role_id', 8);
            })->paginate(10);

        } else {
            $driver_info = User::where('role_id', 8)->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.transport.drivers.driver_list', compact('driver_info', 'search'));
    }

    public function driver_create_modal()
    {
        return view('admin.transport.drivers.add_driver');
    }

    public function driver_create(Request $request)
    {
        if (!empty($request->photo)) {
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('assets/uploads/user-images/'), $imageName);
            $photo = $imageName;
        } else {
            $photo = '';
        }

        $info = array(
            'gender' => $request->gender,
            'blood_group' => $request->blood_group,
            'birthday' => strtotime($request->birthday),
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photo,
        );

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => '8',
            'school_id' => auth()->user()->school_id,
            'password' => Hash::make($request->password),
            'user_information' => json_encode($info),
        ];

        User::create($data);
        return redirect()->back()->with('message', 'You have successfully added driver.');
    }

    public function driver_edit_modal($id)
    {
        $data['driver'] = User::find($id);
        return view('admin.transport.drivers.edit_driver', $data);
    }

    public function driver_update(Request $request, $id)
    {
        if (!empty($request->photo)) {
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move(public_path('assets/uploads/user-images/'), $imageName);
            $photo = $imageName;
        } else {
            $user_information = User::where('id', $id)->value('user_information');
            $file_name = json_decode($user_information)->photo;

            if ($file_name != '') {
                $photo = $file_name;
            } else {
                $photo = '';
            }
        }

        $info = array(
            'gender' => $request->gender,
            'blood_group' => $request->blood_group,
            'birthday' => strtotime($request->birthday),
            'phone' => $request->phone,
            'address' => $request->address,
            'photo' => $photo,
        );

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => '8',
            'school_id' => auth()->user()->school_id,
            'user_information' => json_encode($info),
        ];

        User::where('id', $id)->update($data);
        return redirect()->back()->with('message', 'You have successfully updated driver.');
    }

    public function driver_delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect()->back()->with('message', 'You have successfully deleted driver.');
    }

    /*--------------------------------------------------------------------------------------------------*/
    // admin panel->vehicle menu
    /*--------------------------------------------------------------------------------------------------*/
    public function vehicle_list(Request $request)
    {
        $search = $request['search'] ?? "";

        if ($search != "") {
            $data['vehicle_info'] = DB::table('vehicles')
                ->where('vehicle_model', 'LIKE', '%' . $search . '%')
                ->orWhere('vehicle_number', 'LIKE', '%' . $search . '%')
                ->where('school_id', auth()->user()->school_id)
                ->paginate(10);
        } else {
            $data['vehicle_info'] = DB::table('vehicles')->where('school_id', auth()->user()->school_id)->paginate(10);
        }

        return view('admin.transport.vehicle.vehicle_list', $data);
    }

    public function vehicle_create_modal()
    {
        $data = [
            'driver_info' => User::where('school_id', auth()->user()->school_id)
                ->where('role_id', 8)->get(),
            'school_id' => auth()->user()->school_id,
        ];

        return view('admin.transport.vehicle.add_vehicle', $data);
    }

    public function vehicle_create(Request $request)
    {
        $data = [
            'school_id' => $request->school_id,
            'driver_id' => $request->assign_driver,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_model' => $request->vehicle_model,
            'chassis_number' => $request->chassis_number,
            'seat' => $request->seat,
            'route' => $request->route,
            'made_year' => strtotime('now'),
        ];

        DB::table('vehicles')->insert($data);
        return redirect()->back()->with('message', 'Vehicle added successfully');
    }

    public function vehicle_edit_modal($id)
    {
        $data['vehicle_info'] = DB::table('vehicles')->where('id', $id)->first();
        $data['driver_info'] = User::where('school_id', auth()->user()->school_id)
            ->where('role_id', 8)->get();
        return view('admin.transport.vehicle.edit_vehicle', $data);
    }

    public function vehicle_update(Request $request, $id)
    {
        $driver = User::where('id', $request->assign_driver)->first();
        $info = json_decode($driver->user_information);

        $data = [
            'driver_id' => $request->assign_driver,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_model' => $request->vehicle_model,
            'chassis_number' => $request->chassis_number,
            'seat' => $request->seat,
            'route' => $request->route,
        ];

        DB::table('vehicles')->where('id', $id)->update($data);
        return redirect()->back()->with('message', 'Updated successfully.');
    }

    public function vehicle_delete($id)
    {
        DB::table('vehicles')->where('id', $id)->delete();
        return redirect()->back()->with('message', 'Deleted successfully.');
    }

    /*--------------------------------------------------------------------------------------------------*/
    // admin panel->assign students
    /*--------------------------------------------------------------------------------------------------*/
    public function assign_student_list(Request $request)
    {
        $filter = $request['type_id'] ?? "";
        $category = $name = $id = $assigned_list = 0;

        // when user try to filter
        if ($filter != "") {
            /*
            if search result is not an int
            show error with try again msg
             */
            if (ctype_digit($request->type_id)) {
                if ($request->category == 'vehicle') {
                    $assigned_list = DB::table('assigned_students')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('vehicle_id', $request->type_id)
                        ->paginate(10);
                    $category = 'Vehicle';

                    $name = DB::table('vehicles')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('id', $request->type_id)
                        ->value('vehicle_number');
                } elseif ($request->category == 'driver') {
                    $assigned_list = DB::table('assigned_students')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('driver_id', $request->type_id)
                        ->paginate(10);
                    $category = 'Driver';

                    $name = DB::table('users')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('id', $request->type_id)
                        ->value('name');
                } elseif ($request->category == 'class') {
                    $assigned_list = DB::table('assigned_students')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('class_id', $request->type_id)
                        ->paginate(10);
                    $category = 'Class';

                    $name = DB::table('classes')
                        ->where('school_id', auth()->user()->school_id)
                        ->where('id', $request->type_id)
                        ->value('name');
                }
            } else {
                return redirect()->back()->with('message', 'Try new search.');
            }

        } else {
            $assigned_list = DB::table('assigned_students')
                ->where('school_id', auth()->user()->school_id)
                ->paginate(10);
        }

        return view('admin.transport.assign.assign_student',
            compact(
                'assigned_list',
                'filter',
                'category',
                'name',
            )
        );
    }

    public function filter_category($type)
    {
        $name = 'name';

        if ($type == 'vehicle') {
            $options = '<option value="">' . 'Select a vehicle' . '</option>';
            $categories = DB::table('vehicles')
                ->where('school_id', auth()->user()->school_id)
                ->get();
            $name = 'vehicle_number';

        } elseif ($type == 'driver') {
            $options = '<option value="">' . 'Select a driver' . '</option>';
            $categories = DB::table('users')
                ->where('role_id', 8)
                ->where('school_id', auth()->user()->school_id)
                ->get();
        } else {
            $options = '<option value="">' . 'Select a class' . '</option>';
            $categories = DB::table('classes')
                ->where('school_id', auth()->user()->school_id)
                ->get();
        }

        foreach ($categories as $category):
            $options .= '<option value="' . $category->id . '">' . $category->$name . '</option>';
        endforeach;
        echo $options;
    }

    public function assign_individual()
    {
        $data = [
            'vehicles' => DB::table('vehicles')->where('school_id', auth()->user()->school_id)->get(),
            'classes' => DB::table('classes')->where('school_id', auth()->user()->school_id)->get(),
            'enrollments' => DB::table('enrollments')->where('school_id', auth()->user()->school_id)->get(),
        ];
        return view('admin.transport.assign.individual', $data);
    }

    public function studentByClass($check)
    {
        $students = DB::table('enrollments')
            ->where('class_id', $check)
            ->where('school_id', auth()->user()->school_id)
            ->get();

        $options = '<option value="">' . 'Select a student' . '</option>';
        foreach ($students as $student):
            $name = DB::table('users')
                ->where('id', $student->user_id)
                ->first();
            $options .= '<option value="' . $name->id . '">' . $name->name . '</option>';
        endforeach;

        echo $options;
    }

    public function create_individual(Request $request)
    {
        $check = DB::table('assigned_students')
            ->where('user_id', $request->student_id)->first();

        // checking student exists or not
        if ($check) {
            return redirect()->back()->with('error', 'Student exists');
        } else {
            $vehicle = DB::table('vehicles')
                ->where('id', $request->vehicle_id)
                ->first();

            $driver = DB::table('vehicles')
                ->where('id', $request->vehicle_id)
                ->first();

            $seat = DB::table('assigned_students')
                ->where('school_id', $vehicle->school_id)
                ->where('vehicle_id', $vehicle->id)
                ->count();

            // if seat available then assign student
            if ($seat < $vehicle->seat) {
                $data = [
                    'school_id' => $vehicle->school_id,
                    'vehicle_id' => $request->vehicle_id,
                    'class_id' => $request->class_id,
                    'user_id' => $request->student_id,
                    'driver_id' => DB::table('vehicles')
                        ->where('id', $request->vehicle_id)
                        ->value('driver_id'),
                ];

                DB::table('assigned_students')->insert($data);
                return redirect()->back()->with('message', 'Student assigned');
            }
            return redirect()->back()->with('error', 'Selected vehicle full');
        }

    }

    public function assign_by_class()
    {
        $data = [
            'vehicles' => DB::table('vehicles')->where('school_id', auth()->user()->school_id)->get(),
            'classes' => DB::table('classes')->where('school_id', auth()->user()->school_id)->get(),
        ];
        return view('admin.transport.assign.by_class', $data);
    }

    public function create_by_class(Request $request)
    {
        $vehicle = DB::table('vehicles')
            ->where('id', $request->vehicle_id)
            ->first();

        $students = DB::table('enrollments')
            ->where('class_id', $request->class_id)
            ->where('school_id', $vehicle->school_id)
            ->get();

        $driver = DB::table('vehicles')
            ->where('id', $request->vehicle_id)
            ->first();

        if ($students->count() == 0) {
            return redirect()->back()->with('error', 'No students in selected class.');
        }

        $skip = count($students);

        if (count($students) < $vehicle->seat) {
            foreach ($students as $student) {
                $check = DB::table('assigned_students')
                    ->where('user_id', $student->user_id)
                    ->where('class_id', $request->class_id)
                    ->first();

                // if any student assigned then skip that student
                if ($check) {
                    $skip--;
                    continue;
                } else {

                    $data = [
                        'school_id' => $vehicle->school_id,
                        'vehicle_id' => $request->vehicle_id,
                        'class_id' => $request->class_id,
                        'user_id' => $student->user_id,
                        'driver_id' => DB::table('vehicles')
                            ->where('id', $request->vehicle_id)
                            ->value('driver_id'),
                    ];

                    DB::table('assigned_students')->insert($data);
                }
            }

            if ($skip == 0) {
                return redirect()->back()->with('error', 'Selected class exists');
            }
            return redirect()->back()->with('message', 'Student assigned');
        }
        return redirect()->back()->with('error', 'Selected vehicle full');
    }

    public function assign_student_remove($id)
    {
        DB::table('assigned_students')->where('id', $id)->delete();
        return redirect()->back()->with('message', 'Student removed');
    }

    /*--------------------------------------------------------------------------------------------------*/
    // driver panel->trip menu
    /*--------------------------------------------------------------------------------------------------*/
    public function routeByVehicle($id)
    {
        $vehicle = DB::table('vehicles')->where('id', $id)->first();
        echo $vehicle->route;
    }

    public function trip_list()
    {
        $query = DB::table('trips')
            ->join('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
            ->select('trips.*', 'vehicles.driver_id')
            ->where('vehicles.driver_id', auth()->user()->id)
            ->where('trips.active', 1);

        $trip = $trip_id = $vehicle_id = 0;
        if ($query->exists()) {
            $trip_id = $query->value('id');
            $trip = 1;
            $vehicle_id = $query->value('vehicle_id');
        }

        $trip_list = DB::table('trips')
            ->join('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
            ->join('users', 'vehicles.driver_id', '=', 'users.id')
            ->select('trips.*', 'vehicles.driver_id', 'vehicles.vehicle_number', 'vehicles.vehicle_model', 'vehicles.route', 'users.name', 'users.user_information')
            ->where('trips.active', 0)
            ->where('vehicles.driver_id', auth()->user()->id)
            ->orderBy('trips.id', 'DESC')->paginate(10);

        $data['trip'] = $trip;
        $data['trip_id'] = $trip_id;
        $data['trip_list'] = $trip_list;
        $data['vehicle_id'] = $vehicle_id;
        $data['vehicles'] = DB::table('vehicles')->where('driver_id', auth()->user()->id)->get();

        $user_timezone = get_settings('timezone');
        date_default_timezone_set($user_timezone);

        return view('driver.trips.list', $data);
    }

    public function trip_create(Request $request)
    {
        $old_trip = DB::table('trips')
            ->join('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
            ->select('trips.*', 'vehicles.driver_id')
            ->where('vehicles.driver_id', auth()->user()->id)
            ->where('trips.active', 1)->exists();

        if ($old_trip) {
            return redirect()->back()->with('warning', "Ops! You already have a trip.");
        } else {
            $students = DB::table('assigned_students')
                ->where('vehicle_id', $request->vehicle_number)
                ->get();

            if ($students->count() < 1) {
                return redirect()->back()->with('warning', 'Selected vehicle is empty.');
            } else {
                $data = [
                    'school_id' => auth()->user()->school_id,
                    'vehicle_id' => $request->vehicle_number,
                    'start_time' => strtotime('now'),
                    'active' => 1,
                ];
                DB::table('trips')->insert($data);
                return redirect()->back()->with('message', 'New Trip Added.');
            }
        }
    }

    public function trip_delete($id)
    {
        DB::table('trips')->where('id', $id)->delete();
        return redirect()->back()->with('message', 'Trip canceled.');
    }

    public function trip_end(Request $request, $id)
    {
        DB::table('trips')->where('id', $id)->update([
            'end_time' => strtotime('now'),
            'active' => 0,
        ]);
        return redirect()->back()->with('message', 'Trip complete.');
    }

    public function update_location(Request $request)
    {
        $location = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        $location = json_encode($location);

        if ($request->track == 'once') {
            DB::table('trips')->where('id', $request->trip_id)->update(['start_from' => $location]);
        } else {
            DB::table('trips')->where('id', $request->trip_id)->update(['update_location' => $location]);
        }
        return response()->json(['status' => 'success']);
    }

    /*--------------------------------------------------------------------------------------------------*/
    // parent panel->trip menu
    /*--------------------------------------------------------------------------------------------------*/
    public function trips_list(Request $request)
    {
        // filter by student
        $active_session = get_school_settings(auth()->user()->school_id)->value('running_session');
        $student_data = User::where('parent_id', auth()->user()->id)
            ->where('school_id', auth()->user()->school_id)->get();
        if (!empty($student_data)) {
            $student_data = $student_data->toArray();
        }

        $filter = $request->filter ?? "";
        $student_id = 0;
        $position = 0;
        $trip_id = 0;

        if ($filter != "") {
            $check_trip = DB::table('trips')
                ->join('assigned_students', 'trips.vehicle_id', '=', 'assigned_students.vehicle_id')
                ->join('users', 'assigned_students.user_id', '=', 'users.id')
                ->select('trips.*', 'assigned_students.user_id', 'users.parent_id')
                ->where('users.parent_id', auth()->user()->id)
                ->where('trips.active', 1)
                ->where('assigned_students.user_id', $request->user_id)
                ->first();

            if ($check_trip != '') {
                $trip_id = $check_trip->id;
                $position = $check_trip->update_location;
            }

            $student_id = $request->user_id;
            if ($position == 0) {
                return redirect()->back()->with('message', 'No trip available.');
            }
        }
        $data = [
            'student_data' => $student_data,
            'filter' => $filter,
            'position' => $position,
            'trip_id' => $trip_id,
            'student_id' => $student_id,
        ];
        return view('parent.trips.list', $data);
    }

    public function get_location(Request $request)
    {
        $record = DB::table('trips')->where('id', $request->trip_id)
            ->where('active', 1)->value('update_location');
        return $record;
    }

}
