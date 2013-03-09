<?php $page_js = array('jquery.js', 'jquery.flot.js') ?>
<?php include 'header.layout.php' ?>

<p><a href="/">Feed List</a></p>

<h1>Database</h1>
<ul>
    <li><strong>File Size:</strong> <?php echo $db_size ?></li>
</ul>

<h1>High Frequency Updates (15+ / day)</h1>
<div id="high_feeds" style="width:1400px;height:600px"></div>

<h1>Updated Daily (1.5 / day)</h1>
<div id="med_feeds" style="width:1400px;height:600px"></div>

<h1>Regularly Updated (0.8 / day)</h1>
<div id="low_feeds" style="width:1400px;height:600px"></div>

<h1>Rarely Updated</h1>
<div id="rare_feeds" style="width:1400px;height:600px"></div>

<script type="text/javascript">
$(document).ready(function(){
    var options = { xaxis: { ticks: [ <?php echo $labels ?> ]}};
    var high_data = [
        <?php foreach ( $data['high'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    var med_data = [
        <?php foreach ( $data['med'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    var low_data = [
        <?php foreach ( $data['low'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    var rare_data = [
        <?php foreach ( $data['rare'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];

    $.plot('#high_feeds', high_data, options);
    $.plot('#med_feeds', med_data, options);
    $.plot('#low_feeds', low_data, options);
    $.plot('#rare_feeds', rare_data, options);
});
</script>

<?php include 'footer.layout.php' ?>
