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


    {{-- trip create area --}}
    <div class="row">
        <div class="col-12">
            <div class="eSection-wrap-2 mb-2">

                @if ($trip_started != 0)
                    <div class="create-trip">
                        {{-- title --}}
                        <p class="mb-2">Create Trips</p>

                        {{-- create new trip form --}}
                        <form method="POST" action="{{ route('driver.trips.list') }}" class="d-flex gap-3">
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


                        <div class="col-md-3">
                            {{-- started trip head section --}}
                            <div class="mb-2 d-flex justify-content-between">
                                <p>Ongoing Trip</p>

                                {{-- delete button --}}
                                <a href="{{ route('driver.trip.delete', $ongoing_trip_id) }}" class="d-inline-block">
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


                            {{-- ongoing trips --}}
                            <div class="ongoing-trip">
                                <form action="{{ route('driver.trips.end', $ongoing_trip_id) }}" method="post">
                                    @csrf

                                    {{-- vehicle_id --}}
                                    <input type="hidden" name="vehicle_id" value="{{ $vh_id }}">

                                    {{-- vehicle number --}}
                                    <div class="fpb-7 mb-2">
                                        <span class="text-12px ps-1">Vh No:</span>
                                        <input type="text" class="form-control eForm-control" id="vehicle_number"
                                            name="vehicle_number" value="{{ $vh_num }}" required readonly>
                                    </div>


                                    {{-- route --}}
                                    <div class="fpb-7 mb-2">
                                        <span class="text-12px ps-1">Route:</span>
                                        <input type="text" class="form-control eForm-control" id="route"
                                            name="route" value="{{ $vh_route }}" required readonly>
                                    </div>


                                    {{-- journey starting time --}}
                                    <div class="fpb-7 mb-2">
                                        <span class="text-12px ps-1">Start Time:</span>
                                        <input type="text" class="form-control eForm-control" id="started_at"
                                            name="started_at" value="{{ date('H:i:s', $ongoing_trip_time) }}" required
                                            readonly>
                                    </div>


                                    {{-- end trip button --}}
                                    <div class="fpb-7 d-flex">
                                        <button class="btn-form flex-grow-1"
                                            type="submit">{{ get_phrase('End Trip') }}</button>
                                    </div>
                                </form>
                            </div>

                        </div>
                    </div>
                @endif
            </div>

            @if (count($total_trips) > 0)
                <div class="eSection-wrap-2 mb-2">

                    <!-- vehicle Table -->
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
                                @foreach ($total_trips as $key => $trip)
                                    @php
                                        $driver_info = DB::table('users')
                                            ->where('id', auth()->user()->id)
                                            ->first();
                                        
                                        $vehicle_info = DB::table('vehicles')
                                            ->where('id', $trip->vehicle_id)
                                            ->first();
                                        
                                        $info = json_decode($driver_info->user_information);
                                        $time = json_decode($trip->trip_time);
                                    @endphp

                                    <tr>
                                        <th scope="row">
                                            <p class="row-number">{{ $key + 1 }}</p>
                                        </th>


                                        <td>
                                            <div class="dAdmin_info_name">
                                                <p>
                                                    <span>{{ get_phrase('Vh No: ') }}</span>
                                                    {{ $vehicle_info->vehicle_number }}
                                                </p>
                                                <p>
                                                    <span>{{ get_phrase('Ch No: ') }}</span>
                                                    {{ $vehicle_info->chassis_number }}
                                                </p>
                                            </div>
                                        </td>


                                        <td>
                                            <div class="dAdmin_info_name">
                                                <p>
                                                    <span>{{ get_phrase('Driver: ') }}</span>
                                                    {{ $driver_info->name }}
                                                </p>
                                                <p>
                                                    <span>{{ get_phrase('Phone: ') }}</span>
                                                    {{ $info->phone }}
                                                </p>
                                            </div>
                                        </td>

                                        <td>{{ $vehicle_info->route }}</td>

                                        <td>
                                            <div class="dAdmin_info_name min-w-150px">

                                                <p>
                                                    <span>{{ get_phrase('Start: ') }}</span>
                                                    {{ date('H:i:s', $time->started_at) }}
                                                </p>


                                                <p>
                                                    <span>{{ get_phrase('End: ') }}</span>
                                                    {{ date('H:i:s', $time->end_at) }}
                                                </p>
                                            </div>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="eSection-wrap-2 mb-2">

                    {{-- if there is no data show error image --}}
                    <div class="empty_box center">
                        <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                        <br>
                        <span class="">{{ get_phrase('No data found') }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script type="text/javascript">
        "use strict";

        var trackLocation = "<?php echo $trip_started; ?>";
        trackLocation = Number(trackLocation); // convert string to int value

        var trip_id = "<?php echo $ongoing_trip_id; ?>";
        trip_id = Number(trip_id); // convert string to int value

        // when track = once, track (ongoing_trips->'from' column) first location
        // when track = twice, track (ongoing_trips->'update_location' column) update location
        var track = 'once';


        // when any trip started then start tracking
        if (trackLocation > 0) {
            var map = L.map('map');
            map.setView([51.505, -0.09], 13);

            let marker, circle, zoomed;
            navigator.geolocation.watchPosition(success, error);

            function success(pos) {
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy;

                // update driver location in database trip table fields
                console.log(lat + ",......" + lng);

                $.ajax({
                    type: "post",
                    url: "{{ route('update.location') }}",
                    data: {
                        latitude: lat,
                        longitude: lng,
                        id: trip_id,
                        track: track,
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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
                    radius: 700
                }).addTo(map);

                /*
                this is popup
                var popup = marker.bindPopup('Your current location').openPopup();
                popup.addTo(map);
                */

                // Adds marker to the map and a circle for accuracy
                if (!zoomed) {
                    zoomed = map.fitBounds(circle.getBounds());
                }

                // Set zoom to boundaries of accuracy circle
                map.setView([lat, lng]);
                // Set map focus to current user position
            };

            function error(err) {
                if (err.code === 1) {
                    alert("Please allow geolocation access");
                } else {
                    alert("Cannot get current location");
                }
            };
        }


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
@endsection
