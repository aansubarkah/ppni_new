<style>
.header h3, h5, p {
    text-align: center;
}

.header p {
    font-size: 90%;
}

.header h5 {
    border-bottom: 3px solid black;
}

.letter table {
    border-collapse: collapse;
    width: 100%;
    vertical-align: text-top;
    text-align: justify;
    text-justify: inter-word;
}

.letter table, th, td {
    border: 1px solid black;
}

.left {
    width: 30%;
    font-weight: bold;
}

.middle {
    width: 30%;
}

.right {
    width: 70%;
}

.disposition table {
    border-collapse: collapse;
    width: 100%;
    vertical-align: text-top;
}

.disposition table, th, td {
    border: 1px solid black;
}
.section1 {
    width: 20%;
    font-weight: bold;
    text-align: center;
    height: 35px;
}

.section2 {
    width: 60%;
    font-weight: bold;
    text-align: center;
    height: 25px;
}
</style>
<div class="header">
    <h3>
    DEWAN PENGURUS WILAYAH<br>
    PERSATUAN PERAWAT NASIONAL INDONESIA<br>
    PROVINSI JAWA TIMUR
    </h3>
    <p>
    Ruko Gateway Blok B 25 Waru-Sidoarjo, Telp (031) 8546954, Fax. 8546955, Email: ppni_jatim@yahoo.co.id
    </p>
    <h5>
    LEMBAR DISPOSISI
    </h5>
</div>
<div class="letter">
    <table>
        <tr>
            <td class="left">SURAT DARI</td>
            <td class="right" colspan="2"><?php echo $letter['sender']['name']; ?></td>
        </tr>
        <tr>
            <td class="left">NOMOR SURAT</td>
            <td class="right" colspan="2"><?php echo $letter['number']; ?></td>
        </tr>
        <tr>
            <td class="left">PERIHAL</td>
            <td class="right" colspan="2"><?php echo $letter['content']; ?></td>
        </tr>
        <tr>
            <td class="left">TANGGAL SURAT</td>
            <td class="right" colspan="2">
<?php
echo $this->Time->format(
    $letter['date'],
    'dd MMM yyyy',
    'Asia/Jakarta',
    null
);
?>
            </td>
        </tr>
        <tr>
            <td class="left">DITERIMA TANGGAL</td>
            <td class="middle">
<?php
echo $this->Time->format(
    $letter['created'],
    'dd MMM yyyy',
    null,
    null
);
?>
            </td>
            <td class="left">NOMOR AGENDA:</td>
        </tr>
        <tr>
            <td class="left">DIKIRIM VIA</td>
            <td class="right" colspan="2"><?php echo $letter['via']['name']; ?></td>
        </tr>
    </table>
</div>
<br>
<div class="disposition">
    <table>
        <tr>
            <td class="section1">TANGGAL</td>
            <td class="section2">ISI DISPOSISI</td>
            <td class="section1">PARAF</td>
        </tr>
        <tr>
            <td class="section1"></td>
            <td class="section2"></td>
            <td class="section1"></td>
        </tr>
        <tr>
            <td class="section1"></td>
            <td class="section2"></td>
            <td class="section1"></td>
        </tr>
        <tr>
            <td class="section1"></td>
            <td class="section2"></td>
            <td class="section1"></td>
        </tr>
    </table>
</div>
