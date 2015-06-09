                <!-- sidebar: style can be found in sidebar.less -->
                <aside class="main-sidebar">
                <section class="sidebar">
                    <ul class="sidebar-menu" id="leftmenu-ul">
			
			<li <?php if(stripos($_SERVER['REQUEST_URI'],"dashboard.php")) echo 'class="active"'; ?>>
                            <a href="dashboard.php">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
			
						
			<li <?php if(stripos($_SERVER['REQUEST_URI'],"add_school.php")) echo 'class="active"'; ?> >
                            <a href="add_school.php" >
                                <i class="fa fa-building-o" ></i> <span>Add School</span>
                            </a>
                        </li>
                        
                        <li <?php if(stripos($_SERVER['REQUEST_URI'],"add_school_admin.php")) echo 'class="active"'; ?> >
                            <a href="add_school_admin.php" >
                                <i class="fa fa-user" ></i> <span>Add School Admin</span>
                            </a>
                        </li>
                        
                        			
			<li <?php if(stripos($_SERVER['REQUEST_URI'],"list_schools.php")) echo 'class="active"'; ?> >
                            <a href="list_schools.php" >
                                <i class="fa fa-bars" ></i> <span>Schools</span>
                            </a>
                        </li>				
               
                       
                      
						
                    </ul>
		</section>								
                </aside>