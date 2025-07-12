@php

use App\Http\Controllers\Addon\OnlineCourse;

@endphp
<div class="row justify-content-center">
	<div class="col-xl-12 mb-4 text-center mt-3">
		<a href="javascript:;" class="btn btn-outline-primary btn-rounded btn-sm ml-1" onclick="showAjaxModal('{{ route('teacher.addons.section_add', ['id' => $course->id]) }}', '{{ get_phrase('Add new section') }}')"><i class="bi bi-plus"></i> {{ get_phrase('Add section') }}</a>
		<a href="javascript:;" class="btn btn-outline-primary btn-rounded btn-sm ml-1" onclick="showAjaxModal('{{ route('teacher.addons.lesson_add', ['id' => $course->id]) }}', '{{ get_phrase('Add new lesson') }}')"><i class="bi bi-plus"></i> {{ get_phrase('Add lesson')}}</a>
        <a href="javascript:;" class="btn btn-outline-primary btn-rounded btn-sm ml-1"  onclick="largeModal('{{ route('teacher.addons.section_sort', ['id' => $course->id]) }}', '{{ get_phrase('Sort Section') }}')"><i class="bi bi-sort-variant"></i> {{ get_phrase('Sort sections')}}</a>
	</div>

	<div class="col-xl-8">
		<div class="row">
			@php
			$lesson_counter = 0;
            $quiz_counter   = 0;
            @endphp
            @foreach($course_sections as $key => $section)
            	<div class="col-xl-12">
            		<div class="card ebg-soft-info text-seconday on-hover-action mb-3" id = "section-{{ $section->id }}">
            			<div class="card-body">
            				<div class="w-100 display-none text-center mb-2" id = "widgets-of-section-{{  $section->id }}">
	            				<button type="button" class="btn btn-outline-secondary btn-rounded btn-sm" name="button" onclick="largeModal('{{ route('teacher.addons.lesson_sort', ['id' => $section->id]) }}', '{{ get_phrase('Sort Lesson ') }}')" ><i class="bi bi-sort-variant"></i> {{  get_phrase('Sort lesson') }}</button>

	                            <button type="button" class="btn btn-outline-secondary btn-rounded btn-sm ml-1" name="button" onclick="showAjaxModal('{{ route('teacher.addons.section_edit', ['id' => $section->id]) }}', '{{ get_phrase('Update Section ') }}')" ><i class="bi bi-pencil-outline"></i> {{  get_phrase('Edit section') }}</button>

	                            <button type="button" class="btn btn-outline-secondary btn-rounded btn-sm ml-1" name="button" onclick="confirmModal('{{ route('teacher.addons.section_delete', ['id' => $section->id]) }}','undefined')"><i class="bi bi-sort-variant"></i> {{  get_phrase('Delete section') }}</button>
	                        </div>

	                        <p class="column-title" class="m-0 py-1"><span class="font-weight-light">{{ get_phrase('section').' '.++$key }}</span>: {{ $section->title }}</p>

	                        <div class="clearfix"></div>

	                        @php
	                        	$lessons = (new OnlineCourse)->get_lessons('section', $section->id);
	                        @endphp

	                        @foreach($lessons as $index => $lesson)
	                        	<div class="card text-secondary on-hover-action mb-2" id="{{ 'lesson-'.$lesson->id }}">
	                        		<div class="card-body thinner-card-body d-flex justify-content-between">
	                        			<h3 class="card-title mb-0">
	                        				<span class="font-weight-light">
	                        					@php 
	                        						$lesson_counter++;
			                        				if($lesson->attachment_type == 'txt' || 
				                        				$lesson->attachment_type == 'pdf' || 
				                        				$lesson->attachment_type == 'doc' || 
				                        				$lesson->attachment_type == 'img'){
		                                                $lesson_type = $lesson->attachment_type;
		                                            }else{
		                                                $lesson_type = 'video';
		                                            }
	                                            @endphp
	                        					<img src="{{ asset('public/assets/lesson_icon/'.$lesson_type.'.png') }}" alt="" height = "16">
	                                            {{ get_phrase('Lesson').' '.$lesson_counter }}
	                        				</span>: <strong>{{ $lesson->title }}</strong>
	                        			</h3>
	                        			<div class="card-widgets display-none" id="widgets-of-lesson-{{ $lesson->id }}">
	                        				<a href="javascript::" onclick="showAjaxModal('{{ route('teacher.addons.lesson_view_edit', ['id' => $lesson->id]) }}', '{{ get_phrase('Edit Lesson ') }}')"><i class="bi bi-pencil"></i></a>
	                        				<a href="javascript::" onclick="confirmModal('{{ route('teacher.addons.lesson_delete', ['id' => $lesson->id]) }}','undefined')"><i class="bi bi-x-lg"></i></a>
	                        			</div>
	                        		</div>
	                        	</div>
	                        @endforeach

            			</div>
            		</div>
            	</div>
            @endforeach
		</div>
	</div>
</div>