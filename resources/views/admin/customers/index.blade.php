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
                    <h1 class="m-0">Customers List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Customers</li>
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
                <button type="button" onclick="window.location.href='{{ route('admin.user.create') }}'"
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
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ __('Name') }}</label>
                                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name"
                                        value="{{ request()->get('name') }}">
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="group">{{ __('Group') }}</label>
                                        <div class="d-flex">
                                            @foreach($groups as $key => $group)
                                            <div class="form-check mr-2">
                                                <input class="form-check-input"
                                                    name="group[]"
                                                    type="checkbox"
                                                    id="inlineCheckbox{{$key}}"
                                                    value="{{$key}}"
                                                    {{ is_array(request()->get('group')) && in_array($key, request()->get('group')) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="inlineCheckbox{{$key}}">{{$group}}</label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @error('group')
                                        <span class="error invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>


                                {{--
                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ __('Status') }}</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            --}}
                            <div class="col-md-6 mb-3">
                                <label for="created_on" class="form-label">{{ __('Created At') }}</label>
                                <input type="date" class="form-control" name="created_on" id="created_on"
                                    value="{{ request()->get('created_on') }}">
                            </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">{{ __('Search') }}</button>
                            <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">{{ __('Reset') }}</a>
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
                                <th>Mobile</th>
                                <th>Group</th>
                           
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $key => $user)

                            <tr>
                                <td>{{ $user->name ?? '' }}</td>
                                <td>{{ $user->mobile ?? '' }}</td>
                                <td>
                                    @foreach($user->groups as $groupName)
                                    {{$groupName->name ?? ''}}{{ $loop->last ? '' : ', ' }}
                                    @endforeach
                                </td>
                                <td>{{$user->created_at ?? ''}}</td>

                                <td>
                                    <!-- Edit Button -->
                                    <a href="{{route('admin.user.edit',['id'=>$user->userId])}}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Delete Button -->
                                    <form id="delete-form-{{ $user->userId }}" action="{{ route('admin.user.destroy', ['id' => $user->userId]) }}" method="POST" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $user->userId }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7"><span class="">No Customer to show</span></td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @if ($users->count() > 0)
                    <div class="pagination_wrapper">
                        {{ $users->appends(request()->query())->links() }}
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