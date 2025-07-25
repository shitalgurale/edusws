@extends('accountant.navigation')

@section('content')
    <div class="mainSection-title">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap gr-15">
                    <div class="d-flex flex-column">
                        <h4>{{ get_phrase('Inventory Category') }}</h4>
                        <ul class="d-flex align-items-center eBreadcrumb-2">
                            <li><a href="#">{{ get_phrase('Home') }}</a></li>
                            <li><a href="#">{{ get_phrase('Inventory') }}</a></li>
                            <li><a href="#">{{ get_phrase('Inventory Category') }}</a></li>
                        </ul>
                    </div>
                    <div class="export-btn-area">
                        <a href="javascript:;" class="export_btn"
                            onclick="rightModal('{{ route('accountant.category.create.modal') }}', '{{ get_phrase('Create Inventory Category') }}')"><i
                                class="bi bi-plus"></i>{{ get_phrase('Add inventory category') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-7 offset-md-2">
            <div class="eSection-wrap">
                @if (count($data) > 0)
                    {{-- inventory category list --}}
                    <div class="table-responsive tScrollFix pb-2">
                        <table class="table eTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ get_phrase('Name') }}</th>
                                    <th class="text-end">{{ get_phrase('Action') }}</th>
                                </tr>
                            </thead>


                            <tbody>
                                @foreach ($data as $key => $display)
                                    <tr>
                                        <td>{{ $data->firstItem() + $key }}</td>
                                        <td>{{ $display['name'] }}</td>

                                        <td>
                                            <div class="adminTable-action">
                                                <button type="button"
                                                    class="eBtn eBtn-black dropdown-toggle table-action-btn-2"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    {{ get_phrase('Actions') }}
                                                </button>
                                                <ul
                                                    class="dropdown-menu dropdown-menu-end eDropdown-menu-2 eDropdown-table-action">
                                                    <li>


                                                        {{-- inventory edit button --}}
                                                        <a class="dropdown-item" href="javascript:;"
                                                            onclick="rightModal('{{ route('accountant.category.edit', $display['id']) }}', 'Edit Inventory Category')">{{ get_phrase('Edit') }}</a>


                                                        {{-- inventory delete button --}}
                                                        <a class="dropdown-item" href="javascript:;"
                                                            onclick="confirmModal('{{ route('accountant.category.delete', $display['id']) }}', 'undefined')">{{ get_phrase('Delete') }}</a>
                                                    </li>



                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


                        {{-- pagination --}}
                        <div
                            class="admin-tInfo-pagi d-flex justify-content-md-between justify-content-center align-items-center flex-wrap gr-15">
                            <p class="admin-tInfo">
                                {{ get_phrase('Showing') . ' 1 - ' . count($data) . ' ' . get_phrase('from') . ' ' . $data->total() . ' ' . get_phrase('data') }}
                            </p>
                            <div class="admin-pagi"> {!! $data->appends(request()->all())->links() !!} </div>
                        </div>
                    </div>
            </div>
        </div>
    @else
        <div class="empty_box w-100 text-center">
            <img class="mb-3" width="150px" src="{{ asset('public/assets/images/empty_box.png') }}" />
            <br>
            <span class="d-block">{{ get_phrase('No data found') }}</span>
        </div>
        @endif
    @endsection
