<nav class="nav">
    <ul class="nav__list container">

    <?php foreach ($categories_name as $key => $value) {  ?>

        <li class="nav__item">
          <a href="all-lots.html"><?php echo $value;?></a>
        </li>

    <?php } ?>

      <!-- <li class="nav__item">
        <a href="all-lots.html">Доски и лыжи</a>
      </li>
      <li class="nav__item">
        <a href="all-lots.html">Крепления</a>
      </li>
      <li class="nav__item">
        <a href="all-lots.html">Ботинки</a>
      </li>
      <li class="nav__item">
        <a href="all-lots.html">Одежда</a>
      </li>
      <li class="nav__item">
        <a href="all-lots.html">Инструменты</a>
      </li>
      <li class="nav__item">
        <a href="all-lots.html">Разное</a>
      </li> -->

    </ul>
  </nav>
  <form class="form form--add-lot container<?php echo ($errors) ? " form--invalid" : "" ?>" action="/add.php" method="post"  enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
      <div class="form__item<?php echo (isset($errors['lot-name'])) ? " form__item--invalid" : "" ?>"> <!-- form__item--invalid -->
        <label for="lot-name">Наименование</label>
        <?php $value = isset($lot['lot-name']) ? $lot['lot-name'] : ""; ?>
        <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?php echo $value;?>">
        <span class="form__error">Введите наименование лота</span>
      </div>
      <div class="form__item<?php echo (isset($errors['category'])) ? " form__item--invalid" : "" ?>">
        <label for="category">Категория</label>
        <?php $value = isset($lot['category']) ? $lot['category'] : ""; ?>
        <select id="category" name="category" >
          <option value="">Выберите категорию</option>

          <?php foreach ($categories as $cat) { ?>
            <?php if($cat['id'] == $value) { ?>
              <option selected value="<?php echo $cat['id'];?>"><?php echo $cat['name'];?></option>
            <?php } else { ?>
              <option value="<?php echo $cat['id'];?>"><?php echo $cat['name'];?></option>
            <?php } ?>
          <?php } ?>

        </select>
        <span class="form__error">Выберите категорию</span>
      </div>
    </div>
    <div class="form__item form__item--wide<?php echo (isset($errors['message'])) ? " form__item--invalid" : "" ?>">
      <label for="message">Описание</label>
      <?php $value = isset($lot['message']) ? $lot['message'] : ""; ?>
      <textarea id="message" name="message" placeholder="Напишите описание лота" ><?php echo $value;?></textarea>
      <span class="form__error">Напишите описание лота</span>
    </div>
    <div class="form__item form__item--file <?php echo (isset($errors['file'])) ? " form__item--invalid " : "" ?> <?php echo (isset($lot['lot_img'])) ? " form__item--uploaded" : "" ?>"> <!-- form__item--uploaded -->
      <label>Изображение</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="<?php echo $lot['lot_img'];?>" width="113" height="113" alt="Изображение лота">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" name="lot_img" value="<?php echo $lot['lot_img'];?>">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
      <span class="form__error"><?php echo $errors['file'];?></span>

    </div>
    <div class="form__container-three<?php echo (isset($errors['lot-rate'])) ? " form__item--invalid" : "" ?>">
      <div class="form__item form__item--small">
        <label for="lot-rate">Начальная цена</label>
        <?php $value = isset($lot['lot-rate']) ? $lot['lot-rate'] : "";?>
        <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?php echo $value;?>">
        <span class="form__error">Введите начальную цену</span>
      </div>
      <div class="form__item form__item--small<?php echo (isset($errors['lot-step'])) ? " form__item--invalid" : "" ?>">
        <label for="lot-step">Шаг ставки</label>
        <?php $value = isset($lot['lot-step']) ? $lot['lot-step'] : ""; ?>
        <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?php echo $value;?>">
        <span class="form__error">Введите шаг ставки</span>
      </div>
      <div class="form__item<?php echo (isset($errors['lot-date'])) ? " form__item--invalid" : "" ?>">
        <label for="lot-date">Дата окончания торгов</label>
        <?php $value = isset($lot['lot-date']) ? $lot['lot-date'] : ""; ?>
        <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?php echo $value;?>">
        <span class="form__error">Введите дату завершения торгов</span>
      </div>
    </div>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <button type="submit" class="button">Добавить лот</button>
  </form>
