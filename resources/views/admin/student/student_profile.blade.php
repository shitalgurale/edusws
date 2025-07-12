<style>
    .profile_img {
        display: flex;
        justify-content: center;
    }

    .student_simg {
        display: flex;
        justify-content: center;
    }

    .name_title h4 {
        font-size: 14px;
        font-weight: 500;
    }

    .text {
        border-top: 1px solid #817e7e21;
    }

    .text h4 {
        border-bottom: 1px solid #817e7e21;
        padding-bottom: 7px;
        padding-top: 5px;
        font-size: 14px;
        font-weight: 400;
    }

    .text h4:last-child {
        border-bottom: none;
    }
</style>

<div class="row">
    <div class="col-12">
        <div class="school_name">
            <h2 class="text-center">{{ DB::table('schools')->where('id', auth()->user()->school_id)->value('title') }}</h2>
        </div>
    </div>
</div>

<section class="profile">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="profile_img">
                    <div class="test_div">
                        <div class="student_simg">
                            @if(!empty($student_details->photo))
                                <img src="{{ $student_details->photo }}" class="rounded-circle div-sc-five">
                            @else
                                <img src="{{ asset('assets/uploads/default.png') }}" class="rounded-circle div-sc-five">
                            @endif
                        </div>
                        <div class="name_title mt-3 text-center">
                            <h4>{{ get_phrase('Name') }} : {{ $student_details->name ?? '-' }}</h4>
                            <h4>{{ get_phrase('Email') }} : {{ null_checker($student_details->email ?? '') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <ul class="nav nav-pills eNav-Tabs-justify" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-jHome-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-jHome" type="button" role="tab" aria-controls="pills-jHome"
                            aria-selected="true">
                            {{ get_phrase('Student Info') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-jProfile-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-jProfile" type="button" role="tab"
                            aria-controls="pills-jProfile" aria-selected="false">
                            {{ get_phrase('Change Password') }}
                        </button>
                    </li>
                    
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-admission-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-admission" type="button" role="tab"
                            aria-controls="pills-admission" aria-selected="false">
                            {{ get_phrase('Admission Details') }}
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-additional-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-additional" type="button" role="tab"
                            aria-controls="pills-additional" aria-selected="false">
                            {{ get_phrase('More Additional Information') }}
                        </button>
                    </li>
                </ul>

                <div class="tab-content eNav-Tabs-content" id="pills-tabContent">
                    {{-- Tab 1 --}}
                    <div class="tab-pane fade show active" id="pills-jHome" role="tabpanel"
                        aria-labelledby="pills-jHome-tab">
                        <div class="text name_title">
                            <h4>{{ get_phrase('Name') }} : {{ $student_details->name ?? '-' }}</h4>
                            <h4>{{ get_phrase('Class') }} : {{ null_checker($student_details->class_name ?? '') }}</h4>
                            <h4>{{ get_phrase('Section') }} : {{ null_checker($student_details->section_name ?? '') }}</h4>
                            <h4>{{ get_phrase('Session') }} : {{ null_checker($student_details->session_title ?? 'N/A') }}</h4>
                            <h4>{{ get_phrase('Parent') }} : {{ null_checker($student_details->parent_name ?? '') }}</h4>
                            <h4>{{ get_phrase('Blood') }} : {{ null_checker(strtoupper($student_details->blood_group ?? '')) }}</h4>
                            <h4>{{ get_phrase('Contact') }} : {{ null_checker($student_details->phone ?? '') }}</h4>
                        </div>
                    </div>

                    {{-- Tab 2 --}}
                    <div class="tab-pane fade" id="pills-jProfile" role="tabpanel" aria-labelledby="pills-jProfile-tab">
                        <form action="{{ route('admin.user_password') }}" method="post">
                            @csrf
                            <div class="fpb-7">
                                <input type="text" class="form-control eForm-control" name="password"
                                    id="password{{ $student_details->user_id ?? '0' }}">
                                <input type="hidden" name="user_id" value="{{ $student_details->user_id ?? '0' }}">
                            </div>

                            <div class="generatePass d-flex">
                                <div class="pt-2">
                                    <button type="button" class="btn-form" style="width: 127px;" aria-expanded="false"
                                        onclick="generatePassword('{{ $student_details->user_id ?? '0' }}')">Generate
                                        Password</button>
                                </div>
                                <div class="ms-3 pt-2">
                                    <button type="submit" class="btn-form float-end">{{ get_phrase('Submit') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
{{-- Tab 3: Admission Details --}}
<div class="tab-pane fade" id="pills-admission" role="tabpanel" aria-labelledby="pills-admission-tab">
    <div class="text name_title">
        @if ($admission_details)

            @php
                $user_info = json_decode($admission_details->user_information, true);
                $birthday_unix = $user_info['birthday'] ?? null;
                $dob_formatted = $birthday_unix ? \Carbon\Carbon::createFromTimestamp($birthday_unix)->format('d-m-Y') : '-';
            @endphp

            <h4>{{ get_phrase('Admission Date') }} : 
                {{ \Carbon\Carbon::parse($admission_details->admission_date)->format('d-m-Y') }}</h4>

            <h4>{{ get_phrase('Date of Birth') }} : {{ $dob_formatted }}</h4>

            <h4>{{ get_phrase('Class') }} : 
                {{ DB::table('classes')->where('id', $admission_details->class_id)->value('name') ?? 'N/A' }}</h4>

            <h4>{{ get_phrase('Section') }} : 
                {{ DB::table('sections')->where('id', $admission_details->section_id)->value('name') ?? 'N/A' }}</h4>

            <h4>{{ get_phrase('Session') }} : 
                {{ DB::table('sessions')->where('id', $admission_details->session_id)->value('session_title') ?? 'N/A' }}</h4>

            <h4>{{ get_phrase('Caste') }} : {{ $admission_details->caste ?? '-' }}</h4>
            <h4>{{ get_phrase('Nationality') }} : {{ $admission_details->nationality ?? '-' }}</h4>
            <h4>{{ get_phrase('Father Name') }} : {{ $admission_details->father_name ?? '-' }}</h4>
            <h4>{{ get_phrase('Mother Name') }} : {{ $admission_details->mother_name ?? '-' }}</h4>

        @else
            <p>No admission details found.</p>
        @endif
    </div>
</div>



                    {{-- Tab 4: More Additional Info --}}
                    <div class="tab-pane fade" id="pills-additional" role="tabpanel" aria-labelledby="pills-additional-tab">
                        <div class="text">
                            <div class="row">
                                <div class="col-lg-6">
                                    @php
                                        $extra_info = json_decode($student_details->student_info ?? '');
                                    @endphp
                                    <ul>
                                        @if(!empty($extra_info) && is_array($extra_info))
                                            @foreach ($extra_info as $key => $info)
                                                <h4>{{ $key + 1 }}. {{ $info }}</h4>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> {{-- End Tab 4 --}}
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function generatePassword(id) {
        var length = 12;
        var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
        var password = "";
        for (var i = 0; i < length; ++i) {
            var randomNumber = Math.floor(Math.random() * charset.length);
            password += charset[randomNumber];
        }
        document.getElementById("password" + id).value = password;
    }
</script>
