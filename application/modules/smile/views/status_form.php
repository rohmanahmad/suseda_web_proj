<div>
    <b>Status with smiley</b>
</div>
<div class="left">
    <?=img('assets/images/icons/tara_sita.gif')?>
</div>
<div>
    <?=form_open()?>
    <input type="text" name="status_text" id="status_text" value=""/>
    <input type="submit" value="Update" name="submit"/>
    <?=form_close()?>
    <?=$smiley_table?>
</div>
<div class="status_result">
    <div class="left big">
        <?=img('assets/images/icons/tara_sita_big.jpg')?>
    </div>
    <div class="left buble">
        <?=$status_result?>
    </div>
</div>