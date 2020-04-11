<?php
/**
 * This handles the data processing for the Media page.
 **/
function engine_media_page()
{
  if (isset($_POST['DeleteSubmit']) && isset($_POST['Delete']) && !empty($_POST['Delete']))
  {
    $file = htmlspecialchars($_POST['Delete']);
    if (file_exists($file) && substr($file,0,11) == "../uploads/")
      unlink($file) or die("Couldn't delete file");
  }

  if (isset($_POST['AddSubmit']))
  {
    $targetDir = "../uploads/";
    $targetFile = $targetDir . basename($_FILES["UploadFile"]["name"]);
    $fineToUpload = 1;
    $fileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));

    if (file_exists($targetFile) && $targetFile != $targetDir . "favicon.ico" && $targetFile != $targetDir . "apple-touch-icon.png") //Check if file already exists
    {
      echo "File already exists.";
      $fineToUpload = 0;
    }

    if ($_FILES["UploadFile"]["size"] > 2000000) //Check file size
    {
      echo "Your file is too large.";
      $fineToUpload = 0;
    }

    if (!($fileType == "jpg" || $fileType == "jpeg" || $fileType == "png" || $fileType == "bmp" || $fileType == "gif" || $fileType == "tiff" || $fileType == "ogg" || $fileType == "ogv" || $fileType == "webm" || $fileType == "mp4" || $fileType == "txt" || $fileType == "rtf" || $fileType == "pdf" || $fileType == "docx" || $fileType == "pptx" || $fileType == "xlsx" || $fileType == "csv" || $fileType == "odt" || $fileType == "odp" || $fileType == "ods" || $fileType == "odg" || $fileType == "mp3" || $fileType == "ico")) //Allow certain file formats
    {
      echo "Allowed formats are: jpg, jpeg, png, bmp, gif, tiff, ogg, ogv, webm, mp4, mp3, txt, rtf, pdf, docx, pptx, xlsx, csv, odt, odp, ods, odg, ico.";
      $fineToUpload = 0;
    }

    if ($fineToUpload == 0) //Check if $fineToUpload is set to 0 by an error
      echo "Sorry, your file was not uploaded.";
    else //if everything is ok, try to upload file
    {
      if ($targetFile == $targetDir . "favicon.ico")
      {
        if (file_exists("../uploads/favicon.ico"))
          unlink("../uploads/favicon.ico") or die("Couldn't delete old file.");
      }
      else if ($targetFile == $targetDir . "apple-touch-icon.png")
      {
        if (file_exists("../uploads/apple-touch-icon.png"))
          unlink("../uploads/apple-touch-icon.png") or die("Couldn't delete old file.");
      }

      if (move_uploaded_file($_FILES["UploadFile"]["tmp_name"], $targetFile))
        echo "The file ". basename( $_FILES["UploadFile"]["name"]). " has been uploaded.";
      else
        echo "Sorry, there was an error uploading your file.";
    }
  }
  UI_media_page('Page');
}

/**
 * This handles the UI for the Media page, and the Media plugin on the Add or Edit Posts page.
 * @param pageOrPlugin - Holds Page or Plugin depending on whether the page has been called, or the plugin.
 **/
function UI_media_page($pageOrPlugin)
{
  if ($pageOrPlugin != 'Plugin'){ ?><div class="container-fluid">
<div class="row">
  <form class="form-horizontal col-10 offset-1">
    <fieldset class="form-group">
      <legend>Your Media:</legend>
    </fieldset>
  </form>
</div>
<div class="row">  <?php } ?>
    <?php if ($pageOrPlugin != 'Plugin'){ ?><div class="col-10 offset-1">  <?php } else { ?><div class="col-12">  <?php } ?>
    <div class="table-responsive">
      <table class="table table-condensed">
        <thead>
          <tr>
<?php if ($pageOrPlugin != 'Plugin'){ ?>
            <th>Image:</th>
            <th>HTML Code (copy into a blog post):</th>
            <th>Location:</th>
            <th>Uploaded on:</th>
            <th>Delete:</th>
<?php } else { ?>
            <th>Image:</th>
            <th>Copy to clipboard:</th>
            <th>HTML Code (copy into a blog post):</th>
            <th>Location:</th>
<?php } ?>
          </tr>
        </thead>
        <tbody>
            <?php sub_UI_media_page_FindAndPrintFileData($pageOrPlugin); ?>
        </tbody>
      </table>
    </div>
      <?php if ($pageOrPlugin != 'Plugin'){ ?><form method="post" enctype="multipart/form-data">
      <div class="row">
        <input type="file" name="UploadFile" class="form-control-file col-6" id="UploadFile">
        <input type="submit" class="btn btn-light col-6" name="AddSubmit" id="AddSubmit" value="Add Media" />
      </div>
    </form><?php } ?>
  </div>
  <?php if ($pageOrPlugin != 'Plugin'){ ?></div>  <?php } ?>
<?php if ($pageOrPlugin != 'Plugin'){ ?></div><?php }
}

