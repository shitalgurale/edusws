@extends('teacher.navigation')
   
@section('content')

<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('Add new course') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Online Course') }}</a></li>
              <li><a href="#">{{ get_phrase('Create new course') }}</a></li>
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
      		<p class="column-title">{{ get_phrase('COURSE ADDING FORM') }}</p>
      		<a href="{{ route('teacher.addons.courses') }}" class="back-listing cg-10"><i class="bi bi-arrow-left"></i> {{ get_phrase('Go Back') }}</a>
      	</div>
        <form class="required-form" action="{{ route('teacher.addons.course_create') }}" method="post" enctype="multipart/form-data">
          @csrf
          <ul
            class="nav nav-tabs eNav-Tabs-custom"
            id="myTab"
            role="tablist"
          >
              <li class="nav-item" role="presentation">
                <button
                  class="nav-link active"
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
          <div
            class="tab-content eNav-Tabs-content"
            id="myTabContent"
          >
            <div
              class="tab-pane fade show active"
              id="cBasic"
              role="tabpanel"
              aria-labelledby="cBasic-tab"
            >
            	<div class="row justify-content-center">
                <div class="col-xl-7">
                  <div
                    class="row fmb-14 justify-content-between align-items-center"
                  >
                      <label
                        for="title"
                        class="col-sm-2 col-eForm-label"
                        >{{ get_phrase('Course title') }}*</label
                      >
                      <div class="col-sm-10 col-md-9 col-lg-10">
                        <input
                          type="text"
                          placeholder="Enter course title"
                          class="form-control eForm-control"
                          id="title"
                          name="title"
                          required
                        />
                      </div>
                  </div>
                  <div
                    class="row fmb-14 justify-content-between align-items-center"
                  >
                    <label for="description" class="col-sm-2 col-eForm-label"
                      >{{ get_phrase('Description') }}</label
                    >
                    <div class="col-sm-10 col-md-9 col-lg-10">
                      <textarea
                        class="form-control eForm-control"
                        id="description"
                        name="description"
                        row="5"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="tab-pane fade"
              id="cAcademic"
              role="tabpanel"
              aria-labelledby="cAcademic-tab"
            >
             	<div class="row justify-content-center pt-2">
             		<div class="col-xl-7">
	                <div class="row fmb-14 justify-content-between align-items-center">
	                	<label for="class_id" class="col-sm-2 col-eForm-label">{{ get_phrase('Class') }}*</label>
	                	<div class="col-sm-10 col-md-9 col-lg-10">
	                        <select name="class_id" id="class_id" class="form-select eForm-select eChoice-multiple-with-remove" required onchange="classWiseSubject(this.value)">
	                            <option value="">{{ get_phrase('Select a class') }}</option>
	                            @foreach($classes as $class)
	                                <option value="{{ $class->id }}">{{ $class->name }}</option>
	                            @endforeach
	                        </select>
	                    </div>
	                </div>
	                <div class="row fmb-14 justify-content-between align-items-center">
	                	<label for="subject_id" class="col-sm-2 col-eForm-label">{{ get_phrase('Subject') }}</label>
	                	<div class="col-sm-10 col-md-9 col-lg-10">
	                        <select name="subject_id" id="subject_id" class="form-select eForm-select eChoice-multiple-with-remove" required >
	                            <option value="">{{ get_phrase('First select a class') }}</option>
	                        </select>
                        </div>
	                </div>
             		</div>
             	</div>
            </div>
            <div
              class="tab-pane fade"
              id="cOutcomes"
              role="tabpanel"
              aria-labelledby="cOutcomes-tab"
            >
            	<div class="row justify-content-center">
                <div class="col-xl-7">
                  <div 
                    class="row fmb-14 justify-content-between align-items-center"
                  >
                    <label for="outcomes_desc" class="col-sm-2 col-eForm-label">{{ get_phrase('Outcomes') }}</label>
                    <div class="col-sm-10 col-md-9 col-lg-10">
                      <textarea
                        class="form-control eForm-control"
                        id="outcomes"
                        name="outcomes"
                        row="5"
                      ></textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="tab-pane fade"
              id="cMedia"
              role="tabpanel"
              aria-labelledby="cMedia-tab"
            >
              <div class="row justify-content-center">
                <div class="col-xl-7">
                  <div
                    class="row fmb-14 justify-content-between align-items-center"
                  >
                    <label
                        for="course_overview_provider"
                        class="col-sm-2 col-eForm-label"
                        >{{ get_phrase('Course overview provider') }}</label
                      >
                    <div class="col-sm-10 col-md-9 col-lg-10">
                      <select class="form-select eForm-select eChoice-multiple-with-remove" name="course_overview_provider" id="course_overview_provider">
                        <option value="youtube">{{ get_phrase('Youtube') }}</option>
                        <option value="vimeo">{{ get_phrase('Vimeo') }}</option>
                        <option value="html5">{{ get_phrase('HTML5') }}</option>
                      </select>
                    </div>
                  </div>
                  <div
                    class="row fmb-14 justify-content-between align-items-center"
                  >
                    <label
                      for="course_overview_url"
                      class="col-sm-2 col-eForm-label"
                      >{{ get_phrase('Course overview url') }}</label
                    >
                    <div class="col-sm-10 col-md-9 col-lg-10">
                      <input
                        type="text"
                        placeholder="E.g: https://www.youtube.com/watch?v=oBtf8Yglw2w"
                        class="form-control eForm-control"
                        name="course_overview_url"
                        id="course_overview_url"
                        required
                      />
                    </div>
                  </div>
                  <div
                    class="row fmb-14"
                  >
                    <label for="course_thumbnail" class="col-sm-2 col-eForm-label">{{ get_phrase('Course thumbnail') }}</label>
                    <div class="eCard d-block text-center bg-light col-sm-10 col-md-9 col-lg-6 thumbnail-margin">
                      <img src="{{ asset('public/assets/uploads/course_thumbnails/course-thumbnail.png') }}" class="pt-2" width="290px" height="290px"
                          alt="...">
                      <div class="eCard-body">
                        <input class="form-control eForm-control-file" id="formFileSm" type="file" name="thumbnail">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div
              class="tab-pane fade"
              id="cFinish"
              role="tabpanel"
              aria-labelledby="cFinish-tab"
            >
              <div class="row">
                <div class="col-12">
                  <div class="text-center">
                    <h4 class="mt-0"><i class="bi bi-check-all"></i></h4>
                    <h4 class="mt-0"><?php echo get_phrase("Thank you"); ?> !</h4>

                    <span class="w-75 mb-2 mx-auto"><?php echo get_phrase('You are just one click away'); ?></span>

                    <div class="mb-3 mt-3">
                      <button type="button" class="eBtn eBtn-blue text-center" onclick="checkRequiredFields()"><?php echo get_phrase('Submit'); ?></button>
                    </div>
                  </div>
                </div> <!-- end col -->
              </div>
            </div>

          </div>
        </form>
      </div>
    </div>

	</div>
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

  function checkRequiredFields() {
    var pass = 1;
    $('form.required-form').find('input, select').each(function(){
      if($(this).prop('required')){
        if ($(this).val() === "") {
          pass = 0;
        }
      }
    });

    if (pass === 1) {
      $('form.required-form').submit();
    }else {
      error_required_field();
    }
  }

  function error_required_field() {
    toastr.error('Oh snap! </br>Please fill all the required fields', '', { closeButton: true, timeOut: 4000, progressBar: true, allowHtml: true });
  }

</script>

@endsection