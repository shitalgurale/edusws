<style type="text/css">
    .d-hidden{
        display: none;
    }

</style>

<form action="{{ route('teacher.addons.lesson_view_update', ['id' => $lesson_details->id]) }}" method="post" enctype="multipart/form-data">
    @csrf
    @php
        $lesson_name = $lesson_details['lesson_type'];
    @endphp
   
     <div class="alert alert-info" role="alert"> Lesson type :<strong>  
    @if($lesson_name == 'video-url')
        Video
    @elseif($lesson_name == 'other-img')
        Image
    @elseif($lesson_name == 'other-txt')
        Text
    @elseif($lesson_name == 'other-doc')
        Documentation
    @else
    @endif
    
     </strong>
      </div>
    <div class="form-group">
        <label for="title">{{ get_phrase('Title') }}</label>
        <input class="form-control" type="text" value="{{ $lesson_details->title }}" name="title" id="title" required>
        <small class="text-muted">{{ get_phrase('Provide a Lesson name') }}</small>
    </div>
    <div class="form-group">
        
      @if($lesson_details['lesson_type'] >= 'video')
        <div class="dv_none" id="video">
             @if($lesson_details['lesson_type'] >= 'youtube_vimeo')
            <div class="dv_none" id = "youtube_vimeo">
                <div class="form-group mb-2">
                    <label>{{ get_phrase('Video url') }}</label>
                    <input type="text" id = "video_url" value="{{ $lesson_details->video_url }}" name = "video_url" class="form-control">
                    <label class="form-label mt-2 d-hidden" id = "perloader"><i class="mdi mdi-spin mdi-loading">&nbsp;</i>{{ get_phrase('Analyzing the url') }}</label>
                    <label class="form-label mt-2 d-hidden text-danger" id = "invalid_url">{{ get_phrase('Invalid url').'. '.get_phrase('Your video source has to be either youtube or vimeo') }}</label>
                </div>

                <div class="form-group mb-2">
                    <label>{{ get_phrase('Duration') }}</label>
                    <input type="text" value="{{ $lesson_details->duration  }}" name = "duration" id = "duration" class="form-control" autocomplete="off">
                </div>
            </div>
             @else       
            <div class="dv_none" id = "html5">
                <div class="form-group mb-2">
                    <label>{{ get_phrase('video Url'); }}</label>
                    <input type="text" id = "video_url" name = "video_url" class="form-control" value="{{ $lesson_details->video_url }}">
                </div>

                <div class="form-group mb-2">
                    <label>{{ get_phrase('duration')}}</label>
                    <input type="text" class="form-control" data-toggle='timepicker' data-minute-step="5" name="duration" id = "duration" data-show-meridian="false"value="{{ $lesson_details->duration }}">
                </div>
            </div>
            @endif
       </div>
     @else
        <div class="dv_none" id = "other">
            <div class="form-group mb-2">
                <label> {{ get_phrase('Attachment') }}</label>
                <div class="input-group">
                    <div class="custom-filds">
                        <input type="file" class="custom-files-input" name="attachment" >
                        <input type="hidden" class="custom-files-input" name="old_attachment" value="{{$lesson_details->attachment}}">
                        <label class="custom-files-label" for="attachment">{{ get_phrase('Attachment') }} </label>
                    </div>
                </div>
            </div>
       </div>
     @endif   
     <div class="form-group mb-2">
            <label> <?php echo get_phrase('Summary'); ?></label>
            <textarea name="summary" id="summary" cols="30" rows="10">{{$lesson_details->summary}}</textarea>
        </div>
    </div>
    <div class="text-right">
        <button class = "eBtn eBtn-green" type="submit" name="button">{{ get_phrase('Submit') }}</button>
    </div>
</form>


<script>
    $(document).ready(function() {
        $('#summary').summernote({
            popover: {
           
            },
            height:330,
        });
    });
</script>
