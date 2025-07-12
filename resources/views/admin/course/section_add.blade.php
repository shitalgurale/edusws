

<form action="{{ route('admin.addons.section_create', ['id' => $course_id]) }}" method="post">
    @csrf
    <div class="form-group">
        <label for="title">{{ get_phrase('Title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required>
        <small class="text-muted">{{ get_phrase('Provide a section name') }}</small>
    </div>
    <div class="text-right">
        <button class = "eBtn eBtn-green" type="submit" name="button">{{ get_phrase('Submit') }}</button>
    </div>
</form>