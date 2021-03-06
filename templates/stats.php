<?php $page_js = array('jquery.js', 'jquery.flot.js') ?>
<?php include Template::file('layout/header') ?>

<p><a href="/">Feed List</a></p>

<?php if ( isset($data['high']) ) : ?>
<h1>High Frequency Updates (15+ / day)</h1>
<div id="high_feeds" style="width:1400px;height:600px"></div>
<?php endif ?>

<?php if ( isset($data['med']) ) : ?>
<h1>Updated Daily (1.5 / day)</h1>
<div id="med_feeds" style="width:1400px;height:600px"></div>
<?php endif ?>

<?php if ( isset($data['low']) ) : ?>
<h1>Regularly Updated (0.8 / day)</h1>
<div id="low_feeds" style="width:1400px;height:600px"></div>
<?php endif ?>

<?php if ( isset($data['rare']) ) : ?>
<h1>Rarely Updated</h1>
<div id="rare_feeds" style="width:1400px;height:600px"></div>
<?php endif ?>

<script type="text/javascript">
$(document).ready(function(){
    var options = { xaxis: { ticks: [ <?php echo $labels ?> ]}};
    <?php if ( isset($data['high']) ): ?>
    var high_data = [
        <?php foreach ( $data['high'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    <?php endif ?>
    <?php if ( isset($data['med']) ): ?>
    var med_data = [
        <?php foreach ( $data['med'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    <?php endif ?>
    <?php if ( isset($data['low']) ) : ?>
    var low_data = [
        <?php foreach ( $data['low'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    <?php endif ?>
    <?php if ( isset($data['rare']) ) : ?>
    var rare_data = [
        <?php foreach ( $data['rare'] as $f ) : ?>
        { label: '<?php echo $f['label'] ?>', data: [ <?php echo $f['points'] ?> ] },
        <?php endforeach ?>
    ];
    <?php endif ?>

    <?php if ( isset($data['high']) ) : ?>
    $.plot('#high_feeds', high_data, options);
    <?php endif ?>
    <?php if ( isset($data['med']) ) : ?>
    $.plot('#med_feeds', med_data, options);
    <?php endif ?>
    <?php if ( isset($data['low']) ) : ?>
    $.plot('#low_feeds', low_data, options);
    <?php endif ?>
    <?php if ( isset($data['rare']) ) : ?>
    $.plot('#rare_feeds', rare_data, options);
    <?php endif ?>
});
</script>

<?php include Template::file('layout/footer') ?>
