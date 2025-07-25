@extends('admin.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Assign Student') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Transport') }}</a></li>
                            <li><a href="#">{{ get_phrase('Assign Student') }}</a></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-3">
                        {{-- assign single student --}}
                        <div class="export-btn-area">
                            <a href="javascript:;" class="export_btn"
                                onclick="rightModal('{{ route('admin.assign.individual') }}', '{{ get_phrase('Assign student') }}')">{{ get_phrase('Individual') }}</a>
                        </div>

                        {{-- assign as class --}}
                        <div class="export-btn-area">
                            <a href="javascript:;" class="export_btn"
                                onclick="rightModal('{{ route('admin.assign.by_class') }}', '{{ get_phrase('Assign by class') }}')">{{ get_phrase('By Class') }}</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2">

                <div class="row">
                    <div class="col-12 d-flex justify-content-center gap-5">

                        {{-- filter area --}}
                        <div class="filter">
                            <form method="GET" class="d-flex align-items-center gap-2"
                                action="{{ route('admin.assign.student.list') }}">
                                <div class="min-w-150px">
                                    <select name="category" id="category"
                                        class="form-select eForm-select eChoice-multiple-with-remove"
                                        onchange="filterCategory(this.value)" required>

                                        @if ($category == 0)
                                            <option value="">{{ get_phrase('Category') }}</option>
                                        @else
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endif
                                        <option value="vehicle">Vehicle</option>
                                        <option value="driver">Driver</option>
                                        <option value="class">Class</option>
                                    </select>
                                </div>

                                <div class="min-w-250px">
                                    <select name="type_id" id="type_id"
                                        class="form-select eForm-select eChoice-multiple-with-remove" required>
                                        @if ($category == 0)
                                            <option value="{{ $filter }}">{{ get_phrase('First select category') }}
                                            @else
                                            <option value="{{ $name }}">{{ $name }}</option>
                                        @endif
                                        </option>
                                    </select>
                                </div>

                                <div>
                                    <button class="btn-form btn-secondary"
                                        type="submit">{{ get_phrase('Filter') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @if (count($assigned_list) > 0)
                    <div class="table-responsive assign_student" id="assign_student">
                        <table class="table eTable eTable-2">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ get_phrase('Vehicle info') }}</th>
                                    <th scope="col">{{ get_phrase('Driver Name') }}</th>
                                    <th scope="col">{{ get_phrase('Student Name') }}</th>
                                    <th scope="col">{{ get_phrase('Class') }}</th>
                                    <th scope="col" class="text-center">{{ get_phrase('Action') }}</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($assigned_list as $key => $list)
                                    <tr>
                                        {{-- serial --}}
                                        <td>{{ $assigned_list->firstItem() + $key }}</td>

                                        {{-- vehicle info --}}
                                        <td>
                                            @php
                                                $vehicle = DB::table('vehicles')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->vehicle_id)
                                                    ->first();
                                            @endphp
                                            <span>{{ $vehicle->vehicle_number }}</span>,
                                            <span>{{ $vehicle->vehicle_model }}</span>
                                        </td>

                                        {{-- driver name --}}
                                        <td>
                                            @php
                                                $driver = DB::table('users')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->driver_id)
                                                    ->first();
                                            @endphp
                                            @if(empty($driver))
                                            <span>Driver Removed</span>
                                            @else
                                            <span>{{ $driver->name }}</span>
                                            @endif
                                        </td>

                                        {{-- student name --}}
                                        <td>
                                            @php
                                                $student = DB::table('users')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->user_id)
                                                    ->first();
                                            @endphp
                                            @if(empty($student))
                                            <span>Student Removed</span>
                                            @else
                                            <span>{{ $student->name }}</span>
                                            @endif
                                            
                                        </td>

                                        {{-- driver name --}}
                                        <td>
                                            @php
                                                $class = DB::table('classes')
                                                    ->where('school_id', auth()->user()->school_id)
                                                    ->where('id', $list->class_id)
                                                    ->first();
                                            @endphp
                                            <span>{{ $class->name }}</span>
                                        </td>

                                        {{-- remove button --}}
                                        <td>
                                            <a class="btn btn-secondary text-12px" href="javascript:;"
                                                onclick="confirmModal('{{ route('assign.student.remove', $list->id) }}', 'undefined');">{{ get_phrase('Remove') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- pagination --}}
                        <div
                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' 1 - ' . count($assigned_list) . ' ' . get_phrase('from') . ' ' . $assigned_list->total() . ' ' . get_phrase('data') }}
                            </p>
                            <div class="admin-pagi">
                                {!! $assigned_list->appends(request()->all())->links() !!}
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


    <script type="text/javascript">
        "use strict";
        $(document).ready(function() {
            $(".eChoice-multiple-with-remove").select2();
        });

        function filterCategory(classId) {
            let url = "{{ route('filter.category', ['type' => ':classId']) }}";
            url = url.replace(":classId", classId);
            $.ajax({
                url: url,
                success: function(response) {
                    $('#type_id').html(response);
                }
            });
        }
    </script>
@endsection
