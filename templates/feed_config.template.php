<?php $page_css = array('reader.less', 'feed_config.less') ?>
<?php $page_js = array('less.js', 'jquery.js') ?>
<?php include 'header.layout.php' ?>

<div id="container">
    <div id="header">
        <h1>RSS Feeds</h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a href="/">Feeds</a></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <table id='feed-table'>
            <tr>
                <th colspan="2">RSS Feed</th>
                <th>Folder</th>
                <th>Sorting</th>
                <th>Update</th>
                <th>Display</th>
            </tr>
            <?php $row = 0; foreach ( $feeds as $feed ) : $row ++ ?>
            <tr class="<?php echo $row % 2 == 0 ? 'even' : 'odd' ?>">
                <td id="td-active">
                    <input id="chk-active-<?php echo $feed->id ?>" class="chk-active" type="checkbox"<?php echo $feed->active ? ' checked="checked"' : '' ?> />
                </td>
                <td id="td-name-url"><span class="name"><?php echo $feed->name ?></span><br/>
                <a href="<?php echo $feed->url ?>" class="url"><?php echo $feed->url ?></a></td>
                <td id="td-folder">
                    <select id="sel-folder-<?php echo $feed->id ?>" class="sel-folder">
                        <option value="none">None</option>
                        <?php foreach ( $folders as $folder ) : ?>
                        <option value="<?php echo $folder->id ?>"<?php echo $feed->folder_id==$folder->id ? ' selected="selected"' : '' ?>><?php echo $folder->name ?></option>
                        <?php endforeach ?>
                    </select>
                </td>
                <td id="td-sorting">
                    <select id="sel-sorting-<?php echo $feed->id ?>" class="sel-sorting">
                        <option value="false" <?php echo !$feed->newest_first ? ' selected="selected"' : '' ?>>Ascending</option>
                        <option value="true" <?php echo $feed->newest_first ? ' selected="selected"' : '' ?>>Descending</option>
                    </select>
                </td>
                <td id="td-update">
                    <select id="sel-update-<?php echo $feed->id ?>" class="sel-update">
                        <option value="10"<?php echo $feed->update_freq==10 ? ' selected="selected"' : '' ?>>10 mins</option>
                        <option value="30"<?php echo $feed->update_freq==30 ? ' selected="selected"' : '' ?>>30 mins</option>
                        <option value="60"<?php echo $feed->update_freq==60 ? ' selected="selected"' : '' ?>>1 hr</option>
                        <option value="120"<?php echo $feed->update_freq==120 ? ' selected="selected"' : '' ?>>2 hrs</option>
                        <option value="240"<?php echo $feed->update_freq==240 ? ' selected="selected"' : '' ?>>4 hrs</option>
                        <option value="360"<?php echo $feed->update_freq==360 ? ' selected="selected"' : '' ?>>6 hrs</option>
                        <option value="480"<?php echo $feed->update_freq==480 ? ' selected="selected"' : '' ?>>8 hrs</option>
                        <option value="720"<?php echo $feed->update_freq==720 ? ' selected="selected"' : '' ?>>12 hrs</option>
                        <option value="1440"<?php echo $feed->update_freq==1440 ? ' selected="selected"' : '' ?>>1 day</option>
                    </select>
                </td>
                <td id="td-paging">
                    <select id="sel-page-<?php echo $feed->id ?>" class="sel-page">
                        <option value="10"<?php echo $feed->per_page==10 ? ' selected="selected"' : '' ?>>10</option>
                        <option value="20"<?php echo $feed->per_page==20 ? ' selected="selected"' : '' ?>>20</option>
                        <option value="40"<?php echo $feed->per_page==40 ? ' selected="selected"' : '' ?>>40</option>
                        <option value="50"<?php echo $feed->per_page==50 ? ' selected="selected"' : '' ?>>50</option>
                        <option value="60"<?php echo $feed->per_page==60 ? ' selected="selected"' : '' ?>>60</option>
                        <option value="100"<?php echo $feed->per_page==100 ? ' selected="selected"' : '' ?>>100</option>
                    </select>
                </td>
            </tr>
            <?php endforeach ?>
        </table>
    </div><!-- content -->
    <div id="footer">
        &copy; 2013 AxiSym3.net All Right Reserved
    </div><!-- footer -->
</div><!-- container -->

