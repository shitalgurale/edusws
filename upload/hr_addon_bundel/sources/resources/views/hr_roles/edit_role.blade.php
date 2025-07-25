<form method="POST" class="d-block ajaxForm" action="{{ route('hr.user_role_update',['id'=>$roles['id']]) }}" enctype="multipart/form-data">
  @csrf
  <div class="form-row">
    <div class="fpb-7">
      <label for="name" class="eForm-label">
        {{ get_phrase('Role name') }}
      </label>
      <input type="text" class="form-control eForm-control" id="name" name="name" value="{{ get_phrase($roles['name']) }}" required>
    </div>

    <div class="fpb-7 pt-2">
      <button class="btn-form" type="submit">
        {{ get_phrase('Update role') }}
      </button>
    </div>
  </div>
</form>
