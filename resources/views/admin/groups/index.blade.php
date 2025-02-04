@extends('layouts.user_type.auth')

@section('page_title', __('Products List'))
<style>
    .small-swal {
        max-width: 350px !important;
        padding: 1rem !important;
    }
    .small-swal-title {
        font-size: 16px !important; /* Adjust title font size */
    }
    .small-swal-content {
        font-size: 14px !important; /* Adjust content font size */
    }
    .small-swal-actions .swal2-confirm,
    .small-swal-actions .swal2-cancel {
        font-size: 14px !important; /* Adjust button font size */
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
                    <h1 class="m-0">Groups List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Groups</li>
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
                <button type="button" onclick="window.location.href='{{ route('admin.products.groups.create') }}'"
                    class="btn btn-primary sb-sidenav-dark border-0 text-white cust--btn">Add</button>
            </div>
            
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
                                <div class="col-md-6 mb-3">
                                    <label for="discount" class="form-label">{{ __('Discount(%)') }}</label>
                                    <input type="text" class="form-control" name="discount" id="discount"
                                        placeholder="{{ __('Discount') }}" maxlength="150" value="{{ request()->get('discount') ?? '' }}"
                                        autocomplete="off">
                                </div>   
                                <!-- Filter by Status -->
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }}</label>
                                    <select name="status" id="status" class="form-control select2">
                                        <option value="">{{ __('Select Status') }}</option>
                                        <option {{ request()->get('status') !== null && request()->get('status') == 1 ? 'selected' : '' }} value="1">{{ __('Active') }}</option>
                                        <option {{ request()->get('status') !== null && request()->get('status') == 0 ? 'selected' : '' }} value="0">{{ __('Inactive') }}</option>

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
                                    <button type="button" class="btn btn-secondary reset-btn" onclick="window.location.href='{{ route('admin.products.groups.index') }}'">{{ __('Reset') }}</button>
                                </div>
                            </div>
                        </form>

                        <hr />
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>                                    
                                    <th>Discount(%)</th>                                   
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groups as $key => $group)
                                <tr>
                                    <td>{{ $group->name ?? '' }}</td>                                   
                                    <td>{{ $group->discount ?? '' }}</td>                                   
                                    <td>
                                        <span
                                            class="p-2 badge {{ $group->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $group->status ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ date('Y-m-d', strtotime($group->created_at)) }}</td>
                                    <td>
                                        <!-- Edit Button -->
                                        <a href="{{ route('admin.products.groups.edit', ['id' => $group->id]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Delete Button -->
                                        <form id="delete-form-{{ $group->id }}" action="{{ route('admin.products.groups.destroy', ['id' => $group->id]) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $group->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                       
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7"><span class="">No Groups to show</span></td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if ($groups->count() > 0)
                        <div class="pagination_wrapper">
                            {{ $groups->onEachSide(PER_PAGE)->withQueryString()->links() }}
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
<!-- /.content-wrapper -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(groupId) {
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
                document.getElementById(`delete-form-${groupId}`).submit();
            }
        });
    }
</script>

@endsection