<?php

use App\Models\Classes;
use App\Models\Subject;
use App\Models\Section;



?>


<form method="POST" action="{{ route('teacher.live_class_update_by_meeting_id') }}" enctype="multipart/form-data" id="live-class-settings-form">
    @csrf
    <div class="form-row">

        <div class="fpb-7">
            <label for="date" class="eForm-label">
                {{ get_phrase('Date') }}
            </label>
            <input type="datetime-local" name="date" class="form-control date" id="date" data-toggle="date-picker" data-single-date-picker="true" value="{{ date('Y-m-d H:i:s', $class_details['date']) }}" required>
        </div>
        <input type="hidden" name="id" value=" {{ $class_details['id'] }}">


        <div class="fpb-7">
            <label for="class_id" class="eForm-label">
                {{ get_phrase('Class') }}
            </label>
            <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onclick="classWiseSection(this.value)">

                <?php $class = Classes::find($class_details['class_id']); ?>

                <?php $classes = Classes::all(); ?>
                @foreach($classes as $class)
                <option value="{{ $class->id }}">
                    {{ $class->name }}
                </option>

                @endforeach
            </select>
        </div>


        <div class="fpb-7">
            <label for="section_id" class="eForm-label">
                {{ get_phrase('Section') }}
            </label>
            <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required>

                <?php $sec = Section::find($class_details['section_id']); ?>

                <option value="{{ $sec->id }}">
                    {{ $sec->name }}
                </option>



            </select>
        </div>

        <div class="fpb-7">
            <label for="subject_id" class="eForm-label">
                {{ get_phrase('Subject') }}
            </label>
            <select name="subject_id" id="subject_id" class="form-select eForm-select eChoice-multiple-with-remove" required>

                <?php $sub = Subject::find($class_details['subject_id']); ?>

                <option value="{{ $sub->id }}">
                    {{ $sub->name }}
                </option>


            </select>
        </div>

        <div class="fpb-7">
            <div class="eForm-label">{{ get_phrase('Enable Waiting') }}</div>
            <label class="switch" class="eForm-label">
                <input  type="checkbox" name="waiting_room" id="waiting_room" value="{{ $class_details['waiting_room'] }}"
                <?php if($class_details['waiting_room']): ?>    checked <?php endif; ?>  >
                <span class="slider round"></span>
            </label>

        </div>

        <div class="fpb-7 custom-file-upload">
            <label for="attachment" class="eForm-label">{{ get_phrase('Upload attachment') }}</label>
            <input class="form-control eForm-control-file" type="file" id="attachment" name="attachment"  value="{{ $class_details['attatchment'] }}">
        </div>

        <div class="fpb-7">
            <label for="topic" class="eForm-label">
                {{ get_phrase('Topic') }}
            </label>
            <textarea class="form-control eForm-control" id="topic" name="topic"  rows="5" placeholder="Provide topic" required>{{ $class_details['topic'] }}</textarea>
        </div>

        <div class="fpb-7">
            <label for="live_class_url" class="eForm-label">{{ get_phrase('Live Class Url') }}</label>
            <input class="form-control eForm-control" type="text" id="live_class" name="live_class_url" placeholder="https://" value="{{ $class_details['live_class_url'] }}" required >
        </div>

        <div class="fpb-7 pt-2">
            <button class="btn-form" type="submit">
                {{ get_phrase('Update Meeting') }}
            </button>
        </div>
    </div>
</form>


<script type="text/javascript">

    "use strict";

    $(document).ready(function () {
      $(".eChoice-multiple-with-remove").select2();
    });

    function classWiseSection(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#section_id').html(response);
                classWiseSubject(classId);

            }
        });
    }

    function classWiseSubject(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ":classId"]) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response){
                $('#subject_id').html(response);
            }
        });
    }
</script>

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>
