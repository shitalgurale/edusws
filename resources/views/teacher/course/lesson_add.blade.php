<style type="text/css">
    .d-hidden{
        display: none;
    }

</style>

<form action="{{ route('teacher.addons.lesson_create', ['id' => $course_id]) }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group mb-3">
        <label>{{ get_phrase('Title') }}</label>
        <input type="text" name = "title" class="form-control" required>
    </div>

    <input type="hidden" name="course_id" value="{{ $course_id }}">

    <div class="form-group mb-3">
        <label for="section_id">{{ get_phrase('Section') }}</label>
        <select class="form-control select2" data-bs-toggle="select2" name="section_id" id="section_id" required>
            @foreach($course_sections as $section)
                <option value="{{ $section->id }}">{{ $section->title }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="form-group mb-3">
        <label for="section_id">{{ get_phrase('Lesson type') }}</label>
        <select class="form-control select2" data-bs-toggle="select2" name="lesson_type" id="lesson_type" required onchange="show_lesson_type_form(this.value)">
            <option value="">{{ get_phrase('Select type of lesson') }}</option>
            <option value="video-url">{{ get_phrase('Video') }}</option> 
            <option value="other-txt">{{ get_phrase('Text file') }}</option>
            <option value="other-pdf">{{ get_phrase('Pdf file') }}</option>
            <option value="other-doc">{{ get_phrase('Document file') }}</option>
            <option value="other-img">{{ get_phrase('Image file') }}</option>  
        </select>
    </div>

    <div class="dv_none" id="video">

        <div class="form-group mb-3">
            <label for="lesson_provider">{{ get_phrase('Lesson provider') }}( {{ get_phrase('For web application') }} )</label>
            <select class="form-control select2" data-toggle="select2" name="lesson_provider" id="lesson_provider" onchange="check_video_provider(this.value)">
                <option value="">{{ get_phrase('Select lesson provider') }}</option>
                <option value="youtube">{{ get_phrase('Youtube') }}</option>
                <option value="vimeo">{{ get_phrase('Vimeo') }}</option>
                <option value="html5">{{ get_phrase('HTML5') }}</option>
            </select>
        </div>
          
        <div class="dv_none" id = "youtube_vimeo">
            <div class="form-group mb-3">
                <label>{{ get_phrase('Video url') }}( {{ get_phrase('For web application') }} )</label>
                <input type="text" id = "video_url" name = "video_url" class="form-control" onblur="ajax_get_video_details(this.value)" placeholder="{{ get_phrase('This video will be shown on web application') }}">
                <label class="form-label d-hidden mt-2" id = "perloader"><i class="mdi mdi-spin mdi-loading">&nbsp;</i>{{ get_phrase('Analyzing the url') }}</label>
                <label class="form-label d-hidden mt-2 text-danger" id = "invalid_url">{{ get_phrase('Invalid url').'. '.get_phrase('Your video source has to be either youtube or vimeo') }}</label>
            </div>

            <div class="form-group mb-3">
                <label>{{ get_phrase('Duration') }}( {{ get_phrase('For web application') }} )</label>
                <input type="text" name = "duration" value="00:00:00" id = "duration" class="form-control" autocomplete="off">
            </div>
        </div>

    </div>
    <div class="dv_none" id = "other">
        <div class="form-group mb-3">
            <label> <?php echo get_phrase('Attachment'); ?></label>
            <div class="input-group">
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="attachment" name="attachment" onchange="changeTitleOfImageUploader(this)">
                    <label class="custom-file-label" for="attachment"><?php echo get_phrase('Attachment'); ?></label>
                </div>
            </div>
        </div>
    </div>
    <div class="dv_none" id = "lesson_summary">
        <div class="form-group mb-3">
            <label> <?php echo get_phrase('Summary'); ?></label>
            <textarea name="summary" id="summary" cols="30" rows="10"></textarea>
        </div>
    </div>
    <div class="text-right">
        <button class = "eBtn eBtn-green" type="submit" name="button"><?php echo get_phrase('Submit'); ?></button>
    </div>
</form>

<script type="text/javascript">
    
    function show_lesson_type_form(param) {
        var checker = param.split('-');
        var lesson_type = checker[0];
        if (lesson_type === "video") {
            $('#other').hide();
            $('#video').show();
        }
        else if (lesson_type === "other") {
            $('#video').hide();
            $('#other').show();
        }
        else {
            $('#video').hide();
            $('#other').hide();
        }
    }

    $(document).ready(function () {
            $("#other").hide();   
        });

</script>

<script>
    $(document).ready(function() {
        $('#summary').summernote({
            popover: {
           
            }
        });
    });
</script>