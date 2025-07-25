@extends('student.navigation')

<?php
$index=1;
$mindex=1;
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
                    <h4>{{ get_phrase('Your Live classes') }}</h4>
                    <ul class="d-flex align-items-center eBreadcrumb-2">
                        <li><a href="#">{{ get_phrase('Home') }}</a></li>
                        <li><a href="#">{{ get_phrase('Live class') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="eSection-wrap">


            <ul class="nav nav-tabs eNav-Tabs-custom"id="myTab"role="tablist" >

                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="upcoming-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#upcomingtable"
                    type="button"
                    role="tab"
                    aria-controls="upcomingtable"
                    aria-selected="false"
                  >
                  {{ get_phrase('Upcoming ') }}<p class="badge bg-success ">
                    {{ count($upcoming_classes) }}
                </p>
                    <span></span>
                  </button>
                </li>


                <li class="nav-item" role="presentation">
                    <button
                      class="nav-link"
                      id="archive-tab"
                      data-bs-toggle="tab"
                      data-bs-target="#archivetable"
                      type="button"
                      role="tab"
                      aria-controls="archivetable"
                      aria-selected="false"
                    >
                    {{ get_phrase('Archive  ') }}<p class="badge bg-danger ">
                      {{ count($archive_classes) }}
                  </p>
                      <span></span>
                    </button>
                  </li>


              </ul>


              <div class="tab-content pb-2" id="nav-tabContent">
                <div class="tab-pane fade show active " id="upcomingtable" role="tabpanel" aria-labelledby="upcoming-tab">

                    <div class="eForm-layouts">
                        @if(count($upcoming_classes) > 0)
                        <table class="table eTable eTable-2">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ get_phrase('Schedule') }}</th>
                                <th scope="col">{{ get_phrase('Class') }}</th>
                                <th scope="col">{{ get_phrase('Section') }}</th>
                                <th scope="col">{{ get_phrase('Subject') }}</th>
                                <th scope="col">{{ get_phrase('Option') }}</th>


                            </thead>
                            <tbody>
                                @foreach($upcoming_classes as $live_class)
                                <tr>
                                    <td>
                                        {{ $index++ }}
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <p><span>{{ get_phrase("Date : ") }} </span>{{ date('D, d-M-Y', $live_class->date) }}</p>
                                            <p><span>{{ get_phrase("Time : ") }}</span>{{ date('h:i A', $live_class->date) }}</p>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <?php $name = Classes::find($live_class->class_id); ?>
                                            <p>{{ $name['name'] }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <?php $name = Section::find($live_class->section_id); ?>
                                            <p>{{ $name['name'] }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <?php $name = Subject::find($live_class->subject_id); ?>
                                            <p>{{ $name['name'] }}</p>

                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ $live_class['live_class_url'] }}" target="blank" type="button" class="export_btn float-end m-1"> <i class="bi bi-play-circle"></i> {{ get_phrase('Join') }}</a>
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <div class="empty_box center">
                                <img class="mb-3" width="167px" src="{{ asset('assets/images/empty_box.png') }}" />
                                <br>
                                <span class="">{{ get_phrase('No data found') }}</span>
                            </div>
                        @endif


                    </div>

                </div>


                <div class="tab-pane fade  " id="archivetable" role="tabpanel" aria-labelledby="archive-tab">

                    <div class="eForm-layouts">
                        @if(count($archive_classes) > 0)
                        <table class="table eTable eTable-2">
                            <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">{{ get_phrase('Schedule') }}</th>
                                <th scope="col">{{ get_phrase('Class') }}</th>
                                <th scope="col">{{ get_phrase('Section') }}</th>
                                <th scope="col">{{ get_phrase('Subject') }}</th>
                              
                            </thead>
                            <tbody>
                                @foreach($archive_classes as $live_class)
                                <tr>
                                    <td>
                                        {{ $mindex++ }}
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <p><span>{{ get_phrase("Date : ") }} </span>{{ date('D, d-M-Y', $live_class->date) }}</p>
                                            <p><span>{{ get_phrase("Time : ") }}</span>{{ date('h:i A', $live_class->date) }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <?php $name = Classes::find($live_class->class_id); ?>
                                            <p>{{ $name['name'] }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <?php $name = Section::find($live_class->section_id); ?>
                                            <p>{{ $name['name'] }}</p>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dAdmin_info_name min-w-full">
                                            <?php $name = Subject::find($live_class->subject_id); ?>
                                            <p>{{ $name['name'] }}</p>

                                          </div>
                                    </td>   
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                            <div class="empty_box center">
                                <img class="mb-3" width="167px" src="{{ asset('assets/images/empty_box.png') }}" />
                                <br>
                                <span class="">{{ get_phrase('No data found') }}</span>
                            </div>
                        @endif

                    </div>

                </div>

              </div>




        </div>
    </div>
</div>
@endsection


