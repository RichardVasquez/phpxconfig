<?php
/*
 * Copyright (C) 2002 Richard R. Vasquez, Jr.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA
 *
 * +----------------------------------------------------------------------+
 * You can also view a copy of the GNU General Public License at
 * http://www.gnu.org/licenses/gpl.txt
 * +----------------------------------------------------------------------+
 *
 * PhpXconfig 1.0
 *
 * +======================================================================+
 * | A PHP class to read an XML file, storing the data for configuration
 * | purposes.  Uses the wonderful PHP.XPath library found at
 * | http://www.carrubbers.org/scripts/php/xpath/
 * +----------------------------------------------------------------------+
 * | Requires PHP version  4.0.5 and up (PHP.XPath requirement)
 * +----------------------------------------------------------------------+
 * | Author: Richard R. Vasquez, Jr. (http://www.chaos.org/)
 * +----------------------------------------------------------------------+
 * | Usage:
 * |
 * | This is a pretty simple class.  Following are the two ways of
 * | initializing it, then an explanation of the method used to retrieve
 * | the data.
 * +----------------------------------------------------------------------+
 * | Static method: Extend the class and set the following variables in
 * | your extension.
 * |
 * |     doctype   -> Set this to the root node tag of your XML file.
 * |     required  -> An array of sections that are immediate children of
 * |                  the root node.  These section *MUST* exist in your
 * |                  XML file for the class to run.  An error will be
 * |                  generated and the class will terminate the program.
 * |     optional  -  An array of sections that are immediate children of
 * |                  the root node.  These section are optional, and the
 * |                  class will be fine if these sections aren't included
 * |                  in the XML file.
 * |     id_attrib -> An attribute that will be searched for in tags found
 * |                  in the XML file.  If the attribute is found, then an
 * |                  an index with that name will be created in the
 * |                  resulting associative array.
 * | Example:
 * |
 * |     class myPhpXconfig extends PhpXconfig
 * |     {
 * |        var doctype   = "myconfig";
 * |        var required  = array("section1", "section2", "section3");
 * |        var optional  = array("section4", "section5");
 * |        var id_attrib = "id";
 * |     }
 * |
 * |     $my_config = myPhpXconfig("filename.xml");
 * +----------------------------------------------------------------------+
 * | Dynamic method: You can instantiate the class as is, then modify the
 * | above listed variables with the following methods:
 * |
 * |     setDoctype($data)  -> sets the above doctype variable.
 * |     addRequired($data) -> adds required section(s).  Takes a single
 * |                           text string for a section name, or an array
 * |                           of strings.
 * |     addOptional($data) -> adds optional section(s).  Takes a single
 * |                           text string for a section name, or an array
 * |                           of strings.
 * |     setID($data)       -> sets the above id_attrib variable.
 * |
 * | Example:
 * |
 * |     $my_config = new PhpXconfig();
 * |     $my_config->setDoctype("myconfig");
 * |     $my_config->addRequired("section1");
 * |     $my_config->addRequired(array("section2", "section3"));
 * |     $my_config->addOptional(array("section4", "section5"));
 * |     $my_config->setID("id");
 * |     $my_config->Parse("filename.xml");
 * +----------------------------------------------------------------------+
 * | Retrieving data: The retrieval method uses a pathlike syntax to
 * | get the config values.  Assume the following XML file:
 * |
 * |  <?xml version="1.0"?>
 * |  <config>
 * |      <site>
 * |         <root>W:/apache/sites/chaos.org/www</root>
 * |         <includes>
 * |            <dir>include</dir>
 * |            <dir>include/meson</dir>
 * |            <dir>include/proton</dir>
 * |            <dir>include/photon</dir>
 * |            <dir>include/functions</dir>
 * |         </includes>
 * |         <url>http://www.chaos.org/</url>
 * |      </site>
 * |      <database>
 * |         <server>localhost</server>
 * |         <user>chaos_db</user>
 * |         <password>chaos_password</password>
 * |         <database>db_chaos</database>
 * |         <tables>
 * |            <table id="users">chaos_users</table>
 * |            <table id="msg">chaos_msg</table>
 * |            <table id="forum">chaos_forum</table>
 * |         </tables>
 * |         <query id="admin">
 * |            SELECT name, password FROM chaos_users WHERE admin=1
 * |         </query>
 * |         <query id="user">
 * |            SELECT name, password FROM chaos_users WHERE admin=0
 * |         </query>
 * |      </database>
 * |      <css>
 * |         <html  id="body">
 * |            background-color: #ffffff;
 * |            font-family: Helvetica, Arial, sans-serif;
 * |            font-size: 1em;
 * |         </html>
 * |         <html  id="p">
 * |            background-color: #ffffff;
 * |            color: #000099;
 * |            font-family: Helvetica, Arial, sans-serif; font-size: 1em;
 * |         </html>
 * |         <class id="back1">
 * |            background-color: #eeeeee;
 * |            font-family: Helvetica, Arial, sans-serif;
 * |            font-size: .75em
 * |         </class>
 * |      </css>
 * |  </config>
 * |
 * | After the file has been parsed, performing the following commands:
 * |
 * |     $dirs = $my_config->getData("/site/includes/dir");
 * |     print_r($dirs);
 * |
 * | Returns the following:
 * |
 * |     Array
 * |     (
 * |        [0] => include
 * |        [1] => include/meson
 * |        [2] => include/proton
 * |        [3] => include/photon
 * |        [4] => include/functions
 * |     )
 * |
 * | These commands:
 * |
 * |     echo $my_config->getData("/database/tables/table/users"));
 * |     echo "<br />";
 * |     echo $my_config->getData("/database/tables/table/msg"));
 * |     echo "<br />";
 * |     echo $my_config->getData("/database/tables/table/forum"));
 * |
 * | Return:
 * |
 * |     chaos_users
 * |     chaos_msg
 * |     chaos_forum
 * +----------------------------------------------------------------------+
 * | Additional:
 * |
 * | Any questions you may have can be answered via email.  To find the
 * | currently valid email address where you can send questions to, go to:
 * | http://www.chaos.org/contact/
 * +----------------------------------------------------------------------+
 */

   require_once("XPath.class.php");

   class PhpXconfig
   {
      // Holding place for xpath parser
      var $xpath;

      // Document type - also the root node
      var $doctype  = "";

      // Required sections under doctype
      var $required = array();

      // Optional sections under doctype
      var $optional = array();

      // Sets the attribute name that will allow for naming indexes in associative arrays
      var $id_attrib = "";

      // Location of final data
      var $_data     = array();

      // Status check to see if fucntions will work
      var $status   = false;

      function configXML($filename = "")
      {
         $filename = trim($filename);

         if($filename != "")
         {
            $this->parse($filename);
         }
      }

      function Parse($filename = "")
      {
         $filename = trim($filename);

         if($this->status == false)
         {
            if(is_file($filename) && is_readable($filename))
            {
               $this->xpath = new XPath($filename);
               $this->status = true;
            }
            else
            {
               die("<strong>configXML::parse()</strong> No valid filename.");
            }
         }

         if($this->doctype == "")
         {
            die("<strong>configXML::parse()</strong> No doctype specified.");
         }

         $all_sections = array_merge($this->required, $this->optional);

         if(count($all_sections) > 0)
         {
            foreach($all_sections as $check)
            {
               $do_check = $this->xpath->match("/" . $this->doctype . "/" . $check);
               if(count($do_check) == 1)
               {
                  $roots[$check] = $do_check[0];
               }
               elseif(count($do_check) == 0)
               {
                  if(in_array($check, $this->required))
                  {
                     die("<strong>configXML::parse()</strong> Missing required section '$check'");
                  }
               }
               else
               {
                  die("<strong>configXML::parse()</strong> More than one (found " . count($do_check) . ") section '$check'");
               }
            }
         }
         else
         {
            die("<strong>configXML::parse()</strong> No known sections under '{$this->doctype}'");
         }

         // Picked up all root sections.  Now pick up the subdata.
         foreach($roots as $check)
         {
            $this->_data[$this->xpath->nodeName($check)] = $this->children($check);
         }
      }

      function children($xpath="")
      {
         if($this->status == false)
         {
            die("<strong>configXML::children()</strong> No initial parsing has occurred.");
         }

         if($xpath == "")
         {
            return array();
         }

         $temp_xpath = $xpath . "/*";

         $children = $this->xpath->match($temp_xpath);

         $attribs  = $this->xpath->getAttributes($xpath);
         $data     = $this->xpath->getDataParts($xpath);
         $out_data = array();
         $fin_data = "";

         $this_node = $this->nodeData($xpath);
         if(isset($this_node["attribs"][$this->id_attrib]))
         {
            $tmp_name = $this_node["attribs"][$this->id_attrib];
            unset($this_node["attribs"][$this->id_attrib]);
            if(count($this_node["attribs"]) == 0)
            {
               unset($this_node["attribs"]);
            }
            $this_node[$tmp_name] = $this_node["CDATA"];
            unset($this_node["CDATA"]);
         }
         elseif(!isset($this_node["CDATA"]))
         {
            if(count($this_node["attribs"]) > 0)
            {
               $this_node = $this_node["attribs"];
            }
         }
         else
         {
            if(count($this_node["attribs"]) > 0)
            {
               $tmp_attribs = $this_node["attribs"];
               $this_node = array_merge(array("CDATA" => $this_node["CDATA"]), $this_node["attribs"]);
            }
            else
            {
               $this_node = $this_node["CDATA"];
            }
         }

         if(count($children) == 0)
         {
            return $this_node;
         }

         $out_data = array();
         foreach($children as $child)
         {
            $child_node = $this->xpath->nodeName($child);
            $child_data = $this->children($child);
            if(!isset($out_data[$child_node]))
            {
               $out_data[$child_node] = $child_data;
            }
            else
            {
               if(!is_array($out_data[$child_node]))
               {
                  $tmp_child = $out_data[$child_node];
                  $out_data[$child_node] = array($tmp_child);
               }

               if(!is_array($child_data))
               {
                  array_push($out_data[$child_node], $child_data);
               }
               else
               {
                  foreach($child_data as $idx => $val)
                  {
                     if(!isset($out_data[$child_node][$idx]))
                     {
                        $out_data[$child_node][$idx] = $val;
                     }
                     else
                     {
                        if(!is_array($out_data[$child_node][$idx]))
                        {
                           $out_data[$child_node][$idx] = array($out_data[$child_node][$idx]);
                        }
                        array_push($out_data[$child_node][$idx], $val);
                     }
                  }
               }
            }
         }
         return $out_data;
      }

      function nodeData($xpath)
      {
         if($this->status == false)
         {
            die("<strong>configXML::nodeData()</strong> No initial parsing has occurred.");
         }

         $attribs  = $this->xpath->getAttributes($xpath);
         $data     = $this->xpath->getDataParts($xpath);
         $out_data = array();
         $fin_data = "";

         foreach($data as $text)
         {
            $text = trim($text);
            if($text != "")
            {
               array_push($out_data, $text);
            }
         }
         $fin_data = implode(" ", $out_data);

         if($fin_data != "" && count($attribs) > 0)
         {
            return array(
               "CDATA"   => $fin_data,
               "attribs" => $attribs
            );
         }
         elseif($fin_data == "" && count($attribs) > 0)
         {
            return array(
               "attribs" => $attribs
            );
         }
         else
         {
            return array(
               "CDATA" => $fin_data
            );
         }
      }

      function getData($text = "/")
      {

         $path = explode("/", trim($text));

         if(count($path) > 0)
         {
            $idx_list = array();
            foreach($path as $part)
            {
               if(trim($part) != "")
               {
                  array_push($idx_list, $part);
               }
            }

            if(count($idx_list) > 0)
            {
               $tmp_data = $this->_data;
               while(count($idx_list) > 0)
               {
                  $tmp_idx  = $idx_list[0];
                  $tmp_data = $tmp_data[$tmp_idx];
                  array_shift($idx_list);
               }
               return $tmp_data;
            }
            else
            {
               return $this->_data;
            }
         }

         return NULL;
      }

      function addRequired($data = NULL)
      {
         return $this->_addSection($data, "required");
      }

      function addOptional($data = NULL)
      {
         return $this->_addSection($data, "optional");
      }

      function setDoctype($data = NULL)
      {
         if(!$this->_validName($data))
         {
            die("<strong>configXML::setDoctype()</strong> Invalid doctype name '$data'");
         }

         $this->doctype = $data;
         return true;
      }

      function setID($data = NULL)
      {
         if(!$this->_validName($data))
         {
            die("<strong>configXML::setID()</strong> Invalid id attribute name '$section'");
         }

         $this->id_attrib = $data;
      }

      function _validName($text = NULL)
      {
         $data = trim($text);
         if($data == "")
         {
            return false;
         }

         // I *know* the Unicode combining/extender Unicode chars aren't represented
         if(eregi("([a-z_:])([-a-z0-9._:~]*)", $text, $temp))
         {
            return true;
         }
         else
         {
            return false;
         }
      }

      function _addSection($data = NULL, $location = "")
      {
         switch(strtolower($location))
         {
            case "required":
               $temp_loc = &$this->required;
               break;
            case "optional":
               $temp_loc = &$this->optional;
               break;
            default:
               return false;
         }

         if(is_array($data))
         {
            if(count($data) > 0)
            {
               foreach($data as $section)
               {
                  if(!$this->_validName($section))
                  {
                     die("<strong>configXML::add$location()</strong> Invalid section name '$section'");
                  }

                  $section = trim($section);
                  array_push($temp_loc, $section);
               }
               $temp_loc = array_unique($temp_loc);
               return true;
            }
            else
            {
               return false;
            }
         }
         else
         {
            if(!$this->_validName($data))
            {
               die("<strong>configXML::add$location()</strong> Invalid section name '$data'");
            }

            array_push($temp_loc, $data);
            $temp_loc = array_unique($temp_loc);
            return true;
         }
         return false;
      }
   }
?>