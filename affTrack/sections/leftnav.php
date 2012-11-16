
      <div id="sidebar">
        <ul class="sideNav">
          <li><a href="index.php" <?PHP if(!isset($_GET['section'])) echo 'class="active"'; ?>>Dashboard</a></li>
          <li><a href="?section=users" <?PHP if($_GET['section'] == 'users') echo 'class="active"'; ?>>User Interactions</a></li>
          <li><a href="?section=usersignups" <?PHP if($_GET['section'] == 'usersignups') echo 'class="active"'; ?>>User Signups</a></li>
          <li><a href="?section=purchases" <?PHP if($_GET['section'] == 'purchases') echo 'class="active"'; ?>>Purchases</a></li>
          <li><a href="?section=changepass" <?PHP if($_GET['section'] == 'changepass') echo 'class="active"'; ?>>Change Password</a></li>
        </ul>
        <!-- // .sideNav -->
      </div>
      <!-- // #sidebar -->
