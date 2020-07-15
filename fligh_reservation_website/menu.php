<nav class="navbar bg-light">
    <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">HOME</a></li>
        <?php
        if(!is_logged()){
            echo "<li class='nav-item'><a class='nav-link' href='register.php'>REGISTRAZIONE</a></li>";
            echo "<li class='nav-item'><a class='nav-link' href='login.php'>LOGIN</a></li>";
        }else{
            echo "<li class='nav-item'><a class='nav-link' href='logout.php'>LOGOUT</a></li>";
        }
        ?>
    </ul>
</nav>
