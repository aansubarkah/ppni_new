Nomor Surat: <strong><?php echo $letter['number']; ?></strong><br>
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
Disposisi Dari: <strong><?php echo $disposition['user']['fullname']; ?></strong><br>
Isi: <strong><?php echo $disposition['content']; ?></strong><br>
Tanggal: <strong>
<?php
echo $this->Time->format(
    $disposition['created'],
    'dd MMM yyyy',
    null,
    null
);
?>
</strong><br>
Link: <?php echo $this->Url->build('/letters/view/' . $letter['id'], true); ?>
