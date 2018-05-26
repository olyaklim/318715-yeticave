<nav class="nav">
    <ul class="nav__list container">

      <?php $name_category = ''; ?>

      <?php foreach ($categories_id as $cat) { ?>

        <?php
        $active_category = isset($active_category) ? $active_category : "";

        if ($active_category == $cat['id']) {
          $name_category = htmlspecialchars($cat['name']);
        }
        ?>

        <li class="nav__item <?php echo ($active_category == $cat['id']) ? " nav__item--current" : "" ?>">
          <a href="all-lots.php?id_categ=<?php echo $cat['id'];?>"><?php echo htmlspecialchars($cat['name']);?></a>
        </li>

      <?php } ?>

    </ul>
  </nav>
  <div class="container">
    <section class="lots">
      <h2>Все лоты в категории <span>«<?php echo $name_category;?>»</span></h2>
      <ul class="lots__list">

        <?php foreach ($lots as $key => $value) { ?>

        <li class="lots__item lot">
            <div class="lot__image">
                <img src="<?php echo htmlspecialchars($value['url_pictures']); ?>" width="350" height="260" alt="<?php echo htmlspecialchars($value['name']); ?>">
            </div>

            <div class="lot__info">
            <span class="lot__category"><?php echo htmlspecialchars($value['category']); ?></span>
                <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?php echo $value['id'];?>"><?php echo htmlspecialchars($value['name']); ?></a></h3>
                <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?php echo format_price($value['price']); ?></span>
                    </div>
                    <div class="lot__timer timer">
                        <?php echo get_lot_time_end($value['dt_end']); ?>
                    </div>
                </div>
            </div>
        </li>

        <?php } ?>

      </ul>
    </section>

    <?php if ($tpl_data['pages_count'] > 1) { ?>

      <ul class="pagination-list">

        <?php if ($tpl_data['cur_page'] > 1) { ?>
          <li class="pagination-item pagination-item-prev">
            <a href="/all-lots.php?id_categ=<?php echo $active_category;?>&page=<?php echo($tpl_data['cur_page'] - 1);?>">Назад</a>
          </li>
        <?php } else { ?>
          <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
        <?php } ?>

        <?php foreach ($tpl_data['pages'] as $page) { ?>

          <li class="pagination-item <?php if ($page == $tpl_data['cur_page']) { ?>pagination-item-active<?php } ?>">
            <a href="/all-lots.php?id_categ=<?php echo $active_category;?>&page=<?=$page;?>"><?=$page;?></a>
          </li>

        <?php } ?>

        <?php if ($tpl_data['cur_page'] <  count($tpl_data['pages'])) { ?>
           <li class="pagination-item pagination-item-next">
            <a  href="/all-lots.php?id_categ=<?php echo $active_category;?>&page=<?php echo($tpl_data['cur_page'] + 1);?>">Вперед</a>
          </li>

        <?php } else { ?>
         <li class="pagination-item pagination-item-next"><a>Вперед</a></li>
        <?php } ?>

      </ul>
    <?php } ?>

  </div>
