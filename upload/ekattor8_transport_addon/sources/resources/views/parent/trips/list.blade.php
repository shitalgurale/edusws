@extends('parent.navigation')

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


    <div class="eSection-wrap-2 mb-2">
        <div class="row justify-content-center">
            <div class="col-4 col-md-6 col-lg-4">
                <form action="{{ route('parent.trips.list') }}" method="post">
                    @csrf
                    <div class="d-flex gap-2">
                        <select name="user_id" id="user_id" class="form-select eForm-select eChoice-multiple-with-remove"
                            required>

                            @php
                                $name = DB::table('users')
                                    ->where('id', $student_id)
                                    ->value('name');
                            @endphp

                            @if ($student_id > 0)
                                <option value="{{ $student_id }}">{{ $name }}</option>
                            @else
                                <option value="">{{ get_phrase('Select a student') }}</option>
                            @endif
                            @foreach ($student_data as $key => $details)
                                <option value="{{ $details['id'] }}">{{ $details['name'] }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="eBtn btn-secondary px-5" name="filter"
                            value="fitler">{{ get_phrase('Filter') }}</button>
                    </div>
                </form>
            </div>
        </div>


        @if ($position != '')
            <div class="mt-4" id="map" style="height: 350px; width: 100%;"></div>
        @else
            <div class="empty_box center mt-3">
                <img class="mb-3" width="150px" src="{{ asset('assets/images/empty_box.png') }}" />
                <br>
                <span class="">{{ get_phrase('No data found') }}</span>
            </div>
        @endif
    </div>

    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script type="text/javascript">
        "use strict";

        var trip_id = "{{ $trip_id }}";
        trip_id = Number(trip_id); // convert string to int value

        if (trip_id > 0) {
            var map = L.map('map').setView([51.505, -0.09], 13);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            var lat = 0;
            var lng = 0;

            var oldLat = 0;
            var oldLng = 0;

            var update = setInterval(locate, 3000);

            function locate() {
                $.ajax({
                    type: "post",
                    url: "{{ route('get.location') }}",
                    data: {
                        trip_id: trip_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {

                        var marker, circle;
                        let zoomed;

                        if (response == "") {
                            clearInterval(update);
                            window.location.reload(true);
                        } else {
                            var position = response;
                            var location = JSON.parse(position);
                            lat = location.latitude;
                            lng = location.longitude;

                            /*
                            | if new positon is same as previous
                            | don't execute next codes bellow
                            */
                            if (oldLat != lat) {
                                // console.log(lat + '......' + lng);

                                // Removes any existing marker and circule
                                if (marker) {
                                    map.removeLayer(marker);
                                    map.removeLayer(circle);
                                }

                                // add marker and circle
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
                            } else {
                                if (marker) {
                                    map.removeLayer(marker);
                                    map.removeLayer(circle);
                                }
                            }

                            // store previous position
                            oldLat = lat;
                            oldLng = lng;
                        }
                    }
                });
            }
        }
    </script>
@endsection
