@extends('driver.navigation')

@section('content')
    {{-- breadcrum --}}
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Trips') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Trips') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2 mb-2">

                @if ($trip == 0)
                    <div class="create-trip">
                        <p class="mb-2">Create Trips</p>

                        {{-- create new trip form --}}
                        <form method="POST" action="{{ route('driver.trips.create') }}" class="d-flex gap-3">
                            @csrf
                            {{-- vehicle number --}}
                            <div class="fpb-7 min-w-250px">
                                <select name="vehicle_number" id="vehicle_number"
                                    class="form-select eForm-select eChoice-multiple-with-remove" required
                                    onchange="routeByVehicle(this.value)">
                                    <option value="">{{ get_phrase('Select a vehicle') }}</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_number }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- start journey --}}
                            <div class="fpb-7 min-w-250px">
                                <input type="text" class="form-control eForm-control" id="start_journey"
                                    name="start_journey" readonly>
                            </div>

                            {{-- start trip button --}}
                            <div class="fpb-7 min-w-100px">
                                <button class="btn-form" type="submit" name="submit"
                                    value="create_trip">{{ get_phrase('Start Trip') }}</button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="row">
                        {{-- map --}}
                        <div class="col-md-9">
                            <div id="map" style="height: 350px; width: 100%; border-radius: 5px;">
                            </div>
                        </div>


                        <div class="col-md-3 col-sm-12 mt-3 mt-md-0">
                            {{-- started trip head section --}}
                            <div class="mb-2 d-flex justify-content-between">
                                <p>Ongoing Trip</p>

                                {{-- delete button --}}
                                <a href="{{ route('driver.trip.delete', $trip_id) }}" class="d-inline-block">
                                    <span class="d-flex justify-content-center align-items-center rounded-circle bg-danger"
                                        style="width:30px; height:30px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="15px"
                                            height="15px">
                                            <path
                                                d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z" />
                                        </svg>
                                    </span>
                                </a>
                            </div>

                            @php
                                $active_trip = DB::table('trips')
                                    ->join('vehicles', 'trips.vehicle_id', '=', 'vehicles.id')
                                    ->select('trips.*', 'vehicles.vehicle_number', 'vehicles.route')
                                    ->where('trips.id', $trip_id)
                                    ->first();
                            @endphp


                            {{-- ongoing trips info --}}
                            <div class="ongoing-trip">
                                <form action="{{ route('driver.trips.end', $active_trip->id) }}" method="post">
                                    @csrf
                                    {{-- vehicle_id --}}
                                    <input type="hidden" name="vehicle_id" value="{{ $active_trip->vehicle_id }}">

                                    <div class="row g-3">

                                        {{-- vehicle number --}}
                                        <div class="col-md-12 col-sm-4 mt-2">
                                            <div>
                                                <span class="text-12px ps-1">Vh No:</span>
                                                <input type="text" class="form-control eForm-control" id="vehicle_number"
                                                    name="vehicle_number" value="{{ $active_trip->vehicle_number }}"
                                                    required readonly>
                                            </div>
                                        </div>

                                        {{-- route --}}
                                        <div class="col-md-12 col-sm-4 mt-2">
                                            <div>
                                                <span class="text-12px ps-1">Route:</span>
                                                <input type="text" class="form-control eForm-control" id="route"
                                                    name="route" value="{{ $active_trip->route }}" required readonly>
                                            </div>
                                        </div>

                                        {{-- journey starting time --}}
                                        <div class="col-md-12 col-sm-4 mt-2">
                                            <div>
                                                <span class="text-12px ps-1">Start Time:</span>
                                                <input type="text" class="form-control eForm-control" id="started_at"
                                                    name="start_time" value="{{ date('H:i:s', $active_trip->start_time) }}"
                                                    required readonly>
                                            </div>
                                        </div>

                                        {{-- end trip button --}}
                                        <div class="col-12">
                                            <div class="fpb-7 d-flex">
                                                <button class="btn-form flex-grow-1"
                                                    type="submit">{{ get_phrase('End Trip') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>


    {{-- trip list --}}
    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2 mb-2">
                @if (count($trip_list) > 0)
                    <div class="table-responsive driver_list" id="driver_list">
                        <table class="table eTable eTable-2">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">{{ get_phrase('Vehicle Info') }}</th>
                                    <th scope="col">{{ get_phrase('Driver Info') }}</th>
                                    <th scope="col">{{ get_phrase('Route') }}</th>
                                    <th scope="col">{{ get_phrase('Time') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($trip_list as $key => $single_trip)
                                    <tr>
                                        @php
                                            $info = json_decode($single_trip->user_information);
                                        @endphp

                                        <th scope="row">
                                            <p class="row-number">{{ $trip_list->firstItem() + $key }}</p>
                                        </th>
                                        <td>
                                            <span>{{ $single_trip->vehicle_number }}</span><br>
                                            <span>{{ $single_trip->vehicle_model }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $single_trip->name }}</span><br>
                                            <span>{{ $info->phone }}</span>
                                        </td>

                                        <td>{{ $single_trip->route }}</td>

                                        <td class="min-w-200px">
                                            <span>Start: {{ date('H:i:s', $single_trip->start_time) }}</span><br>
                                            <span>End: {{ date('H:i:s', $single_trip->end_time) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div
                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' 1 - ' . count($trip_list) . ' ' . get_phrase('from') . ' ' . $trip_list->total() . ' ' . get_phrase('data') }}
                            </p>
                            <div class="admin-pagi">
                                {!! $trip_list->appends(request()->all())->links() !!}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                        <span class="">{{ get_phrase('No data found') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script type="text/javascript">
        "use strict";
        // get route when select a vehicle
        function routeByVehicle(classId) {
            let url = "{{ route('driver.vehicle.route', ['id' => ':classId']) }}";
            url = url.replace(":classId", classId);
            $.ajax({
                url: url,
                success: function(response) {
                    $('#start_journey').val(response);
                }
            });
        }

        $(document).ready(function() {
            $(".eChoice-multiple-with-remove").select2();
        });
    </script>

    <script>
        var trip = "{{ $trip }}";
        trip = Number(trip);

        var trip_id = "{{ $trip_id }}";
        trip_id = Number(trip_id);
        var track = 'once';

        if (trip > 0) {
            var map = L.map('map');
            map.setView([51.505, -0.09], 13);

            let marker, circle, zoomed;
            navigator.geolocation.watchPosition(success, error);

            function success(pos) {
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                // update driver location in database trip table
                $.ajax({
                    type: "post",
                    url: "{{ route('update.location') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        latitude: lat,
                        longitude: lng,
                        trip_id: trip_id,
                        track: track,
                    },
                });

                // now start tracking update location
                track = 'twice';
                if (marker) {
                    map.removeLayer(marker);
                    map.removeLayer(circle);
                }

                // Removes any existing marker and circule (new ones about to be set)
                marker = L.marker([lat, lng], {
                    opacity: 1,
                }).addTo(map);
                circle = L.circle([lat, lng], {
                    radius: accuracy
                }).addTo(map);
                if (!zoomed) {
                    zoomed = map.fitBounds(circle.getBounds());
                }
                map.setView([lat, lng]);
            }

            function error(err) {
                if (err.code === 1) {
                    alert("Please allow geolocation access");
                } else {
                    // alert("Cannot get current location");
                }
            };
        }
    </script>

@endsection
