@extends('driver.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Assigned Student') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Assigned student') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">
                <div>
                    <p class="mb-3 text-secondary">Search by vehicle</p>
                </div>


                <form method="POST" action="{{ route('assigned.student.list') }}" class="d-flex gap-3">
                    @csrf
                    <div class="fpb-7 min-w-250px">
                        <select name="vehicle_number" id="vehicle_number"
                            class="form-select eForm-select eChoice-multiple-with-remove" required>
                            <option value="">{{ get_phrase('Select a vehicle') }}</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- show student button --}}
                    <div class="fpb-7 min-w-100px">
                        <button class="btn-form" type="submit">{{ get_phrase('Search') }}</button>
                    </div>
                </form>
            </div>


        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="eSection-wrap-2">
                @if (count($assigned_students) > 0)
                    <div class="table-responsive">
                        <table class="table eTable eTable-2">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ get_phrase('Name') }}</th>
                                    <th scope="col">{{ get_phrase('Email') }}</th>
                                    <th scope="col">{{ get_phrase('Phone') }}</th>
                                    <th scope="col">{{ get_phrase('Class') }}</th>
                                    <th scope="col">{{ get_phrase('Vehicle No') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($assigned_students as $key => $student)
                                    {{-- decode all json data --}}
                                    @php
                                        
                                        $user = DB::table('users')
                                            ->where('id', $student->user_id)
                                            ->first();
                                        
                                        $info = json_decode($user->user_information);
                                        $user_image = $info->photo;
                                        if (!empty($info->photo)) {
                                            $user_image = 'uploads/user-images/' . $info->photo;
                                        } else {
                                            $user_image = 'uploads/user-images/thumbnail.png';
                                        }
                                    @endphp

                                    <tr>
                                        {{-- serial no --}}
                                        <th>
                                            <p class="row-number">{{ $assigned_students->firstItem() + $key }}</p>
                                        </th>

                                        {{-- student photo and name --}}
                                        <td>
                                            <div class="dAdmin_profile d-flex align-items-center">
                                                <div class="dAdmin_profile_img">
                                                    <img class="img-fluid" width="50" height="50"
                                                        src="{{ asset('assets') }}/{{ $user_image }}" />
                                                </div>
                                                <div class="dAdmin_profile_name">
                                                    <h4>{{ $user->name }}</h4>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- student email --}}
                                        <td>
                                            {{ $user->email }}
                                        </td>

                                        {{-- student phone --}}
                                        <td>
                                            {{ $info->phone }}
                                        </td>

                                        {{-- student class --}}
                                        <td>
                                            @php
                                                $class = DB::table('classes')
                                                    ->where('id', $student->class_id)
                                                    ->first();
                                            @endphp
                                            {{ $class->name }}
                                        </td>

                                        {{-- vehicle numnber --}}
                                        <td class="min-w-150px">
                                            @php
                                                $vehicle = DB::table('vehicles')
                                                    ->where('id', $student->vehicle_id)
                                                    ->first();
                                            @endphp
                                            {{ $vehicle->vehicle_model }},
                                            {{ $vehicle->vehicle_number }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- pagination --}}
                        <div
                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' 1 - ' . count($assigned_students) . ' ' . get_phrase('from') . ' ' . $assigned_students->total() . ' ' . get_phrase('data') }}
                            </p>
                            <div class="admin-pagi">
                                {!! $assigned_students->appends(request()->all())->links() !!}
                            </div>
                        </div>
                    </div>
                @else
                    {{-- if there is no data show error image --}}
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                        <span class="">{{ get_phrase('No data found') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
