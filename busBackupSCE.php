<?php
require_once('busConsistirPemissao.php');
require_once('Conn.php');	
set_time_limit(0); 
ignore_user_abort(true);
date_default_timezone_set('Brazil/East');
setlocale(LC_ALL, "pt_BR", "ptb");
$dia = date("d");
$mes = date("m");
$mesExtenso = gmstrftime("%B", time());
$ano = date("Y");
$horaReferencia = date('H').date('i')."00";
$horaMinuto     = date('H').":".date('i');
require 'phpzip.inc.php';

$hostdb     = $hostname_conn;
$db_name    = $database_conn;
$userdb     = $username_conn;
$passdb     = $password_conn;

if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {
	$hostdb     = '127.0.0.1';
	$db_name    = 'mysql';
	$userdb     = 'root';
	$passdb     = 'root';
}

$tabelas = array ('sce_configuracao','sce_usuario','sce_banco','sce_contribuinte','sce_quota','sce_titulo','sce_premios','sce_ocorrencia','sce_arquivo','sce_remessa','sce_texto','sce_controle_email');
$tempdir = "/tmp"; // diretorio temporario
#$filename = 'sql.'.time().'.txt';
$filename = 'Backup_SCE.txt';
$incluir_insert = 1; // imprime os INSERT's tambem
$con = mysql_pconnect($hostdb,$userdb,$passdb);
mysql_select_db($db_name);
$fp = fopen($filename,"w");
for ($x=0; $x<count($tabelas); $x++) {
   $saida = getTableDef($db_name, $tabelas[$x], "\n");
   fputs($fp,$saida."\n\n");
 
   if ($incluir_insert) {
      getTableContentFast($db_name, $tabelas[$x], '', '');
      fputs($fp,"\n\n");
   }
}

$nomePasta = '';
if (PHP_OS == "WIN32" || PHP_OS == "WINNT") {$nomePasta = "";}
$nomeArquivoDownload = $nomePasta.$filename;
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="'.$nomeArquivoDownload.'"');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($nomeArquivoDownload));
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Expires: 0');
readfile($nomeArquivoDownload);

exit;


EXIT;

// gerar o arquivo zipado
$zipname = ereg_replace("txt$","zip",$filename);
$zip = new PHPZip();
$files[]=$filename;
$zip -> Zip($files, $zipname);
 
$tamanho = filesize($zipname);
 
// imprimir arquivo p/ download
header("Content-Type: application/zip");
header("Content-Length: $tamanho");
header("Content-Disposition: attachment; filename=$zipname");
header("Content-Transfer-Encoding: binary");
 
// abrir e enviar o arquivo
$fp = fopen("$zipname", "r");
fpassthru($fp);
fclose($fp);
 
// remover os arquivos temporarios
unlink($filename);
unlink($zipname);
 
// FIM DO PROGRAMA
// --------------------------------------------------------
 
 
// --------------------------------------------------------
// PROCEDIMENTOS - Baseado no csdigo do phpmyadmin
function sqlAddslashes($a_string = '', $is_like = FALSE) {
  if ($is_like) {
    $a_string = str_replace('\\', '\\\\\\\\', $a_string);
  } else {
    $a_string = str_replace('\\', '\\\\', $a_string);
  }
  $a_string = str_replace('\'', '\\\'', $a_string);
 
  return $a_string;
} // end of the 'sqlAddslashes()' function
 
 
function backquote($a_name, $do_it = TRUE) {
  if ($do_it && PMA_MYSQL_INT_VERSION >= 32306 && !empty($a_name)
      && $a_name != '*') {
 
     if (is_array($a_name)) {
        $result = array();
        reset($a_name);
        while(list($key, $val) = each($a_name)) {
           $result[$key] = '`' . $val . '`';
        }
        return $result;
     } else {
        return '`' . $a_name . '`';
     }
  } else {
     return $a_name;
  }
} // end of the 'backquote()' function
 
