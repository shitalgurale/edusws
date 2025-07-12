@include('student.course.header_top')

<style type="text/css">
    .h-550px{
        height: 550px;
    }
</style>

  @if(!empty($course_name))
 <div class="preview-header">
    <div class="container-xxl">
        <div class="row">
           <div class="col-lg-9 col-md-8">
              <div class="preview-head-left">
                  <h5><img src="{{asset('course/logo-light-sm.png')}}" alt="">{{get_phrase('Online Course')}} |  {{$preview->title}} </h5>
              </div>
           </div>
           <div class="col-lg-3 col-md-4">
              <div class="preview-head-right">
                 <a class="course_btn" href="#"><i class="bi bi-chevron-left"></i><i class="bi bi-chevron-right"></i></a>
                  <a class="course_btn" href="{{route('student.addons.courses')}}"><i class="bi bi-chevron-left"></i>{{get_phrase('Back to Course')}}</a>
              </div>
           </div>
        </div>
    </div>
 </div>


  <div class="preview-area">
    <div class="lesson-container-fluid">
        <div class="row">
          @if($lesson_details)
            <div class="col-lg-9">
               <div class="lesson-left-video">
                  @if($lesson_details->lesson_type == 'video' || $lesson_details->lesson_type == 'video-url')
                    @if($lesson_details->video_type == 'youtube')
                      <div class="video_player">
                        <div class="plyr__video-embed" id="player">
                            <iframe src="{{$lesson_details->video_url}}" allowfullscreen allowtransparency allow="autoplay"></iframe>
                          </div>
                      </div> 
                      @elseif($lesson_details->video_type == 'vimeo')
                      <div class="video_player">
                        <div class="plyr__video-embed" id="player">
                            <iframe class="w-100 h-550px" src="{{$lesson_details->video_url}}" allowfullscreen allowtransparency allow="autoplay"></iframe>
                          </div>
                      </div> 
                      @elseif($lesson_details->video_type == 'html5')
                      <div class="video_player">
                        <div class="plyr__video-embed" id="player">
                            <iframe src="{{$lesson_details->video_url}}" allowfullscreen allowtransparency allow="autoplay" class="w-100 h-550px"></iframe>
                          </div>
                      </div> 
                      @endif
                  @elseif($lesson_details->lesson_type == 'other-img' )
                    <div class="upload-image player">
                      <img src="{{asset('assets/uploads/lesson-image/'.$lesson_details->attachment)}}" alt="">
                  </div> 
                  @elseif($lesson_details->lesson_type == 'other-pdf' || $lesson_details->lesson_type == 'other-doc' || $lesson_details->lesson_type == 'other-txt')
                  <div class="video_player">
                        <div class="plyr__video-embed" id="player">
                           <iframe src="{{asset('assets/uploads/lesson-image/'.$lesson_details->attachment)}}" allowfullscreen allowtransparency allow="autoplay" class="w-100 h-550px"></iframe>
                       </div>
                    </div>
                  @else
                  @endif
                  <div class="lesson-summary" id='summary'>
                   <div class="card">
                     <div class="card-body">
                         <h5 class="card-title">{{get_phrase('Note:')}}</h5>
                         @if($lesson_details->summary)
                         <p class="card-text">{!! $lesson_details->summary !!}</p>
                         @else
                         <p class="card-text">{{ get_phrase('No Added Summary ') }}</p>
                         @endif
                     </div>
                   </div>
                 </div>
               </div> 
            </div>
            <div class="col-lg-3">
               <div class="accordion-area">
                  <h3 class="ac_title">{{get_phrase('Course content')}}</h3>  
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                      <li class="nav-item" role="presentation">
                          <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">{{get_phrase('Lesson')}}</button>
                      </li>
                    </ul>
                    <div class="tab-content" id="pills-tabContent">

                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                    <div class="accordion" id="accordionExample">
                            @foreach($course_sections as $course_section)
                             @php
                              $lessons = DB::table('lesson')->where('section_id',$course_section->id)->orderBy('order', 'ASC')->get();
                             @endphp
                            <div class="accordion-item course-accordion">
                                <h4 class="accordion-header" id="headingOne{{$course_section->id}}">
                                <button class="accordion-button btn-link " href="" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne{{$course_section->id}}" aria-expanded="false" aria-controls="collapseOne{{$course_section->id}}">
                                   {{ $course_section->title }}</button>
                                </h4>
                                  <div id="collapseOne{{$course_section->id}}" class="accordion-collapse collapse" aria-labelledby="headingOne{{$course_section->id}}" data-bs-parent="#accordionExample">
                                   <div class="accordion-body">
                                      @php
                                        $i = 1;
                                      @endphp
                                       @foreach($lessons as $lesson )
                                       <div class="single-lesson">
                                          <div class="form-group">
                                              <input type="checkbox" class="form-check-input" id="3" onchange="markThisLessonAsCompleted(this.id)">
                                          </div>
                                          <a   class="lesson-design" href="{{route('student.addons.courses.course_preview', ['course_id' => $preview->id, 'lesson_id' => $lesson->id])}}">{{$lesson-> title}}</a>
                                                 
                                       </div>
                                       @endforeach
                                   </div>
                                </div>
                            </div>
                            @endforeach    
                         </div>
                      </div> 
                    </div>
                 </div>
            </div>
              @else
               <div class="not-created">
                  <p>{{get_phrase('lesson not uploaded yet?')}}</p>
               </div>
           @endif
        </div>
    </div>
  </div>
@else

<div class="container">
   <div class="row">
      <div class="col-lg-12">
           <div class="error-text-center">
              <div class="text-error">
                  <h4>{{get_phrase('404')}}</h4>   
                  <p>{{ get_phrase('Oh snap! This is not the web page you are looking for. ') }}</p>
                  <a href="{{ route('student.addons.courses') }}" class="back-btn" >{{get_phrase('Back to Home')}}</a>
              </div>
           </div>
       </div>
   </div>
</div>

@endif

@include('student.course.footer_bottom')

<script>
   $(document).ready(function() {
    $('.collapse').on('shown.bs.collapse', function() {
        var activeTabId = $(this).attr('id');
        localStorage.setItem('activeTabId', activeTabId);
    });
});
$(document).ready(function() {
    var activeTabId = localStorage.getItem('activeTabId');
    if (activeTabId) {
        $('#' + activeTabId).collapse('show');
    }
});
</script>