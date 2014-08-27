<?php $page_css = array('reader.less', 'folder_config.less') ?>
<?php $page_js = array('less.js', 'jquery.js') ?>
<?php include 'header.layout.php' ?>

<div id="container">
    <div id="header">
        <h1>Folders</h1>
    </div><!-- header -->
    <div id="navigation">
        <ul>
            <li><a href="/">Feed List</a></li>
            <li><a href="/feedConfig">Feeds</a></li>
        </ul>
    </div><!-- navigation -->
    <div id="content">
        <table id='folder-table'>
            <tr>
                <th><img id="add-folder" src="/static/images/new_folder.png" width="24" /></th>
                <th>Folder</th>
                <th>Display</th>
                <th>Sort</th>
            </tr>
            <tr id="tr-new-folder" class="even">
                <td>&nbsp;</td>
                <td id="td-new-name" colspan="7">
                    <label for="name">Name:</label> <input id="new-folder-name" name="name" type="text"/>
                    <input id="new-folder-button" type="button" value="Add Folder"/>
                </td>
            </tr>
            <?php $row = 0; foreach ( $folders as $folder ) : $row ++ ?>
            <tr class="<?php echo $row % 2 == 0 ? 'even' : 'odd' ?>">
                <td><?php echo $folder->feed_count ?></td>
                <td class="td-name">
                    <span id="name-<?php echo $folder->id ?>" class="name"><?php echo $folder->name ?></span>
                    <img id="delete-<?php echo $folder->id ?>" class="delete" src="/static/images/delete.png" />
                </td>
                <td class="td-sort">
                    <select id="sel-page-<?php echo $folder->id ?>" class="sel-page">
                        <option value="10"<?php echo $folder->per_page==10 ? ' selected="selected"' : '' ?>>10 per page</option>
                        <option value="20"<?php echo $folder->per_page==20 ? ' selected="selected"' : '' ?>>20 per page</option>
                        <option value="25"<?php echo $folder->per_page==25 ? ' selected="selected"' : '' ?>>25 per page</option>
                        <option value="40"<?php echo $folder->per_page==40 ? ' selected="selected"' : '' ?>>40 per page</option>
                        <option value="50"<?php echo $folder->per_page==50 ? ' selected="selected"' : '' ?>>50 per page</option>
                        <option value="60"<?php echo $folder->per_page==60 ? ' selected="selected"' : '' ?>>60 per page</option>
                        <option value="100"<?php echo $folder->per_page==100 ? ' selected="selected"' : '' ?>>100 per page</option>
                    </select>
                </td>
                <td class="td-display">
                    <select id="sel-sorting-<?php echo $folder->id ?>" class="sel-sorting">
                        <option value="false" <?php echo !$folder->newest_first ? ' selected="selected"' : '' ?>>Ascending</option>
                        <option value="true" <?php echo $folder->newest_first ? ' selected="selected"' : '' ?>>Descending</option>
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
            url: "/folderConfig",
            type: "POST",
            dataType: 'json',
            data: JSON.stringify({ op: 'del-folder', id: id })

        }).fail(function() {
            alert("Unable to delete folder");

        }).done(function() {
            location.reload(true);

        }).always(function() {
            $("#busy-del").remove();
            button.show();
        });
    });
    $("#add-folder").click(function() {
        if ( $("#tr-new-folder").css('visibility') == 'visible' ) {
            $("#tr-new-folder").css('visibility', 'collapse');
            $("#new-folder-name").val('');
        } else {
            $("#tr-new-folder").css('visibility', 'visible');
            $("#new-folder-name").focus();
        }
    });
    $("#new-folder-button").click(function() {
        var name = $("#new-folder-name").val();
        var button = $('#add-folder');

        if (name.length == 0) {
            alert('Name is required');
            return;
        }

        button.hide();
        button.after('<img id="busy-new-folder" src="/static/images/busy.gif" />');
        $("#new-folder-name").val('');
        $("#tr-new-folder").css('visibility', 'collapse');

        $.ajax({
            url: "/folderConfig",
            type: "POST",
            dataType: 'json',
            data: JSON.stringify({ op: 'new-folder', name: name })

        }).fail(function() {
            alert("Unable to create folder");

        }).done(function() {
            location.reload(true);

        }).always(function() {
            $("#busy-new-folder").remove();
            button.show();
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
            url: "/folderConfig",
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
            alert("Unable to update folder");

        }).always(function() {
          $("#busy-"+id).remove();
          select.show();
        });
    });
    $(".sel-sorting").change(function() {
        var id = this.id.substr(12);
        var select = $(this);
        var newValue = $(select).children(':selected').first().val();

        select.hide();
        select.after('<img id="busy-'+ id +'" src="/static/images/busy.gif" />');

        $.ajax({
            url: "/folderConfig",
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
            alert("Unable to update folder");

        }).always(function() {
          $("#busy-"+id).remove();
          select.show();
        });
    });
});
</script>

<?php include 'footer.layout.php' ?>
