@extends('admin.navigation')
   
@section('content')
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Edit course') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Online Course') }}</a></li>
              <li><a href="#">{{ get_phrase('Edit course') }}</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
	<div class="col-12">
		<div class="eSection-wrap-2">
			<div class="col-md-12 pb-3">
				<div class="d-flex justify-content-between align-items-center">
      		<p class="column-title">{{ get_phrase('COURSE EDITING FORM') }}</p>
      		<a href="{{ route('admin.addons.courses') }}" class="back-listing cg-10"><i class="bi bi-arrow-left"></i> {{ get_phrase('Go Back') }}</a>
      	</div>
			</div>
			<form class="required-form" action="{{ route('admin.addons.course_update',['id' => $course->id]) }}" method="post" enctype="multipart/form-data">
				@csrf
				<ul class="nav nav-tabs eNav-Tabs-custom" id="myTab" role="tablist">
					<li class="nav-item" role="presentation">
						<button
              class="nav-link active"
              id="cCurriculum-tab"
              data-bs-toggle="tab"
              data-bs-target="#cCurriculum"
              type="button"
              role="tab"
              aria-controls="cCurriculum"
              aria-selected="true"
            >
              {{ get_phrase('Curriculum') }}
              <span></span>
            </button>
					</li>
					<li class="nav-item" role="presentation">
						<button
              class="nav-link"
              id="cBasic-tab"
              data-bs-toggle="tab"
              data-bs-target="#cBasic"
              type="button"
              role="tab"
              aria-controls="cBasic"
              aria-selected="true"
            >
              {{ get_phrase('Basic') }}
              <span></span>
            </button>
					</li>
					<li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="cAcademic-tab"
              data-bs-toggle="tab"
              data-bs-target="#cAcademic"
              type="button"
              role="tab"
              aria-controls="cAcademic"
              aria-selected="false"
            >
              {{ get_phrase('Academic') }}
              <span></span>
            </button>
		       </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="cOutcomes-tab"
              data-bs-toggle="tab"
              data-bs-target="#cOutcomes"
              type="button"
              role="tab"
              aria-controls="cOutcomes"
              aria-selected="false"
            >
              {{ get_phrase('Outcomes') }}
              <span></span>
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="cMedia-tab"
              data-bs-toggle="tab"
              data-bs-target="#cMedia"
              type="button"
              role="tab"
              aria-controls="cMedia"
              aria-selected="false"
            >
              {{ get_phrase('Media') }}
              <span></span>
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button
              class="nav-link"
              id="cFinish-tab"
              data-bs-toggle="tab"
              data-bs-target="#cFinish"
              type="button"
              role="tab"
              aria-controls="cFinish"
              aria-selected="false"
            >
              {{ get_phrase('Finish') }}
              <span></span>
            </button>
          </li>
				</ul>
				<div class="tab-content eNav-Tabs-content" id="myTabContent">

					<div class="tab-pane fade show active" id="cCurriculum" role="tabpanel" aria-labelledby="cCurriculum-tab">
						@include('admin.course.curriculum')
					</div>

					<div class="tab-pane fade" id="cBasic" role="tabpanel" aria-labelledby="cBasic-tab">
          	<div class="row justify-content-center">
          		<div class="col-xl-7">
          			<div class="row fmb-14 justify-content-between align-items-center">
          				<label for="title" class="col-sm-2 col-eForm-label">{{ get_phrase('Course title') }}*</label>
          				<div class="col-sm-10 col-md-9 col-lg-10">
          					<input type="text" placeholder="Enter course title" class="form-control eForm-control" id="title" name="title" value="{{ $course->title }}" required/>
          				</div>
          			</div>
          			<div class="row fmb-14 justify-content-between align-items-center">
          				<label for="description" class="col-sm-2 col-eForm-label">{{ get_phrase('Description') }}</label>
          				<div class="col-sm-10 col-md-9 col-lg-10">
          					<textarea class="form-control eForm-control" id="description" name="description"row="5">{{ $course->description }}</textarea>
          				</div>
          			</div>
          		</div>
          	</div>
          </div>

          <div class="tab-pane fade" id="cAcademic" role="tabpanel" aria-labelledby="cAcademic-tab">
           	<div class="row justify-content-center pt-2">
                <div class="col-xl-7">
	                <div class="row fmb-14 justify-content-between align-items-center">
	                	<label for="class_id" class="col-sm-2 col-eForm-label">{{ get_phrase('Class') }}*</label>
	                	<div class="col-sm-10 col-md-9 col-lg-10">
	                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSubject(this.value)">
	                            <option value="">{{ get_phrase('Select a class') }}</option>
	                            @foreach($classes as $class)
	                                <option value="{{ $class->id }}" @php if($class->id == $course->class_id) echo 'selected'; @endphp>{{ $class->name }}</option>
	                            @endforeach
	                        </select>
	                    </div>
	                </div>
	                <div class="row fmb-14 justify-content-between align-items-center">
	                	<label for="subject_id" class="col-sm-2 col-eForm-label">{{ get_phrase('Subject') }}</label>
	                	<div class="col-sm-10 col-md-9 col-lg-10">
	                        <select name="subject_id" id="subject_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
	                            <option value="">{{ get_phrase('First select a class') }}</option>
	                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" @php if($subject->id == $course->subject_id) echo 'selected'; @endphp>{{ $subject->name }}</option>
                              @endforeach
	                        </select>
                        </div>
	                </div>
	                <div class="row fmb-14 justify-content-between align-items-center">
                  		<label for="description" class="col-sm-2 col-eForm-label"
                        >{{ get_phrase('Instructor') }}*</label>
	                    <div class="col-sm-10 col-md-9 col-lg-10">
							<select name="user_id" id="user_id" class="form-select eForm-select eChoice-multiple-with-remove" required>
								<option value="">{{ get_phrase('Select a teacher') }}</option>
								@foreach($teachers as $teacher)
									<option value="{{ $teacher->id }}" @php if($teacher->id == $course->user_id) echo 'selected'; @endphp>{{ $teacher->name }}</option>
								@endforeach
							</select>
	                    </div>
	                </div>
           		</div>
           	</div>
          </div>


          <div class="tab-pane fade" id="cOutcomes" role="tabpanel" aria-labelledby="cOutcomes-tab">
          	<div class="row justify-content-center">
          		<div class="col-xl-7">
								<div class="row fmb-14 justify-content-between align-items-center">
									<label for="outcomes_desc" class="col-sm-2 col-eForm-label">{{ get_phrase('Outcomes') }}</label>
									<div class="col-sm-10 col-md-9 col-lg-10">
										<textarea class="form-control eForm-control" id="outcomes" name="outcomes" row="5">{{ $course->outcomes }}</textarea>
									</div>
								</div>
              </div>
          	</div>
          </div>


          <div class="tab-pane fade" id="cMedia" role="tabpanel" aria-labelledby="cMedia-tab">
          	<div class="row justify-content-center">
          		<div class="col-xl-7">
          			<div class="row fmb-14 justify-content-between align-items-center">
          				<label for="course_overview_provider" class="col-sm-2 col-eForm-label">{{ get_phrase('Course overview provider') }}</label>
          				<div class="col-sm-10 col-md-9 col-lg-10">
          					<select class="form-select eForm-select eChoice-multiple-with-remove" name="course_overview_provider" id="course_overview_provider">
          						<option value="youtube">{{ get_phrase('Youtube') }}</option>
          						<option value="vimeo">{{ get_phrase('Vimeo') }}</option>
          						<option value="html5">{{ get_phrase('HTML5') }}</option>
          					</select>
          				</div>
          			</div>
          			<div class="row fmb-14 justify-content-between align-items-center">
          				<label for="course_overview_url" class="col-sm-2 col-eForm-label">{{ get_phrase('Course overview url') }}</label>
          				<div class="col-sm-10 col-md-9 col-lg-10">
          					<input type="text" placeholder="E.g: https://www.youtube.com/watch?v=oBtf8Yglw2w" class="form-control eForm-control" name="course_overview_url" id="course_overview_url" value="{{ $course->course_overview_url }}" required />
          				</div>
          			</div>
          			<div class="row fmb-14">
          				<label for="course_thumbnail" class="col-sm-2 col-eForm-label">{{ get_phrase('Course thumbnail') }}</label>
          				<div class="eCard d-block text-center bg-light col-sm-10 col-md-9 col-lg-6 thumbnail-margin">
          					@if(file_exists( public_path().'/assets/uploads/course_thumbnails/'.$course->thumbnail ) && is_file(public_path().'/assets/uploads/course_thumbnails/'.$course->thumbnail))
          						<img src="{{ asset('public/assets/uploads/course_thumbnails/'.$course->thumbnail) }}" class="pt-2" width="290px" height="290px"  alt="...">
          					@else
          						<img src="{{ asset('public/assets/uploads/course_thumbnails/course-thumbnail.png') }}" class="pt-2" width="290px" height="290px"  alt="...">
          					@endif
          					<div class="eCard-body">
          						<input class="form-control eForm-control-file" id="formFileSm" type="file" name="thumbnail">
          						<input class="form-control eForm-control-file" value="{{$course->thumbnail}}" id="formFileSm" type="hidden" name="old_thumbnail">
          					</div>
          				</div>
          			</div>
          		</div>
          	</div>
          </div>


          <div class="tab-pane fade" id="cFinish" role="tabpanel" aria-labelledby="cFinish-tab">
          	<div class="row">
          		<div class="col-12">
          			<div class="text-center">
          				<h4 class="mt-0"><i class="bi bi-check-all"></i></h4>
          				<h4 class="mt-0"><?php echo get_phrase("Thank you"); ?> !</h4>
          				<span class="w-75 mb-2 mx-auto"><?php echo get_phrase('You are just one click away'); ?></span>
          				<div class="mb-3 mt-3">
          					<button type="submit" class="eBtn eBtn-blue text-center" onclick="checkRequiredFields()"><?php echo get_phrase('Submit'); ?></button>
          				</div>
          			</div>
          		</div>
          	</div>
          </div>
				</div>
      </form>	
		</div>
	<div>			
</div>

<script type="text/javascript">
	
	"use strict";

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

@endsection