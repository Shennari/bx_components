<?php if (!empty($arResult['TEXT']) && !$arResult['COOKIE']):?>
<div class="slim-banner">
  <div class="slim-banner__inner">
    <p class="slim-banner__text"><?php echo $arResult['TEXT']?></p>
    <div class="icon icon_type_cross icon_size_xs">
      <svg viewBox="0 0 20 20" id="cross">
        <g stroke-width="1" fill-rule="evenodd" stroke-linecap="square">
          <path d="M0.1,19.9 L19.9,0.1"></path>
          <path d="M0.1,0.1 L19.9,19.9"></path>
        </g>
      </svg>
    </div>
  </div>
</div>
<?php endif;?>