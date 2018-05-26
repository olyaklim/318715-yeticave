<nav class="nav">
    <ul class="nav__list container">

      <?php foreach ($categories_id as $cat) {  ?>

        <li class="nav__item">
          <a href="all-lots.php?id_categ=<?php echo $cat['id'];?>"><?php echo htmlspecialchars($cat['name']);?></a>
        </li>

      <?php } ?>

    </ul>
  </nav>
  <section class="lot-item container">
    <h2><?php echo htmlspecialchars($lots['name']); ?></h2>
    <div class="lot-item__content">
      <div class="lot-item__left">
        <div class="lot-item__image">
          <img src="<?php echo htmlspecialchars($lots['url_pictures']); ?>" width="730" height="548" alt="<?php echo htmlspecialchars($lots['name']); ?>">
        </div>
        <p class="lot-item__category">Категория: <span><?php echo htmlspecialchars($lots['category']); ?></span></p>
        <p class="lot-item__description"><?php echo htmlspecialchars($lots['description']); ?></p>
      </div>
      <div class="lot-item__right">

       <?php if (isset($_SESSION['user'])) { ?>

        <div class="lot-item__state">
          <div class="lot-item__timer timer">
            <?php echo get_lot_time_end($lots['dt_end']); ?>
          </div>
          <div class="lot-item__cost-state">
            <div class="lot-item__rate">
              <span class="lot-item__amount">Текущая цена</span>
              <span class="lot-item__cost"><?php echo format_price($price_lot); ?></span>
            </div>
            <div class="lot-item__min-cost">
              Мин. ставка <span><?php echo $min_price; ?> р</span>
            </div>
          </div>


          <?php if ($rate_visible) { ?>
          <form class="lot-item__form <?php echo ($errors) ? "form--invalid" : "" ?>" action="" method="post">
            <p class="lot-item__form-item <?php echo (isset($errors['cost'])) ? "form__item--invalid" : "" ?>">
              <label for="cost">Ваша ставка</label>
              <?php $value = isset($rate['cost']) ? htmlspecialchars($rate['cost']) : ""; ?>
              <input id="cost" type="number" name="cost" placeholder="12 000" value="<?php echo $value;?>">
              <span class="form__error"><?php echo $errors['cost'];?></span>
            </p>
            <button type="submit" class="button">Сделать ставку</button>
          </form>
          <?php } ?>

        </div>

       <?php } ?>

        <div class="history">
          <h3>История ставок (<span><?php echo count($table_rates);?></span>)</h3>
          <table class="history__list">

          <?php foreach ($table_rates as $value) {  ?>

              <tr class="history__item">
                  <td class="history__name"><?php echo htmlspecialchars($value['name_user']);?></td>
                  <td class="history__price"><?php echo format_price($value['price_user']); ?></td>
                  <td class="history__time"><?php echo get_rate_time($value['dt_registration']); ?></td>
              </tr>

          <?php } ?>

          </table>
        </div>
      </div>
    </div>
  </section>
