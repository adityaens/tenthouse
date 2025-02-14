@extends('layouts.user_type.auth')

@section('page_title', __('Products List'))
<style>
    .small-swal {
        max-width: 350px !important;
        padding: 1rem !important;
    }

    .small-swal-title {
        font-size: 16px !important;
        /* Adjust title font size */
    }

    .small-swal-content {
        font-size: 14px !important;
        /* Adjust content font size */
    }

    .small-swal-actions .swal2-confirm,
    .small-swal-actions .swal2-cancel {
        font-size: 14px !important;
        /* Adjust button font size */
    }
</style>

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Products List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Products</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            @include('components.alert')
            <div class="d-flex justify-content-end mb-2">
                <button type="button" onclick="window.location.href='{{ route('admin.products.create') }}'"
                    class="btn btn-primary sb-sidenav-dark border-0 text-white cust--btn">Add</button>
            </div>
            {{--<ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Customer List </li>
            </ol> --}}
            <div class="card mb-4">
                <!-- <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Customer User List
                        </div> -->
                <div class="card-body">

                    <div class="filter-form-wrapper mb-5">

                        <form class="form filter-form" method="GET">
                            <div class="row">
                                <!-- Search by Name -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        placeholder="{{ __('Enter Name') }}" maxlength="150" value="{{ request()->get('name') ?? '' }}"
                                        autocomplete="off">
                                </div>

                                <!-- Filter by Category -->
                                <div class="col-md-6 mb-3">
                                    <label for="cat_id" class="form-label">{{ __('Category') }}</label>
                                    <select name="cat_id" id="cat_id" class="form-control select2">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @forelse ($productCategories as $productCategory)
                                        <option {{ request()->get('cat_id') == $productCategory->id ? 'selected' : '' }} value="{{ $productCategory->id }}">{{ $productCategory->name }}</option>
                                        @empty

                                        @endforelse
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Filter by Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="">{{ __('Select Status') }}</option>
                                        <option {{ request()->get('status') == 1 ? 'selected' : '' }} value="1">{{ __('Active') }}</option>
                                        <option {{ request()->get('status') == 0 ? 'selected' : '' }} value="0">{{ __('Inactive') }}</option>
                                    </select>
                                </div>

                                <!-- Filter by Created On -->
                                <div class="col-md-6 mb-3">
                                    <label for="created_on" class="form-label">{{ __('Created On') }}</label>
                                    <input type="date" class="form-control" name="created_on" id="created_on" value="{{ request()->get('created_on') ?? '' }}">
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="row">
                                <div class="col-md-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2">{{ __('Search') }}</button>
                                    <button type="button" class="btn btn-secondary reset-btn" onclick="window.location.href='{{ route('admin.products.index') }}'">{{ __('Reset') }}</button>
                                </div>
                            </div>
                        </form>

                        <hr />
                    </div>
                    <div class="row justify-content-end mb-2">
                        <div class="col-md-1">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                Import
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>S. No.</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Qty.</th>
                                    <th>Price({{ CURRENCY }})</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Created On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @forelse($products as $key => $product)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{ $product->name ?? '' }}</td>
                                    <td>
                                        <img src="{{ asset($product->images[0]->image_path ?? 'assets/img/product_default_img.jpg') }}"
                                            alt="{{ $product->name ?? '' }}" width="100">
                                    </td>
                                    <td>{{ $product->quantity ?? '' }}</td>
                                    <td>{{ $product->price ?? '' }}</td>
                                    <td>{{ $product->category->name ?? '' }}</td>
                                    <td>
                                        <span
                                            class="p-2 badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ date('Y-m-d', strtotime($product->created_at)) }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.products.edit', ['id' => $product->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <form id="delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', ['id' => $product->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $product->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <!-- <form action="{{route('admin.products.destroy',['id' => $product->id])}}" method="POST" style="display: inline-block;"
                                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form> -->
                                    </td>
                                </tr>
                                @php $i++; @endphp
                                @empty
                                <tr>
                                    <td colspan="9"><span class="">No Products to show</span></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($products->count() > 0)
                        <div class="pagination_wrapper">
                            {{ $products->onEachSide(PER_PAGE)->withQueryString()->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>


<!-- Import Products Modal -->
<div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Import Products</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="import-form" enctype="multipart/form-data">
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="csvFile" name="csvFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning import-btn">Import</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(productId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'small-swal',
                title: 'small-swal-title',
                content: 'small-swal-content',
                actions: 'small-swal-actions'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${productId}`).submit();
            }
        });
    }

    //For showing selected file input
    document.getElementById('csvFile').addEventListener('change', function(event) {
        let fileName = event.target.files[0] ? event.target.files[0].name : "Choose file";
        this.nextElementSibling.innerText = fileName;
    });

    //Importing CSV File
    $(document).ready(function() {
        $('.import-btn').click(function(event) {
            let element = $(this);
            event.preventDefault();

            let formData = new FormData($('#import-form')[0]);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: "{{ route('admin.products.import') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                before: function() {
                    element.removeClass('btn-warning').addClass('btn-info').text('<i class="fa-solid fa-gear fa-shake"></i>');
                },
                success: function(response) {
                    element.removeClass('btn-info').addClass('btn-warning').text('Import');
                    if (response.status == 1) {
                        $('#staticBackdrop').hide();
                        Swal.fire({
                            title: 'success',
                            text: "Products imported successfully.",
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: "An error occured.",
                            icon: 'error'
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Oops!',
                        text: "An error occured.",
                        icon: 'error'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        });
    });
</script>

@endsection