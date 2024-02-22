        <header>
            <h1>Let’s order fresh items for you.</h1>
<?php   if (empty($user)) {?>
            <div>
                <a class="signup_btn" href="<?= base_url('signup') ?>">Signup</a>
                <a class="login_btn" href="<?= base_url('login') ?>">Login</a>
            </div>
<?php   } else { ?>
            <div class="dropdown show">
                <a class="btn btn-secondary dropdown-toggle profile_dropdown" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= base_url('assets/images/users/' . $user['image']) ?>" alt="#">
                    <span><?= $user['name'] ?></span>
                </a>
                <section class="dropdown-menu user_dropdown" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a>
                </section>
            </div>
<?php   } ?>
        </header>