/**
* Returns $table's CREATE definition
*
* @param   string   the database name
* @param   string   the table name
* @param   string   the end of line sequence
*
* @return  string   the CREATE statement on success
*
* @global  boolean  whether to add 'drop' statements or not
* @global  boolean  whether to use backquotes to allow the use of special
*                   characters in database, table and fields names or not
*
* @see     PMA_htmlFormat()
*
* @access  public
*/
function getTableDef($db, $table, $crlf) {
   global $drop;
   global $use_backquotes;
   global $con;
 
	$drop = "SIM";
 
   $schema_create = '';
   if (!empty($drop)) {
      $schema_create .= 'DROP TABLE IF EXISTS ' .
      backquote($table) . ';' . $crlf;
   }
 
   // For MySQL < 3.23.20
   $schema_create .= 'CREATE TABLE ' .
   backquote($table) . ' (' . $crlf;
 
   $local_query   = 'SHOW FIELDS FROM ' . backquote($table) . ' FROM '
   . backquote($db);
 
   $result = mysql_query($local_query,$con);
 
   while ($row = mysql_fetch_array($result)) {
      $schema_create     .= '   ' .
      backquote($row['Field'])
      . ' ' . $row['Type'];
 
      if (isset($row['Default']) && $row['Default'] != '') {
           $schema_create .= ' DEFAULT \'' .
           sqlAddslashes($row['Default']) . '\'';
      }
 
      if ($row['Null'] != 'YES') {
           $schema_create .= ' NOT NULL';
      }
 
      if ($row['Extra'] != '') {
           $schema_create .= ' ' . $row['Extra'];
      }
 
      $schema_create     .= ',' . $crlf;
   } // end while
 
   mysql_free_result($result);
   $schema_create = ereg_replace(',' . $crlf . '$', '', $schema_create);
 
   $local_query = 'SHOW KEYS FROM ' . backquote($table) . ' FROM '
   . backquote($db);
 
   $result = mysql_query($local_query,$con);
   while ($row = mysql_fetch_array($result)) {
       $kname    = $row['Key_name'];
       $comment  = (isset($row['Comment'])) ? $row['Comment'] : '';
       $sub_part = (isset($row['Sub_part'])) ? $row['Sub_part'] : '';
 
       if ($kname != 'PRIMARY' && $row['Non_unique'] == 0) {
           $kname = "UNIQUE|$kname";
       }
 
       if ($comment == 'FULLTEXT') {
           $kname = 'FULLTEXT|$kname';
       }
 
       if (!isset($index[$kname])) {
           $index[$kname] = array();
       }
 
       if ($sub_part > 1) {
           $index[$kname][] = backquote($row['Column_name']) . '(' . $sub_part . ')';
       } else {
           $index[$kname][] = backquote($row['Column_name']);
       }
   } // end while
   mysql_free_result($result);
 
   while (list($x, $columns) = @each($index)) {
       $schema_create .= ',' . $crlf;
       if ($x == 'PRIMARY') {
          $schema_create .= '   PRIMARY KEY (';
       } else if (substr($x, 0, 6) == 'UNIQUE') {
          $schema_create .= '   UNIQUE ' . substr($x, 7) . ' (';
       } else if (substr($x, 0, 8) == 'FULLTEXT') {
          $schema_create .= '   FULLTEXT ' . substr($x, 9) . ' (';
       } else {
          $schema_create .= '   KEY ' . $x . ' (';
       }
       $schema_create .= implode($columns, ', ') . ')';
   } // end while
 
   $schema_create .= $crlf . ');';
 
   return $schema_create;
} // end of the 'getTableDef()' function
 
