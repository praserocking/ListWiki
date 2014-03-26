<?php

/********************

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License along
    with this program; if not, write to the Free Software Foundation, Inc.,
    51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
    
@Author: Shenbaga Prasanna,S
@Class: ListWiki
@Description: Returns the content on the wikipedia url given as a PHP Array with their subtitles as key or as JSON in the same format.
@Written at Freshdesk's Hackathon.,College of Enginnering,Guindy.
@Date: 23/03/2014
@Email: shenbagaprasanna@gmail.com
@github: github.com/praserocking

*********************/
class ListWiki{
    
    private $url;
    private $content_array;
    
    function __construct($URL){
        $this->url=$URL;
        self::getContents();
    }
    
    private function removeDOMClass($classname,$finder){
            $nodes=$finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
            foreach($nodes as $node){
                $node->parentNode->removeChild($node);
            }
        }
    
    private function removeDOMId($id,$dom){
            $node=$dom->getElementById($id);
            $pnode=$node->parentNode;
            $pnode->removeChild($node);
        }
        
    private function getContents(){
        error_reporting(0);
        
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch,CURLOPT_USERAGENT," Mozilla/5.0 (Windows NT 6.2; WOW64)
                                            AppleWebKit/537.36 (KHTML, like Gecko)
                                            Chrome/31.0.1650.63
                                            Safari/537.36 ");
        curl_setopt($ch,CURLOPT_URL,$this->url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        $file=curl_exec($ch);
        
        $dom=new DomDocument();
        $dom->loadHTML($file);
        $finder=new DomXPath($dom);
        
        $classname="toctext";
        $nodes=$finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        
        $headings=array();
        foreach($nodes as $node){
            $headings[]=$node->nodeValue;
        }
        
        self::removeDOMClass("thumbcaption",$finder);
        self::removeDOMClass("reference",$finder);
        self::removeDOMClass("mw-headline",$finder);
        self::removeDOMClass("seealso",$finder);
        self::removeDOMClass("wikitable",$finder);
        self::removeDOMClass("de1",$finder);
        self::removeDOMClass("infobox",$finder);
        self::removeDOMClass("mw-editsection",$finder);
        self::removeDOMClass("rellink relarticle mainarticle",$finder);
        
        self::removeDOMId("mw-panel",$dom);
        self::removeDOMId("mw-head",$dom);
        self::removeDOMId("footer",$dom);
        self::removeDOMId("toc",$dom);
        
        $file=$dom->saveHTML();
        
        
        $contents=array();
        $parts=explode("<h2>",$file);
        $temp_parts=$parts;
        $final_parts=array();
        foreach($temp_parts as $i){
            if(strstr($i,"<h3>")){
                $tmp=explode("<h3>",$i);
                foreach($tmp as $j){
                    if(strstr($j,"<h4>")){
                        $tm=explode("<h4>",$j);
                        foreach($tm as $w)
                            $final_parts[]=$w;
                    }else{
                        $final_parts[]=$j;
                    }
                }
            }else{
                $final_parts[]=$i;
            }
        }
        $init=1;$hea=0;
        $content_map=array();
        foreach($final_parts as $cntnt){
            $hoho=strip_tags($final_parts[$init++]);
            $hoho=str_replace("\t"," ",$hoho);
            $content_map[$headings[$hea++]]=trim(stripslashes($hoho));
            $hoho=str_replace("\n","<br/>",$hoho);
        }
        $intro=explode('class="mw-content-ltr">',$final_parts[0])[1];
        $content_map['Introduction']= trim(strip_tags($intro));
        
        $this->content_array=$content_map;
    }
    
    function getArray(){
        return $this->content_array;
    }
    
    function getJSON(){
        return json_encode($this->content_array);
    }
}
/*
 ***Testing**
 ************

$test=new ListWiki("http://en.wikipedia.org/wiki/PHP");
echo $test->getJSON();

*/
?>
