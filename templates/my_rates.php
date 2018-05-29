<nav class="nav">
    <ul class="nav__list container">

        <?php foreach ($categories as $cat) {  ?>

            <li class="nav__item">
                <a href="all-lots.php?id_categ=<?php echo $cat['id'];?>"><?php echo htmlspecialchars($cat['name']);?></a>
            </li>

        <?php } ?>

    </ul>
  </nav>

<section class="lots">
    <div class="lots__header">
        <h2>Список моих ставок</h2>
    </div>

    <div class="history">
    <table class="history__list">

    <?php foreach ($table_rates as $value) {  ?>

        <?php $win_lot = in_array($value['lot_id'], $user_win_lots ); ?>

        <tr class="history__item <?php echo ($win_lot) ? "history__item--win" : "" ?>">
            <td class="history__name">
                <a class="text-link" href="lot.php?id=<?php echo $value['lot_id'];?>">
                    <?php echo htmlspecialchars($value['name_lot']); ?>
                </a>
            </td>
            <td class="history__name"><?php echo htmlspecialchars($value['price_user']);?></td>
            <td class="history__price"><?php echo htmlspecialchars($value['dt_registration']); ?></td>

            <td class="history__time">
                <?php if($win_lot) { ?>
                    <?php echo htmlspecialchars($value['user_contact']); ?>
                <?php } ?>
            </td>

        </tr>

    <?php } ?>

  </table>

    </div>
</section>
