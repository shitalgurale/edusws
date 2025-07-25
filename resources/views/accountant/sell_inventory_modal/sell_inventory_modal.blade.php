<div class="eoff-form">
    <form method="POST" enctype="multipart/form-data" class="d-block ajaxForm"
        action="{{ route('accountant.inventory.sell', $inventory->id) }}">
        @csrf
        <div class="form-row">

            {{-- product name --}}
            <div class="fpb-7">
                <label for="product_name" class="eForm-label">{{ get_phrase('Product Name') }}
                    <span class="required">*</span>
                </label>
                <input class="form-control eForm-control" id="product_name" type="text" name="product_name"
                    value="{{ $inventory->product_name }}">
            </div>

            {{-- inventory name --}}
            <div class="fpb-7">
                <label for="category_name" class="eForm-label">{{ get_phrase('Select an Inventory') }}</label>
                <select name="category_name" id="category_name"
                    class="form-select eForm-select eChoice-multiple-with-remove" required>
                    <option value="{{ $inventory->category_id }}">{{ $inventory->category_name }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>


            {{-- inventory quantity --}}
            <div class="fpb-7">
                <label for="total_marks" class="eForm-label">{{ get_phrase('Quantity') }}<span
                        class="required">*</span></label>
                <div>
                    <input class="form-control eForm-control" id="total_marks" type="number" min="1"
                        name="quantity" value="{{ $inventory->quantity }}">
                </div>
            </div>


            {{-- inventory price --}}
            <div class="fpb-7">


                <label for="total_marks" class="eForm-label">{{ get_phrase('Unit price') }}<span
                        class="required">*</span></label>
                <div>
                    <input class="form-control eForm-control" id="total_marks" type="number" min="1"
                        name="price" value="{{ $inventory->price }}">
                </div>
            </div>


            {{-- issue date --}}
            <div class="fpb-7">
                <label for="date" class="eForm-label">{{ get_phrase('Date') }}<span
                        class="required">*</span></label>
                <input type="text" class="form-control eForm-control inputDate" id="date" name="date"
                    value="{{ date('m-d-y', $inventory->date) }}" />
            </div>


            {{-- sell button --}}
            <div class="fpb-7 pt-2">
                <button class="btn-form" type="submit">{{ get_phrase('Sell') }}</button>
            </div>
        </div>
    </form>
</div>


<script type="text/javascript">
    "use strict";


    function classWiseSubject(classId) {
        let url = "{{ route('admin.class_wise_subject', ['id' => ':classId']) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response) {
                $('#subject_id').html(response);
            }
        });
    }

    $(function() {
        $('.inputDate').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                minYear: 1901,
                maxYear: parseInt(moment().format("YYYY"), 10),
            },
            function(start, end, label) {
                var years = moment().diff(start, "years");
            }
        );
    });

    $(document).ready(function() {
        $(".eChoice-multiple-with-remove").select2();
    });
</script>
