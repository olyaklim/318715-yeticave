  <nav class="nav">
    <ul class="nav__list container">

      <?php foreach ($categories_id as $cat) {  ?>

        <li class="nav__item">
          <a href="all-lots.php?id_categ=<?php echo $cat['id'];?>"><?php echo htmlspecialchars($cat['name']);?></a>
        </li>

      <?php } ?>

    </ul>
  </nav>

  <form class="form container<?php echo ($errors) ? " form--invalid" : "" ?>" action="/sign-up.php" method="post" enctype="multipart/form-data">
    <h2>Регистрация нового аккаунта</h2>
    <div class="form__item<?php echo (isset($errors['email'])) ? " form__item--invalid" : "" ?>">
      <label for="email">E-mail*</label>
      <?php $value = isset($user['email']) ? htmlspecialchars($user['email']) : ""; ?>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?php echo $value;?>">
      <span class="form__error"><?php echo($errors['email']);?></span>
    </div>
    <div class="form__item<?php echo (isset($errors['password'])) ? " form__item--invalid" : "" ?>">
      <label for="password">Пароль*</label>
      <?php $value = isset($user['password']) ? htmlspecialchars($user['password']) : ""; ?>
      <input id="password" type="text" name="password" placeholder="Введите пароль" value="<?php echo $value;?>">
      <span class="form__error">Введите пароль</span>
    </div>
    <div class="form__item<?php echo (isset($errors['name'])) ? " form__item--invalid" : "" ?>">
      <label for="name">Имя*</label>
      <?php $value = isset($user['name']) ? htmlspecialchars($user['name']) : ""; ?>
      <input id="name" type="text" name="name" placeholder="Введите имя" value="<?php echo $value;?>">
      <span class="form__error">Введите имя</span>
    </div>
    <div class="form__item<?php echo (isset($errors['message'])) ? " form__item--invalid" : "" ?>">
      <?php $value = isset($user['message']) ? htmlspecialchars($user['message']) : ""; ?>
      <label for="message">Контактные данные*</label>
      <textarea id="message" name="message" placeholder="Напишите как с вами связаться" ><?php echo $value;?></textarea>
      <span class="form__error">Напишите как с вами связаться</span>
    </div>
    <div class="form__item form__item--file form__item--last <?php echo (isset($errors['file'])) ? " form__item--invalid " : "" ?> <?php echo (isset($user['user_img'])) ? " form__item--uploaded" : "" ?>">
      <label>Аватар</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="<?php echo htmlspecialchars($user['user_img']);?>" width="113" height="113" alt="Ваш аватар">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" value="<?php echo htmlspecialchars($user['user_img']);?>" name="user_img">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
  </form>
