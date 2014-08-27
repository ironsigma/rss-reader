<?php $page_css = array('reader.less', 'feed_config.less') ?>
<?php $page_js = array('less.js', 'jquery.js', 'url-validate.js') ?>
<?php include Template::file('layout/header') ?>

<div id="container">
    <div id="header">
        <h1>RSS Feeds</h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a href="/">Feed List</a></li>
            <li><a href="/folderConfig">Folders</a></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <table id='feed-table'>
            <tr>
                <th><img id="add-rss" src="/static/images/add-rss.png" width="24" /></th>
                <th>RSS Feed</th>
                <th>Folder</th>
                <th>Update</th>
                <th>Display</th>
            </tr>
            <tr id="tr-new-rss" class="even">
                <td>&nbsp;</td>
                <td id="td-new-name" colspan="7">
                    <label for="name">Name:</label> <input id="new-rss-name" name="name" type="text"/></br/>
                    <label for="url">URL:</label> <input id="new-rss-url" name="url" type="text"/>
                    <input id="new-rss-button" type="button" value="Add Feed"/>
                </td>
            </tr>
            <?php $row = 0; foreach ( $feeds as $feed ) : $row ++ ?>
            <tr class="<?php echo $row % 2 == 0 ? 'even' : 'odd' ?>">
                <td class="td-active">
                    <input id="chk-active-<?php echo $feed->id ?>" class="chk-active" type="checkbox"<?php echo $feed->active ? ' checked="checked"' : '' ?> />
                </td>
                <td class="td-name-url">
                    <span id="name-<?php echo $feed->id ?>" class="name"><?php echo $feed->name ?></span><br/>
                    <img id="delete-<?php echo $feed->id ?>" class="delete" src="/static/images/delete.png" />
                    <a href="<?php echo $feed->url ?>" class="url"><?php echo TemplateUtil::abbr($feed->url) ?></a>
                </td>
                <td class="td-folder">
                    <select id="sel-folder-<?php echo $feed->id ?>" class="sel-folder">
                        <option value="none">None</option>
                        <?php foreach ( $folders as $folder ) : ?>
                        <option value="<?php echo $folder->id ?>"<?php echo $feed->folder_id==$folder->id ? ' selected="selected"' : '' ?>><?php echo $folder->name ?></option>
                        <?php endforeach ?>
                    </select><br />
                    <span class="counts"><?php echo array_key_exists($feed->id, $totalPostCount) ? $totalPostCount[$feed->id] : '0' ?> / <?php echo array_key_exists($feed->id, $unreadPostCount) ? $unreadPostCount[$feed->id] : '0' ?> unread</span>
                </td>
                <td class="td-update">
                    <select id="sel-update-<?php echo $feed->id ?>" class="sel-update">
                        <option value="10"<?php echo $feed->update_freq==10 ? ' selected="selected"' : '' ?>>10 mins</option>
                        <option value="15"<?php echo $feed->update_freq==15 ? ' selected="selected"' : '' ?>>15 mins</option>
                        <option value="30"<?php echo $feed->update_freq==30 ? ' selected="selected"' : '' ?>>30 mins</option>
                        <option value="60"<?php echo $feed->update_freq==60 ? ' selected="selected"' : '' ?>>1 hr</option>
                        <option value="120"<?php echo $feed->update_freq==120 ? ' selected="selected"' : '' ?>>2 hrs</option>
                        <option value="240"<?php echo $feed->update_freq==240 ? ' selected="selected"' : '' ?>>4 hrs</option>
                        <option value="360"<?php echo $feed->update_freq==360 ? ' selected="selected"' : '' ?>>6 hrs</option>
                        <option value="480"<?php echo $feed->update_freq==480 ? ' selected="selected"' : '' ?>>8 hrs</option>
                        <option value="720"<?php echo $feed->update_freq==720 ? ' selected="selected"' : '' ?>>12 hrs</option>
                        <option value="1440"<?php echo $feed->update_freq==1440 ? ' selected="selected"' : '' ?>>1 day</option>
                    </select><br/>
                    <span class="last-update"><?php echo array_key_exists($feed->id, $updates) ? date('M j, g:ia', $updates[$feed->id]->updated) : 'Never' ?></span>
                </td>
                <td class="td-display">
                    <select id="sel-sorting-<?php echo $feed->id ?>" class="sel-sorting">
                        <option value="false" <?php echo !$feed->newest_first ? ' selected="selected"' : '' ?>>Ascending</option>
                        <option value="true" <?php echo $feed->newest_first ? ' selected="selected"' : '' ?>>Descending</option>
                    </select><br/>
                    <select id="sel-page-<?php echo $feed->id ?>" class="sel-page">
                        <option value="10"<?php echo $feed->per_page==10 ? ' selected="selected"' : '' ?>>10 per page</option>
                        <option value="20"<?php echo $feed->per_page==20 ? ' selected="selected"' : '' ?>>20 per page</option>
                        <option value="40"<?php echo $feed->per_page==40 ? ' selected="selected"' : '' ?>>40 per page</option>
                        <option value="50"<?php echo $feed->per_page==50 ? ' selected="selected"' : '' ?>>50 per page</option>
                        <option value="60"<?php echo $feed->per_page==60 ? ' selected="selected"' : '' ?>>60 per page</option>
                        <option value="100"<?php echo $feed->per_page==100 ? ' selected="selected"' : '' ?>>100 per page</option>
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
$(function() {
    $(".delete").click(function() {
        var button = $(this);
        button.hide();
        button.after('<img id="busy-del" class="delete" src="/static/images/busy.gif" />');

        var id = this.id.substr(7);
        var name = $('#name-'+id).text();

        var result = window.confirm(name + "\n Are you sure?");
        if (result == false) {
            $("#busy-del").remove();
            button.show();
            return;
        }


        $.ajax({
            url: "/feedConfig",
            type: "POST",
            dataType: 'json',
            data: JSON.stringify({ op: 'del-feed', id: id })

        }).fail(function() {
            alert("Unable to delete feed");

        }).done(function() {
            location.reload(true);

        }).always(function() {
            $("#busy-del").remove();
            button.show();
        });
    });
    $("#add-rss").click(function() {
        if ( $("#tr-new-rss").css('visibility') == 'visible' ) {
            $("#tr-new-rss").css('visibility', 'collapse');
            $("#new-rss-url").val('');
            $("#new-rss-name").val('');
        } else {
            $("#tr-new-rss").css('visibility', 'visible');
            $("#new-rss-name").focus();
        }
    });
    $("#new-rss-button").click(function() {
        var name = $("#new-rss-name").val();
        var url = $("#new-rss-url").val();
        var button = $('#add-rss');

        if (name.length == 0) {
            alert('Name is required');
            return;
        }
        if (!isUrl(url)) {
            alert('Invalid url: "'+ url +'"');
            return;
        }

        button.hide();
        button.after('<img id="busy-new-rss" src="/static/images/busy.gif" />');
        $("#new-rss-name").val('');
        $("#new-rss-url").val('');
        $("#tr-new-rss").css('visibility', 'collapse');

        $.ajax({
            url: "/feedConfig",
            type: "POST",
            dataType: 'json',
            data: JSON.stringify({ op: 'new-feed', name: name, url: url })

        }).fail(function() {
            alert("Unable to create feed");

        }).done(function() {
            location.reload(true);

        }).always(function() {
            $("#busy-new-rss").remove();
            button.show();
        });
    });
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

<?php include Template::file('layout/footer') ?>
