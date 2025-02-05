 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-light bg-light">
   <!-- Left navbar links -->
   <ul class="navbar-nav">
     <li class="nav-item">
       <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
     </li>
   </ul>

   <!-- Right navbar links -->
   <ul class="navbar-nav ms-auto">

     <!-- Dropdown Menu -->
     <li class="nav-item dropdown">
       <a class="nav-link" data-bs-toggle="dropdown" href="#">
         <i class="fa-solid fa-square-caret-down"></i>
       </a>
       <ul class="dropdown-menu dropdown-menu-end">
         <li>
           <a class="dropdown-item" href="{{route('admin.edit_profile')}}">
             Profile
           </a>
         </li>
         <li><hr class="dropdown-divider"></li>
         <li>
           <form action="{{ route('admin.logout') }}" method="post">
             @csrf
             <button type="submit" class="dropdown-item">LogOut</button>
           </form>
         </li>
       </ul>
     </li>

   </ul>
 </nav>

 <!-- /.navbar -->