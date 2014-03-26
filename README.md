ListWiki
========

A PHP Class to get the textual contents of a wikipedia page as a Array or JSON.

You can create an object to the class by passing the wikipedia URL as parameter to the constructor. It will support parameterized constructor only.
for instance,
        
        $obj = new ListWiki("http://en.wikipedia.org/wiki/PHP");
        
there are only two functions you can use.

        $json = $obj->getJSON();    //Gets the content as JSON of the wiki page
        $arr = $obj->getArray();    //Gets the content as PHP Array
        
Limitations:

* There might be some encoding problems which you have to take care of. I could not fix it properly, any forks will be welcomed.
* The content at the beginning will be with the key as 'Introduction' on the array and JSON returned.
        
        