/**
 * This handles the file data for the UI for the Media page.
 * @param pageOrPlugin - Holds Page or Plugin depending on whether the page has been called, or the plugin.
 **/
function sub_UI_media_page_FindAndPrintFileData($pageOrPlugin)
{
  if ($pageOrPlugin == 'Plugin')
    echo '<script>function copier(id){var copyText = document.getElementById(id);copyText.select();document.execCommand("copy");alert("Copied!");}</script>';
  $count = 0;
  foreach(array_filter(glob('../uploads'.'/*'),'is_file') as $file)
  {
    if (strcasecmp(substr($file,-20),'apple-touch-icon.png') == 0 || strcasecmp(substr($file,-11),'favicon.ico') == 0)
    {
      if ($pageOrPlugin != 'Plugin')
        echo '<tr><td></td><td></td><td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td></tr>';
      //Show the apple touch icon and favicon.  Don't do anything to show in the plugin.  It'll just confuse people.
    }
    else if (strcasecmp(substr($file,-4),'.png') == 0 || strcasecmp(substr($file,-4),'.jpg') == 0 || strcasecmp(substr($file,-5),'.jpeg') == 0 || strcasecmp(substr($file,-4),'.bmp') == 0 || strcasecmp(substr($file,-4),'.gif') == 0 || strcasecmp(substr($file,-5),'.tiff') == 0)
    {
      echo '<tr>';
      if ($pageOrPlugin != 'Plugin')
        echo '<td><img src="' . PROTOCOL . URL . substr($file,2) . '" alt="' . substr($file,11) . '" style="height:8vh;width:auto;" /></td><td>&ltimg src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; alt=&quot;' . substr($file,11) . '&quot; /&gt;</td><td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td><td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $file . '" /><input type="submit" class="btn btn-light btn-sm" name="DeleteSubmit" value="Delete" /></form></td>';
      else
      {
        echo '<td><img src="' . PROTOCOL . URL . substr($file,2) . '" alt="' . substr($file,11) . '" style="height:8vh;width:auto;" /></td><td>';
        echo "<input type='text' style='position:absolute;top:-900px;left:-900px;' id='" . $count . "' value='<img src=\"" . PROTOCOL . URL . substr($file,2) . "\" alt=\"" . substr($file,11) . "\" />' />";
        echo '<p class="btn btn-light btn-sm" onclick="copier(\'' . $count . '\')">Copy Link</p></td><td>&ltimg src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; alt=&quot;' . substr($file,11) . '&quot; /&gt;</td><td>' . substr($file,2) . '</td>';
      }
      echo '</tr>';
    }
    else if (strcasecmp(substr($file,-4),'.mp4') == 0 || strcasecmp(substr($file,-5),'.webm') == 0 || strcasecmp(substr($file,-4),'.ogv') == 0)
    {
      echo '<tr><td>No Image Available.</td><td>&lt;video controls&gt;&ltsource src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; /&gt;Your Web Browser Doesn&#39;t Support Videos!&lt;/video&gt;</td>' . '<td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
      if ($pageOrPlugin != 'Plugin')
        echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $file . '" /><input type="submit" class="btn btn-light btn-sm" name="DeleteSubmit" value="Delete" /></form></td>';
      echo '</tr>';
    }
    else if (strcasecmp(substr($file,-4),'.mp3') == 0 || strcasecmp(substr($file,-4),'.ogg') == 0)
    {
      echo '<tr><td>No Image Available.</td><td>&lt;audio controls&gt;&ltsource src=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; /&gt;Your Web Browser Doesn&#39;t Support Audio!&lt;/audio&gt;</td>' . '<td>' . substr($file,2) . '</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
      if ($pageOrPlugin != 'Plugin')
        echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $file . '" /><input type="submit" class="btn btn-light btn-sm" name="DeleteSubmit" value="Delete" /></form></td>';
      echo '</tr>';
    }
    else
    {
      echo '<tr><td>No Image Available.</td>' . '<td>' . substr($file,2) . '</td><td>&lta href=&quot;' . PROTOCOL . URL . substr($file,2) . '&quot; title=&quot;' . substr($file,11) . '&quot; &gt;' . PROTOCOL . URL . substr($file,2) . '&lt;/a&gt;</td><td> ' . date ("Y-m-d H:i:s.", filemtime($file)) . '</td>';
      if ($pageOrPlugin != 'Plugin')
        echo '<td><form method="post" style="display:inline;"><input id="Delete" name="Delete" type="hidden" value="' . $file . '" /><input type="submit" class="btn btn-light btn-sm" name="DeleteSubmit" value="Delete" /></form></td>';
      echo '</tr>';
    }
    $count++;
  }
}

/**
 * This handles the Media page plugin.
 **/
function engine_media_plugin()
{
  UI_media_page('Plugin');
}
?>