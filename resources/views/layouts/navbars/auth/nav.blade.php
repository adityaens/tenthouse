 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-white navbar-light">
   <!-- Left navbar links -->
   <ul class="navbar-nav">
     <li class="nav-item">
       <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
     </li>
     <!-- <li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li> -->
   </ul>

   <!-- Right navbar links -->
   <ul class="navbar-nav ml-auto">

     <!-- Messages Dropdown Menu -->
     <li class="nav-item dropdown">
       <a class="nav-link" data-toggle="dropdown" href="#">
         <i class="fa-solid fa-square-caret-down"></i>
       </a>
       <div class="dropdown-menu dropdown-menu-right">
         <a class="nav-link"  href="{{route('admin.edit_profile')}}">
           Profile
         </a>

         <div class="dropdown-divider"></div>
         <form action="{{ route('admin.logout') }}" method="post">
           @csrf
           <button type="submit" class="dropdown-item">LogOut</button>
         </form>
       </div>
       </div>
     </li>

   </ul>
 </nav>
 <!-- /.navbar -->