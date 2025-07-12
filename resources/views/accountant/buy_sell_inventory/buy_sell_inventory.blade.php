@extends('accountant.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Buy & Sell Report') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Inventory') }}</a></li>
                            <li><a href="#">{{ get_phrase('Buy & Sell Report') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs eNav-Tabs-custom"id="myTab"role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="buy_status_tab" data-bs-toggle="tab" data-bs-target="#buyingtable"
                type="button" role="tab" aria-controls="buyingtable" aria-selected="false">
                {{ get_phrase('Buy Report') }}
                <span></span>
            </button>
        </li>

        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sell_status_tab" data-bs-toggle="tab" data-bs-target="#sellingtable" type="button"
                role="tab" aria-controls="sellingingtable" aria-selected="false">
                {{ get_phrase('Sell Report') }}
                <span></span>
            </button>
        </li>
    </ul>


    <div class="tab-content pb-2" id="nav-tabContent">
        <div class="tab-pane fade show active" id="buyingtable" role="tabpanel" aria-labelledby="buy_status_tab">

            <div class="eForm-layouts">
                <div class="row">
                    <div class="col-12">
                        <div class="eSection-wrap">

                            <form method="post" enctype="multipart/form-data" class="d-block ajaxForm pt-3"
                                action="{{ route('accountant.buy_sell_inventory') }}">
                                @csrf
                                <div class="row">
                                    <div class="row justify-content-center">
                                        <div class="col-xl-4 mb-3">
                                            <input type="text" class="form-control eForm-control" name="eDateRange"
                                                value="{{ date('m/d/Y', $buy_date_start) . ' - ' . date('m/d/Y', $buy_date_end) }}">
                                        </div>

                                        <div class="col-xl-2 mb-3">
                                            <button type="submit" class="eBtn eBtn btn-secondary form-control"
                                                name="buy_filter">{{ get_phrase('Filter') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </form>

                            @if (count($buy_inventory) > 0)
                                {{-- table list --}}
                                <div class="card-body exam_content" id="offline_exam_export">
                                    <div class="table-responsive tScrollFix pb-2">
                                        <table class="table eTable">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Product Name') }}
                                                    </th>
                                                    <th scope="col" class="text-center">
                                                        {{ get_phrase('Category') }}</th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Quantity') }}
                                                    </th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Unit Price') }}
                                                    </th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Total Price') }}
                                                    </th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Date') }}</th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Action') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($buy_inventory as $key => $inventory)
                                                    <tr>
                                                        <th>
                                                            {{ $buy_inventory->firstItem() + $key }}
                                                        </th>

                                                        <td class="text-center">
                                                            {{ $inventory->product_name }}
                                                        </td>

                                                        <td class="text-center">
                                                            <b>{{ $inventory->category_name }}</b>
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $inventory->quantity }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $inventory->price }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $inventory->total_price }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ date('m-d-y', $inventory->date) }}
                                                        </td>




                                                        {{-- action button --}}
                                                        <td class="text-center">
                                                            <a class="btn btn-secondary text-12px"
                                                                href="{{ route('accountant.buy_sell_invoice.buy_invoice', $inventory->id) }}">{{ get_phrase('Invoice') }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- buying pagination --}}
                                        <div
                                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                                            <p class="admin-tInfo">
                                                {{ get_phrase('Showing') . ' 1 - ' . count($buy_inventory) . ' ' . get_phrase('from') . ' ' . $buy_inventory->total() . ' ' . get_phrase('data') }}
                                            </p>
                                            <div class="admin-pagi">
                                                {!! $buy_inventory->appends(request()->all())->links() !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @else
                                <div class="empty_box w-100 text-center">
                                    <img class="mb-3" width="150px"
                                        src="{{ asset('public/assets/images/empty_box.png') }}" />
                                    <br>
                                    <span class="d-block">{{ get_phrase('No data found') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show " id="sellingtable" role="tabpanel" aria-labelledby="sell_status_tab">

            <div class="eForm-layouts">
                <div class="row">
                    <div class="col-12">
                        <div class="eSection-wrap">

                            <form method="post" enctype="multipart/form-data" class="d-block ajaxForm pt-3"
                                action="{{ route('accountant.buy_sell_inventory') }}">
                                @csrf
                                <div class="row">
                                    <div class="row justify-content-center">
                                        <div class="col-xl-4 mb-3">
                                            <input type="text" class="form-control eForm-control" name="eDateRange"
                                                value="{{ date('m/d/Y', $sell_date_start) . ' - ' . date('m/d/Y', $sell_date_end) }}">
                                        </div>


                                        <div class="col-xl-2 mb-3">
                                            <button type="submit" class="eBtn eBtn btn-secondary form-control"
                                                name="sell_filter">{{ get_phrase('Filter') }}</button>
                                        </div>

                                    </div>
                                </div>
                            </form>

                            @if (count($sell_inventory) > 0)
                                {{-- table list --}}
                                <div class="card-body exam_content" id="offline_exam_export">
                                    <div class="table-responsive tScrollFix pb-2">
                                        <table class="table eTable">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" class="text-center">
                                                        {{ get_phrase('Product Name') }}
                                                    </th>
                                                    <th scope="col" class="text-center">
                                                        {{ get_phrase('Category') }}</th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Quantity') }}
                                                    </th>
                                                    <th scope="col" class="text-center">
                                                        {{ get_phrase('Unit Price') }}
                                                    </th>
                                                    <th scope="col" class="text-center">
                                                        {{ get_phrase('Total Price') }}
                                                    <th scope="col" class="text-center">{{ get_phrase('Date') }}
                                                    </th>
                                                    <th scope="col" class="text-center">{{ get_phrase('Action') }}
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($sell_inventory as $key => $inventory)
                                                    <tr>
                                                        <th>
                                                            {{ $sell_inventory->firstItem() + $key }}
                                                        </th>

                                                        <td class="text-center">
                                                            {{ $inventory->product_name }}
                                                        </td>

                                                        <td class="text-center">
                                                            <b>{{ $inventory->category_name }}</b>
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $inventory->quantity }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $inventory->price }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ $inventory->total_price }}
                                                        </td>

                                                        <td class="text-center">
                                                            {{ date('m-d-y', $inventory->date) }}
                                                        </td>


                                                        {{-- action button --}}
                                                        <td class="text-center">
                                                            <a class="btn btn-secondary text-12px"
                                                                href="{{ route('accountant.buy_sell_invoice.sell_invoice', $inventory->id) }}">{{ get_phrase('Invoice') }}</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        {{-- selling pagination --}}
                                        <div
                                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                                            <p class="admin-tInfo">
                                                {{ get_phrase('Showing') . ' 1 - ' . count($sell_inventory) . ' ' . get_phrase('from') . ' ' . $sell_inventory->total() . ' ' . get_phrase('data') }}
                                            </p>
                                            <div class="admin-pagi">
                                                {!! $sell_inventory->appends(request()->all())->links() !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="empty_box w-100 text-center">
                                    <img class="mb-3" width="150px"
                                        src="{{ asset('public/assets/images/empty_box.png') }}" />
                                    <br>
                                    <span class="d-block">{{ get_phrase('No data found') }}</span>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

<script type="text/javascript">
    "use strict";

    function classWiseSection(classId) {
        let url = "{{ route('admin.class_wise_sections', ['id' => ':classId']) }}";
        url = url.replace(":classId", classId);
        $.ajax({
            url: url,
            success: function(response) {
                $('#section_id').html(response);
            }
        });
    }
</script>
