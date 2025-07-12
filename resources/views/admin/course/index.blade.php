@extends('admin.navigation')
   
@section('content')

<?php 

use App\Models\Addon\Course;
use App\Models\Addon\Lesson;
use App\Models\Addon\CourseSection;

?>

<style type="text/css">
    .a-teacher{
        font-size:13px;
        color : #727cf5;
    }
</style>
<div class="mainSection-title">
    <div class="row">
      <div class="col-12">
        <div
          class="d-flex justify-content-between align-items-center flex-wrap gr-15"
        >
          <div class="d-flex flex-column">
            <h4>{{ get_phrase('All Courses') }}</h4>
            <ul class="d-flex align-items-center eBreadcrumb-2">
              <li><a href="#">{{ get_phrase('Home') }}</a></li>
              <li><a href="#">{{ get_phrase('Online Courses') }}</a></li>
            </ul>
          </div>
          <div class="export-btn-area">
            <a href="{{ route('admin.addons.course_add') }}" class="export_btn">{{ get_phrase('Create new course') }}</a>
          </div>
        </div>
      </div>
    </div>
</div>


<div class="row">
	<div class="col-12">

	    <div class="eMain">
	      <div class="row">

	        <div class="col-md-6">
	          <div class="eCard eCard-special">
	            <div class="eCard-body">
	            	<div class="d-flex justify-content-md-center">
	                  <i class="bi bi-bell"></i>
	                </div>
	            	<h5 class="eCard-title d-flex justify-content-md-center">{{ count($active_course) }}</h5>
	            	<p class="eCard-text d-flex justify-content-md-center">
	            		{{ get_phrase('Active Courses') }}
	            	</p>
	            </div>
	          </div>
	        </div>

	        <div class="col-md-6">
	          <div class="eCard eCard-special">
	            <div class="eCard-body">
	            	<div class="d-flex justify-content-md-center">
	                  <i class="bi bi-bell"></i>
	                </div>
	            	<h5 class="eCard-title d-flex justify-content-md-center">{{ count($inactive_course) }}</h5>
	            	<p class="eCard-text d-flex justify-content-md-center">
	            		{{ get_phrase('Inactive Courses') }}
	            	</p>
	            </div>
	          </div>
	        </div>

	      </div>
	    </div>

        <div class="eSection-wrap-2">
            <div class="table-responsive">
                @if(count($courses) > 0)
                    <table id="basic-datatable" class="table eTable eTable-2">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ get_phrase('Title') }}</th>
                                <th>{{ get_phrase('Class') }}</th>
                                <th>{{ get_phrase('Lesson and Section') }}</th>
                                <th>{{ get_phrase('Status') }}</th>
                                <th>{{ get_phrase('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $key => $course)

                                @php 
                                $section = CourseSection::where('course_id', $course->id)->get();
                                $lesson = Lesson::where('course_id', $course->id) ->get();
                                @endphp

                                <tr>
                                    <td><?php echo ++$key; ?></td>
                                    <td>
                                        <strong><a class="a-teacher" href="{{ route('admin.addons.course_edit', ['id' => $course->id]) }}">{{ $course->title }}</a></strong><br>
                                        <small class="text-muted">{{ get_phrase('Teacher') }}:<b>{{ user_name($course->user_id) }}</b></small>
                                    </td>

                                    <td>
                                        <span class="bg bg-dark-lighten">{{ class_name($course->class_id, $course->school_id) }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo '<b>'.get_phrase('Total section').'</b>: '.count($section); ?></small><br>
                                        <small class="text-muted"><?php echo '<b>'.get_phrase('Total lesson').'</b>: '.count($lesson); ?></small><br>
                                    </td>
                                    <td class="text">
                                        <?php if ($course['status'] == 'active'): ?>
                                            <span class="eBadge ebg-success"><?php echo get_phrase('Active'); ?></span>
                                        <?php else: ?>
                                            <span class="bg bg-dark"><?php echo get_phrase('Inactive'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                    <div class="dropdown  dropright ">
                                          <button class="btn option-icon btn-outline-primary  dropdown-toggle btn-rounded btn-icon btn-sm " type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
                                          <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="{{ route('admin.addons.course_edit', ['id' => $course->id]) }}">Edit</a></li>
                                            <li><a class="dropdown-item" href="javascript:;" onclick="confirmModal('{{ route('admin.addons.course_delete', ['id' => $course->id]) }}','undefined')">Delete</a></li>
                                          </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                <div class="empty_box center">
                  <img class="mb-3" width="150px" src="{{ asset('public/assets/images/empty_box.png') }}" />
                  <br>
                  <span class="">{{ get_phrase('No data found') }}</span>
                </div>
                @endif
            </div>
        </div>

	</div>
</div>

@endsection