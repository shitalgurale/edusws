<div class="mb-3">
    <label>Subject</label>
    <input type="text" name="subject" class="form-control" required>
</div>

<div class="mb-3">
    <label>Message</label>
    <textarea name="message" rows="5" class="form-control" required></textarea>
</div>

@if(isset($classes))
<div class="mb-3">
    <label>Select Class</label>
    <select name="class_id" class="form-control">
        <option value="all">-- All Classes --</option>
        @foreach($classes as $class)
            <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
    </select>
</div>
@endif

@if(isset($students))
<div class="mb-3">
    <label>Select Student</label>
    <select name="student_id" class="form-control">
        <option value="all">-- All Students --</option>
        @foreach($students as $student)
            <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
        @endforeach
    </select>
</div>
@endif

@if(isset($parents))
<div class="mb-3">
    <label>Select Parent</label>
    <select name="parent_id" class="form-control">
        <option value="all">-- All Parents --</option>
        @foreach($parents as $parent)
            <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
        @endforeach
    </select>
</div>
@endif

<div class="mb-3">
    <label>Attachment (optional)</label>
    <input type="file" name="attachment" class="form-control">
</div>

<button type="submit" class="btn btn-primary">Send Mail</button>
