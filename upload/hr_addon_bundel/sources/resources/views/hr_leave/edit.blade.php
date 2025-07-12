<form method="POST" class="d-block ajaxForm" action="{{ route('hr.add_update_delete_leave_request', ['action' => 'update', 'id' => $leave['id']]) }}">

    @csrf

    <div class="form-row">


        <div class="fpb-7">
            <label for="start_date" class="eForm-label">
                {{ get_phrase('Start date') }}<span class="required">*</span>
            </label>
            <input type="text" class="form-control eForm-control inputDate" id="start_date" name="start_date" value="{{ date('m/d/Y', $leave['start_date']) }}" />
        </div>

        <div class="fpb-7">
            <label for="end_date" class="eForm-label">
                {{ get_phrase('End date') }}<span class="required">*</span>
            </label>
            <input type="text" class="form-control eForm-control inputDate" id="end_date" name="end_date" value="{{ date('m/d/Y', $leave['end_date']) }}" />
        </div>


        <div class="fpb-7">
            <label for="field-1" class="eForm-label">
                {{ get_phrase('Reason') }}
            </label>
            <textarea class="form-control eForm-control" name="reason" rows="3" required>{{ $leave['reason'] }}</textarea>

        </div>





        <div class="form-group  col-md-12">
            <button class="btn btn-block btn-primary" type="submit">
                {{ get_phrase('Update') }}
            </button>
        </div>
    </div>
</form>

<script>
    "use strict";

    $(function () {
      $('.inputDate').daterangepicker(
        {
          singleDatePicker: true,
          showDropdowns: true,
          minYear: 1901,
          maxYear: parseInt(moment().format("YYYY"), 10),
        },
        function (start, end, label) {
          var years = moment().diff(start, "years");
        }
      );
    });
    
    $(".ajaxForm").submit(function(e) {
        var form = $(this);
        ajaxSubmit(e, form, showAllLeave);
    });
</script>
