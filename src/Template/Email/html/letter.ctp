Nomor: <strong><?php echo $letter['number']; ?></strong><br>
Tanggal: <strong>
<?php
echo $this->Time->format(
    $letter['date'],
    'dd MMM yyyy',
    null,
    null
);
?>
</strong><br>
Pengirim: <strong><?php echo $letter['sender']['name']; ?></strong><br>
Perihal: <strong><?php echo $letter['content']; ?></strong><br>
Link: <?php echo $this->Url->build('/letters/view/' . $letter['id'], true); ?>
