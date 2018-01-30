<html>
  <head>
    <title>Database migration for BibSonomy - ext_bibsonomy to ext_bibsonomy_csl</title>
  </head>
  <body>
    <h1>Database migration for BibSonomy - ext_bibsonomy to ext_bibsonomy_csl</h1>
    <p>
      <form action="" method="post">
        <p>User: <input name="user" value="<?php echo @$_POST["user"]; ?>" type=text></p>
        <p>Pass: <input name="pass" value="<?php echo @$_POST["pass"]; ?>" type=password></p>
        <p>Host: <input name="host" value="<?php echo @$_POST["host"]; ?>" type=text></p>
        <p>Datenbank: <input name="db" value="<?php echo @$_POST["db"]; ?>" type=text></p>
        <button type=submit>Migrate</button></form>
    </p>
    <?php
        /*$source = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                        <T3FlexForms>
                            <data>
                                <sheet index="bib_general">
                                    <language index="lDEF">
                                        <field index="bib_content">
                                            <value index="vDEF">/user/butonic</value>
                                        </field>
                                        <field index="bib_include_publications">
                                            <value index="vDEF">1</value>
                                        </field>
                                        <field index="bib_publication_count">
                                            <value index="vDEF">500</value>
                                        </field>
                                        <field index="bib_include_tagcloud">
                                            <value index="vDEF">1</value>
                                        </field>
                                        <field index="bib_publications_width">
                                            <value index="vDEF">100%</value>
                                        </field>
                                        <field index="bib_tagcloud_width">
                                            <value index="vDEF">100%</value>
                                        </field>
                                    </language>
                                </sheet>
                                <sheet index="bib_tagcloud">
                                    <language index="lDEF">
                                        <field index="bib_tagcloud_size">
                                            <value index="vDEF">100</value>
                                        </field>
                                        <field index="bib_show_relatedtags">
                                            <value index="vDEF">0</value>
                                        </field>
                                        <field index="bib_tagcloud_blacklist">
                                            <value index="vDEF"></value>
                                        </field>
                                    </language>
                                </sheet>
                                <sheet index="bib_layout">
                                    <language index="lDEF">
                                        <field index="bib_layout_type">
                                            <value index="vDEF">/layout/custom</value>
                                        </field>
                                        <field index="bib_show_quickbar">
                                            <value index="vDEF">0</value>
                                        </field>
                                        <field index="bib_select_entrytype">
                                            <value index="vDEF">all</value>
                                        </field>
                                        <field index="bib_update_layouts">
                                            <value index="vDEF">0</value>
                                        </field>
                                    </language>
                                </sheet>
                                <sheet index="bib_login">
                                    <language index="lDEF">
                                        <field index="bib_login_name">
                                            <value index="vDEF">test.user</value>
                                        </field>
                                        <field index="bib_user_password">
                                            <value index="vDEF">f5cc5te32fb8f13e0k8d6ee37bfe25ff</value>
                                        </field>
                                    </language>
                                </sheet>
                                <sheet index="bib_misc">
                                    <language index="lDEF">
                                        <field index="bib_decode_iso">
                                            <value index="vDEF">0</value>
                                        </field>
                                        <field index="bib_show_metadata">
                                            <value index="vDEF">0</value>
                                        </field>
                                        <field index="bib_server_url">
                                            <value index="vDEF">http://www.bibsonomy.org</value>
                                        </field>
                                    </language>
                                </sheet>
                            </data>
                        </T3FlexForms>';*/

      /*
        source database columns: tt_content: pi_flexform;

        target database columns: tt_content: tx_extbibsonomy_login_name, tx_extbibsonomy_api_key, tx_extbibsonomy_server_url, tx_extbibsonomy_export_layout,
        tx_extbibsonomy_export_content, tx_extbibsonomy_decode_iso, tx_extbibsonomy_include_tagcloud, tx_extbibsonomy_include_publications,
        tx_extbibsonomy_tagcloud_size, tx_extbibsonomy_related_tags, tx_extbibsonomy_meta_output;
      */

      if(empty($_POST['host']) || empty($_POST['user']) || empty($_POST['pass']) || empty($_POST['db'])) exit;

      $con = mysql_connect(@$_POST["host"],@$_POST["user"],@$_POST["pass"]) 
      or die('Invalid/No data sent!');

      // select a database:
      mysql_select_db(@$_POST["db"])
      or die('Could not select database!');
      
      $result = mysql_query("SELECT uid, pi_flexform FROM tt_content WHERE list_type='ext_bibsonomy_pi1' AND deleted=0")
      or die('Error: ' . mysql_error());
      
      echo "<p><b>All database entries have been converted. Don't forget to clear the cache.</b></p>";

      while ($row = mysql_fetch_array($result)) {
      $typ = "Unknown";
      //$newtype = "'"; //in emergency, break the script instead of the database
      $dataArr = Main($row['pi_flexform']);

      //print_r($dataArr);

      //print_r($sheets);

      if ($dataArr["include_tagcloud"] == 1) {
          //require_once('tagcloud.php');
          $typ = 'Tagcloud'; $newtype = 'extbibsonomycsl_tagcloud';
$export = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="bib_general">
            <language index="lDEF">
                <field index="settings.bib_tag_content_key">
                    <value index="vDEF">' . $dataArr["content_key"] . '</value>
                </field>
                <field index="settings.bib_tag_content_value">
                    <value index="vDEF">' . $dataArr["content_value"] . '</value>
                </field>
                <field index="settings.bib_tags">
                    <value index="vDEF"></value>
                </field>
                <field index="settings.bib_blacklist_tags">
                    <value index="vDEF">' . $dataArr["tagcloud_blacklist"] . '</value>
                </field>
                <field index="settings.bib_tag_maxcount">
                    <value index="vDEF">' . $dataArr["tagcloud_size"] . '</value>
                </field>
            </language>
        </sheet>
        <sheet index="bib_login">
            <language index="lDEF">
                <field index="settings.bib_tag_server">
                    <value index="vDEF">' . $dataArr["server_url"] . '/api</value>
                </field>
                <field index="settings.bib_tag_login_name">
                    <value index="vDEF">' . $dataArr["login_name"] . '</value>
                </field>
                <field index="settings.bib_tag_api_key">
                    <value index="vDEF">' . $dataArr["api_key"] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>';
} else {
$export = '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
<T3FlexForms>
    <data>
        <sheet index="bib_general">
            <language index="lDEF">
                <field index="settings.bib_content_key">
                    <value index="vDEF">' . $dataArr["content_key"] . '</value>
                </field>
                <field index="settings.bib_content_value">
                    <value index="vDEF">' . $dataArr["content_value"] . '</value>
                </field>
                <field index="settings.bib_tags">
                    <value index="vDEF"></value>
                </field>
                <field index="settings.bib_unselect_tags">
                    <value index="vDEF"></value>
                </field>
                <field index="settings.bib_search">
                    <value index="vDEF"></value>
                </field>
                <field index="settings.bib_maxcount">
                    <value index="vDEF">' . $dataArr["publication_count"] . '</value>
                </field>
                <field index="settings.bib_custom_api_query">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="bib_ordering">
            <language index="lDEF">
                <field index="settings.bib_grouping">
                    <value index="vDEF">none</value>
                </field>
                <field index="settings.bib_display_anchors">
                    <value index="vDEF">0</value>
                </field>
                <field index="settings.bib_sorting">
                    <value index="vDEF">year</value>
                </field>
                <field index="settings.bib_sorting_order">
                    <value index="vDEF">desc</value>
                </field>
                <field index="settings.bib_type_order">
                    <value index="vDEF"></value>
                </field>
            </language>
        </sheet>
        <sheet index="bib_layout">
            <language index="lDEF">
                <field index="settings.bib_stylesheet">
                    <value index="vDEF">http://www.zotero.org/styles/acs-chemical-biology</value>
                </field>
                <field index="settings.bib_ownstylesheet">
                    <value index="vDEF"></value>
                </field>
                <field index="settings.bib_link_abstract">
                    <value index="vDEF">0</value>
                </field>
                <field index="settings.bib_link_bibtex">
                    <value index="vDEF">0</value>
                </field>
                <field index="settings.bib_link_endnote">
                    <value index="vDEF">0</value>
                </field>
                <field index="settings.bib_link_url">
                    <value index="vDEF">0</value>
                </field>
                <field index="settings.bib_link_doc">
                    <value index="vDEF">nothing</value>
                </field>
                <field index="settings.bib_css_attribute_for_list_item">
                    <value index="vDEF">							
                                    

                                    .bibsonomy_publications {
                                    font-size: 1.0em;
                                    }

                                    .bibsonomy_publications span.citeproc-title { 
                                    display: block;
                                    text-decoration: none;
                                    font-weight: bold;
                                    font-size: 1.1em;
                                    }

                                    .bibsonomy_publications span.biblink {
                                    display: block;
                                    font-size: 1.1em;
                                    line-height: 2em;
                                    margin-right: 1.5em;
                                    float: left;
                                    }

                                    .citeproc-collection-title {
                                    font-style:italic;
                                    }
                                </value>
                </field>
            </language>
        </sheet>
        <sheet index="bib_login">
            <language index="lDEF">
                <field index="settings.bib_server">
                    <value index="vDEF">' . $dataArr["server_url"] . '/</value>
                </field>
                <field index="settings.bib_login_name">
                    <value index="vDEF">' . $dataArr["login_name"] . '</value>
                </field>
                <field index="settings.bib_api_key">
                    <value index="vDEF">' . $dataArr["api_key"] . '</value>
                </field>
            </language>
        </sheet>
    </data>
</T3FlexForms>';
/*require_once('publications.php');*/ $typ = 'Publikationsliste'; $newtype = 'extbibsonomycsl_publicationlist';}

      $uid = $row['uid'];
      $result2 = mysql_query("UPDATE tt_content SET pi_flexform='$export', list_type='$newtype' WHERE uid=$uid") or die('Error: ' . mysql_error());
      echo '<textarea readonly cols="110" rows="100">' . $export . '</textarea>';

      //1. change extension key in db to the one matching
      //2. require_once->flexform
      //3. pi_flexform->db

      echo "<p>Entry above: $typ.</p>";
}

      function copyFromTo($string, $start, $end) {
        $string = " ".$string;
        $ini = strpos($string,$start);
        if ($ini == 0) return "";
        $ini += strlen($start);
        $len = strpos($string,$end,$ini) - $ini;
        return substr($string,$ini,$len);
      }

      function getMainPart($input) {
        return copyFromTo($input, "<data>", "</data>");
      }

      function getSheet($input, $label) {
        return copyFromTo($input, "<sheet index=\"bib_$label\">", "</sheet>");
      }

      function getField($input, $label) {
        return copyFromTo($input, "<field index=\"bib_$label\">", "</field>");
      }

      function getFieldValue($input) {
        return copyFromTo($input, "<value index=\"vDEF\">", "</value>");
      }

      function removeLang($input) {
        return copyFromTo($input, "<language index=\"lDEF\">", "</language>");
      }

      function Main($source) {
        $source = getMainPart($source);

        $sheets = removeLang(getSheet($source, "general"));
        $sheets .= removeLang(getSheet($source, "tagcloud"));
        $sheets .= removeLang(getSheet($source, "layout"));
        $sheets .= removeLang(getSheet($source, "login"));
        $sheets .= removeLang(getSheet($source, "misc"));
      
        $dataKeys = array('content', 'include_tagcloud', 'publication_count', 'include_publications', 'tagcloud_width', 'publications_width', 'show_relatedtags', 'tagcloud_blacklist', 'layout_type', 'show_quickbar', 'select_entrytype', 'update_layouts', 'login_name', 'user_password' => 'api_key', 'server_url', 'show_metadata', 'decode_iso', 'tagcloud_size',);

        foreach($dataKeys as $oldkey => $newkey) {
          if (is_numeric($oldkey)) $oldkey = $newkey;
          //remove => easy template//$dataArr['settings.bib_' . $newkey] = getFieldValue(getField($sheets, $oldkey));
          $dataArr[$newkey] = getFieldValue(getField($sheets, $oldkey));
        }
      
        //adjustment for new format
        $dataArr["content_key"] = copyFromTo($dataArr["content"], '/', '/');
        $dataArr["content_value"] = copyFromTo(getField($sheets, 'content'), $dataArr["content_key"] . '/' , '<');

        return $dataArr;
      }
    ?>
  </body>
</html>