/**
* php >= 4.0.5 only : get the content of $table as a series of INSERT
* statements.
* After every row, a custom callback function $handler gets called.
*
* Last revision 13 July 2001: Patch for limiting dump size from
* vinay@sanisoft.com & girish@sanisoft.com
*
* @param   string   the current database name
* @param   string   the current table name
* @param   string   the 'limit' clause to use with the sql query
* @param   string   the name of the handler (function) to use at the end
*                   of every row. This handler must accept one parameter
*                   ($sql_insert)
*
* @return  boolean  always true
*
* @global  boolean  whether to use backquotes to allow the use of special
*                   characters in database, table and fields names or not
* @global  integer  the number of records
* @global  integer  the current record position
*
* @access  private
*
* @see     PMA_getTableContent()
*
* @author  staybyte
*/
function getTableContentFast($db, $table, $add_query = '', $handler) {
   global $use_backquotes;
   global $rows_cnt;
   global $current_row;
   global $con;
   global $fp;
 
  $local_query = 'SELECT * FROM ' . backquote($db) . '.' . backquote($table)
  . $add_query;
 
  $result = mysql_query($local_query,$con);
  if ($result != FALSE) {
     $fields_cnt = mysql_num_fields($result);
     $rows_cnt   = mysql_num_rows($result);
 
     // Checks whether the field is an integer or not
     for ($j = 0; $j < $fields_cnt; $j++) {
         $field_set[$j] = backquote(mysql_field_name($result, $j), $use_backquotes);
         $type = mysql_field_type($result, $j);
         if ($type == 'tinyint' || $type == 'smallint' ||
             $type == 'mediumint' || $type == 'int' ||
             $type == 'bigint'  ||$type == 'timestamp') {
             $field_num[$j] = TRUE;
         } else {
             $field_num[$j] = FALSE;
         }
     } // end for
 
     // Sets the scheme
     if (isset($GLOBALS['showcolumns'])) {
         $fields = implode(', ', $field_set);
         $schema_insert = 'INSERT INTO ' . backquote($table)
         . ' (' . $fields . ') VALUES (';
     } else {
         $schema_insert = 'INSERT INTO ' .
         backquote($table) . ' VALUES (';
     }
 
     $search = array("\x00", "\x0a", "\x0d", "\x1a"); //\x08\\x09, not required
     $replace      = array('{FONTE}', '\n', '\r', '\Z');
     $current_row  = 0;
 
     @set_time_limit($GLOBALS['cfg']['ExecTimeLimit']);
 
     // loic1: send a fake header to bypass browser timeout if data
     //        are bufferized - part 1
     if (!empty($GLOBALS['ob_mode']) || (isset($GLOBALS['zip'])
         || isset($GLOBALS['bzip']) || isset($GLOBALS['gzip']))) {
         $time0 = time();
     }
 
     while ($row = mysql_fetch_row($result)) {
         $current_row++;
         for ($j = 0; $j < $fields_cnt; $j++) {
            if (!isset($row[$j])) {
                 $values[] = 'NULL';
            } else if ($row[$j] == '0' || $row[$j] != '') {
                 // a number
                 if ($field_num[$j]) {
                     $values[] = $row[$j];
                 } else {
                    // a string
                    $values[] = "'" . str_replace($search, $replace,
                    sqlAddslashes($row[$j])) . "'";
                 }
           } else {
              $values[] = "''";
           } // end if
        } // end for
 
        // Extended inserts case
        if (isset($GLOBALS['extended_ins'])) {
            if ($current_row == 1) {
               $insert_line  = $schema_insert . implode(', ', $values) . ');';
            } else {
               $insert_line  = '(' . implode(', ', $values) . ');';
            }
        } else {
        // Other inserts case
           $insert_line = $schema_insert . implode(', ', $values) . ');';
        }
        unset($values);
 
        // Call the handler
        fputs($fp,$insert_line . "\n");
 
        // loic1: send a fake header to bypass browser timeout if data
        //        are bufferized - part 2
        if (isset($time0)) {
            $time1 = time();
            if ($time1 >= $time0 + 30) {
               $time0 = $time1;
               header('X-pmaPing: Pong');
            }
        } // end if
     } // end while
  } // end if ($result != FALSE)
  mysql_free_result($result);
 
  return TRUE;
} // end of the 'getTableContentFast()' function
?>