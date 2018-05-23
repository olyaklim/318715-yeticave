<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $key => $value) {  ?>

            <li class="nav__item">
                <a href="all-lots.html"><?php echo $value;?></a>
            </li>

        <?php } ?>
    </ul>
</nav>

<form class="form container <?php echo ($errors) ? " form--invalid" : "" ?>" action="" method="post"> <!-- form--invalid -->
<h2>Вход</h2>
<div class="form__item<?php echo (isset($errors['email'])) ? " form__item--invalid" : "" ?>"> <!-- form__item--invalid -->
  <label for="email">E-mail*</label>
  <?php $value = isset($form['email']) ? $form['email'] : ""; ?>
  <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?php echo $value;?>">
  <span class="form__error"><?php echo $errors['email'];?></span>
</div>
<div class="form__item form__item--last<?php echo (isset($errors['password'])) ? " form__item--invalid" : "" ?>">
  <label for="password">Пароль*</label>
  <?php $value = isset($form['email']) ? $form['password'] : ""; ?>
  <input id="password" type="text" name="password" placeholder="Введите пароль" value="<?php echo $value;?>">
  <span class="form__error"><?php echo $errors['password'];?></span>
</div>
<button type="submit" class="button">Войти</button>
</form>
