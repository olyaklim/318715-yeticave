<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
    <ul class="promo__list">

        <?php foreach ($categories_id as $cat) { ?>

            <li class="promo__item">
                <a class="promo__link" href="all-lots.php?id_categ=<?php echo $cat['id'];?>"><?php echo htmlspecialchars($cat['name']);?></a>
            </li>

        <?php } ?>

    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
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
