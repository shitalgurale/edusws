@extends('teacher.navigation')


<?php

use App\Models\Classes;
use App\Models\Subject;
use App\Models\Section;

?>

@section('content')

<div class="mainSection-title">
    <div class="row">
        <div class="col-12">
            <div
              class="d-flex justify-content-between align-items-center flex-wrap gr-15"
            >
                <div class="d-flex flex-column">
                    <h4>{{ get_phrase('Add New Live Class') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Live class') }}</a></li>
                        <li><a href="#">{{ get_phrase('Add New') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">
            <div class="col-md-6 pb-3">
                <div class="eForm-layouts">
                    <form method="POST" action="{{ route('teacher.live_class_adding') }}" enctype="multipart/form-data" id="live-class-settings-form">
                        @csrf
                        <div class="form-row">

                            <div class="fpb-7">
                                <label for="date" class="eForm-label">
                                    {{ get_phrase('Date') }}
                                </label>
                                <input type="datetime-local" name="date" class="form-control date" id="date" data-toggle="date-picker" data-single-date-picker="true" value="{{ date('Y-m-d', strtotime(date('m/d/Y'))) }}" required>
                            </div>

                            <div class="fpb-7">
                                <label for="class_id" class="eForm-label">
                                    {{ get_phrase('Class') }}
                                </label>
                                <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSection(this.value)">
                                    <option value="">
                                        {{ get_phrase('Select a class') }}
                                    </option>

                                    <?php $classes = Classes::where('school_id', auth()->user()->school_id)->get(); ?>
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
                                <select name="section_id" id="section_id" class="form-select eForm-select eChoice-multiple-with-remove" required >

                                    <option value="" >{{ get_phrase('First select a class') }}</option>

                                </select>
                            </div>

                            <div class="fpb-7">
                                <label for="subject_id" class="eForm-label">
                                    {{ get_phrase('Subject') }}
                                </label>
                                <select name="subject_id" id="subject_id" class="form-select eForm-select eChoice-multiple-with-remove" required>

                                    <option value="">
                                        {{ get_phrase('First select a class') }}
                                    </option>


                                </select>
                            </div>

                            <div class="fpb-7 custom-file-upload">
                                <label for="attachment" class="eForm-label">{{ get_phrase('Upload attachment') }}</label>
                                <input class="form-control eForm-control-file" type="file" id="attachment" name="attachment">
                            </div>

                            <div class="fpb-7">
                                <label for="live_class_url" class="eForm-label">{{ get_phrase('Live Class Url') }}</label>
                                <input class="form-control eForm-control" type="text" id="live_class" name="live_class_url" placeholder="https://" required >
                            </div>

                            <div class="fpb-7">
                                <label for="topic" class="eForm-label">
                                    {{ get_phrase('Topic') }}
                                </label>
                                <textarea class="form-control eForm-control" id="topic" name="topic" rows="5" placeholder="Provide topic" required ></textarea>
                            </div>

                            <div class="fpb-7">
                                <button class="btn-form" type="submit">
                                    {{ get_phrase('Create meeting') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    "use strict";
    function classWiseSection(classId) {
        let url = "{{ route('class_wise_sections', ['id' => ":classId"]) }}";
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
        let url = "{{ route('class_wise_subject', ['id' => ":classId"]) }}";
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

    input:checked + .slider {
      background-color: #2196F3;
    }

    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
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