<script type="text/javascript">
$(".chk-active").click(function() {
    var id = this.id.substr(11);
    var checkbox = $(this);
    var newValue = checkbox.prop('checked');

    checkbox.hide();
    checkbox.after('<img id="busy-'+ id +'" src="/static/images/busy.gif" />');

    $.ajax({
        url: "/feedConfig",
        type: "POST",
        dataType: 'json',
        data: JSON.stringify({ id: id, op: 'active', value: newValue })

    }).fail(function() {
        checkbox.prop('checked', !newValue);
        alert("Unable to update feed");

    }).always(function() {
        $("#busy-"+id).remove();
        checkbox.show();
    });
});
$(".sel-sorting").change(function() {
    var id = this.id.substr(12);
    var select = $(this);
    var newValue = $(select).children(':selected').first().val();

    select.hide();
    select.after('<img id="busy-'+ id +'" src="/static/images/busy.gif" />');

    $.ajax({
        url: "/feedConfig",
        type: "POST",
        dataType: 'json',
        data: JSON.stringify({ id: id, op: 'sort', value: (newValue == "true") })

    }).fail(function() {
        $(select).children(':selected').removeProp('selected');
        $(select).children().each(function(idx, option) {
            if (option.value != newValue) {
                $(option).prop('selected', 'selected');
            }
        });
        alert("Unable to update feed");

    }).always(function() {
      $("#busy-"+id).remove();
      select.show();
    });
});
$(".sel-update").one('focus', function() {
    var select = $(this);
    select.data('previous', select.children(':selected').first().val());

}).change(function() {
    var id = this.id.substr(11);
    var select = $(this);
    var prevValue = select.data('previous');
    var newValue = select.children(':selected').first().val();

    select.hide();
    select.after('<img id="busy-'+ id +'" src="/static/images/busy.gif" />');

    $.ajax({
        url: "/feedConfig",
        type: "POST",
        dataType: 'json',
        data: JSON.stringify({ id: id, op: 'update', value: newValue })

    }).fail(function() {
        $(select).children(':selected').removeProp('selected');
        $(select).children().each(function(idx, option) {
            if (option.value == prevValue) {
                $(option).prop('selected', 'selected');
            }
        });
        alert("Unable to update feed");

    }).always(function() {
      $("#busy-"+id).remove();
      select.show();
    });
});
$(".sel-page").one('focus', function() {
    var select = $(this);
    select.data('previous', select.children(':selected').first().val());

}).change(function() {
    var id = this.id.substr(9);
    var select = $(this);
    var prevValue = select.data('previous');
    var newValue = select.children(':selected').first().val();

    select.hide();
    select.after('<img id="busy-'+ id +'" src="/static/images/busy.gif" />');

    $.ajax({
        url: "/feedConfig",
        type: "POST",
        dataType: 'json',
        data: JSON.stringify({ id: id, op: 'page', value: newValue })

    }).fail(function() {
        $(select).children(':selected').removeProp('selected');
        $(select).children().each(function(idx, option) {
            if (option.value == prevValue) {
                $(option).prop('selected', 'selected');
            }
        });
        alert("Unable to update feed");

    }).always(function() {
      $("#busy-"+id).remove();
      select.show();
    });
});
$(".sel-folder").one('focus', function() {
    var select = $(this);
    select.data('previous', select.children(':selected').first().val());

}).change(function() {
    var id = this.id.substr(11);
    var select = $(this);
    var prevValue = select.data('previous');
    var newValue = select.children(':selected').first().val();

    select.hide();
    select.after('<img id="busy-'+ id +'" src="/static/images/busy.gif" />');

    $.ajax({
        url: "/feedConfig",
        type: "POST",
        dataType: 'json',
        data: JSON.stringify({ id: id, op: 'folder', value: newValue })

    }).fail(function() {
        $(select).children(':selected').removeProp('selected');
        $(select).children().each(function(idx, option) {
            if (option.value == prevValue) {
                $(option).prop('selected', 'selected');
            }
        });
        alert("Unable to update feed");

    }).always(function() {
      $("#busy-"+id).remove();
      select.show();
    });
});
</script>

<?php
function formatUpdateInterval($mins) {
    $str = "";
    if ( $mins >= 1440 ) {
        $days = intval($mins / 1440);
        $mins = $mins % 1440;
        $str .= "${days} day";
        if ( $days > 1 ) {
            $str .= 's';
        }
    }
    if ( $mins >= 60 ) {
        $hrs = intval($mins / 60);
        $mins = $mins % 60;
        $str .= " ${hrs} hr";
        if ( $hrs > 1 ) {
            $str .= 's';
        }
    }
    if ( $mins != 0 ) {
        $str .= " ${mins} min";
        if ( $mins > 1 ) {
            $str .= 's';
        }
    }
    return $str;
}
?>

<?php include 'footer.layout.php' ?>
