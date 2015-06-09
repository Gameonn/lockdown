                <!-- sidebar: style can be found in sidebar.less -->
                <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu" id="leftmenu-ul">
						<li <?php if(stripos($_SERVER['REQUEST_URI'],"dashboard.php")) echo 'class="active"'; ?>>
                            <a href="dashboard.php">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
			
						
						<li <?php if(stripos($_SERVER['REQUEST_URI'],"add_user.php")) echo 'class="active"'; ?> >
                            <a href="add_user.php" >
                                <i class="fa fa-user" ></i> <span>Add User</span>
                            </a>
                        </li>
                       
                       <li <?php if(stripos($_SERVER['REQUEST_URI'],"school.php")) echo 'class="active"'; ?> >
                            <a href="school.php" >
                                <i class="fa fa-building-o" ></i> <span>School</span>
                            </a>
                        </li>
                        
                        			
		<!-- <li <?php if(stripos($_SERVER['REQUEST_URI'],"edit_school.php")) echo 'class="active"'; ?> >
                            <a href="edit_school.php" >
                                <i class="fa fa-edit" ></i> <span>Edit School</span>
                            </a>
                        </li> -->
                        
                        <li <?php if(stripos($_SERVER['REQUEST_URI'],"lockdown.php")) echo 'class="active"'; ?> >
                            <a href="lockdown.php" >
                                <i class="fa fa-unlock" ></i> <span>Lockdown</span>
                            </a>
                        </li>
                        			
			<li <?php if(stripos($_SERVER['REQUEST_URI'],"users.php")) echo 'class="active"'; ?> >
                            <a href="users.php" >
                                <i class="fa fa-users" ></i> <span>Users</span>
                            </a>
                        </li>			  			
                </ul>
		</section>								
                </aside>