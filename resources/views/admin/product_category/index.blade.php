@extends('layouts.user_type.auth')

@section('page_title', __('products list'))

@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Product Category</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">edit</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    @include('components.alert')
    <div class="d-flex justify-content-end mb-2">
      <button type="button" onclick="window.location.href='{{ route('admin.products.category.create') }}'"
        class="btn btn-primary sb-sidenav-dark border-0 text-white cust--btn">Add</button>
    </div>
    <div class="card mb-4">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-head-fixed text-nowrap">
            <thead>
              <tr align="center">
                <th>Sr.No.</th>
                <th>Category Name</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php $sr=1; @endphp
              @forelse($categories as $category)
              <tr align="center">
                <td>{{ $sr }}</td>
                <td>{{ $category->name }}</td>
                <td>{{ $category->cat_status == 1 ? 'Active' : 'Inactive' }}</td>
                <td>
                  <a href="{{ route('admin.products.category.edit', $category->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form id="delete-form-{{ $category->id }}" action="{{ route('admin.products.category.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $category->id }})">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              @php $sr++ @endphp
              @empty
              <tr>
                <td colspan="4">
                  <span>No Product Categories to show</span>
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->
    </div>
  </div>
  <!-- /.row -->
</div><!-- /.container-fluid -->
<!-- /.content-wrapper -->
@endsection
@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(categoryId) {
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
                document.getElementById(`delete-form-${categoryId}`).submit();
            }
        });
    }
</script>
@endsection